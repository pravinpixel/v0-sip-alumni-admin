<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationContact extends Model
{
    use HasFactory;
    protected $fillable = [
        'organization_id',
        'name',
        'email_id',
        'phone_number'
    ];
}
