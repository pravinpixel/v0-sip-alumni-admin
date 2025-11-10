<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpcomingWeekOverdueReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tasks;


    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function build()
    {
        return $this->subject('Your upcoming week due Task - Weekly Report')
                    ->view('emails.upcoming_week_overdue',['tasks' => $this->tasks]);
    }
}
