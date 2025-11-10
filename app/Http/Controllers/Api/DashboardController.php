<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BranchLocation;
use App\Models\Employee;
use App\Models\Iallert;
use App\Models\Role;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function myTask(Request $request, $type = null)
    {
        $tasks = Task::query()
            ->with('dueDates', 'documents', 'status', 'assignedToEmployee.branchLocation')
            ->with(['assignedToEmployee' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'email', 'profile_image', 'employee_id', 'branch_id')
                    ->selectRaw("CONCAT(first_name, ' ', last_name) as name");
            }])
            ->whereNotIn('status_id', [1, 3, 8, 9])
            ->whereNotNull('task_no')
            ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
            ->where('assigned_to', Auth::user()->id)
            ->get();

        $ageCategories = [
            '0-15_days' => 0,
            '16-30_days' => 0,
            'greater_than_30_days' => 0,
        ];

        $tasks->each(function ($task) use (&$ageCategories) {
            $age_date_check = Carbon::parse($task->created_at)->format('Y-m-d');
            $daysDifference = Carbon::now()->startOfDay()->diffInDays($age_date_check);

            $age = $task->is_recurrence == 1
                ? Carbon::now()->startOfDay()->diffInDays($task->deadline) ?? ''
                : $daysDifference;

            if ($age >= 0 && $age <= 15) {
                $ageCategories['0-15_days']++;
            } elseif ($age >= 16 && $age <= 30) {
                $ageCategories['16-30_days']++;
            } elseif ($age > 30) {
                $ageCategories['greater_than_30_days']++;
            }

            // $ageCategories['less_than_30_days'] = $ageCategories['0-15_days'] + $ageCategories['16-30_days'];
        });

        $task_age = Task::query()
            ->with(['assignedByEmployee' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'email', 'profile_image', 'employee_id', 'branch_id')
                    ->selectRaw("CONCAT(first_name, ' ', last_name) as name");
            }])
            ->where('status_id', 2)
            ->whereNotNull('task_no')
            ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
            ->where('assigned_to', Auth::user()->id)
            ->selectRaw('assigned_by, COUNT(*) as task_count')
            ->groupBy('assigned_by')
            ->get();

        $currentDate = now()->toDateString();

        $task_overdue = Task::where('status_id', 2)
            ->whereDate('deadline', '<', $currentDate)
            ->where('assigned_to', Auth::user()->id)
            ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
            ->count();

        $task_due_today = Task::where('status_id', 2)
            ->whereDate('deadline', '=', $currentDate)
            ->where('assigned_to', Auth::user()->id)
            ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
            ->count();

        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();


        $task_due_this_week = Task::where('status_id', 2)
            ->where('deadline', '>=', $startOfWeek)
            ->where('deadline', '<=', $endOfWeek)
            ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
            ->where('assigned_to', Auth::user()->id)
            ->count();

        $task_rest_due = Task::where('status_id', 2)
            ->where('deadline', '>', $endOfWeek)
            ->where('assigned_to', Auth::user()->id)
            ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
            ->count();




        return response()->json([
            'ageCategories' => $ageCategories,
            'my_task_age' => $task_age,
            'task_overdue' => $task_overdue,
            'task_due_today' => $task_due_today,
            'task_due_this_week' => $task_due_this_week,
            'task_rest_due' => $task_rest_due
        ]);
    }


    public function assignedTask(Request $request, $type = null)
    {
        $tasks = Task::query()
            ->with('dueDates', 'documents', 'status', 'assignedToEmployee.branchLocation')
            ->with(['assignedToEmployee' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'email', 'profile_image', 'employee_id', 'branch_id')
                    ->selectRaw("CONCAT(first_name, ' ', last_name) as name");
            }])
            ->whereNotIn('status_id', [1, 3, 8, 9])
            ->whereNotNull('task_no')
            ->where('assigned_by', Auth::user()->id)
            ->get();

        $ageCategories = [
            '0-15_days' => 0,
            '16-30_days' => 0,
            'greater_than_30_days' => 0,
        ];

        $tasks->each(function ($task) use (&$ageCategories) {
            $age_date_check = Carbon::parse($task->created_at)->format('Y-m-d');
            $daysDifference = Carbon::now()->startOfDay()->diffInDays($age_date_check);

            $age = $task->is_recurrence == 1
                ? Carbon::now()->startOfDay()->diffInDays($task->deadline) ?? ''
                : $daysDifference;

            if ($age >= 0 && $age <= 15) {
                $ageCategories['0-15_days']++;
            } elseif ($age >= 16 && $age <= 30) {
                $ageCategories['16-30_days']++;
            } elseif ($age > 30) {
                $ageCategories['greater_than_30_days']++;
            }

            // $ageCategories['less_than_30_days'] = $ageCategories['0-15_days'] + $ageCategories['16-30_days'];
        });

        $task_age = Task::query()
            ->with(['assignedToEmployee' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'email', 'profile_image', 'employee_id', 'branch_id')
                    ->selectRaw("CONCAT(first_name, ' ', last_name) as name");
            }])
            ->where('status_id', 2)
            ->whereNotNull('task_no')
            ->where('assigned_by', Auth::user()->id)
            ->selectRaw('assigned_to, COUNT(*) as task_count')
            ->groupBy('assigned_to')
            ->get();

        $currentDate = now()->toDateString();

        $task_overdue = Task::where('status_id', 2)
            ->whereDate('deadline', '<', $currentDate)
            ->where('assigned_by', Auth::user()->id)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_recurrence', 1);
                });
            })->count();

        $task_due_today = Task::where('status_id', 2)
            ->whereDate('deadline', '=', $currentDate)
            ->where('assigned_by', Auth::user()->id)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_recurrence', 1);
                });
            })->count();

        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();


        $task_due_this_week = Task::where('status_id', 2)
            ->where('deadline', '>=', $startOfWeek)
            ->where('deadline', '<=', $endOfWeek)
            ->where('assigned_by', Auth::user()->id)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_recurrence', 1);
                });
            })->count();

        $task_rest_due = Task::where('status_id', 2)
            ->where('deadline', '>', $endOfWeek)
            ->where('assigned_by', Auth::user()->id)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_recurrence', 1);
                });
            })->count();


        return response()->json([
            'ageCategories' => $ageCategories,
            'my_task_age' => $task_age,
            'task_overdue' => $task_overdue,
            'task_due_today' => $task_due_today,
            'task_due_this_week' => $task_due_this_week,
            'task_rest_due' => $task_rest_due
        ]);
    }

    public function empDirectory(Request $request)
    {
        $search = $request->query('search');
        $empTaskCounts = Employee::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->with('designation')
            ->addSelect([
                'pending_task_count' => Task::whereColumn('tasks.assigned_to', 'employees.id')
                    ->whereNotIn('tasks.status_id', [1, 3, 8, 9])
                    ->whereNotNull('tasks.task_no')
                    ->selectRaw('count(*)')
            ])
            ->get()->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->first_name . ' ' . $emp->last_name,
                    'profile_image' => $emp->profile_image,
                    'designation' => $emp->designation,
                    'pending_task_count' => $emp->pending_task_count,
                ];
            })
            ->toArray();

        return response()->json($empTaskCounts);
    }

    public function ialertGraph(Request $request)
    {
        $currentDate = now()->toDateString();
        $subDays = function ($days) {
            return now()->subDays($days);
        };

        $emp = Employee::find(Auth::id());
        $json_branch = json_decode($emp->branch_id, true);
        if (is_array($json_branch)) {
            $branches = $json_branch;
        } else {
            $branches = [$json_branch];
        }
        $branch_codes = BranchLocation::whereIn('id', $branches)->pluck('branch_code')->toArray();

        $role_check = Role::where('id', $emp->role_id)->pluck('name')->toArray();

        if (in_array("Business Development Executive", $role_check)) {
            $data = [
                'follow-up-overdue' => [
                    'wc_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereDate('wc_date', '<', $currentDate)->count(),
                    'ba_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereDate('ba_customer_commitment_date', '<', $currentDate)->count(),
                    'customer_follow_up_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereDate('customer_follow_up_date', '=', $currentDate)->count(),
                    'payment_commited_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereDate('payment_commited_on', '=', $currentDate)->count(),
                    'internal_follow_up_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereDate('internal_follow_up_date', '<', $currentDate)->count(),
                ],
                'pending-work' => [
                    'wcr_blank' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereNull('wcr_status')->whereNotNull('customer_email')->count(),
                    'wcr_no' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('wcr_status', '0')->count(),
                    'ba_blank' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereNull('bill_ac_status')->where('wcr_status', '1')->count(),
                    'ba_no' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('bill_ac_status', '0')->count(),
                    'tough_nut_yes' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('tough_nut_status', '1')->count(),
                    'rnr' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereNotNull('rnr')->count(),
                ],
                'age-wise-invoice' => [
                    'zero_to_7_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereBetween('age', [$subDays(7), now()])->count(),
                    'lessthan_30_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereBetween('age', [$subDays(30), $subDays(7)])->count(),
                    'days_30_60' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereBetween('age', [$subDays(60), $subDays(30)])->count(),
                    'days_60_90' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->whereBetween('age', [$subDays(90), $subDays(60)])->count(),
                    'greater_than_90_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('age', '<', $subDays(90))->count(),
                    'greater_than_120_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('age', '<', $subDays(120))->count(),
                    'greater_than_150_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('age', '<', $subDays(150))->count(),
                    'greater_than_200_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('age', '<', $subDays(200))->count(),
                    'greater_than_365_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('age', '<', $subDays(365))->count(),
                ],
                'value-wise-invoice' => [
                    'invoice_value_1000' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('invoice_value', '<=', 1000)->count(),
                    'invoice_value_1001' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('invoice_value', '>=', 1001)->where('invoice_value', '<=', 10000)->count(),
                    'invoice_value_10001' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('invoice_value', '>=', 10001)->where('invoice_value', '<=', 50000)->count(),
                    'invoice_value_50001' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('invoice_value', '>=', 50001)->where('invoice_value', '<=', 100000)->count(),
                    'invoice_value_100001' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('invoice_value', '>=', 100001)->where('invoice_value', '<=', 500000)->count(),
                    'invoice_value_500000' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bde_id', $emp->employee_id)->where('invoice_value', '>', 500000)->count(),
                ],
            ];
        } else {
            $data = [
                'follow-up-overdue' => [
                    'wc_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->whereDate('wc_date', '<', $currentDate)->count(),
                    'ba_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->whereDate('ba_customer_commitment_date', '<', $currentDate)->count(),
                    'customer_follow_up_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->whereDate('customer_follow_up_date', '=', $currentDate)->count(),
                    'payment_commited_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->whereDate('payment_commited_on', '=', $currentDate)->count(),
                    'internal_follow_up_overdue' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->whereDate('internal_follow_up_date', '<', $currentDate)->count(),
                ],
                'pending-work' => [
                    'wcr_blank' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->whereNull('wcr_status')->whereNotNull('customer_email')->count(),
                    'wcr_no' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('wcr_status', '0')->count(),
                    'ba_blank' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->whereNull('bill_ac_status')->where('wcr_status', '1')->count(),
                    'ba_no' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('bill_ac_status', '0')->count(),
                    'tough_nut_yes' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('tough_nut_status', '1')->count(),
                    'rnr' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->whereNotNull('rnr')->count(),
                ],
                'age-wise-invoice' => [
                    'zero_to_7_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('age', '>=', 0)->where('age', '<=', 7)->count(),
                    'lessthan_30_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('age', '<', 30)->count(),
                    'days_30_60' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('age', '>=', 30)->where('age', '<=', 60)->count(),
                    'days_60_90' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('age', '>=', 60)->where('age', '<=', 90)->count(),
                    'greater_than_90_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('age', '>', 90)->count(),
                    'greater_than_120_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('age', '>', 120)->count(),
                    'greater_than_150_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('age', '>', 150)->count(),
                    'greater_than_200_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('age', '>', 200)->count(),
                    'greater_than_365_days' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('age', '>', 365)->count(),
                ],
                'value-wise-invoice' => [
                    'invoice_value_1000' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('invoice_value', '<=', 1000)->count(),
                    'invoice_value_1001' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('invoice_value', '>=', 1001)->where('invoice_value', '<=', 10000)->count(),
                    'invoice_value_10001' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('invoice_value', '>=', 10001)->where('invoice_value', '<=', 50000)->count(),
                    'invoice_value_50001' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('invoice_value', '>=', 50001)->where('invoice_value', '<=', 100000)->count(),
                    'invoice_value_100001' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('invoice_value', '>=', 100001)->where('invoice_value', '<=', 500000)->count(),
                    'invoice_value_500000' => Iallert::where('os_value', '!=', 0)->whereIn('branch_id', $branch_codes)->where('invoice_value', '>', 500000)->count(),
                ],
            ];
        }

        return response()->json($data);
    }
}
