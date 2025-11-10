<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskComment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function documents() {
        return $this->hasMany(TaskCommentDocument::class, 'comment_id', 'id')->select('id', 'comment_id', 'document','name');
    }

    public function from() {
        return $this->hasOne(Employee::class, 'id', 'from_id')->select('id', 'first_name', 'last_name','profile_image')->selectRaw("CONCAT(first_name, ' ', last_name) as name");
    }

    public function to() {
        return $this->hasOne(Employee::class, 'id', 'to_id')->select('id', 'first_name', 'last_name','profile_image')->selectRaw("CONCAT(first_name, ' ', last_name) as name");
    }
}
