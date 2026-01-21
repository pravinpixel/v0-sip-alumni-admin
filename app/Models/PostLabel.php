<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLabel extends Model
{
    use HasFactory;
    protected $table = 'post_labels';
    protected $fillable = [
        'post_id',
        'label_id',
    ];

    public function label()
    {
        return $this->belongsTo(Labels::class, 'label_id');
    }

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }
}