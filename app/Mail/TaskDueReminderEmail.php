<?php

namespace App\Mail;

use App\Helpers\UtilsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskDueReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $subject = '🔔 Task Master Notification Alert - ' . $this->data['task_no'] . '-' . $this->data['name'];

        $data = [
            'task' => $this->data
        ];

        $mailMessage = $this
            ->subject($subject)
            ->view('emails.task-due', $data);

        $mailMessage->withSymfonyMessage(function ($message) {
            $task_no = $this->data['task_no'];
            if (!$task_no) {
                $task_no = rand(10000, 99999);
            }

            $customMessageId = $task_no . '@taskmaster.designonline.in';
            $message->getHeaders()->addIdHeader('Message-ID', $customMessageId);
            $message->getHeaders()->addTextHeader('In-Reply-To', $customMessageId);
            $message->getHeaders()->addTextHeader('References', $customMessageId);
        });

        $cc = UtilsHelper::addTaskCC($this->data);
        if ($cc) {
            $mailMessage->cc($cc);
        }

        return $mailMessage;
    }

}
