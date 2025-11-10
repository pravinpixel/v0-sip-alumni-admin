<?php

namespace App\Mail;

use App\Helpers\UtilsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskRatingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $subject = '🔔 Task Master Notification Alert - ' . $this->data['task_no'] .'-'. $this->data['name'];

        $data = [
            'task' => $this->data
        ];

        $mailMessage = $this
            ->subject($subject)
            ->view('emails.task-rating',  $data);

        $mailMessage->withSymfonyMessage(function ($message) {
            $customMessageId = $this->data['task_no'].'@taskmaster.designonline.in';
            $message->getHeaders()->addIdHeader('Message-ID', $customMessageId);
            $message->getHeaders()->addTextHeader('In-Reply-To', $customMessageId);
            $message->getHeaders()->addTextHeader('References', $customMessageId);
        });

        // $cc = UtilsHelper::addTaskCC($this->data);
        // if ($cc) {
        //     $mailMessage->cc($cc);
        // }

        return $mailMessage;

    }



}
