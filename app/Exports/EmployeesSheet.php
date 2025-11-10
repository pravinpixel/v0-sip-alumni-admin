<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeesSheet implements FromCollection, WithHeadings,WithTitle
{
    public function collection()
    {
        // Fetch Employee ID and Name from the Employee table
        return Employee::select('employee_id', DB::raw("CONCAT(first_name, ' ', last_name) as employee_name"))
            ->where('status', '1')
            ->whereNull('deleted_at')
            ->get();
    }

    public function headings(): array
    {
        // Define the headings for the Employees sheet
        return ['Employee ID', 'Employee Name'];
    }

    public function title(): string
    {
        return 'Employees';
    }
}
