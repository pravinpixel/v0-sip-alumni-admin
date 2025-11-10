<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;



class TasksImport implements WithHeadingRow ,WithMultipleSheets 
{
    protected $expectedHeadings = [
        'task_list',
        'subject',
        'task_description',
        'priority',
        'assigned_from',
        'assigned_to',
        'followers',
        'additional_followers',
        'end_date',
    ];
    

    public function sheets(): array
    {
        return [
            // 'Employees' => new EmployeesImport(),
            'TaskType' => new TaskTypeImport(),
            'Priority' => new PriorityImport(),
            'TaskList' => new TaskListImport(),
        ];
    }

   

    // public function customValidationMessages()
    // {
    //     return [
    //         'task_list.required' => 'Task List Column is required.',
    //         'task_subject.required' => 'Task Subject Column is required.',
    //         'task_description.required' => 'Task Description Column  is required.',
    //         'priority.required' => 'Priority Column is required.',
    //         '*.assigned_from' => 'Assigned From Column is required.',
    //         '*.assigned_to' => 'Assigned To Column is required.',
    //         '*.followers' => 'Followers Column is required.',
    //         '*.additional_followers' => 'Additional Followers Column is required.',
    //         '*.end_date' =>  'End Column is required.'
    //     ];
    // }

    

    // public function onHeadingRow(HeadingRowImport $heading): void
    // {
    //     $headings = $heading->toArray();

    //     foreach ($this->expectedHeadings as $expectedHeading) {
    //         if (!in_array($expectedHeading, $headings, true)) {
    //             throw new \Exception("Column heading mismatch: Missing '$expectedHeading'.");
    //         }
    //     }
    // }
}

