<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeCreateNotification extends Notification
{
    use Queueable;
    protected $employee;
    /**
     * Create a new notification instance.
     */
    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        
        $subject = 'Welcome on board to ✅ | Task Master ';
        $setting = Setting::where('name', 'signature')->first();
        $this->employee['signature_logo'] = $setting ? $setting->value : null;
        $mailMessage = (new MailMessage)
                        ->subject($subject)
                        ->view('emails.employee-create', [
                               'employee' => $this->employee,
                        ]);
        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
