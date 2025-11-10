<?php

namespace App\Exports;

use App\Models\TaskCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TaskCategoriesSheet implements FromCollection, WithHeadings,WithTitle
{
    public function collection()
    {
        // Fetch Task Type ID and Name from the Task Category table
        return TaskCategory::where('status', '1')->select('id', 'name')->get();
    }

    public function headings(): array
    {
        // Define the headings for the Task Categories sheet
        return ['Task Type ID', 'Task Type Name'];
    }

    public function title(): string
    {
        return 'TaskType';
    }
}

