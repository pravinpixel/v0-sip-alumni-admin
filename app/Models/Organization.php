<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = ['primary_contact_detail_1','primary_contact_detail_2'];

    protected $fillable = [
        'customer_code',
        'company_name',
        'location_id',
        'address',
        'primary_mail_id1',
        'primary_mail_id2',
        'primary_phone1',
        'primary_phone2',
        'primary_name1',
        'primary_name2',
    ];

    public function contactMaster()
    {
        return $this->hasMany(ContactMaster::class, 'customer_code' , 'customer_code');
    }

    public function organizationContacts()
    {
        return $this->hasMany(OrganizationContact::class, 'organization_id' , 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    
    

    public function getPrimaryContactDetail1Attribute()
    {
        if(empty($this->primary_name1) && empty($this->primary_mail_id1) && empty($this->primary_phone1)) {
            return '-';
        }
        return $this->primary_name1 . ' / ' . $this->primary_mail_id1 . ' / ' . $this->primary_phone1;
    }

    public function getPrimaryContactDetail2Attribute()
    {
        if(empty($this->primary_name2) && empty($this->primary_mail_id2) && empty($this->primary_phone2)) {
            return '-';
        }
        return $this->primary_name2 . ' / ' . $this->primary_mail_id2 . ' / ' . $this->primary_phone2;
    }


}
