<?php

namespace App\Imports;

use App\Exceptions\CustomValidationException;
use App\Helpers\UtilsHelper;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskCreatedEmail;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TaskListImport implements ToCollection,WithHeadingRow,WithValidation
{
    // 
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'error' => 'TaskList Sheet is Empty'
            ]);
        }
        try {
            foreach ($rows as $index => $row) {

                $endDate = $row['end_date'];
                if (is_numeric($endDate)) {
                    $endDate = Date::excelToDateTimeObject($endDate)->format('Y-m-d');
                }

                $row['assigned_from'] = Employee::where('employee_id', $row['assigned_from'])->first()->id ?? null;
                $row['assigned_to'] = Employee::where('employee_id', $row['assigned_to'])->first()->id ?? null;
                $row['followers'] = implode(',', Employee::whereIn('employee_id', explode(',', $row['followers']))->pluck('id')->toArray());
                $is_recurrence = "0";

                $task = new Task;
                $task->name = $row['task_subject'];
                $task->description = $row['task_description'];
                $task->deadline = $endDate;
                $task->task_category_id = $this->getIdByName($row['task_list'],'task_category');
                $task->date = today()->toDateString();
                $task->priority_id = $this->getIdByName($row['priority'],'priority');
                $task->assigned_to = $row['assigned_to'];
                $task->assigned_by = $row['assigned_from'];
                $task->created_by = $row['assigned_from'];
                $task->followers = $row['followers'];
                $task->additional_followers = $row['additional_followers'];
                $task->status_id = 2;
                $task->is_recurrence = $is_recurrence;
                $task->recurrence = null;
                $task->task_no = UtilsHelper::getTaskMaxNo();
                $task->save();

                try {
                    Mail::to([
                        $task->assignedto->email,
                        $task->assignedby->email,
                    ])->send(new TaskCreatedEmail($task));
                } catch (\Exception $e) {
                    $task_mail = Task::find($task->id);
                    $task_mail->is_mail_failed = 1;
                    $task_mail->save();
                    // Log the exception but do not break the flow
                    Log::error('Task notification failed: ' . $e->getMessage());
                }
          }
        } catch (\Exception $e) {
            Log::error('Task creation failed: ' . $e->getMessage());
        }
    }

//     public function convertExcelDate($excelDate)
// {
//     if (is_numeric($excelDate)) {
//         $excelDateTime = $excelDate . ' 00:00:00';
//         // Convert Excel serial number to a Carbon date
//         $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelDate));
//     } else {
//         $excelDateTime = $excelDate . ' 00:00:00';
//         // Parse the string date into a Carbon instance
//         $date = Carbon::createFromFormat('d-m-Y H:i:s', $excelDateTime)->timezone('Asia/Kolkata')->format('Y-m-d');

    
//         dd($excelDateTime, $date);
//     }
//     dd($date);

//     // Format the date as yyyy-mm-dd
//     return $date->format('Y-m-d');
// }


    public function rules(): array
    {
        return [
            '*.task_list' => 'required|string|exists:task_categories,name|max:255',
            '*.task_subject' => 'required|string',
            '*.task_description' => 'required|string',
            '*.priority' => 'required|string',
            '*.assigned_from' => 'required|exists:employees,employee_id',
            '*.assigned_to' => 'required|exists:employees,employee_id',
            // '*.followers' => ['required', function ($attribute, $value, $fail) {
            //     $ids = explode(',', $value);
            //     foreach ($ids as $id) {
            //         if (!ctype_digit($id)) {
            //             $fail("The $attribute must be a comma-separated list of numbers.");
            //         } elseif (!DB::table('employees')->where('id', $id)->exists()) {
            //             $fail("The $attribute contains an ID that does not exist in the employees table.");
            //         }
            //     }
            // }],
            // '*.additional_followers' => ['required', 'string', function ($attribute, $value, $fail) {
            //     $emails = explode(',', $value);
            //     foreach ($emails as $email) {
            //         if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
            //             $fail("The $attribute must be a comma-separated list of valid email addresses.");
            //         }
            //     }
            // }],
            '*.end_date' => 'required',
        ];
    }

    public function headingRow(): int
    {
       return 1;
    }

    public function getIdByName($name,$type){
        $name = strtolower($name);
        if ($type == 'priority') {
            $status = Status::where('name', $name)
                ->where('type', 'priority')
                ->first();
        }
        if ($type == 'task_category') {
            $status = TaskCategory::where('name', $name)
                ->first();
        }
        return $status ? $status->id : null;
    }
}
