<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class AccessGuard
{
   public function checkRole($id,$group_name){
      $count_group=DB::table('roles')
        ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
        ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
        ->select(DB::raw('count(*) as count'))
        ->groupBy('roles.name', 'permissions.group_name')
        ->where('roles.id',$id)->where('permissions.group_name',$group_name)
        ->first();
           if($count_group){
                  return $count_group->count;
           }
           return 0;
    }
}
