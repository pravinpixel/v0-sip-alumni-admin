<?php

namespace App\Http\Middleware;

use App\Models\Alumnis;
use Closure;
use Illuminate\Http\Request;

class AlumniAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if alumni is logged in
        if (!session('alumni_logged_in')) {

            // If not logged in → redirect to alumni login page
            return redirect()->route('alumni.login')
                ->with('error', 'Please login to continue.');
        }

        // Check if alumni is blocked
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

        return $next($request);
    }
}
