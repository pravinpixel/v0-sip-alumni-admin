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

class ForumPost extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = ['reply_count'];
    protected $table = 'forum_post';
    protected $fillable = [
        'alumni_id',
        'title',
        'description',
        'labels',
        'likes',
        'views',
        'status'
    ];

    protected $casts = [
        'labels' => 'array',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumnis::class, 'alumni_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumReplies::class, 'forum_post_id');
    }

    public function getReplyCountAttribute()
    {
        return $this->replies()->count();
    }

}
