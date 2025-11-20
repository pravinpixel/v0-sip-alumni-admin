<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostPinned extends Model
{
    use HasFactory;
    protected $table = 'post_pinned';
    protected $fillable = ['alumni_id', 'post_id'];

    public function alumni()
    {
        return $this->belongsTo(Alumnis::class, 'alumni_id');
    }

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }
}