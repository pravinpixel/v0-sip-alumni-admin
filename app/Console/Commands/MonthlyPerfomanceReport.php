<?php

namespace App\Console\Commands;

use App\Mail\MonthlyPerfomanceReportMail;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MonthlyPerfomanceReport extends Command
{
    protected $signature = 'app:monthly-perfomance-report';
    protected $description = 'Command description';


    public function handle()
    {
        $employees = Employee::with('tasks')->where('status', '1')->get();
        $signatureSetting = Setting::where('name', 'signature')->first();
        $signatureLogo = $signatureSetting ? $signatureSetting->value : null;
    
        foreach ($employees as $employee) {
            $baseTaskQuery = Task::query()
                ->where('assigned_to', $employee->id)
                ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)')
                ->whereNotIn('status_id', [1, 3, 8, 9]);
    
            // Completed and Pending Task Counts
            $overall_completed_task = (clone $baseTaskQuery)->where('status_id', 1)->count();
            $overall_pending_task = (clone $baseTaskQuery)->where('status_id', 2)->count();
    
            // Get rating data
            $ratingData = DB::table('tasks')
                ->where('status_id', 1)
                ->where('assigned_by', $employee->id)
                ->select(
                    DB::raw('SUM(task_rating) as totalRatings'),
                    DB::raw('COUNT(id) as ratingCount')
                )
                ->first();
    
            $overall_rating = $ratingData && $ratingData->ratingCount > 0
                ? number_format($ratingData->totalRatings / $ratingData->ratingCount, 1)
                : 0;
    
            // Tasks Collection for the report
            $tasksCollection = $baseTaskQuery->with('assignedby')->get();
    
            $tasks = [
                'tasks' => $tasksCollection,
                'auth_user' => $tasksCollection->first()?->assignedto,
                'signature_logo' => $signatureLogo,
                'overall_completed_task' => $overall_completed_task,
                'overall_pending_task' => $overall_pending_task,
                'overall_rating' => $overall_rating
            ];
            dd($tasks);
            
    
            try {
                Mail::to($employee->email)->send(new MonthlyPerfomanceReportMail($tasks));
            } catch (\Exception $e) {
                Log::error("Failed to send performance report to {$employee->email}: " . $e->getMessage());
            }
        }
    
        $this->info('Performance reports have been sent successfully.');
    }
    
}
