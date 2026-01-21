<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pincodes extends Model
{
    use HasFactory;
    protected $table = 'pincodes';
    protected $fillable = ['pincode', 'city_id'];

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id');
    }
}