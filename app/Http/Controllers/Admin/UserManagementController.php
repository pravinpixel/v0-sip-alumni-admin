<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CenterLocations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
class UserManagementController extends Controller
{
    protected $pageNumber = 1;
    protected $perPage = 10;



    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status'); 
        $role = $request->input('role'); 
        $perPage = $request->input('pageItems') ?? 10;

        $query = User::with('role');
        $roles = Role::all();
        if ($search) {
            $searchLower = strtolower(trim($search));
            $statusMap = ['active' => '1', 'inactive' => '0'];
            $searchStatus = $statusMap[$searchLower] ?? null;

            $query->where(function ($q) use ($search, $searchStatus) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%")
                    ->orWhereHas('role', fn($r) => $r->where('name', 'like', "%{$search}%"));

                if ($searchStatus !== null) {
                    $q->orWhere('status', $searchStatus);
                }
            });
        }

        if (!empty($status)) {
            $query->whereIn('status', (array) $status);
        }

        if (!empty($role)) {
            $query->whereIn('role_id', (array) $role);
        }

        $datas = $query->orderBy('id', 'desc')->paginate($perPage);
        $currentPage = $datas->currentPage();
        $serialNumberStart = ($currentPage - 1) * $perPage + 1;
        $total_count = User::count();

        return view('masters.user.index', [
            'datas' => $datas,
            'search' => $search,
            'total_count' => $total_count,
            'serialNumberStart' => $serialNumberStart,
            'roles' => $roles
        ]);
    }

    public function create(Request $request)
    {
        $roles = Role::where('status', '1')->whereNull('deleted_at')->with([
            'permissions' => function ($query) {
                $query->whereNotIn('name', ['i_alert_employee.create', 'i_alert_employee.view', 'i_alert_employee.edit', 'i_alert_employee.delete', 'i_alert_employee.comment']);
            }
        ])->get();
        $roles = $roles->reject(function ($role) {
            return $role->permissions->isEmpty();
        });
        $locations = CenterLocations::All();
        return view("masters.user.add_edit", ['roles' => $roles, 'locations' => $locations]);
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::where('status', '1')->whereNull('deleted_at')->with([
            'permissions' => function ($query) {
                $query->whereNotIn('name', ['i_alert_employee.create', 'i_alert_employee.view', 'i_alert_employee.edit', 'i_alert_employee.delete', 'i_alert_employee.comment']);
            }
        ])->get();
        $roles = $roles->reject(function ($role) {
            return $role->permissions->isEmpty();
        });
        $locations = CenterLocations::All();
        return view("masters.user.add_edit", ['user' => $user, 'roles' => $roles, 'locations' => $locations]);
    }

    public function getValidationRules($id = null, $isUpdatingPassword = false)
    {
        $rule_arr = [
            'user_name' => 'required',
            'email' => ['required', 'email', 'unique:users,email,' . $id . ',id,deleted_at,NULL', 'regex:/(.+)@(.+)\.(.+)/i'],
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:0,1',
            'password' => 'required|min:6',
            'retype_password' => 'required|same:password|min:6',
        ];

        $role = Role::find(request()->role_id);
        if($role && $role->name === 'Franchisee'){
            $rule_arr['center_location_id'] = 'required|exists:center_locations,id';
        }

        return $rule_arr;
    }

    function getValidationMessages()
    {
        return [
            'retype_password.required' => 'The confirm password field is required.',
            'retype_password.same' => 'The confirm password field must match password.',
            'retype_password.min' => 'The confirm password field must be at least 6 characters.',
            'role_id.required' => 'The role field is required.',
            'center_location_id.required' => 'The center location field is required for Franchisee role.',
        ];
    }

    public function save(Request $request)
    {

        $id = $request->id ?? NULL;
        $isUpdatingPassword = $id == null || $request->filled('password') || $request->filled('retype_password');
        $validatedData = Validator::make($request->all(), $this->getValidationRules($id, $isUpdatingPassword), $this->getValidationMessages());

        if ($validatedData->fails()) {
            return $this->returnError($validatedData->errors(), 'Validation Error', 422);
        }
        try {
            if (isset($id)) {
                $user = User::find($id);
                $user->name = $request->input('user_name');
                $user->email = $request->input('email');
                $user->status = $request->input('status');
                $user->role_id = $request->input('role_id');

                // Only update password if provided
                if ($request->filled('password')) {
                    $user->password = bcrypt($request->input('password'));
                    $user->hash_password = Crypt::encryptString($request->input('password'));
                }

                $user->center_location_id = $request->input('center_location_id');
                $user->update();
                $user->roles()->detach();
                if ($request->input('role_id')) {
                    $role = Role::find($request->input('role_id'));
                    $user->assignRole($role->name);
                }
                return $this->returnSuccess($user, "User updated successfully");
            } else {
                $user = new User();
                $user->name = $request->input('user_name');
                $user->user_id = generateUserId();
                $user->email = $request->input('email');
                $user->status = $request->input('status');
                $user->password = bcrypt($request->input('password'));
                $user->hash_password = Crypt::encryptString($request->input('password'));
                $user->role_id = $request->input('role_id');
                $user->center_location_id = $request->input('center_location_id');
                $user->save();
                if ($request->input('role_id')) {
                    $role = Role::find($request->input('role_id'));
                    $user->assignRole($role->name);
                }
                return $this->returnSuccess($user, "User created successfully");
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

    }

    public function delete($id)
    {
        try {
            $user = User::find($id)->delete();
            return $this->returnSuccess([], "User deleted successfully");
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function toggleStatus(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            if($user->role->status !== 1){
                return $this->returnError("Cannot change status of user with inactive role");
            }
            $user->status = $request->status;
            $user->save();

            return $this->returnSuccess($user, "User status updated successfully");
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

}
