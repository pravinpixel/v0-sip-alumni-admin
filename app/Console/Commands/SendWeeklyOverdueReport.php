<?php

namespace App\Console\Commands;

use App\Mail\WeeklyOverdueReportMail;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyOverdueReport extends Command
{
    protected $signature = 'app:send-weekly-overdue-report';
    protected $description = 'Send weekly overdue report to each employee';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $employees = Employee::where('status', '1')->get();

        foreach ($employees as $employee) {
            $tasksCollection = Task::with('assignedto')
                ->whereNotIn('status_id', [1, 3, 8, 9])
                ->whereDate('deadline', '<', now()->format('Y-m-d'))
                ->where('assigned_to', $employee->id)
                ->get();

            if ($tasksCollection->isNotEmpty()) {
                $tasks = [
                    'tasks' => $tasksCollection,
                    'auth_user' => $tasksCollection->first()?->assignedto
                ];

                $setting = Setting::where('name', 'signature')->first();
                $tasks['signature_logo'] = $setting ? $setting->value : null;
                try {
                     Mail::to($employee->email)->send(new WeeklyOverdueReportMail($tasks));
                } catch (\Exception $e) {
                    // Log the exception but do not break the flow
                    Log::channel('cron')->info('Task Weekly overdue report notification failed: ' . $e->getMessage());
                }
            }
        }

        $this->info('Weekly overdue reports have been sent successfully.');
    }

}
