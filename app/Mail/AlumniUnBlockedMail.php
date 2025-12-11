<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class AlumniUnBlockedMail extends Mailable
{
     use Queueable, SerializesModels;
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
