<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class AlumniAcceptRequestMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;


    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        return $this->subject('Connection Request Accepted on Alumni Portal')
                    ->view('emails.alumni_accept_request')
                    ->with('data', $this->data);
    }
}
