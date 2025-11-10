<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AllTasksExport;
use App\Exports\IalertAdminExport;
use App\Exports\MonthlyExport;
use App\Exports\OverdueExport;
use App\Http\Controllers\Controller;
use App\Models\BranchLocation;
use App\Models\Employee;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{

    public function export(Request $request)
    {
        // Retrieve filters
        $filters = [
            'search' => $request->input('search'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'assigned_by' => $request->input('assigned_by'),
            'assigned_to' => $request->input('assigned_to'),
            'priority' => $request->input('priority'),
            'recurrence' => $request->input('recurrence'),
            'task_type' => $request->input('task_type'),
            'status' => $request->input('status'),
        ];


        // Export the filtered data
        //return Excel::download(new AllTasksExport($filters), 'tasks.csv');

        return Excel::download(new AllTasksExport($filters), 'tasks.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tasks.csv"',
            'X-Accel-Buffering' => 'no'
        ]);
    }

    public function ialertReport(Request $request)
    {
        return view('reports.ialert.index');
    }

    public function ialertExport(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'assigned_by' => $request->input('assigned_by'),
            'assigned_to' => $request->input('assigned_to'),
            'priority' => $request->input('priority'),
            'recurrence' => $request->input('recurrence'),
            'task_type' => $request->input('task_type'),
            'status' => $request->input('status'),
        ];

        return Excel::download(new IalertAdminExport(), 'ialert-admin.xlsx');
    }

    public function rawCount()
    {
        $currentDate = now()->toDateString();

        return Task::withCount('dueDates')->where(function ($query) {
            $query->where(function ($query) {
                $query->where('is_recurrence', 0)
                    ->whereNull('parent_id');
            })->orWhere(function ($query) {
                $query->whereNotNull('parent_id')
                    ->where('is_recurrence', 1);
            });
        })->count();
    }

    public function rawReport(Request $request)
    {
        $page = $request->input('page', 1);
        $per_page = $request->input('per_page', 10);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $searchQuery = $request->input('search');
        $assignedBy = $request->input('assigned_by');
        $assignedTo = $request->input('assigned_to');
        $priority = $request->input('priority');
        $recurrence = $request->input('recurrence');
        $task_type = $request->input('task_type');
        $status = $request->input('status');

        $query = Task::withCount('dueDates')->where(function ($query) {
            $query->where(function ($query) {
                $query->where('is_recurrence', 0)
                    ->whereNull('parent_id');
            })->orWhere(function ($query) {
                $query->whereNotNull('parent_id')
                    ->where('is_recurrence', 1);
            });
        });

        if ($searchQuery) {
            $query->where(function ($query) use ($searchQuery) {

                $query->where('task_no', 'like', '%' . $searchQuery . '%')
                    ->orWhere('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('deadline', 'like', '%' . $searchQuery . '%')
                    ->orWhere('rating_remark', 'like', '%' . $searchQuery . '%')
                    ->orWhere('task_rating', 'like', '%' . $searchQuery . '%');


                $query->orWhereHas('assignedby', function ($q) use ($searchQuery) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $searchQuery . '%')
                        ->orWhereHas('branchLocation', function ($q) use ($searchQuery) {
                            $q->where('name', 'like', '%' . $searchQuery . '%');
                        })->orWhere('employee_id', 'like', '%' . $searchQuery . '%');
                });


                $query->orWhereHas('assignedto', function ($q) use ($searchQuery) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $searchQuery . '%')
                        ->orWhereHas('branchLocation', function ($q) use ($searchQuery) {
                            $q->where('name', 'like', "%{$searchQuery}%");
                        });
                });


                $query->orWhereHas('category', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });


                $query->orWhereHas('priority', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });


                $query->orWhereHas('status', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });


                $query->orWhere('additional_followers', 'like', '%' . $searchQuery . '%');

                $query->orWhereHas('documents', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });
            });
        }


        if ($startDate) {
            $query->whereDate('deadline', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('deadline', '<=', $endDate);
        }
        if ($assignedBy) {
            $query->where('assigned_by', '=', $assignedBy);
        }
        if ($assignedTo) {
            $query->where('assigned_to', '=', $assignedTo);
        }
        if ($priority) {
            $query->where('priority_id', '=', $priority);
        }
        if (isset($recurrence)) {
            $query->where('is_recurrence', '=', $recurrence);
        }
        if ($task_type) {
            $query->where('task_category_id', '=', $task_type);
        }

        if ($status) {
            $query->where('status_id', '=', $status);
        }

        $tasks = $query->paginate($per_page, ['*'], 'page', $page);
        $currentPage = $tasks->currentPage();
        $serialNumberStart = ($currentPage - 1) * $per_page + 1;

        $employees = Employee::where('status', 1)->get();
        $priorities = Status::where('type', 'priority')->get();
        $types = TaskCategory::withTrashed()->where('status', '1')->get();
        $statuses = Status::where('type', 'status')->whereNotIn('id', [3, 9])->get();

        if ($request->ajax()) {
            return response()->json([
                'tasks' => view('reports.raw.index', compact('tasks', 'serialNumberStart', 'employees', 'priorities', 'types', 'statuses'))->render(),
                'pagination' => $tasks->links('pagination::bootstrap-4')->render()
            ]);
        }

        $total_count = $this->rawCount();

        return view('reports.raw.index', [
            'tasks' => $tasks,
            'employees' => $employees,
            'priorities' => $priorities,
            'types' => $types,
            'statuses' => $statuses,
            'serialNumberStart' => $serialNumberStart,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total_count' => $total_count,
        ]);
    }

    public function overdueCount()
    {
        $currentDate = now()->toDateString();

        return Task::where('status_id', 2)->where(function ($query) use ($currentDate) {
            $query->WhereDate('deadline', '<', $currentDate);
        })
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_recurrence', 1);
                });
            })->count();
    }

    public function overdueReport(Request $request)
    {
        $page = $request->input('page', 1);
        $per_page = $request->input('per_page', 10);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $searchQuery = $request->input('search');
        $assignedBy = $request->input('assigned_by');
        $assignedTo = $request->input('assigned_to');
        $priority = $request->input('priority');
        $recurrence = $request->input('recurrence');
        $task_type = $request->input('task_type');

        $currentDate = now()->toDateString();

        // ->whereDate('deadline', '>', Carbon::now()->addDays(30))

        $query = Task::withCount('dueDates')
            ->where('status_id', 2)
            ->whereDate('deadline', '<', $currentDate)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_recurrence', 1);
                });
            });

        if ($searchQuery) {
            $query->where(function ($query) use ($searchQuery) {

                $query->where('task_no', 'like', '%' . $searchQuery . '%')
                    ->orWhere('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('deadline', 'like', '%' . $searchQuery . '%')
                    ->orWhere('rating_remark', 'like', '%' . $searchQuery . '%')
                    ->orWhere('task_rating', 'like', '%' . $searchQuery . '%');


                $query->orWhereHas('assignedby', function ($q) use ($searchQuery) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $searchQuery . '%')
                        ->orWhereHas('branchLocation', function ($q) use ($searchQuery) {
                            $q->where('name', 'like', '%' . $searchQuery . '%');
                        });
                });


                $query->orWhereHas('assignedto', function ($q) use ($searchQuery) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $searchQuery . '%')
                     ->orWhereHas('branchLocation', function ($q) use ($searchQuery) {
                            $q->where('name', 'like', "%{$searchQuery}%");
                        });
                });


                $query->orWhereHas('category', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });


                $query->orWhereHas('priority', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });


                $query->orWhereHas('status', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });


                $query->orWhere('additional_followers', 'like', '%' . $searchQuery . '%');

                $query->orWhereHas('documents', function ($q) use ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                });
            });
        }

        if ($startDate) {
            $query->whereDate('deadline', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('deadline', '<=', $endDate);
        }
        if ($assignedBy) {
            $query->where('assigned_by', '=', $assignedBy);
        }
        if ($assignedTo) {
            $query->where('assigned_to', '=', $assignedTo);
        }
        if ($priority) {
            $query->where('priority_id', '=', $priority);
        }
        if ($recurrence) {
            $query->where('is_recurrence', '=', $recurrence);
        }
        if ($task_type) {
            $query->where('task_category_id', '=', $task_type);
        }

        $tasks = $query->paginate($per_page, ['*'], 'page', $page);
        $currentPage = $tasks->currentPage();
        $serialNumberStart = ($currentPage - 1) * $per_page + 1;

        $employees = Employee::where('status', 1)->get();
        $priorities = Status::where('type', 'priority')->get();
        $types = TaskCategory::withTrashed()->where('status', '1')->get();

        if ($request->ajax()) {
            return response()->json([
                'tasks' => view('reports.overdue.index', compact('tasks', 'serialNumberStart', 'employees', 'priorities', 'types'))->render(),
                'pagination' => $tasks->links('pagination::bootstrap-4')->render()
            ]);
        }
        $total_count = $this->overdueCount();

        return view('reports.overdue.index', [
            'tasks' => $tasks,
            'employees' => $employees,
            'priorities' => $priorities,
            'types' => $types,
            'serialNumberStart' => $serialNumberStart,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total_count' => $total_count,
        ]);
    }
    public function overdueExport(Request $request)
    {
        // Retrieve filters
        $filters = [
            'search' => $request->input('search'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'assigned_by' => $request->input('assigned_by'),
            'assigned_to' => $request->input('assigned_to'),
            'priority' => $request->input('priority'),
            'task_type' => $request->input('task_type'),
        ];

        // Export the filtered data
        return Excel::download(new OverdueExport($filters), 'overdue-tasks.xlsx');
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $employeeId = $request->input('employee');
        $branch = $request->input('branch');
        $searchQuery = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = Employee::where('status', '1')->with('tasks');

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $searchQuery . '%')
                    ->orWhereHas('branchLocation', function ($q) use ($searchQuery) {
                        $q->where('name', 'like', '%' . $searchQuery . '%');
                    });
            });
        }

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        if ($branch) {
            $query->where('branch_id', 'like', $branch);
        }

        $employees = $query->paginate($perPage);

        $employeesLists = Employee::where('status', '1')->get();
        $types = BranchLocation::where('status', '1')->get();

        $serialNumberStart = ($employees->currentPage() - 1) * $perPage + 1;

        $reportData = $employees->getCollection()->map(function ($employee) use ($serialNumberStart, $month, $year) {
            // Base query for tasks
            $tasksQuery = $employee->tasks()->where('status_id', 1)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now()->toDateString())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                        ->orWhere(function ($query) {
                            $query->where('is_recurrence', 1)
                                ->whereNotNull('parent_id');
                        });
                });

            // Apply month and year filters for the current employee's tasks
            if ($month) {
                $tasksQuery->whereMonth('created_at', $month);
            } elseif ($year) {
                $tasksQuery->whereYear('created_at', $year);
            } else {
                // Default to current month if no specific month/year is provided
                $tasksQuery->whereBetween('created_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ]);
            }

            // Total tasks assigned with applied filters
            $totalTasksAssigned = Task::where('assigned_to', $employee->id)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now()->toDateString())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->whereIn('status_id', [1, 2])
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)->whereNull('parent_id');
                    })->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)->whereNotNull('parent_id');
                    });
                });

            if ($month) {
                $totalTasksAssigned->whereMonth('created_at', $month);
            } elseif ($year) {
                $totalTasksAssigned->whereYear('created_at', $year);
            } else {
                // Default to current month if no specific month/year is provided
                $totalTasksAssigned->whereBetween('created_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ]);
            }
            $totalTasksAssigned = $totalTasksAssigned->count();

            // Overall assigned tasks (without time filter)
            $overallTasksAssigned = Task::where('assigned_to', $employee->id)
                ->whereIn('status_id', [1, 2])
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now()->toDateString())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)->whereNull('parent_id');
                    })->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)->whereNotNull('parent_id');
                    });
                })
                ->count();

            // Monthly completed tasks
            $monthlycompletedTasks = Task::where('assigned_to', $employee->id)
                ->where('status_id', 1)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now()->toDateString())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)->whereNull('parent_id');
                    })->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)->whereNotNull('parent_id');
                    });
                });
            if ($month) {
                $monthlycompletedTasks->whereMonth('created_at', $month);
            } elseif ($year) {
                $monthlycompletedTasks->whereYear('created_at', $year);
            } else {
                // Default to current month if no specific month/year is provided
                $monthlycompletedTasks->whereBetween('created_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ]);
            }
            $monthlycompletedTasks = $monthlycompletedTasks->count();

            // Completed tasks in general
            $completedTasks = $employee->tasks()->where('status_id', 1)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now()->toDateString())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)->whereNull('parent_id');
                    })->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)->whereNotNull('parent_id');
                    });
                })->count();

            // Pending tasks
            $totalTaskPending_0_14 = Task::where('status_id', 2)
                ->where('assigned_to', $employee->id)
                ->whereDate('created_at', '>=', now()->subDays(13)->toDateString())
                ->whereDate('created_at', '<=', now()->toDateString())
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now()->toDateString())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
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
                ->count();
            // dd(now()->subDays(14)->toDateString(),now()->subDays(30)->toDateString());


            $totalTaskPending_15_30 = Task::where('status_id', 2)
                ->where('assigned_to', $employee->id)
                ->whereDate('created_at', '>=', now()->subDays(30)->toDateString())
                ->whereDate('created_at', '<=', now()->subDays(14)->toDateString())
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now()->toDateString())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                        ->orWhere(function ($query) {
                            $query->where('is_recurrence', 1)
                                ->whereNotNull('parent_id');
                        });
                })->count();

            // Task pending for 30+ days
            $totalTaskPending_30_plus = Task::where('status_id', 2)
                ->where('assigned_to', $employee->id)
                ->whereDate('created_at', '<', now()->subDays(30)->toDateString())
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now()->toDateString())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                        ->orWhere(function ($query) {
                            $query->where('is_recurrence', 1)
                                ->whereNotNull('parent_id');
                        });
                })->count();


            // Pending tasks calculation
            $pendingTasks = $totalTaskPending_0_14 + $totalTaskPending_15_30 + $totalTaskPending_30_plus;

            // Rating data for completed tasks
            $ratingData = $tasksQuery
                ->where('assigned_to', $employee->id)
                ->where('is_self_assign', 0)
                ->select(DB::raw('SUM(task_rating) as totalRatings'), DB::raw('COUNT(id) as ratingCount'))
                ->first();

            $rating = $ratingData && $ratingData->ratingCount > 0 ? number_format($ratingData->totalRatings / $ratingData->ratingCount, 1) : 0;

            // Overall ratings (ignoring month/year filter for this)
            $overallRatingData = DB::table('tasks')
                ->where('status_id', 1)
                ->where('assigned_to', $employee->id)
                ->where('is_self_assign', 0)
                ->select(DB::raw('SUM(task_rating) as totalRatings'), DB::raw('COUNT(id) as ratingCount'))
                ->first();

            $overallRating = $overallRatingData && $overallRatingData->ratingCount > 0 ? number_format($overallRatingData->totalRatings / $overallRatingData->ratingCount, 1) : 0;

            return [
                'serial_no' => $serialNumberStart++,
                'branch' => $employee->branch_name ?? '-',
                'employee_name' => ($employee->first_name ?? '-') . ' ' . ($employee->last_name ?? '-'),
                'totalTasksAssigned' => $totalTasksAssigned,
                'overallTasksAssigned' => $overallTasksAssigned,
                'completedTasks' => $completedTasks,
                'monthlycompletedTasks' => $monthlycompletedTasks,
                'rating' => $rating,
                'overall_rating' => $overallRating,
                'totalTaskPending_0_14' => $totalTaskPending_0_14,
                'totalTaskPending_15_30' => $totalTaskPending_15_30,
                'totalTaskPending_30_plus' => $totalTaskPending_30_plus,
                'pendingTasks' => abs($pendingTasks),
            ];
        });



        $employees->setCollection($reportData);

        if ($request->ajax()) {
            return response()->json([
                'employees' => view('reports.monthly.index', compact('employees', 'month', 'year', 'employeesLists', 'types', 'serialNumberStart'))->render(),
                'pagination' => $employees->links('pagination::bootstrap-4')->render()
            ]);
        }

        $total_count = Employee::where('status', '1')->count();

        return view('reports.monthly.index', [
            'employees' => $employees,
            'month' => $month,
            'total_count' => $total_count,
            'types' => $types,
            'year' => $year,
            'employeesLists' => $employeesLists,
            'serialNumberStart' => $serialNumberStart,
        ]);
    }


    public function monthlyExport(Request $request)
    {
        // Retrieve filters
        $filters = [
            'search' => $request->input('search'),
            'year' => $request->input('year'),
            'month' => $request->input('month'),
            'employee' => $request->input('employee'),
            'branch' => $request->input('branch'),
            'per_page' => $request->input('per_page', 10),
        ];

        // Export the filtered data
        return Excel::download(new MonthlyExport($filters), 'tasks.xlsx');
    }
}
