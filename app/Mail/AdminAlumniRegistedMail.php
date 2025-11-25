<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class AdminAlumniRegistedMail extends Mailable
{
    use Queueable, SerializesModels;
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
