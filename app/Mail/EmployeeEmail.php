<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $password;
    public $email;
    public $name;
    /**
     * Create a new message instance.
     */
    public function __construct($name,$email,$password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Account Information')
                    ->view('employee_email.mail_template')
                    ->with([
                        'name' => $this->name,
                        'email' => $this->email,
                        'password' => $this->password,
                    ]);
    }
}
