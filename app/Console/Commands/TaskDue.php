<?php

namespace App\Console\Commands;

use App\Helpers\UtilsHelper;
use App\Mail\TaskDueReminderEmail;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Task;
use App\Models\TaskDueDate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TaskDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:task-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for overdue tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Log::channel('cron')->info('New Recurrence Task Creation');


        $today = Carbon::now()->timezone('Asia/Kolkata')->format('Y-m-d');
        $tasks = Task::whereDate('deadline', $today)
            ->whereNotIn('status_id', [1,3,8,9])
            ->whereNotNull('parent_id')
            ->where('is_recurrence', 1)
            ->addSelect(['max_date' => Task::select('deadline')
                ->whereColumn('parent_id', 'tasks.parent_id')
                ->orderBy('deadline', 'desc')
                ->limit(1)
            ])
            ->having('max_date', '<=', $today)
            ->get();

        $path = UtilsHelper::getStoragePath();
        $addDays = config('app.task_create_days_count');


        foreach ($tasks as $task) {
            $parent = Task::where('id', $task->parent_id)->whereIn('status_id', [1, 2])->first();
            if ($parent) {
                $new_recurrence = UtilsHelper::isDateInRRule($parent->recurrence, $task->deadline, 'after');

                if (count($new_recurrence['occurrences']) > 0) {
                    foreach ($new_recurrence['occurrences'] as $occurrence) {
                        $dead_line = Carbon::parse($occurrence['start'])->setTimezone('Asia/Kolkata')->format('Y-m-d');

                        $taskExist = Task::where('parent_id', $parent->id)
                            ->where('deadline', $dead_line)->first();
                        if ($taskExist) {
                            Log::channel('cron')->info('Task Already Created: ' . $taskExist->id . ' Deadline: ' . $dead_line);
                            continue;
                        }
                        $taskStartDate = Carbon::parse($occurrence['start'])->setTimezone('Asia/Kolkata')->subDays($addDays)->format('Y-m-d');

                        $childTask = $parent->replicate();
                        $childTask->task_no = UtilsHelper::getTaskMaxNo();
                        $childTask->deadline = $dead_line;
                        $childTask->date = $taskStartDate;
                        $childTask->status_id = 2;
                        $childTask->status_date = null;
                        $childTask->task_rating = null;
                        $childTask->rating_remark = null;
                        $childTask->mark_as_completed = null;
                        $childTask->parent_id = $parent->id;
                        $childTask->is_mail_send = 0;
                        $childTask->save();

                        $parent->documents->each(function ($document) use ($childTask, $path) {
                            $file = str_replace($path, '', $document['document']);
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                            $fileName = "admin/document_" . uniqid() . "_" . time() . "." . $ext;
                            Storage::disk('public')->copy($file, $fileName);

                            $childTask->documents()->create([
                                'document' => $fileName,
                                'name' => $document['name'],
                            ]);
                        });

                    }
                }


            }
        }

        Log::channel('cron')->info('New Recurrence Task End');

        Log::channel('cron')->info('Task Due Email Start');

        $tasks = UtilsHelper::getOverdueTasks();

        foreach ($tasks as $task) {
            if($task->is_recurrence == 1){
                $daysDifference = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($task->deadline), false);
                if ($daysDifference == 0) {
                    $daysChangeMessage = "Today";
                } elseif ($daysDifference > 0) {
                    $daysChangeMessage = $daysDifference . " Days left";
                } else {
                    $daysChangeMessage = abs($daysDifference) . " Days overdue";
                }
                $task['due_date_string'] = $daysChangeMessage;
            }else{

                $daysDifference = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($task->created_at), false);
                $task['due_date_string'] = abs($daysDifference);
            }

            $task['due_change_count'] = TaskDueDate::where('task_id', $task->id)->count();
            $task['auth_user'] = $task->assignedby;
            $setting = Setting::where('name', 'signature')->first();
            $task['signature_logo'] = $setting ? $setting->value : null;

            $to = [];
            if ($task->assignedto) {
                $to[]= $task->assignedto->email;
            }
            $deadline = $task->deadline;
            $currentDate = now()->toDateString();
            Log::channel('cron')->info('today date' . $currentDate);

            // if ($deadline === $currentDate) {
            //     $notification = new Notification;
            //     $notification->module = 'task';
            //     $notification->action = 'task due';
            //     $notification->message = 'task due today';
            //     $notification->task_id = $task->id;
            //     $notification->to_id = $task->assigned_to;
            //     $notification->created_by = $task->assigned_to;
            //     $notification->save();
            // }else{
            //     $notification = new Notification;
            //     $notification->module = 'task';
            //     $notification->action = 'task overdue';
            //     $notification->message = 'task overdue';
            //     $notification->task_id = $task->id;
            //     $notification->to_id = $task->assigned_to;
            //     $notification->created_by = $task->assigned_to;
            //     $notification->save();
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
        }

        Log::channel('cron')->info('Task Due Email End');

    }
}
