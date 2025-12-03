<?php

namespace App\Http\Middleware;

use App\Models\Alumnis;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumniAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('alumni_logged_in')) {
            return redirect()->route('alumni.login')
                ->with('error', 'Please login to continue.');
        }

        try {
            DB::connection()->getPdo();
            
            $alumni = Alumnis::find(session('alumni.id'));

            if (!$alumni) {
                session()->flush();
                return redirect()->route('alumni.login')
                    ->with('error', 'Your account does not exist.');
            }
            
            if ($alumni->status == 'blocked') {
                session()->flush();
                return redirect()->route('alumni.login')
                    ->with('error', 'Your account has been blocked by admin.');
            }

        } catch (\Exception $e) {
            return redirect()->route('alumni.login')
                ->with('error', 'Database connection error. Please try again later.');
        }

        return $next($request);
    }
}