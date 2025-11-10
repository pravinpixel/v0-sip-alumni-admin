<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\TaskCategory;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class TaskTemplateExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Task List (Headings Only)
        $sheets[] = new TaskListSheet();

        // Sheet 2: Employees
        $sheets[] = new EmployeesSheet();

        // Sheet 3: Task Categories
        $sheets[] = new TaskCategoriesSheet();

        // Sheet 4: Priority
        $sheets[] = new PrioritySheet();

        return $sheets;
    }
}

