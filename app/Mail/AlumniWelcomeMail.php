<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;

class AlumniWelcomeMail extends Mailable
{
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
