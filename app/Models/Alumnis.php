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
        'status',
        'is_request_ribbon',
        'is_directory_ribbon',
        'remarks',
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

    public function pinned()
    {
        return $this->hasMany(PostPinned::class, 'alumni_id');
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'alumni_id');
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->image) && Storage::disk('public')->exists($this->image)) {
            return url('storage/' . $this->image);
        }

        return asset('images/avatar/blank.png');
    }
}
