<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class AlumniWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        return $this->subject('Welcome to SIP Abacus Alumni Network')
                    ->view('emails.alumni_welcome')
                    ->with('data', $this->data);
    }
}
