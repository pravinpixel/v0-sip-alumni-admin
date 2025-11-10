<?php

namespace App\Console\Commands;

use App\Helpers\UtilsHelper;
use App\Mail\TaskDueReminderEmail;
use App\Models\Setting;
use App\Models\Task;
use App\Models\TaskDueDate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TaskTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:task-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task Creation Command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('cron')->info('Task Due Email Start');

        // $tasks = UtilsHelper::getOverdueTasks();

        $task = Task::whereNotIn('status_id', [1, 3,8,9])
        ->where(function ($query) {
            $currentDate = now()->toDateString();

            $query->whereDate('deadline', '=', $currentDate) // Tasks due today
            ->orWhereDate('deadline', '<', $currentDate); // Tasks overdue
        })
        ->with('documents')
        ->first();

        // dd($task);

        // foreach ($tasks as $task) {
           
        // }

        $daysDifference = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($task->deadline), false);
        if ($daysDifference != 0) {
            $daysChangeMessage = $daysDifference > 0
                ? "Deadline extended by $daysDifference days."
                : "Deadline shortened by " . abs($daysDifference) . " days.";
            $task['deadline_change'] = $daysChangeMessage;
        }
        $task['due_change_count'] = TaskDueDate::where('task_id', $task->id)->count();
        $task['auth_user'] = $task->assignedby;
        $setting = Setting::where('name', 'signature')->first();
        $task['signature_logo'] = $setting ? $setting->value : null;

        $to = ['panneer63834@gmail.com','ratheeshskic@gmail.com'];
        // if ($task->assignedto ==) {
        //     $to[] = $task->assignedto->email;
        // }
        // if ($task->assignedby ==) {
        //     $to[] = $task->assignedby->email;
        // }

        if (count($to) > 0) {
            try {
                Mail::to($to)->send(new TaskDueReminderEmail($task));
                Log::channel('cron')->info('Email sent for task ' . $task->id);
            } catch (\Exception $e) {
                Log::channel('cron')->error('Error sending email for task ' . $task->id . ': ' . $e->getMessage());
            }
        } else {
            Log::channel('cron')->info('No email found for task ' . $task->id);
        }

        Log::channel('cron')->info('Task Due Email End');


    }
}
