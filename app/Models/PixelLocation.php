<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PixelLocation extends Model
{
    use HasFactory;
    protected $table = 'franchisee';
    protected $fillable = ['state', 'city', 'zip', 'prefered_center_location'];
}