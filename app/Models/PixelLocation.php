<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PixelLocation extends Model
{
    use HasFactory;
    protected $table = 'pixel_locations';
    protected $fillable = ['state', 'city', 'pincode', 'area'];
}