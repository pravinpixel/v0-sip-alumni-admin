<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MobileOtp extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'mobile_otp';
    protected $fillable = ['mobile_number', 'otp', 'is_verified', 'expires_at'];
}