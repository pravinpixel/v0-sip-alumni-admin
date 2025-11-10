<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class BaseMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected $task_no;

    public function __construct($task_no)
    {
        $this->task_no = $task_no;
    }

    public function build()
    {
        $this->withSymfonyMessage(function (Email $message) {
            $customMessageId = $this->task_no . '@taskmaster.designonline.in';
            $message->getHeaders()->addIdHeader('Message-ID', $customMessageId);
            $message->getHeaders()->addTextHeader('In-Reply-To', $customMessageId);
            $message->getHeaders()->addTextHeader('References', $customMessageId);
        });

        return $this;
    }
}
