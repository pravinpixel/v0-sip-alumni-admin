<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers\UtilsHelper;

class Setting extends Model
{
    use HasFactory;

    protected $fillable=['name','value'];

    protected function value(): Attribute
    {
     
        $path = UtilsHelper::getStoragePath();
        

        return Attribute::make(
            get: fn ($value) => ($value==null) ? null : $path.$value,
        );
    }
}
