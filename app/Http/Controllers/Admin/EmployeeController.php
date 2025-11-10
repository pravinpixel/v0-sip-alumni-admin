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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    // public function index(Request $request)
    // {
    //     $search = $request->input('search');
    //     $status = $request->input('status');
    //     $perPage = $request->input('pageItems');
    //     $selectedBranchName = $request->input('branch');
    //     $selectedLocationName = $request->input('location');
    //     $selectedDepartmentName = $request->input('department');
    //     $selectedDesignationName = $request->input('designation');
    //     $selectedReportingManager = $request->input('reportingManager');

    //     $query = Employee::query();
    //     if ($search) {
    //         $branch_id = BranchLocation::where('name', $search)->value('id');
    //         $rep_manager = Employee::where('first_name', $search)->orWhere('last_name', $search)->value('id');
    //         $rep_managers = (string) $rep_manager;
    //         $branch_ids = (string) $branch_id;
    //         $query->where(function ($q) use ($search, $branch_ids, $rep_managers) {
    //                 $q->where(DB::raw("REPLACE(CONCAT(first_name, ' ', last_name), ' ', '')"), 'like', '%' . str_replace(' ', '', $search) . '%')
    //                     ->orWhere('email', 'like', '%' . $search . '%')
    //                     ->orWhere('phone_number', 'like', '%' . $search . '%')
    //                     ->orWhere('employee_id', 'like', '%' . $search . '%')
    //                     ->orWhereRaw('JSON_UNQUOTE(reporting_manager) REGEXP ?', ['\\"'.$rep_managers.'\\"'])
    //                     ->orWhereRaw('JSON_UNQUOTE(branch_id) REGEXP ?', ['\\"'.$branch_ids.'\\"'])
    //                     ->orWhereHas('location', function ($query) use ($search) {
    //                         $query->where('name', 'like', '%' . $search . '%');
    //                     })->orWhereHas('designation', function ($query) use ($search) {
    //                         $query->where('name', 'like', '%' . $search . '%');
    //                     })->orWhereHas('department', function ($query) use ($search) {
    //                         $query->where('name', 'like', '%' . $search . '%');
    //                     });
    //         });
    //     }

    //     if ($status === '1' || $status === '0') {
    //         $query->where('status', $status);
    //     }

    //     if ($selectedBranchName) {
    //         $branchId = BranchLocation::where('name', $selectedBranchName)->value('id');
    //         $branchId = (string) $branchId;
    //         $query->orWhereRaw('JSON_UNQUOTE(branch_id) REGEXP ?', ['\\"'.$branchId.'\\"']);
    //     }

    //     if (!empty($selectedReportingManager)) {
    //         $idsArray = explode(',', $selectedReportingManager);
    //         $idsRegex = implode('|', array_map(fn($id) => '(^|[^0-9])' . intval($id) . '([^0-9]|$)', $idsArray));
    //         $query->whereRaw('JSON_UNQUOTE(reporting_manager) REGEXP ?', [$idsRegex])
    //             ->whereNull('deleted_at');
    //     }

    //     if ($selectedLocationName) {
    //         $locationIds = Location::where('name', $selectedLocationName)->pluck('id');
    //         $query->whereIn('location_id', $locationIds);
    //     }

    //     if ($selectedDepartmentName) {
    //         $departmentIds = Department::where('name', $selectedDepartmentName)->pluck('id');
    //         $query->whereIn('department_id', $departmentIds);
    //     }

    //     if ($selectedDesignationName) {
    //         $designationIds = Designation::where('name', $selectedDesignationName)->pluck('id');
    //         $query->whereIn('designation_id', $designationIds);
    //     }


    //     $users = $query->orderBy('id', 'desc')->paginate($perPage);



    //      $currentPage = $users->currentPage();
    //      $serialNumberStart = ($currentPage - 1) * $perPage + 1;

    //     $designations = Designation::where('status', '1')->whereNull('deleted_at')->get();
    //     // $roles = Role::where('status', '1')->whereNull('deleted_at')->get();
    //     $departments = Department::where('status', '1')->whereNull('deleted_at')->get();
    //     $branches = BranchLocation::where('status', '1')->whereNull('deleted_at')->get();
    //     $locations = Location::where('status', '1')->whereNull('deleted_at')->get();
    //     $managerIds = Employee::whereNotNull('reporting_manager')
    //         ->pluck('reporting_manager') 
    //         ->flatMap(function ($jsonString) {
    //             return json_decode($jsonString, true) ?? []; 
    //         })
    //         ->unique() 
    //         ->toArray();
    //     $reportingManagers = Employee::whereIn('id', $managerIds)
    //     ->get(['id', 'first_name', 'last_name']);    

    //     $total_count = Employee::count();


    //     return view('masters.employee.index', [
    //         'users' => $users,
    //         'selectedStatus' => $status,
    //         'search' => $search,
    //         'total_count' => $total_count,
    //         'serialNumberStart' => $serialNumberStart,
    //         'designations' => $designations,
    //         // 'roles' => $roles,
    //         'departments' => $departments,
    //         'branches' => $branches,
    //         'locations' => $locations,
    //         'reportingManagers' => $reportingManagers
    //     ]);
    // }

    public function index(Request $request)
    {
        $search  = $request->input('search');
        $status  = $request->input('status');
        $stateId = $request->input('state_id');
        $cityId  = $request->input('city_id');
        $year    = $request->input('year');
        $occupation = $request->input('occupation');
        $perPage = $request->input('pageItems', 10);

        $query = Alumnis::query();

        // 🔍 Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('mobile_number', 'like', "%$search%")
                    ->orWhere('occupation_text', 'like', "%$search%");
            });
        }

        // ✔ Status Filter
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // ✔ State Filter
        if (!empty($stateId)) {
            $query->where('state_id', $stateId);
        }

        // ✔ City Filter
        if (!empty($cityId)) {
            $query->where('city_id', $cityId);
        }

        // ✔ Year Filter
        if (!empty($year)) {
            $query->where('year_of_completion', $year);
        }

        // ✔ Occupation Auto-Suggest Filter
        if (!empty($occupation)) {
            $query->where('occupation_text', 'like', "%$occupation%");
        }

        // Pagination
        $alumnis = $query->orderBy('id', 'desc')->paginate($perPage);

        $currentPage = $alumnis->currentPage();
        $serialNumberStart = ($currentPage - 1) * $perPage + 1;

        // Dropdown Data
        // $states   = State::orderBy('name')->get();
        // $cities   = City::orderBy('name')->get();
        $years    = Alumnis::orderBy('year_of_completion', 'desc')
            ->pluck('year_of_completion')
            ->unique();

        // $occupations = Occupation::orderBy('name')->get();

        return view('masters.employee.index', [
            'alumnis' => $alumnis,
            'serialNumberStart' => $serialNumberStart,
            // 'states' => $states,
            // 'cities' => $cities,
            'years' => $years,
            // 'occupations' => $occupations,
            'search' => $search,
            'status' => $status
        ]);
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
