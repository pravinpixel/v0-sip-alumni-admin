<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers\UtilsHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;


class Alumnis extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'alumnis';
    protected $fillable = [
        'full_name',
        'year_of_completion',
        'city_id',
        'email',
        'mobile_number',
        'occupation_id',
        'status'
    ];

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id');
    }

}
