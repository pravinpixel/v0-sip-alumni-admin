<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TaskComment;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'deadline',
        'task_category_id',
        'date',
        'priority_id',
        'assigned_to',
        'assigned_by',
        'created_by',
        'followers',
        'additional_followers',
        'status_id',
        'recurrence',
    ];


    public function assignedto() {
        return $this->hasOne('App\Models\Employee', 'id', 'assigned_to')
        ->select('id', 'first_name', 'last_name', 'email', 'profile_image','designation_id', 'branch_id','employee_id','location_id','reporting_manager')
        ->selectRaw("CONCAT(first_name, ' ', last_name) as name")
        ->with(['branchLocation' => function($query) {
            $query->select('id', 'name');
        }]);
    }

    public function assignedby() {
        return $this->hasOne('App\Models\Employee', 'id', 'created_by')
            ->select('id', 'first_name', 'last_name', 'email', 'profile_image','designation_id', 'branch_id','location_id','employee_id','reporting_manager')
            ->selectRaw("CONCAT(first_name, ' ', last_name) as name")
            ->with(['branchLocation' => function($query) {
                $query->select('id', 'name');
            }]);
    }

    public function status() {
        return $this->hasOne('App\Models\Status','id', 'status_id')->where('type','status')->select('id','name');
    }

    public function dueDates()
    {
        return $this->hasMany(TaskDueDate::class, 'task_id' , 'id');
    }

    public function priority()
    {
        return $this->belongsTo('App\Models\Status', 'priority_id')
                    ->where('type', 'priority')
                    ->withoutGlobalScopes()
                    ->select('id', 'name');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\TaskCategory', 'task_category_id')
                    ->withoutGlobalScopes()
                    ->select('id', 'name');
    }

    public function getFollowersDetailsAttribute()
    {
        $followerIds = explode(',', $this->followers);

        if (empty($followerIds)) {
            return collect();
        }
        return Employee::whereIn('id', $followerIds)
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as full_name"))
            ->get();
    }


     public function assignedByEmployee()
     {
         return $this->belongsTo(Employee::class, 'assigned_by');
     }

     public function assignedToEmployee()
     {
         return $this->belongsTo(Employee::class, 'assigned_to');
     }

    public function documents() {
        return $this->hasMany(TaskDocument::class, 'task_id', 'id')->select('id', 'task_id', 'document','name');
    }

    public function comments() {
        return $this->hasMany(TaskComment::class, 'task_id', 'id')->orderBy('id', 'desc');
    }


}
