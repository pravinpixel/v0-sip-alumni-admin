<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterLocations extends Model
{
    use HasFactory;
    protected $table = 'center_locations';
    protected $fillable = ['name', 'pincode_id', 'is_custom'];
    public function pincode()
    {
        return $this->belongsTo(Pincodes::class, 'pincode_id');
    }
}