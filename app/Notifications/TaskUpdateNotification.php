<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskUpdateNotification extends Notification
{
    use Queueable;

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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = '🔔 Task Master Notification Alert - ' . $this->data['task_no'] .'-'. $this->data['name'];

        $data = [
            'task' => $this->data
        ];
        // dd($data);

        $mailMessage = (new MailMessage())
            ->subject($subject)
            ->view('emails.task-due-change',  $data);

        if (isset($this->data['followers'])) {
            $cc =$this->data['followers'];
            $mailMessage->cc($cc);
        }
        if(isset($this->data['attach'])){
            $mailMessage->attach(
                public_path('calendar.ics'),
            );
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
