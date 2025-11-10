<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDueDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id','old_date','new_date'
    ];
}
