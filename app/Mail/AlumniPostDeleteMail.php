<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class AlumniPostDeleteMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;


    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        return $this->subject('Your Forum Post Has Been Moved to Archive')
                    ->view('emails.alumni_post_deleted')
                    ->with('data', $this->data);
    }
}
