<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $token;
    public $email;
    public $name;
    /**
     * Create a new message instance.
     */
    public function __construct($token,$email,$name)
    {
        $this->token = $token;
        $this->email = $email;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Sample Email')
                    ->view('admin_email.mail_template')
                    ->with([
                        'token' => $this->token,
                        'email' => $this->email,
                        'name' => $this->name
                    ]);
    }
}
