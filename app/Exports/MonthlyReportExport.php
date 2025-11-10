<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class MonthlyReportExport implements FromArray, WithHeadings
{
    protected $reportData;

    public function __construct(array $reportData)
    {
        foreach ($reportData as &$task) {
            unset($task['setting'], $task['auth_user']);
        }
        $this->reportData = $reportData;
    }

    public function array(): array
    {
        return $this->reportData;
    }

    public function headings(): array
    {
        return [
            ['Branch', 'User Name', 'Total Task Created', 'Total Open Task', 'Total Closed Task', 'Average Star Rating'],
        ];
    }

    public function map($task): array
    {
        return [
           $task['branch'],
           $task['assignedto'],
           $task['total_assigned_task'],
           $task['total_pending_task'],
           $task['total_completed_task'],
           $task['rating'],
        ];
    }
}

