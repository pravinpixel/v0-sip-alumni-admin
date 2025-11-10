<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class EnsureUserHasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public $user;
    public function handle(Request $request, Closure $next,$access_menu=null)
    {    
        $this->user = Auth::guard('web')->user();
        if ($this->user && !$this->user->hasPermissionTo($access_menu)) {
            return response()->view('403');
        }
        if(!$this->user){
            return response()->view('auth.login');
        }
                   
        return $next($request);
    }
}
