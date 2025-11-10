<?php

namespace App\Imports;

use App\Models\Status;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\ValidationException;

class PriorityImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'error' => 'Priority Sheet is Empty'
            ]);
        }

        foreach ($rows as $row) {
            if(!$row['priority_id']) {
                $data = new Status();
                $data->name = $row['priority_name'];
                $data->type = 'priority';
                $data->save();
            }
        }
    }
}
