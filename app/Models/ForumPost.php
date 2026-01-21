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

    protected $appends = ['reply_count', 'likes_count', 'views_count', 'labels'];
    protected $table = 'forum_post';
    protected $fillable = [
        'alumni_id',
        'title',
        'description',
        'likes',
        'views',
        'status',
        'remarks'
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

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getViewsCountAttribute()
    {
        return $this->views()->count();
    }

    public function likes()
    {
        return $this->hasMany(PostLikes::class, 'post_id');
    }

    public function views()
    {
        return $this->hasMany(PostViews::class, 'post_id');
    }

    public function pinned()
    {
        return $this->hasMany(PostPinned::class, 'post_id');
    }

    public function postlabels()
    {
        return $this->hasMany(PostLabel::class, 'post_id')->with('label');
    }

    public function getLabelsAttribute()
    {
        return $this->postlabels()->with('label:id,name')->get()->pluck('label.name');
    }

    public function reports()
    {
        return $this->hasMany(PostReport::class, 'post_id');
    }

}
