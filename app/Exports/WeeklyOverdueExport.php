<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WeeklyOverdueExport implements FromArray, WithHeadings, WithMapping
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }


    public function headings(): array
    {
        return [
            'Task Assigned By',
            'Task ID',
            'Task Subject',
            'Task Due Date',
        ];
    }

    public function map($d): array
    {
        $assignedBy = \App\Models\Employee::where('id', $d['assigned_by'])->first();
        if ($assignedBy) {
            $d['assigned_by'] = $assignedBy->first_name . ' ' . $assignedBy->last_name;
        } else {
            $d['assigned_by'] = 'N/A';
        }
        return [
            $d['assigned_by'],
            $d['task_no'],
            $d['name'],
            $d['deadline'],
        ];
    }
}
