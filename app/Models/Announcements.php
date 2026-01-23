<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcements extends Model
{
    use HasFactory;
    protected $table = 'announcements';
    protected $fillable = ['title', 'description', 'expiry_date', 'status'];

    protected $casts = [
        'expiry_date' => 'datetime::Y-m-d H:i:s',
    ];

}