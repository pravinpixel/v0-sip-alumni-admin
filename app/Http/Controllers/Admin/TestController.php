<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UtilsHelper;
use App\Http\Controllers\Controller;
use App\Mail\IAlertURBillingAccountInvoice;
use App\Models\Task;
use App\Notifications\TaskCreatedNotification;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Recurr\Exception\InvalidRRule;
use Recurr\Exception\InvalidWeekday;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Components\Calendar;

use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\RecurrenceCollection;

class TestController extends Controller
{

    public function mail()
    {


//        try {
//            Mail::mailer('ialert_smtp')->raw('This is a test email body content', function ($message) {
//                $message->from('invoicefollowup@ushafire.in', 'Sender Name')->to('test@gmail.com')
//                    ->subject('Test Email');
//            });
//        } catch (\Exception $e) {
//            dd($e->getMessage());
//        }
//
//        return "test email sent successfully";
//        $data = [
//            'name' => 'John Doe',
//            'email' => '',
//            'task_id' => 'TM/XXX',
//            //'cc' => 'cc1@gmail.com, cc2@gmail.com',
//        ];

        // notification mail example for all user start
        // Notification::route('mail', 'test@example.com')->notify(new TaskCreatedNotification($data));

        // notification mail example for login user
      //  $user = auth()->user();
        //$user->notify(new TaskCreatedNotification($data));

        //view template
        //return (new TaskCreatedNotification($data))->toMail($user);

        // ical create
        //        $event = Event::create('Event Name')
        //            ->startsAt(new DateTime('2023-08-01 20:00:00'))
        //            ->endsAt(new DateTime('2023-08-01 21:00:00'))
        //            ->address('123 Main St, City, Country')
        //            ->description('Event Description')
        //            ->organizer('organizer@example.com');
        //        $calendar = Calendar::create('My Calendar')->event($event);
        //        $icalContent = $calendar->get();
        //        file_put_contents('calendar.ics', $icalContent);


        return 'test email sent successfully';
    }

    public function recurrence(Request $request)
    {

        $rrule_string = $request->input('rrule');
        // dd($rrule_string);
        $addDays = config('app.task_create_days_count');
        $desiredTimezone = 'Asia/Kolkata';



        $startDate = Carbon::now($desiredTimezone);
        // dd($startDate);
        $endDate = Carbon::now($desiredTimezone)->addDays($addDays);
        // dd($endDate);
        $dateArr = [$startDate, $endDate];
        // dd($dateArr);

        $rrule_string = preg_replace('/;TZID=.*?:/', ':', $rrule_string);
        // dd($rrule_string);
        $rrule_string = preg_replace('/DTSTART:(\d{8})T(\d{6})/', 'DTSTART:$1', $rrule_string);
        // dd($rrule_string);
        if (preg_match('/DTSTART:(\d{8})/', $rrule_string, $matches)) {
            // dd(1);
            $date = DateTime::createFromFormat('Ymd', $matches[1]);

            $date->modify('+1 day');

            $rrule_string = preg_replace('/DTSTART:\d{8}/', 'DTSTART:' . $date->format('Ymd'), $rrule_string);
        }
        // dd($rrule_string);


        $result['test'] = UtilsHelper::isDateInRRule($rrule_string, $dateArr);


        //   $rrule_string = preg_replace('/;TZID=.*?:/', ':', $rrule_string);

        $rule = new Rule($rrule_string);
        $transformer = new ArrayTransformer();

        $occurrences = $transformer->transform($rule)
            ->toArray();


        foreach ($occurrences as $occurrence) {

            $result['new'][] = [
                'timZoneStart' => $occurrence->getStart()->format('Y-m-d H:i:s'),
                'start' => $occurrence->getStart()->setTimezone(new \DateTimeZone($desiredTimezone))->format('Y-m-d H:i:s'),
                'end' => $occurrence->getEnd()->format('Y-m-d H:i:s')
            ];
        }



        return $result;




        $parentTasks = Task::where(['status_id' => 2])
            ->whereNotNull('recurrence')
            ->whereNull('parent_id')->get();

        $createdTaskCount = UtilsHelper::recurrenceTask($parentTasks);

        return ['status' => 'success', 'message' => 'Task Created Successfully', 'created_task_count' => $createdTaskCount];


    }
}
