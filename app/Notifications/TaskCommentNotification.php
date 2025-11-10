<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Helpers\UtilsHelper;

class TaskCommentNotification extends Notification
{
    use Queueable;
    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $doc_paths = [];

        foreach ($this->data->documents as $file) {
            $path = UtilsHelper::getStoragePath();
            $doc = $path . $file;

            $doc_paths[] = $doc;
        }

        $this->data->documents = $doc_paths;
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
        $subject = '🔔 Task Master Notification Alert - ' . $this->data->task->task_no . '-' . $this->data['name'];

        $data = [
            'task' => $this->data
        ];
        
        $mailMessage = (new MailMessage())
            ->subject($subject)
            ->view('emails.task-comment',  $data);

        if (isset($this->data['followers'])) {
            $cc = $this->data['followers'];
            $mailMessage->cc($cc);
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
