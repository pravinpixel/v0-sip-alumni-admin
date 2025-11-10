<?php

namespace App\Console\Commands;

use App\Mail\AssigneeWiseOverdueReportMail;
use App\Mail\UpcomingWeekOverdueReportMail;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AssigneeWiseWeeklyOverdueReport extends Command
{
    protected $signature = 'app:assignee-wise-weekly-overdue-report';
    protected $description = 'Command description';

   public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $employees = Employee::with('tasks')->where('status', '1')->get();
    
        foreach ($employees as $employee) {
            $startDate = now()->addWeek()->startOfWeek();
            $endDate = now()->addWeek()->endOfWeek();

            $tasksCollection = Task::with('assignedby')
                ->whereNotIn('status_id', [1, 3, 8, 9])
                ->whereBetween('deadline', [$startDate, $endDate])
                ->where('assigned_to', $employee->id)
                ->get();
    
            if ($tasksCollection->isNotEmpty()) {
                $tasks = [
                    'tasks' => $tasksCollection,
                    'auth_user' => $tasksCollection->first()?->assignedto
                ];

                $setting = Setting::where('name', 'signature')->first();
                $tasks['signature_logo'] = $setting ? $setting->value : null;
    
                Mail::to($employee->email)->send(new AssigneeWiseOverdueReportMail($tasks));
            }
        }
    
        $this->info('Assignee wise weekly overdue reports have been sent successfully.');
    }
}
