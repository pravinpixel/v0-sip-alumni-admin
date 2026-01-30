<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class AlumniOtpMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;


    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        return $this->subject('Your OTP Code for SIP Abacus Alumni Portal Verification')
                    ->view('emails.alumni_otp')
                    ->with('data', $this->data);
    }
}
