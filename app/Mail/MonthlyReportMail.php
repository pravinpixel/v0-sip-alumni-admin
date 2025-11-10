<?php

namespace App\Mail;

use App\Exports\MonthlyReportExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Excel;

class MonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;

    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    public function build()
    {

        if (file_exists(storage_path('app/MonthlyReport.xlsx'))) {
            unlink(storage_path('app/MonthlyReport.xlsx'));
        }
        $export = new MonthlyReportExport([$this->employee]);
        $excel = app(Excel::class);
        $excel->store($export, 'MonthlyReport.xlsx', 'local');

        return $this->subject('Your Monthly Report ')
            ->attach(storage_path('app/MonthlyReport.xlsx'))
            ->view('emails.monthly_report',['tasks' => $this->employee]);
    }
}
