<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
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

        $datas = $query->orderBy('id', 'desc')->paginate($perPage);
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
        return view("masters.role.add_edit");
    }


    public function edit($id)
    {
        $role = Role::find($id);
        return view("masters.role.add_edit", ['role' => $role]);
    }

    public function save(Request $request)
    {
        $id = $request->id ?? NULL;
        $validatedData = Validator::make($request->all(), [
            'role_name' => [
                'required',
                Rule::unique('roles', 'name')->ignore($id)->whereNull('deleted_at')
            ],
            'role_name.unique' => ' This Role name Already exist.',
            'role_name.required' => ' This Role name field is required.',
        ]);

        if ($validatedData->fails()) {
            return $this->returnError($validatedData->errors(), 'Validation Error', 422);
        }
        try {
            $permissions = $request->input('permissions');

            if (isset($id)) {
                $role = Role::find($id);
                $originalData = $role->getOriginal();
                $users = User::where('role_id', $id)->first();
                if ($users && $originalData['status'] == 1 && $request->has('status') == 0) {
                    return $this->returnError('This role is already in use by user');
                }
                $role->name = $request->input('role_name');
                $role->status = $request->has('status') ? 1 : 0;
                $role->update();

                if ($permissions) {
                    $role->syncPermissions($permissions);
                }
                return $this->returnSuccess($role, "Role updated successfully");
            } else {
                $role = new Role;
                $role->name = $request->input('role_name');
                $role->status = $request->has('status') ? 1 : 0;
                $role->save();


                if (!empty($permissions)) {
                    $role->syncPermissions($permissions);

                }
                return $this->returnSuccess($role, "Role created successfully");
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);
        $users = User::where('role_id', $id)->first();
        if ($users) {
            return $this->returnError('This role is already in use by user');
        }
        $role->delete();
        return $this->returnSuccess($id, "Role deleted successfully");
    }



    public function role_user($id)
    {
        // Check if there are any active users with the given role_id
        $hasActiveUsers = User::where('role_id', $id)->where('status', 1)->exists();
        // Return the result as JSON
        return response()->json(['hasActiveUsers' => $hasActiveUsers]);
    }

    public function toggleStatus(Request $request)
    {
        try {
            $role = Role::findOrFail($request->role_id);

            // Check if role has active users when trying to deactivate
            if ($request->status == 0) {
                $hasActiveUsers = User::where('role_id', $role->id)->where('status', 1)->exists();
                if ($hasActiveUsers) {
                    return $this->returnError('Cannot deactivate role with active users');
                }
            }

            $role->status = $request->status;
            $role->save();

            return $this->returnSuccess($role, "Role status updated successfully");
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

}
