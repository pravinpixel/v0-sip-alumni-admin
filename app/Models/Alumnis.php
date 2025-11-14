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
use Illuminate\Support\Facades\Storage;

class Alumnis extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'alumnis';
    protected $appends = ['image_url'];
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

    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id');
    }

    public function sendconnections()
    {
        return $this->hasMany(AlumniConnections::class, 'sender_id');
    }

    public function receiveconnections()
    {
        return $this->hasMany(AlumniConnections::class, 'receiver_id');
    }

    public function getImageUrlAttribute()
    {
    if (!empty($this->image)) {
        // Image path based on your directory
        $path =  $this->image;

        // Generate a full URL (works with storage:link)
        return asset(Storage::url($path));
    }

    // Return default placeholder if no image exists
    return asset('images/avatar/blank.png');
}

}
