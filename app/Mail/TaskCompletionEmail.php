<?php

namespace App\Mail;

use App\Helpers\UtilsHelper;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class TaskCompletionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
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
            ->view('emails.task-complete', $data);

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

        return $mailMessage;

    }
}
