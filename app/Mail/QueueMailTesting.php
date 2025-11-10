<?php

namespace App\Mail;

use App\Helpers\UtilsHelper;
use App\Models\Employee;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Components\Calendar;
use Illuminate\Support\Facades\Log;

class QueueMailTesting extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
   public function __construct($data)
{
    // Ensure data is cast to stdClass to avoid data loss
    $this->data = is_array($data) ? (object) $data : $data;

    Log::channel('QueueMailTesting cron')->info($this->data);
    
    // $setting = Setting::where('name', 'signature')->first();
    // $this->data->signature_logo = $setting ? $setting->value : null;

    $daysDifference = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($this->data->deadline), false);
    if ($daysDifference == 0) {
        $daysChangeMessage = "Today";
    } elseif ($daysDifference == 1) {
        $daysChangeMessage = $daysDifference . " Day";
    } else {
        $daysChangeMessage = $daysDifference . " Days";
    }
    $this->data->due_date_string = $daysChangeMessage;
   
}

    public function build()
    {
        $subject = '🔔 Task Master Notification Alert - ' . $this->data['task_no'] . '-' . $this->data['name'];
        $auth_user = Employee::where('id', $this->data['created_by'])->first();
        $setting = Setting::where('name', 'signature')->first();
        $signatureLogo = $setting ? $setting->value : null;
        $dataObject = (object) $this->data;
        $dataObject->signature_logo = $signatureLogo;
        $dataObject->auth_user = $auth_user;
        $mailMessage = $this
            ->subject($subject)
            ->view('emails.task-created', ['task' => $dataObject]);

        $mailMessage->withSymfonyMessage(function ($message) {
            $customMessageId = $this->data['task_no'].'@taskmaster.designonline.in';
            $message->getHeaders()->addIdHeader('Message-ID', $customMessageId);
            $message->getHeaders()->addTextHeader('In-Reply-To', $customMessageId);
            $message->getHeaders()->addTextHeader('References', $customMessageId);
        });

        $cc = UtilsHelper::addTaskCC($this->data);
        if ($cc) {
            $mailMessage->cc($cc);
        }

        $icsFilePath = $this->generateICS($this->data);
        if ($icsFilePath) {
            $mailMessage->attach($icsFilePath, [
                'as' => 'task.ics',
                'mime' => 'text/calendar',
            ]);
        }

        return $mailMessage;
    }


    private function generateICS($task)
    {
        $organizer =Employee::where('id',$task->created_by)->with('designation')->first();
        $event = Event::create($task['name'])
            ->startsAt(Carbon::parse($task['date']))
            ->endsAt(Carbon::parse($task['deadline']))
            ->description($task['description'])
            ->organizer($organizer->email);

        $calendar = Calendar::create($task['name'])
            ->event($event);

        $icalContent = $calendar->get();

        $fileName = $task['task_no'] . '.ics';
        $filePath = storage_path('app/public/ics/' . $fileName);
        file_put_contents($filePath, $icalContent);

        return $filePath;
    }

}
