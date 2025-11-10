<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CustomValidationException;
use App\Exports\TaskTemplateExport;
use App\Helpers\UtilsHelper;
use App\Http\Controllers\Controller;
use App\Imports\TasksImport;
use App\Imports\TaskListImport;
use App\Models\BranchLocation;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Location;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;


class TaskController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('pageItems');
        $selectedEmployeeName = $request->input('employee');
        $selectedDesignationName = $request->input('designation');
        $selectedDepartmentName = $request->input('department');
        $selectedBranchLocationName = $request->input('branch');
        $selectedLocationName = $request->input('location');
        $selectedAssignedBy = $request->input('assigned_by');
        $selectedAssignedTo = $request->input('assigned_to');
        $selectedPriority = $request->input('priority');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $tasksQuery = Task::query()
        ->where(function ($query) {
            $query->where(function ($query) {
                $query->where('is_recurrence', 0)
                      ->whereNull('parent_id');
            })->orWhere(function ($query) {
                $query->whereNotNull('parent_id')
                      ->where('is_recurrence', 1);
            });
        });

        if ($search) {
            $tasksQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('task_no', 'like', '%' . $search . '%');

                    $query->orWhereHas('assignedby', function ($q) use ($search) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%');
                });

                $query->orWhereHas('assignedto', function ($q) use ($search) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%');
                });
            });
        }

        if ($status == '1' || $status == '2' || $status == '3') {
            $tasksQuery->where('status_id', $status);
        }

        if ($selectedEmployeeName) {
            $employeeIds = Employee::where(DB::raw("CONCAT(first_name, ' ', last_name)"), $selectedEmployeeName)
                            ->where('status', '1')
                            ->whereNull('deleted_at')
                            ->pluck('id');
            if ($employeeIds->isNotEmpty()) {
                $tasksQuery->where(function ($query) use ($employeeIds) {
                    $query->whereIn('assigned_to', $employeeIds)
                        ->orWhereIn('assigned_by', $employeeIds);
                });
            }
        }

        if ($selectedDesignationName) {
            $designationIds = Designation::where('name', $selectedDesignationName)
                ->where('status', '1')
                ->pluck('id');
            if ($designationIds->isNotEmpty()) {
                $tasksQuery->where(function ($query) use ($designationIds) {
                    $query->whereHas('assignedToEmployee', function ($q) use ($designationIds) {
                        $q->whereIn('designation_id', $designationIds);
                    })->orWhereHas('assignedByEmployee', function ($q) use ($designationIds) {
                        $q->whereIn('designation_id', $designationIds);
                    });
                });
            }
        }

        if ($selectedDepartmentName) {
            $departmentIds = Department::where('name', $selectedDepartmentName)
                ->where('status', '1')
                ->pluck('id');
            if ($departmentIds->isNotEmpty()) {
                $tasksQuery->where(function ($query) use ($departmentIds) {
                    $query->whereHas('assignedToEmployee', function ($q) use ($departmentIds) {
                        $q->whereIn('department_id', $departmentIds);
                    })->orWhereHas('assignedByEmployee', function ($q) use ($departmentIds) {
                        $q->whereIn('department_id', $departmentIds);
                    });
                });
            }
        }

        if ($selectedBranchLocationName) {
            $branchLocationIds = BranchLocation::where('name', $selectedBranchLocationName)
                ->where('status', '1')
                ->pluck('id');
            if ($branchLocationIds->isNotEmpty()) {
                $tasksQuery->where(function ($query) use ($branchLocationIds) {
                    $query->whereHas('assignedToEmployee', function ($q) use ($branchLocationIds) {
                        $q->whereIn('branch_id', $branchLocationIds);
                    })->orWhereHas('assignedByEmployee', function ($q) use ($branchLocationIds) {
                        $q->whereIn('branch_id', $branchLocationIds);
                    });
                });
            }
        }

        if ($selectedLocationName) {
            $locationIds = Location::where('name', $selectedLocationName)
                ->where('status', '1')
                ->pluck('id');
            if ($locationIds->isNotEmpty()) {
                $tasksQuery->where(function ($query) use ($locationIds) {
                    $query->whereHas('assignedToEmployee', function ($q) use ($locationIds) {
                        $q->whereIn('location_id', $locationIds);
                    })->orWhereHas('assignedByEmployee', function ($q) use ($locationIds) {
                        $q->whereIn('location_id', $locationIds);
                    });
                });
            }
        }

        if ($selectedAssignedBy) {
            $assignedByIds = Employee::where(DB::raw("CONCAT(first_name, ' ', last_name)"), $selectedAssignedBy)
                                ->where('status', '1')
                                ->whereNull('deleted_at')
                                ->pluck('id');
            if ($assignedByIds->isNotEmpty()) {
                $tasksQuery->whereIn('assigned_by', $assignedByIds);
            }
        }

        if ($selectedAssignedTo) {
            $assignedToIds = Employee::where(DB::raw("CONCAT(first_name, ' ', last_name)"), $selectedAssignedTo)
                                ->where('status', '1')
                                ->whereNull('deleted_at')
                                ->pluck('id');
            if ($assignedToIds->isNotEmpty()) {
                $tasksQuery->whereIn('assigned_to', $assignedToIds);
            }
        }

        if ($selectedPriority) {
            $tasksQuery->where('priority_id', $selectedPriority);
        }

        if ($startDate && $endDate) {
            $tasksQuery->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $tasksQuery->whereDate('created_at', $startDate);
        } elseif ($endDate) {
            $tasksQuery->whereDate('created_at', $endDate);
        }

        $users = $tasksQuery->orderBy('created_at', 'desc')->paginate($perPage);
        $currentPage = $users->currentPage();
        $serialNumberStart = ($currentPage - 1) * $perPage + 1;

        $employees = Employee::where('status', '1')->whereNull('deleted_at')->get();
        $designations = Designation::where('status', '1')->whereNull('deleted_at')->get();
        $departments = Department::where('status', '1')->whereNull('deleted_at')->get();
        $branches = BranchLocation::where('status', '1')->whereNull('deleted_at')->get();
        $locations = Location::where('status', '1')->whereNull('deleted_at')->get();

        $total_count = Task::where(function ($query) {
            $query->where(function ($query) {
                $query->where('is_recurrence', 0)
                      ->whereNull('parent_id');
            })->orWhere(function ($query) {
                $query->whereNotNull('parent_id')
                      ->where('is_recurrence', 1);
            });
        })->count();

        return view('masters.task.index', [
            'users' => $users,
            'selectedStatus' => $status,
            'search' => $search,
            'total_count' => $total_count,
            'serialNumberStart' => $serialNumberStart,
            'employees' => $employees,
            'designations' => $designations,
            'departments' => $departments,
            'branches' => $branches,
            'locations' => $locations
        ]);
    }


    public function view(Request $request, $id)
    {
        $user = Task::with(['assignedto','documents', 'comments.documents'])->find($id);

        
        
            $followerIds = explode(',',  $user['followers']);
            if (!empty($followerIds) && count($followerIds) > 0) {

            $task_followers =  Employee::whereIn('id', $followerIds)
            ->select('id', 'first_name', 'last_name', 'email', 'profile_image')
            ->selectRaw("CONCAT(first_name, ' ', last_name) as name")
            ->get();
            $task_followers_names = $task_followers->pluck('name')->implode(', ');
            $user['task_followers_names'] = $task_followers_names;
            }else{
                $user['task_followers_names'] = '';
            }

        if (!$user) {
            return redirect()->back()->withErrors('Task not found.');
        }

        $created_by = Employee::find($user->created_by);

        if ($created_by) {
            $user['created_by_name'] = $created_by->name;
            $user['profile_image'] = $created_by->profile_image;
        } else {
            $user['created_by_name'] = 'Unknown';
            $user['profile_image'] = null;
        }
        if($user->is_recurrence == 1){
            $recurrence =$user->recurrence;
            $get_recurrence_details = UtilsHelper::parseEventDetails($recurrence);
            $user['recurrence_details'] = $get_recurrence_details;

            $recurrenceDetails = $user['recurrence_details'];

            $startDate = $recurrenceDetails['startDate'];
            $endDate = $recurrenceDetails['endDate'];

            $startDateTime = new DateTime($startDate);
            $endDateTime = new DateTime($endDate);

            $formattedStartDate = $startDateTime->format('d-m-Y');
            $formattedEndDate = $endDateTime->format('d-m-Y');

            $recurrenceDetails['startDate'] = $formattedStartDate;
            $recurrenceDetails['endDate'] = $formattedEndDate;

            $user['recurrence_details'] = $recurrenceDetails;

        }
        return view('masters.task.action', ['user' => $user]);
    }

    public function importView(Request $request)
    {
        return view('masters.task.tasks-import');
    }

    public function import(Request $request)
    {
        $validatedData = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());
    
        if ($validatedData->fails()) {
            // If Laravel validation fails, return validation error
            return response()->json([
                'status' => false,
                'type' => 'laravel_validation',
                'errors' => $validatedData->errors(),
            ], 422);
        }

    try {
        Excel::import(new TasksImport, $request->file('file')->store('temp'));
        return response()->json([
            'status' => true,
            'message' => "Import successfully",
        ]);

    } catch (CustomValidationException $e) {
        return response()->json([
            'status' => false,
            'type' => 'validation_error',
            'errors' => $e->getErrors(),
        ], 422);

    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
       
        $failures = $e->failures();
        $excelErrors = [];
        foreach ($failures as $failure) {
            $row = $failure->row(); // Row where the failure occurred
            $errors = $failure->errors(); // Get the validation errors
            
            foreach ($errors as $error) {
                $excelErrors[] = "Row $row: $error";
            }
        }
        return response()->json([
            'status' => false,
            'type' => 'excel_validation',
            'errors' => $excelErrors,
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'type' => 'general_error', // Indicating a general error
            'message' => $e->getMessage(),
        ], 500);
    }
    }

    public function exportTaskTemplate()
    {
        return Excel::download(new TaskTemplateExport, 'task_template.xlsx');
    }


    public function getValidationRules()
    {
        $rule_arr = [
            'file' => 'required|mimes:xlsx,xls',
        ];

        return $rule_arr;
    }

    function getValidationMessages() {
        return [
            'file.required' => 'Import File is required.',
        ];
    }
}
