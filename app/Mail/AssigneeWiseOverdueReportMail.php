<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssigneeWiseOverdueReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tasks;


    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function build()
    {
        return $this->subject('Assignee wise Overdue Task- Weekly Report')
                    ->view('emails.assigneewise_overdue_report',['tasks' => $this->tasks]);
    }
}
