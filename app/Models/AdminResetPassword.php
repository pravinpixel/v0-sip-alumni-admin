<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminResetPassword extends Model
{
    use HasFactory;
    protected $table = 'admin_password_reset';
    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];
    public $timestamps = false;
}
