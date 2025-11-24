<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;

class AlumniBlockedMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        return $this->subject('Your Alumni Profile Has Been Blocked')
                    ->view('emails.alumni_blocked')
                    ->with('data', $this->data);
    }
}
