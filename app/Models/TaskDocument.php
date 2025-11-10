<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers\UtilsHelper;


class TaskDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable=['task_id','document','name'];

    protected function Document(): Attribute
    {
     
        $path = UtilsHelper::getStoragePath();
        

        return Attribute::make(
            get: fn ($value) => ($value==null) ? null : $path.$value,
        );
    }
}
