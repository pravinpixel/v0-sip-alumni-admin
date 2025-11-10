<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskCreatedNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    private function generateICS($task)
    {
        $startDateTime = Carbon::parse($task['date'])->format('Ymd\THis');
        $endDateTime = Carbon::parse($task['deadline'])->format('Ymd\THis');
        $now = Carbon::now()->format('Ymd\THis');

        $icsContent = "
        BEGIN:VCALENDAR
        VERSION:2.0
        PRODID:-//Your Organization//Your App//EN
        CALSCALE:GREGORIAN
        METHOD:PUBLISH
        BEGIN:VEVENT
        DTSTAMP:" . $now . "
        DTSTART:" . $startDateTime . "
        DTEND:" . $endDateTime . "
        SUMMARY:" . $task['name'] . "
        DESCRIPTION:" . $task['description'] . "
        STATUS:CONFIRMED
        END:VEVENT
        END:VCALENDAR
        ";

        return $icsContent;
    }

    private function saveICSFile($task)
    {
        $icsContent = $this->generateICS($task);
        $fileName = 'calendar.ics';
        $filePath = 'public/admin/' . $fileName;
        Storage::disk('local')->put($filePath, $icsContent);
    
        return storage_path('app/' . $filePath);
    }

    /**
     * Get the emails representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = '🔔 Task Master Notification Alert - ' . $this->data['task_no'] . '-' . $this->data['name'];

        $data = [
            'task' => $this->data
        ];


        $mailMessage = (new MailMessage())
            ->subject($subject)
            ->view('emails.task-created',  $data);

        if (isset($this->data['followers'])) {
            $cc = $this->data['followers'];
            $mailMessage->cc($cc);
        }
        
        $icsFilePath = $this->saveICSFile($this->data);
        if ($icsFilePath) {
            $mailMessage->attach($icsFilePath, [
                'as' => 'task.ics',
                'mime' => 'text/calendar',
            ]);
        }

        return $mailMessage;
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
