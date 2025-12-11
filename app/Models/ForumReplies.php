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

class ForumReplies extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'forum_replies';
    protected $fillable = [
        'forum_post_id',
        'alumni_id',
        'parent_reply_id',
        'message',
        'status'
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumnis::class, 'alumni_id');
    }

    public function forumPost()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }
    public function parentReply()
    {
        return $this->belongsTo(ForumReplies::class, 'parent_reply_id');
    }

    public function childReplies()
    {
        return $this->hasMany(ForumReplies::class, 'parent_reply_id');
    }

}
