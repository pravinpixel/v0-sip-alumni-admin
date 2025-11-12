<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EmployeeEmail;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Alumnis;
use App\Models\Department;
use App\Models\BranchLocation;
use App\Models\Designation;
use App\Models\Location;
use App\Models\Role;
use App\Models\Task;
use App\Notifications\EmployeeCreateNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        return view('masters.directory.index');
    }

    public function getData(Request $request)
    {
        try {
            $query = Alumnis::with(['city', 'occupation'])->orderBy('id', 'desc');

            // Apply filters
            if ($request->filled('batch')) {
                $query->where('year_of_completion', $request->batch);
            }
            if ($request->filled('location')) {
                $query->whereHas('city', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->location . '%');
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return '<span>' . Carbon::createFromFormat('Y-m-d H:i:s', $row['created_at'])->setTimezone('Asia/kolkata')->format('d-m-Y h:i A') . '</span>';
                })
                ->editColumn('alumni', function ($row) {
                    $img = $row->image ? asset($row->image) : asset('images/avatar/blank.png');
                    $occ = $row->occupation->name ?? '—';
                    return '<div style="display:flex;align-items:center;gap:12px;">
                        <img src="' . $img . '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    </div>';
                })
                ->addColumn('full_name', function ($row) {
                    return '<span style="background-color:#fff3cd;color:#ff8c42;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">' . ($row->full_name ?? '—') . '</span>';
                })
                ->addColumn('batch', function ($row) {
                    return '<span style="background-color:#fff3cd;color:#ff8c42;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">' . ($row->year_of_completion ?? '—') . '</span>';
                })
                ->addColumn('mobile_number', function ($row) {
                    return '<span style="background-color:#fff3cd;color:#ff8c42;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;">' . ($row->mobile_number ?? '—') . '</span>';
                })
                ->addColumn('location', function ($row) {
                    return ($row->city?->state?->name ?? '-') . ', ' . ($row->city?->name ?? '-');
                })
                ->addColumn('occupation', function ($row) {
                    return $row->occupation->name ?? '-';
                })
                ->addColumn('connections', function ($row) {
                    return '<button onclick="viewProfile(' . $row->id . ')" class="btn btn-sm btn-primary">View Profile</button>';
                })
                ->addColumn('action', function ($row) {
                    return '
                      <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" id="actionMenu' . $row->id . '" data-bs-toggle="dropdown" aria-expanded="false" style="padding:5px 8px; border:none;">
                                <i class="fas fa-ellipsis-v"></i>
                                     </button>
                                <ul class="dropdown-menu" aria-labelledby="actionMenu' . $row->id . '">
                                      <li><a class="dropdown-item" href="javascript:void(0)" onclick="sendMessage(' . $row->id . ')">Send Message</a></li>
                               </ul>
                               </div>';
                })
                ->rawColumns(['alumni', 'batch', 'location', 'action', 'full_name', 'mobile_number', 'created_at', 'connections'])
                ->make(true);
        } catch (\Throwable $e) {
            Log::error('Directory DataTable error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function create(Request $request)
    {
        $departments = Department::where('status', '1')->get();
        $branches = BranchLocation::where('status', '1')->get();
        $employees = Employee::where('status', 1)->get();
        $roles = Role::where('status', '1')->whereNull('deleted_at')->with(['permissions' => function ($query) {
            $query->whereIn('name', ['i_alert_employee.create', 'i_alert_employee.view', 'i_alert_employee.edit', 'i_alert_employee.delete', 'i_alert_employee.comment', 'i_alert_employee.attachment']);
        }])->get();
        $roles = $roles->reject(function ($role) {
            return $role->permissions->isEmpty();
        });
        $locations = Location::where('status', '1')->get();
        $designations = Designation::where('status', '1')->get();

        return view(
            'masters.employee.action',
            [
                'departments' => $departments,
                'branches' => $branches,
                'locations' => $locations,
                'employees' => $employees,
                'roles' => $roles,
                'designations' => $designations
            ]
        );
    }

    public function get(Request $request, $id)
    {
        $user = Employee::find($id);

        $departments = Department::where('status', '1')->get();
        $employees = Employee::where('status', 1)->get();
        $roles = Role::where('status', '1')->whereNull('deleted_at')->with(['permissions' => function ($query) {
            $query->whereIn('name', ['i_alert_employee.create', 'i_alert_employee.view', 'i_alert_employee.edit', 'i_alert_employee.delete', 'i_alert_employee.comment', 'i_alert_employee.attachment']);
        }])->get();
        $roles = $roles->reject(function ($role) {
            return $role->permissions->isEmpty();
        });
        $branches = BranchLocation::where('status', '1')->get();
        $locations = Location::where('status', '1')->get();
        $designations = Designation::where('status', '1')->get();

        return view(
            'masters.employee.action',
            [
                'user' => $user,
                'departments' => $departments,
                'branches' => $branches,
                'locations' => $locations,
                'employees' => $employees,
                'roles' => $roles,
                'designations' => $designations
            ]
        );
    }

    public function save(Request $request)
    {

        if (!empty($request->branch_id) && $request->branch_id[0] === 'all') {
            $branchIds = array_filter($request->branch_id, function ($id) {
                return $id !== 'all';
            });

            $request->merge([
                'branch_id' => array_values($branchIds)
            ]);
        }
        $id = $request->id ?? NULL;
        $validatedData = Validator::make($request->all(), $this->getValidationRules($id), $this->getValidationMessages());

        if ($validatedData->fails()) {
            return $this->returnError($validatedData->errors(), 'Validation Error', 422);
        }
        try {
            DB::beginTransaction();
            if (isset($id)) {
                if ($request->input('status') == 0) {
                    Task::where('assigned_to', $id)->delete();
                    Task::where('assigned_by', $id)->delete();
                }
                $user = Employee::findOrFail($id);
                $user->employee_id = $request->input('employee_id');
                $user->first_name = $request->input('first_name');
                $user->last_name =  $request->input('last_name');
                $user->phone_number = $request->input('mobile');
                $user->password = bcrypt($request->input('password'));
                $user->hash_password = Crypt::encryptString($request->input('password'));
                $user->email = $request->input('email');
                $user->wcr_date_extended = $request->input('wcr_date_extended') ?? 0;
                $user->status = $request->input('status');
                $user->department_id = $request->input('department_id');
                $user->branch_id = json_encode($request->input('branch_id'));
                $user->reporting_manager = json_encode($request->input('reporting_manager'));
                $user->role_id = $request->input('role_id');
                $user->location_id = $request->input('location_id');
                $user->designation_id = $request->input('designation_id');
                // Handle profile picture update if provided
                if (!empty($request->file('profile_image')) && $request->input('avatar_remove') == 0) {
                    $fileName = "image_" . uniqid() . "_" . time() . "." . $request->file('profile_image')->extension();
                    $path = $request->file('profile_image')->storeAs('public/admin/', $fileName);
                    $user->profile_image = 'admin/' . $fileName;
                }

                if ($request->input('avatar_remove') == 1) {
                    // Path to the profile images folder
                    $profileImagePath = storage_path("app/public/admin/");
                    // Check if the user has a profile image and if the file exists
                    if ($user->profile_image && file_exists($profileImagePath . $user->profile_image)) {
                        // Delete the existing profile image file
                        unlink($profileImagePath . $user->profile_image);
                    }
                    // Update the user's profile image field to be empty
                    $user->profile_image = null;

                    if (!empty($request->file('profile_image'))) {
                        $fileName = "image_" . uniqid() . "_" . time() . "." . $request->file('profile_image')->extension();
                        $path = $request->file('profile_image')->storeAs('public/admin/', $fileName);
                        $user->profile_image = 'admin/' . $fileName;
                    }
                }
                $user->save();
                DB::commit();
                return $this->returnSuccess($user, "Employee updated successfully");
            } else {

                $user = new Employee;
                $user->employee_id = $request->input('employee_id');
                $user->first_name = $request->input('first_name');
                $user->last_name =  $request->input('last_name');
                $user->phone_number = $request->input('mobile');
                $user->password = bcrypt($request->input('password'));
                $user->hash_password = Crypt::encryptString($request->input('password'));
                $user->email = $request->input('email');
                $user->status = $request->input('status');
                $user->department_id = $request->input('department_id');
                $user->location_id = $request->input('location_id');
                $user->designation_id = $request->input('designation_id');
                $user->branch_id = json_encode($request->input('branch_id'));
                $user->reporting_manager = json_encode($request->input('reporting_manager'));
                $user->role_id = $request->input('role_id');

                // Handle profile picture update if provided
                if (!empty($request->file('profile_image')) && $request->input('avatar_remove') == 0) {
                    $fileName = "image_" . uniqid() . "_" . time() . "." . $request->file('profile_image')->extension();
                    $path = $request->file('profile_image')->storeAs('public/admin/', $fileName);
                    $user->profile_image = 'admin/' . $fileName;
                }

                if ($request->input('avatar_remove') == 1) {
                    // Path to the profile images folder
                    $profileImagePath = storage_path("app/public/admin/");
                    // Check if the user has a profile image and if the file exists
                    if ($user->profile_image && file_exists($profileImagePath . $user->profile_image)) {
                        // Delete the existing profile image file
                        unlink($profileImagePath . $user->profile_image);
                    }
                    // Update the user's profile image field to be empty
                    $user->profile_image = null;

                    if (!empty($request->file('profile_image'))) {
                        $fileName = "image_" . uniqid() . "_" . time() . "." . $request->file('profile_image')->extension();
                        $path = $request->file('profile_image')->storeAs('public/admin/', $fileName);
                        $user->profile_image = 'admin/' . $fileName;
                    }
                }

                $user->save();

                DB::commit();

                try {
                    if ($user) {
                        $user->notify(new EmployeeCreateNotification($user));
                    }
                } catch (\Exception $e) {
                    $employee = Employee::find($user->id);
                    $employee->is_mail_failed = 1;
                    $employee->save();
                    // Log the exception but do not break the flow
                    Log::error('Employee notification failed: ' . $e->getMessage());
                }
                return $this->returnSuccess($user, "Employee created successfully");
            }
        } catch (\Exception $e) {
            return back()->with('errors', $e->getMessage());
        }
    }


    public function getValidationRules($id = null)
    {
        $rule_arr = [
            'employee_id'  => 'required|string|unique:employees,employee_id,' . $id . ',id,deleted_at,NULL',
            'first_name' => 'required|string|max:200',
            'last_name' => 'required|string|max:200',
            'email' => ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i'],
            'mobile' => 'required|string|max:15|min:10|unique:employees,phone_number,' . $id . ',id,deleted_at,NULL',
            'profile_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'status' => 'required|boolean',
            'department_id' => 'required|exists:departments,id',
            'location_id' => 'required|exists:locations,id',
            'designation_id' => 'required|exists:designations,id',
            'branch_id' => 'required|exists:branch_locations,id',
            'role_id' => 'nullable|exists:roles,id',
            'reporting_manager' => 'required',
        ];

        if ($id == null) {
            $rule_arr['password'] = 'required|string|min:6';
            // $rule_arr['confirm_password'] = 'required_without:password|required_with:password|min:6|same:password';
        }




        return $rule_arr;
    }

    function getValidationMessages()
    {
        return [
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'email.email' => 'The email address must be a valid email address.',
            'email.unique' => 'The email address has already been taken.',
            // 'confirm_password.required_without' => 'Confirm Password field is required.',
            'department_id.required' => 'The department field is required.',
            'location_id.required' => 'The location field is required.',
            'designation_id.required' => 'The designation field is required.',
            'branch_id.required' => 'The branch field is required.',
        ];
    }


    public function delete(Request $request, $id)
    {
        try {
            $employee = Employee::find($id);
            if ($employee) {
                $employee->delete();
                return response()->json(['message' => 'Employee deleted successfully']);
            } else {
                return response()->json(['message' => 'No Employee Found!']);
            }
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function employee_task($id)
    {
        // Check wheather trying to delete task assigned user
        $isAssignedToTasks = Task::where('assigned_to', $id)
            ->where('status_id', '=', 2)
            ->exists();

        // Return the result as JSON
        return response()->json(['hasActiveTasks' => $isAssignedToTasks]);
    }

    public function sendEmployeeMail(Request $request, $id)
    {
        try {
            $employee = Employee::where('id', $id)
                ->where('status', '1')
                ->whereNull('deleted_at')
                ->first();

            if (!$employee) {
                return $this->returnError('Employee not found, inactive, or deleted', 'Validation Error', 422);
            }
            if ($employee) {
                $employee->notify(new EmployeeCreateNotification($employee));
            }

            return $this->returnSuccess([], "We have e-mailed password to the employee mail!");
        } catch (\Exception $e) {
            // Handle any errors during the mail sending process
            return $this->returnError($e->getMessage());
        }
    }
}
