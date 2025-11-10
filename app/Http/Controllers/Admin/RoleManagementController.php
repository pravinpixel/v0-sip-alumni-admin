<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RoleHasPermission;
use App\Models\RoleUserHasPermission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class RoleManagementController extends Controller
{
  protected $pageNumber = 1;
  protected $perPage = 10;
  
    public function index(Request $request)
    {
          $search = $request->input('search');
          $status = $request->input('status');
          $perPage = $request->input('pageItems');

          $query = Role::query();

          if ($search) {
              $query->where(function ($q) use ($search) {
                  $q->where('name', 'like', '%' . $search . '%');

              });
          }

          if ($status === '1' || $status === '0') {
              $query->where('status', $status);
          }

          $datas = $query->orderBy('id','desc')->paginate($perPage);
          $currentPage = $datas->currentPage();
          $serialNumberStart = ($currentPage - 1) * $perPage + 1;

          $total_count = Role::count();

          return view('masters.role.index', [
              'datas' => $datas,
              'selectedStatus' => $status,
              'search' => $search,
              'total_count' => $total_count,
              'serialNumberStart' => $serialNumberStart,
          ]);
    }

    public function create(Request $request)
    {   
        $all_permissions  = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view("masters.role.add_edit", ['permission_groups'=>$permission_groups,'all_permissions'=>$all_permissions]);
    }


    public function edit($id)
    {   
        $all_permissions  = Permission::all();
        $permission_groups = User::getpermissionGroups();
        $role = Role::find($id);
        return view("masters.role.add_edit", ['permission_groups'=>$permission_groups,'all_permissions'=>$all_permissions,'role'=>$role]);
    }

    public function save(Request $request)
    {   
        $id=$request->id??NULL;
        $validatedData =  Validator::make($request->all(),[
            'role_name' => [
                'required',
                Rule::unique('roles', 'name')->ignore($id)->whereNull('deleted_at')
            ],
            'role_name.unique' => ' This Role name Already exist.',
            'role_name.required' => ' This Role name field is required.',       
        ]);

        if ($validatedData->fails()) {
            return $this->returnError($validatedData->errors(),'Validation Error', 422);
        }
        try{
            $permissions = $request->input('permissions');

            if(isset($id)){
                    $role =Role::find($id);
                    $originalData = $role->getOriginal();
                    $users = User::where('role_id',$id)->first();
                    $emp = Employee::where('role_id',$id)->first();
                    if($users &&  $originalData['status'] == 1 && $request->has('status') == 0){
                        return $this->returnError('This role is already in use by user');
                    }
                    if($emp &&  $originalData['status'] == 1 && $request->has('status') == 0){
                        return $this->returnError('This role is already in use by employee');
                    }
                    $role->name = $request->input('role_name');
                    $role->status = $request->has('status') ? 1 : 0;
                    $role->update();
                    if($permissions){
                        $this->asignRolePermission($permissions,$id);
                    }
            $role->syncPermissions($permissions);
            return $this->returnSuccess($role,"Role updated successfully");
            }else{
            $role = new Role;
            $role->name = $request->input('role_name');
            $role->status = $request->has('status') ? 1 : 0;
            $role->save();
           
            
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
               
            }
            return $this->returnSuccess($role,"Role created successfully");
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }
    
    public function delete($id)
    {
        $role = Role::findOrFail($id);
        $users = User::where('role_id',$id)->first();
        $emp = Employee::where('role_id',$id)->first();
        if($users){
            return $this->returnError('This role is already in use by user');
        }
        if($emp){
            return $this->returnError('This role is already in use by employee');
        }
        $role->delete();
        return $this->returnSuccess($id,"Role deleted successfully");
    }

    public function asignRolePermission($permission,$role_id)
    {     
        $new_permission=Permission::whereIN('name',$permission)->pluck('id')->toArray();
        $current_permission=RoleHasPermission::where('role_id',$role_id)->select('permission_id as id')->pluck('id')->toArray();
        $added_datas=array_diff($new_permission, $current_permission);
        $removed_datas=array_diff($current_permission, $new_permission);

        $users=User::where('role_id',$role_id)->get();
        try{
        if(count($removed_datas)>0){
            foreach($removed_datas as $removed_data){
            $role_has_permission=RoleUserHasPermission::where('permission_id',$removed_data)->where('role_id',$role_id)->delete();
            }
        }
        if(count($added_datas)>0){
            foreach($added_datas as $added_data){
                foreach($users as $user){
                    $ins['permission_id']=$added_data;
                    $ins['role_id']=$role_id;
                    $ins['user_id']=$user->id;
                RoleUserHasPermission::updateOrCreate(['permission_id'=>$added_data,'role_id'=>$role_id,'user_id'=>$user->id],$ins);
                }
            }
        }

        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
        return true;
    }

    public function role_user($id)
    {
        // Check if there are any active users with the given role_id
        $hasActiveUsers = User::where('role_id', $id)->where('status', 1)->exists() || Employee::where('role_id', $id)->where('status', 1)->exists();
        // Return the result as JSON
        return response()->json(['hasActiveUsers' => $hasActiveUsers]);
    }
    
}
