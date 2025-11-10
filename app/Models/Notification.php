<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public function setUpdatedAt($value)
    {
        // Do nothing ignore updated_at field for notification
    }
    public function assignby()
    {
        return $this->hasOne(Employee::class, 'id' , 'created_by')
            ->select('id', 'first_name', 'last_name', 'email','profile_image','employee_id')
            ->selectRaw("CONCAT(first_name, ' ', last_name) as name");
           
    }

    public function mentionedTask()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function mentionedInvoice()
    {
        return $this->belongsTo(Iallert::class, 'ialert_id', 'id');
    }
}
