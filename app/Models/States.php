<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    use HasFactory;

    protected $table = 'states';

    protected $fillable = ['name', 'country_id', 'status'];

    public $timestamps = false;

    public function city()
    {
        return $this->hasMany(Cities::class, 'state_id');
    }
}