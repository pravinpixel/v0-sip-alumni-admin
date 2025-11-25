<?php

namespace App\Http\Middleware;

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

        return $next($request);
    }
}
