<?php

namespace App\Console\Commands;


use App\Helpers\UtilsHelper;
use App\Mail\TaskCreatedEmail;
use App\Models\Employee;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TaskCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:task-create';

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

        Log::channel('cron')->info('Remove ICS File Start');

        $tempFiles = Storage::disk('public')->allFiles('ics');
        foreach ($tempFiles as $file) {
            Storage::disk('public')->delete($file);
        }
        Log::channel('cron')->info('Remove ICS File End');


        Log::channel('cron')->info('Recurrence Task Creation Mail Send Start');

        $today = Carbon::now()->timezone('Asia/Kolkata')->format('Y-m-d');
        $taskCreationMail = Task::where([
            'status_id' => 2,
            'is_mail_send' => '0',
            'is_recurrence' => '1',
        ])->whereNotNull('parent_id')->where('date', $today)->get();

        Log::channel('cron')->info('Mail Task Count: ' . $taskCreationMail->count() . ' Ids: ' . $taskCreationMail->pluck('id') ?? '');

        if($taskCreationMail->count() > 0) {
            foreach ($taskCreationMail as $task) {

                $auth_user = Employee::where('id', $task->assigned_by)->first();
                if (!$auth_user)
                    continue;

                $task->is_mail_send = 1;
                $task->save();

                $task['auth_user'] = $auth_user;

                try {
                    Mail::to([
                        $task->assignedto->email,
                        $task->assignedby->email,
                    ])->send(new TaskCreatedEmail($task));
                   Log::channel('cron')->info('Recurrence Task Creation Mail Send End');
                } catch (\Exception $e) {
                    $task_mail = Task::find($task->id);
                    $task->is_mail_send = 0;
                    $task_mail->is_mail_failed = 1;
                    $task_mail->save();
                    // Log the exception but do not break the flow
                    Log::channel('cron')->info('Task notification failed: ' . $e->getMessage());
                }

            }
        }


        Log::channel('cron')->info('Task Creation Schedule Start');
        $parentTasks = Task::where(['status_id' => 2])
            ->whereNotNull('recurrence')
            ->whereNull('parent_id')->get();
        $createdTaskCount = UtilsHelper::recurrenceTask($parentTasks);
        Log::channel('cron')->info('Task Creation Schedule End');


    }
}
