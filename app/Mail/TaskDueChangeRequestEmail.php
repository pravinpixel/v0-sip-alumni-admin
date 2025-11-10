<?php

namespace App\Mail;

use App\Helpers\UtilsHelper;
use App\Models\Setting;
use App\Models\TaskDueDate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Spatie\IcalendarGenerator\Components\Event;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Spatie\IcalendarGenerator\Components\Calendar;

class TaskDueChangeRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $setting = Setting::where('name', 'signature')->first();
        $this->data['signature_logo'] = $setting ? $setting->value : null;
    }

    public function build()
    {
        $subject = '🔔 Task Master Notification Alert - ' . $this->data['task_no'] . '-' . $this->data['name'];


        $data = [
            'task' => $this->data
        ];

        $mailMessage = $this
            ->subject($subject)
            ->view('emails.task-due-change-request', $data);

        $mailMessage->withSymfonyMessage(function ($message) {
            $customMessageId = $this->data['task_no'].'@taskmaster.designonline.in';
            $message->getHeaders()->addIdHeader('Message-ID', $customMessageId);
            $message->getHeaders()->addTextHeader('In-Reply-To', $customMessageId);
            $message->getHeaders()->addTextHeader('References', $customMessageId);
        });

        $cc = UtilsHelper::addTaskCC($this->data);
        if ($cc) {
            $mailMessage->cc($cc);
        }

        $icsFilePath = $this->generateICS($this->data);
        if ($icsFilePath) {
            $mailMessage->attach($icsFilePath, [
                'as' => 'task.ics',
                'mime' => 'text/calendar',
            ]);
        }

        return $mailMessage;

    }

    private function generateICS($task)
    {   
        $event = Event::create($task['name'])
            ->startsAt(Carbon::parse($task['date']))
            ->endsAt(Carbon::parse($task['deadline']))
            ->description($task['description'])
            ->organizer($task['auth_user']->email);

        $calendar = Calendar::create($task['name'])
            ->event($event);

        $icalContent = $calendar->get();

        $fileName = $task['task_no'] . '.ics';
        $filePath = storage_path('app/public/ics/' . $fileName);
        file_put_contents($filePath, $icalContent);

        return $filePath;
    }


}
