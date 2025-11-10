<?php

namespace App\Imports;

use App\Models\TaskCategory;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TaskTypeImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {

        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'error' => 'TaskType Sheet is Empty'
            ]);
        }
        
        foreach ($rows as $row) {
           if(!$row['task_type_id']){
                    $data = new TaskCategory();
                    $data->name = $row['task_type_name'];
                    $data->status = 1;
                    $data->save();
            }
        }
    }
}

