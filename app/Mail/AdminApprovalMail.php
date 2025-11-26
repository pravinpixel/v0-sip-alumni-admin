<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class AdminApprovalMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;


    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        return $this->subject('New Forum Post Pending Approval')
                    ->view('emails.admin_approval')
                    ->with('data', $this->data);
    }
}
