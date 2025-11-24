<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;

class AlumniUnBlockedMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        return $this->subject('Your Alumni Account Has Been Unblocked')
                    ->view('emails.alumni_unblocked')
                    ->with('data', $this->data);
    }
}
