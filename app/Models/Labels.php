<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Labels extends Model
{
    use HasFactory;
    protected $table = 'labels';

    public function postlabels()
    {
        return $this->hasMany(PostLabel::class, 'label_id');
    }
}