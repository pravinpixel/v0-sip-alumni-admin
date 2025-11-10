<?php

namespace App\Exports;

use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PrioritySheet implements FromCollection ,WithHeadings,WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Status::select('id','name')
        ->where('type', 'priority')
        ->whereNull('deleted_at')
        ->get();
    }

    public function headings(): array
    {
        // Define the headings for the Employees sheet
        return ['Priority ID', 'Priority Name'];
    }

    public function title(): string
    {
        return 'Priority';
    }
}
