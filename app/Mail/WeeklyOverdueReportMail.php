<?php

namespace App\Mail;

use App\Exports\WeeklyOverdueExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Excel;

class WeeklyOverdueReportMail extends Mailable
{
    use Queueable, SerializesModels;


    public $tasks;


    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function build()
    {

        if (file_exists(storage_path('app/weekly_overdue_report.xlsx'))) {
            unlink(storage_path('app/weekly_overdue_report.xlsx'));
        }

        $export = new WeeklyOverdueExport($this->tasks['tasks']->toArray());
        $excel = app(Excel::class);
        $excel->store($export, 'weekly_overdue_report.xlsx', 'local');

        return $this->subject('Your Overdue Task - Weekly Report ')
            ->attach(storage_path('app/weekly_overdue_report.xlsx'))
            ->view('emails.weekly_overdue_report',['tasks' => $this->tasks]);

    }
}
