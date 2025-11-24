<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;

class AdminAlumniRegistedMail extends Mailable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        return $this->subject('New Alumni Registered on Alumni Portal')
                    ->view('emails.admin_alumni_registed')
                    ->with('data', $this->data);
    }
}
