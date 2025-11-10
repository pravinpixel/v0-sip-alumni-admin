<?php

namespace App\Helpers;

use App\Mail\TaskCreatedEmail;
use App\Models\Employee;
use App\Models\Mention;
use App\Models\Notification;
use App\Models\Task;
use RRule\RRule;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Recurr\Exception\InvalidRRule;
use Recurr\Exception\InvalidWeekday;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;

class UtilsHelper
{
    public static function storagePath($path = '')
    {

        return url('/') . Storage::url($path);
    }

    public static function getStoragePath()
    {
        return self::storagePath();
    }

    /**
     * @throws \Exception
     */
    public static function timezoneDateTime($date = null)
    {
        $timezone = 'Asia/Kolkata';
        $startDate = $date ? $date : date('Y-m-d H:i:s');
        return new DateTime($startDate, new \DateTimeZone('UTC'));
        return $date->setTimezone(new \DateTimeZone($timezone));

    }


    public static function getTaskMaxNo()
    {

        $model = 'tasks';
        $prefix = 'TM';
        $prefix_length = strlen($prefix) + 1;

        $max_code = DB::select("SELECT if(max(CAST(SUBSTRING(task_no,$prefix_length) as UNSIGNED)) is null , 0,max(CAST(SUBSTRING(task_no, $prefix_length) as UNSIGNED))) as task_no FROM $model WHERE task_no LIKE '$prefix%'");

        if ($max_code) {
            $max_code = $max_code[0]->task_no + 1;
            if (strlen($max_code) < 5 - 1)
                $max_code = str_pad($max_code, 5, '0', STR_PAD_LEFT);
        } else
            $max_code = '00001';
        return $prefix . $max_code;
    }


    /**
     * @throws InvalidRRule
     * @throws InvalidWeekday
     * @throws \Exception
     */
    public static function isDateInRRule($rrule_string, $date_arr, $first_date = false)
    {
        $rrule_string = preg_replace('/;TZID=.*?:/', ':', $rrule_string);
        $rrule_string = preg_replace('/DTSTART:(\d{8})T(\d{6})/', 'DTSTART:$1', $rrule_string);

        if (preg_match('/DTSTART:(\d{8})/', $rrule_string, $matches)) {
            $date = DateTime::createFromFormat('Ymd', $matches[1]);
            
            $date->modify('+1 day');
            
            $rrule_string = preg_replace('/DTSTART:\d{8}/', 'DTSTART:' . $date->format('Ymd') , $rrule_string);
        }

        $rule = new Rule($rrule_string);
        $transformer = new ArrayTransformer();
        $desiredTimezone = 'Asia/Kolkata';
        $tmp = [];

        if ($first_date) {
            $occurrences = $transformer->transform($rule);
            if($first_date === 'after') {
                $occurrences = $occurrences->startsAfter(Carbon::createFromFormat('Y-m-d', $date_arr)->startOfDay());
            }
            $occurrences = $occurrences->toArray();
            $occurrences = array_slice($occurrences, 0, 1);

        } else {
            if (is_array($date_arr)) {
                $startDate = $date_arr[0];
                $endDate = $date_arr[1];
            } else {
                $startDate = $date_arr;
                $endDate = $date_arr;
            }

            $from = $startDate->copy()->startOfDay();
            $to = $endDate->copy()->endOfDay();

            $occurrences = $transformer->transform($rule)
                ->startsBetween($from, $to)
                ->toArray();
        }

        foreach ($occurrences as $occurrence) {
            $tmp[] = [
                'timZoneStartUTC' => $occurrence->getStart()->format('Y-m-d H:i:s'),
                'start' => $occurrence->getStart()->setTimezone(new \DateTimeZone($desiredTimezone))->format('Y-m-d H:i:s'),
                'end' => $occurrence->getEnd()->format('Y-m-d H:i:s')
            ];
        }
        return [
            'occurrences' => $tmp,
            'is_date_in_recurrence' => count($occurrences) > 0
        ];
    }


    public static function recurrenceTask($parentTasks, $first_date = false)
    {

        $path = UtilsHelper::getStoragePath();
        $addDays = config('app.task_create_days_count');
        $startDate = Carbon::now('Asia/Kolkata');
        $endDate = Carbon::now('Asia/Kolkata')->addDays($addDays-1);
        $dateArr = [$startDate, $endDate];
        $taskStartDate = Carbon::now('Asia/Kolkata')->toDateTimeString();

        Log::channel('cron')->info('Parent Task Count: ' . count($parentTasks) . ' Ids: ' . $parentTasks->pluck('id') ?? '');
        Log::channel('cron')->info('Date Array: ' . json_encode($dateArr) . ' Task Start Date: ' . $taskStartDate);
        Log::channel('cron')->info('Special Task' . $first_date);

        $createdTaskCount = 0;



        foreach ($parentTasks as $parentTask) {
            try {
                $rrule_string = $parentTask->recurrence;
                if (!$rrule_string) {
                    continue;
                }

                $is_date_in_recurrence = UtilsHelper::isDateInRRule($rrule_string, $dateArr, $first_date);

                $childTaskIds = [];

                $auth_user = Employee::where('id', $parentTask->assigned_by)->first();
                if (!$auth_user)
                    continue;
                if (count($is_date_in_recurrence['occurrences']) > 0) {
                    foreach ($is_date_in_recurrence['occurrences'] as $occurrence) {

                        $dead_line = Carbon::parse($occurrence['start'])->setTimezone('Asia/Kolkata')->format('Y-m-d');
                        $task = Task::where('parent_id', $parentTask->id)
                            ->where('deadline', $dead_line)->whereNotIn('status_id', [1,3,8,9])->first();
                        if ($task) {
                            Log::channel('cron')->info('Task Already Created: ' . $task->id . ' Deadline: ' . $dead_line);
                            continue;
                        }

                        $is_mail_send = 1;
                        if ($first_date) {
                            $taskStartDate = Carbon::parse($occurrence['start'])->setTimezone('Asia/Kolkata')->subDays($addDays)->format('Y-m-d');
                            $is_mail_send = 0;
                        }

                        $childTask = $parentTask->replicate();

                        $childTask->task_no = self::getTaskMaxNo();
                        $childTask->deadline = $dead_line;
                        $childTask->date = $taskStartDate;
                        $childTask->status_id = 2;
                        $childTask->status_date = null;
                        $childTask->task_rating = null;
                        $childTask->rating_remark = null;
                        $childTask->mark_as_completed = null;
                        $childTask->parent_id = $parentTask->id;
                        $childTask->ialert_id = $parentTask->ialert_id;
                        $childTask->is_mail_send = $is_mail_send;
                        $childTask->save();

                        $mentions = Mention::where('task_id', $parentTask->id)->pluck('mentioned_id')->toArray();

                        if (!empty($mentions)) {
                            $type = $childTask->ialert_id ? 'invoice' : 'task';
                            foreach ($mentions as $data) {
                                $mention = new Mention;
                                $mention->mentioned_id = $data;
                                $mention->mentioned_by = $data->mentioned_by;
                                $mention->type = $type;
                                $mention->task_id = $childTask->id;
                                $mention->save();

                                $notification = new Notification;
                                $notification->module = 'task';
                                $notification->action = 'task mentioned';
                                $notification->message = 'mentioned you in a task';
                                $notification->task_id = $childTask->id;
                                $notification->to_id = $data;
                                $notification->created_by = $task->created_by;
                                $notification->save();
                            }
                        }

                        $parentTask->documents->each(function ($document) use ($childTask, $path) {
                            $file = str_replace($path, '', $document['document']);
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                            $fileName = "admin/document_" . uniqid() . "_" . time() . "." . $ext;
                            Storage::disk('public')->copy($file, $fileName);

                            $childTask->documents()->create([
                                'document' => $fileName,
                                'name' => $document['name'],
                            ]);
                        });

                        $createdTaskCount++;
                        $childTaskIds[] = $childTask->id;

                        $childTask['auth_user'] = $auth_user;


                        if($childTask->assigned_to == $childTask->assigned_by){

                            try {
                                if (!$first_date) {
                                    Mail::to([
                                        $childTask->assignedto->email
                                    ])->send(new TaskCreatedEmail($childTask));
                                }
                            } catch (\Exception $e) {
                                $task_mail = Task::find($childTask->id);
                                $task_mail->is_mail_failed = 1;
                                $task_mail->save();
                                // Log the exception but do not break the flow
                                Log::channel('cron')->info('Task notification failed: ' . $e->getMessage());
                            }
                            
                        }else{

                            try {
                                if (!$first_date) {
                                    Mail::to([
                                        $childTask->assignedto->email,
                                        $childTask->assignedby->email,
                                    ])->send(new TaskCreatedEmail($childTask));
                                }
                            } catch (\Exception $e) {
                                $task_mail = Task::find($childTask->id);
                                $task_mail->is_mail_failed = 1;
                                $task_mail->save();
                                // Log the exception but do not break the flow
                                Log::channel('cron')->info('Task notification failed: ' . $e->getMessage());
                            }

                        }

                        $notification = new Notification();
                        $notification->module = 'task';
                        $notification->action = 'task created';
                        $notification->message = 'rec task created';
                        $notification->task_id = $childTask->id;
                        $notification->to_id = $childTask->assigned_to;
                        $notification->created_by = $childTask->assigned_by;
                        $notification->save();
                    }
                }


                Log::channel('cron')->info('Parent Task Id: ' . $parentTask->id . ' Child Task Ids: ' . implode(',', $childTaskIds));

            } catch (\Exception $e) {
                Log::channel('cron')->error('Error: ' . $e->getMessage() . ' : Parent Task Id: ' . $parentTask->id);
            }
        }

        Log::channel('cron')->info('Created Task Count: ' . $createdTaskCount);

        return $createdTaskCount;

    }


    public static function addTaskCC($task)
    {
        $followers_email = [];
        $additional_followers = [];
        if ($task->followers) {
            $followers_ids = explode(',', $task->followers);
            $followers_email = Employee::whereIn('id', $followers_ids)->pluck('email')->toArray();
        }
        if ($task->additional_followers) {
            $additional_followers = explode(',', $task->additional_followers);
        }
        
        $assigner_email = [$task->assignedby->email];
        if($task->is_self_assign == 1){
            $cc = array_merge($followers_email, $additional_followers);
        }else{
            $cc = array_merge($followers_email, $additional_followers,$assigner_email);
        }
        return array_filter($cc, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });


    }

    public static function addIalertCC($invoice)
    {
        $cc = [];
        if ($invoice->art_email_id) {
            $cc[] = $invoice->art_email_id;
        }
        if ($invoice->bde_email_id) {
            $cc[] = $invoice->bde_email_id;
        }
        if ($invoice->manager_email_id) {
            $cc[] = $invoice->manager_email_id;
        }
        if (strtolower($invoice->portal_invoice) === 'yes') {
            $cc[] = "indiasupport@ushafire.in";
        }
        // if ($invoice->art_head_email_id) {
        //     $art_head_emails = explode(',', $invoice->art_head_email_id);
        //     $cc = array_merge($cc, $art_head_emails);
        // }
        if ($invoice->additional_emails) {
            $additional_emails = explode(',', $invoice->additional_emails);
            $cc = array_merge($cc, $additional_emails);
        }
        
        return array_filter($cc, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }

    public static function getOverdueTasks()
    {

        $tasks = Task::whereNotIn('status_id', [1,3,8,9])
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })->orWhere(function ($query) {
                    $query->whereNotNull('parent_id')
                        ->where('is_recurrence', 1);
                });
            })
            ->where(function ($query) {
                $currentDate = now()->toDateString();

                $query->whereDate('deadline', '=', $currentDate) // Tasks due today
                ->orWhereDate('deadline', '<', $currentDate); // Tasks overdue
            })
            ->with('documents')
            ->get();

        return $tasks;
    }

    //get recurrence details
    public static function parseEventDetails($eventString)
    {
        $parts = explode('RRULE:', $eventString);

        // Parse the DTSTART value
        $dtstart = str_replace('DTSTART:', '', $parts[0]);

        // Parse the RRULE value
        $rrule = isset($parts[1]) ? trim($parts[1]) : ''; // Ensure to trim and check if it exists

        $result = static::displayRecurrenceDetails($dtstart, $rrule);

        $output = static::showEventDetails($result);
        // dd($output);

        return  $output;
    }

    public static function displayRecurrenceDetails($dtstart, $rruleString)
    {
        // Parse the start date
        $startDate = Carbon::parse($dtstart);

        // Create an RRule instance
        $rrule = new RRule($rruleString);

        // Get recurrence rule details
        $frequency = $rrule->getRule()['FREQ'] ?? null;
        $interval = $rrule->getRule()['INTERVAL'] ?? 1; // Default to 1 if not set
        $byDays = $rrule->getRule()['BYDAY'] ?? []; // Ensure it defaults to an empty array
        $count = $rrule->getRule()['COUNT'] ?? 0;
        $untilDate = $rrule->getRule()['UNTIL'] ?? null; // Check for UNTIL rule

        // Initialize recurrence details
        $recurrenceDetails = "This event starts on " . $startDate->format('l, F j, Y g:i A') . ". ";

        // Initialize the last occurrence date
        $lastOccurrenceDate = null;

        // Determine recurrence frequency
        switch ($frequency) {
            case 'DAILY':
                $lastOccurrenceDate = $startDate->copy()->addDays(($count - 1) * $interval);
                $recurrenceDetails .= "It recurs every $interval day(s). ";
                $recurrenceType = 'daily';
                break;

            case 'WEEKLY':
                if (is_string($byDays)) {
                    $byDays = explode(',', $byDays);
                }
                $days = implode(', ', array_map(function ($dayCode) {
                    return static::getDayName($dayCode);
                }, $byDays));
                $lastOccurrenceDate = static::calculateWeeklyEndDate($startDate, $interval, $count, $byDays);
                $recurrenceDetails .= "It recurs every $interval week(s) on $days. ";
                $recurrenceType = 'weekly';
                break;

            case 'MONTHLY':
                $lastOccurrenceDate = $startDate->copy()->addMonths(($count - 1) * $interval);
                $recurrenceDetails .= "It recurs every $interval month(s). ";
                $recurrenceType = 'monthly';
                break;

            case 'YEARLY':
                $lastOccurrenceDate = $startDate->copy()->addYears(($count - 1) * $interval);
                $recurrenceDetails .= "It recurs every $interval year(s). ";
                $recurrenceType = 'yearly';
                break;

            default:
                $recurrenceDetails .= "Unknown recurrence frequency. ";
                $recurrenceType = 'unknown';
                break;
        }

        // Adjust for UNTIL date if it exists
        if ($untilDate) {
            $untilDate = Carbon::parse($untilDate);
            if ($lastOccurrenceDate && $lastOccurrenceDate->greaterThan($untilDate)) {
                $lastOccurrenceDate = $untilDate; // Cap at UNTIL date
            }
        }

        // Handle count
        if ($count > 0) {
            $recurrenceDetails .= "The event will occur $count time(s). ";
        }

        // Add the end date to the details
        if ($lastOccurrenceDate) {
            $recurrenceDetails .= "The last occurrence is on " . $lastOccurrenceDate->format('l, F j, Y g:i A') . ".";
        }

        // Return both recurrence details and the recurrence type
        return [
            'details' => $recurrenceDetails,
            'recurrenceType' => $recurrenceType
        ];
    }


    public static function calculateWeeklyEndDate($startDate, $interval, $count, $byDays)
    {
        $lastOccurrenceDate = null;

        // Generate occurrences
        for ($i = 0; $i < $count; $i++) {
            // Get the next occurrence based on the specified days
            $nextDate = $startDate->copy()->addWeeks($i * $interval);
            foreach ($byDays as $day) {
                $dayNumber = array_search($day, ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU']);
                $nextDate->next($dayNumber);
            }
            $lastOccurrenceDate = $nextDate;
        }

        return $lastOccurrenceDate;
    }


    public static function getDayName($dayCode)
    {
        $daysMap = [
            'MO' => 'Monday',
            'TU' => 'Tuesday',
            'WE' => 'Wednesday',
            'TH' => 'Thursday',
            'FR' => 'Friday',
            'SA' => 'Saturday',
            'SU' => 'Sunday',
        ];

        return $daysMap[$dayCode] ?? 'Unknown day';
    }

    public static function showEventDetails($eventData)
    {
        // Extract details and recurrenceType from the array passed in $eventData
        $eventDescription = $eventData['details'];
        $recurrenceType = $eventData['recurrenceType'];
    
        // Use preg_match to extract the details from the event description
        preg_match("/starts on (.+?)\./", $eventDescription, $startDateMatches);
        preg_match("/last occurrence is on (.+?)\./", $eventDescription, $endDateMatches);
        preg_match("/recurs every \d week\(s\) on (.+?)\./", $eventDescription, $daysMatches);
    
        // Extract matched details
        $startDate = DateTime::createFromFormat('l, F j, Y g:i A', $startDateMatches[1] ?? '');
        $startDate->modify('+1 day');
        $startDate = $startDate->format('l, F j, Y g:i A');
        $endDate = $endDateMatches[1] ?? null;
        $days = explode(', ', $daysMatches[1] ?? '');
    
        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'days' => $days,
            'recurrenceType' => $recurrenceType
        ];
    }

}
