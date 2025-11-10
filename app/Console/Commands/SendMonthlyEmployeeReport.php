<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;
use App\Mail\MonthlyReportMail;
use App\Models\Setting;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendMonthlyEmployeeReport extends Command
{
    protected $signature = 'app:send-monthly';
    protected $description = 'Send monthly report to each employee';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if (now()->format('d') != now()->endOfMonth()->format('d')) {
            return;
        }

        $start_date = now()->startOfMonth()->format('Y-m-d H:i:s');
        $end_date = now()->endOfMonth()->format('Y-m-d H:i:s');

        //        $start_date = '2024-10-01 00:00:00';
        //        $end_date = '2024-10-31 23:59:59';

        $employees = Employee::where('status', '1')->get();

        foreach ($employees as $employee) {

            $assignedto = $employee;

            $total_assigned_task = $total_completed_task = 0;
            $total_pending_task = $rating = 0;
            if ($assignedto && $assignedto->id) {
                $total_assigned_task = Task::where('assigned_by', $assignedto->id)->whereIn('status_id', [1, 2])
                    ->where('created_at', '>=', $start_date)
                    ->where('created_at', '<=', $end_date)
                    ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->where('is_recurrence', 0)->whereNull('parent_id');
                        })->orWhere(function ($query) {
                            $query->where('is_recurrence', 1)->whereNotNull('parent_id');
                        });
                    })->count();

                $total_completed_task = Task::where('assigned_to', $assignedto->id)
                    ->where('status_id', 1)
                    ->where('created_at', '>=', $start_date)
                    ->where('created_at', '<=', $end_date)
                    ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->where('is_recurrence', 0)->whereNull('parent_id');
                        })->orWhere(function ($query) {
                            $query->where('is_recurrence', 1)->whereNotNull('parent_id');
                        });
                    })->count();

                $ratingData = DB::table('tasks')
                    ->where('status_id', 1)
                    ->where('assigned_to', $assignedto->id)
                    ->where('is_self_assign', 0)
                    ->where('created_at', '>=', $start_date)
                    ->where('created_at', '<=', $end_date)
                    ->select(DB::raw('SUM(task_rating) as totalRatings'), DB::raw('COUNT(id) as ratingCount'))
                    ->first();

                if ($ratingData && $ratingData->ratingCount > 0) {
                    $rating = number_format(($ratingData->totalRatings / $ratingData->ratingCount), 1) ?: 0;
                }

                $total_pending_task = Task::where('assigned_to', $assignedto->id)
                    ->where('status_id', 2)
                    ->where('created_at', '>=', $start_date)
                    ->where('created_at', '<=', $end_date)
                    ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->where('is_recurrence', 0)->whereNull('parent_id');
                        })->orWhere(function ($query) {
                            $query->where('is_recurrence', 1)->whereNotNull('parent_id');
                        });
                    })->count();
            }

            $setting = Setting::where('name', 'signature')->first();
            $auth = Task::where('assigned_by', $assignedto->id)->first();

            $data = [
                'branch' => $assignedto->branch_name ?? '-',
                'assignedto' => ($assignedto->first_name ?? '') . ' ' . ($assignedto->last_name ?? '-'),
                'total_assigned_task' => $total_assigned_task ?: '0',
                'total_pending_task' => $total_pending_task ?: '0',
                'total_completed_task' => $total_completed_task ?: '0',
                'rating' => $rating ?: '0',
                'setting' => $setting ? $setting->value : null,
                'auth_user' => $auth ? $auth->assignedby : null,
            ];


            try {
                // Send the email with the Excel attachment
                Mail::to($employee->email)->send(new MonthlyReportMail($data));
            } catch (\Exception $e) {
                // dd($e->getMessage());
                Log::channel('cron')->info('Task Monthly report notification failed: ' . $e->getMessage());
            }
        }


        $this->info('Monthly reports have been sent successfully.');
    }
}
