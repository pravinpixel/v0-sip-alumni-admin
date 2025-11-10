<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    use HasFactory;

    public function mentionedEmployee()
    {
        return $this->belongsTo(Employee::class, 'mentioned_id', 'id');
    }

    public function mentionedBy()
    {
        return $this->belongsTo(Employee::class, 'mentioned_by', 'id');
    }

    public function mentionedTask()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id')->where(function ($query) {
            $query->whereNotIn('status_id', [1, 3, 8, 9])
                ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)');
        });
    }
}
