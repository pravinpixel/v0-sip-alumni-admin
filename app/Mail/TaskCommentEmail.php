<?php

namespace App\Mail;

use App\Helpers\UtilsHelper;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskCommentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $doc_paths = [];

        $setting = Setting::where('name', 'signature')->first();
        $this->data['signature_logo'] = $setting ? $setting->value : null;

        $age_date_check = $this->data['task']->created_at;
        if ($this->data['is_recurrence'] == 1) {
            $age_date_check = $this->data['task']->deadline;
        }
        $age_date_check = Carbon::parse($age_date_check)->format('Y-m-d');
        $daysDifference = Carbon::now()->startOfDay()->diffInDays($age_date_check);
        if ($daysDifference == 0) {
            $daysChangeMessage = "Today";
        } else {
            $daysChangeMessage = $daysDifference . " days";
        }
        $this->data['due_date_string'] = $daysChangeMessage;


    }

    public function build()
    {
        $subject = '🔔 Task Master Notification Alert - ' . $this->data->task->task_no . '-' . $this->data->task->name;

        $data = ['task' => $this->data];

        $mailMessage = $this
            ->subject($subject)
            ->view('emails.task-comment', $data);

        $mailMessage->withSymfonyMessage(function ($message) {
            $customMessageId = $this->data->task->task_no . '@taskmaster.designonline.in';
            $message->getHeaders()->addIdHeader('Message-ID', $customMessageId);
            $message->getHeaders()->addTextHeader('In-Reply-To', $customMessageId);
            $message->getHeaders()->addTextHeader('References', $customMessageId);
        });


        if ($this->data['send_private'] == '0') {
            $cc = UtilsHelper::addTaskCC($this->data['task']);
            if ($cc) {
                $mailMessage->cc($cc);
            }
        }

        return $mailMessage;

    }


}
