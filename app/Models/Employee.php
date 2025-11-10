<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers\UtilsHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Employee extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;

    // hidden fields
    protected $hidden = ['password', 'created_at', 'updated_at', 'deleted_at'];


    protected $fillable = ['profile_image','is_mail_failed'];

    protected $casts = [
        'branch_id' => 'array',
        'reporting_manager' => 'array',
    ];

    protected $appends = ['branch_name','location','department','designation','reporting_managers'];

    protected function ProfileImage(): Attribute
    {
        $path = UtilsHelper::getStoragePath();

        return Attribute::make(
            get: fn ($value) => ($value==null) ? null : $path.$value,
        );
    }

    public function getBranchNameAttribute(){
        $branch_ids = json_decode($this->branch_id, true);
        if (!is_array($branch_ids) || empty($branch_ids)) {
            return '';
        }
        $branches = BranchLocation::whereIn('id', $branch_ids)->pluck('name')->toArray();
        return !empty($branches) ? implode(', ', $branches) : '';
    }

    

    public function getLocationAttribute(){
        $location_id = $this->location_id;
        $location = Location::where('id', $location_id)->first();
        if ($location) {
            return $location->name;
        }
        return null;
    }

    public function getDepartmentAttribute(){
        $department_id = $this->department_id;
        $department = Department::where('id', $department_id)->first();
        if ($department) {
            return $department->name;
        }
        return null;
    }

    public function getDesignationAttribute(){
        $designation_id = $this->designation_id;
        $designation = Designation::where('id', $designation_id)->first();
        if ($designation) {
            return $designation->name;
        }
        return null;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'iss' => config('app.url'),
        ];
    }

    public function designation() {
        return $this->belongsTo(Designation::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function getReportingManagersAttribute()
    {
        $managerIds = json_decode($this->reporting_manager) ?? [];
        $names = Employee::whereIn('id', $managerIds)->get(['id', 'first_name', 'last_name'])
            ->map(function($manager) {
                return $manager->first_name . ' ' . $manager->last_name;
            })->toArray();
        return implode(', ', $names);
    }


    public function branchLocation() {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function tasks() {
        return $this->hasMany(Task::class , 'assigned_to','id');
    }

}
