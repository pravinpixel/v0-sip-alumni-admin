<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailOtp extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'email_otp';
    protected $fillable = ['email', 'otp', 'is_verified', 'expires_at'];
}