<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{


    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
                'password' => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->returnError($validator->errors());
            }
            $employee_check = Employee::where('phone_number', $request->phone_number)->first();
            if (!$employee_check) {
                return $this->returnError("Invalid User", 401);
            }
            $active_check = Employee::where('phone_number', $request->phone_number)->where('status', 1)->first();
            if (!$active_check) {
                return $this->returnError("This employee is not active");
            }
            // $fieldType = filter_var($request->user_name, FILTER_VALIDATE_EMAIL) ? 'user_name' : 'phone_number';
            $credentials = ['phone_number' => request('phone_number'), 'password' => request('password')];

            if (!$token = auth()->attempt($credentials)) {
                return $this->returnError("Phone number or password is invalid");
            }
            return $this->respondWithToken($token);
        } catch (\Throwable $e) {
            return $this->returnError($this->error ?? $e->getMessage());
        }
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        $data = [
            'user' => $this->returnUser(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
        try {
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Login Successfully'
            ]);
        } catch (\Throwable $e) {
            return $this->returnError($this->error ?? $e->getMessage());
        }
    }

    public function me(Request $request)
    {
        $admin = Auth::user();
        if (!$admin) {
            return $this->returnError('User not found');
        }

        $data = $this->returnUser();

        return $this->returnSuccess($data);
    }

    public function Logout(Request $request)
    {
        try {
            Auth()->logout();
            return $this->returnSuccess([], 'Logout successfully');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }


        try {
            if (Hash::check($request->old_password, Auth::user()->password)) {
                if (!Hash::check($request->new_password, Auth::user()->password,)) {

                    return DB::transaction(function () use ($request) {
                        $data = Employee::find(Auth::id());
                        $data->password = bcrypt($request->new_password);
                        $data->hash_password = Crypt::encryptString($request->new_password);
                        $data->save();
                        return $this->returnSuccess([], 'Password changed successfully');
                    });
                } else {
                    return $this->returnError("The new password should not be same the old password");
                }
            } else {
                return $this->returnError("The Old password is invalid");
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    private function returnUser()
    {
        $user_id = auth()->user()->id;
        $data = Employee::with('department:id,name', 'designation:id,name', 'branchLocation:id,name', 'location:id,name')
            ->find($user_id);
        $user_role_id = $data->role_id;

        $ratingData = DB::table('tasks')
            ->where('status_id', 1)
            ->where('assigned_to', $user_id)
            ->select(DB::raw('SUM(task_rating) as totalRatings'), DB::raw('COUNT(id) as ratingCount'))
            ->first();

        $rating = 0;
        if ($ratingData->totalRatings) {
            $rating = $ratingData->totalRatings / $ratingData->ratingCount;
        }
        $data->rating_count = $rating;
        $totalTasks = DB::table('tasks')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })
                    ->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)
                            ->whereNotNull('parent_id');
                    });
            })
            ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
            ->whereNull('deleted_at')
            ->whereNotIn('status_id', [3, 8])
            ->where('assigned_to', $user_id)
            ->count();


        $completedTasks = DB::table('tasks')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })
                    ->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)
                            ->whereNotNull('parent_id');
                    });
            })
            ->whereNull('deleted_at')
            ->where('assigned_to', $user_id)
            ->where('status_id', 1)
            ->count();

        $data->task_percentage = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        $data->pending_task =  $totalTasks - $completedTasks;
        $data->ialert_tab = $this->getEmployeePermissions($user_role_id);
        return $data;
    }

    function getEmployeePermissions($role_id)
    {
        $role = Role::find($role_id);
        if (!$role) {
            return [];
        }
        if ($role->status == 1) {
            $permissions = $role->permissions()->pluck('name')->toArray();
            $data = new \stdClass();
            $data->edit = in_array('i_alert_employee.edit', $permissions);
            $data->view = in_array('i_alert_employee.view', $permissions);
            $data->delete = in_array('i_alert_employee.delete', $permissions);
            $data->create = in_array('i_alert_employee.create', $permissions);
            $data->comment = in_array('i_alert_employee.comment', $permissions);
            $data->attachment = in_array('i_alert_employee.attachment', $permissions);
            // if($role->name == 'Attachment User'){
            //     $data->attachment_user = true;
            // }else{
            //     $data->attachment_user = false;
            // }
            if ($role->name == 'Accounts Receivable Team') {
                $data->restricted_edit = true;
            } else {
                $data->restricted_edit = false;
            }
            if (!$data->edit && !$data->view && !$data->delete && !$data->create && !$data->comment && !$data->attachment) {
                return new \stdClass();
            }
            return $data;
        }
        return [];
    }
}
