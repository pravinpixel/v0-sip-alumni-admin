<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class TaskListSheet implements FromArray, WithHeadings,WithTitle
{
    public function array(): array
    {
        // Define some dummy data for the TaskList sheet
        return [
            ['HR Team', 'New Task', 'Task Description', 'High', 'EMP001', 'EMP001', 'EMP001,EMP001,EMP001', 'Michael@gmailcom,john@gmailcom', '2024-12-31'],
            // Add more rows as needed
        ];
    }

    public function headings(): array
    {
        // Define the headings for the TaskList sheet
        return [
            'Task List', 
            'Task Subject', 
            'Task Description', 
            'Priority', 
            'Assigned From', 
            'Assigned To', 
            'Followers', 
            'Additional Followers', 
            'End Date'
        ];
    }

    public function title(): string
    {
        return 'TaskList';
    }
}
