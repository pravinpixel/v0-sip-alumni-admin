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
        $alumniId = session('alumni.id');
        if (!$alumniId) {
            return redirect()->route('alumni.login')
                ->with('error', 'Please login to continue.');
        }

        $alumni = Alumnis::with(['city', 'occupation'])->find($alumniId);
        if (!$alumni) {
            session()->forget('alumni');
            return redirect()->route('alumni.login')
                ->with('error', 'Your account does not exist.');
        }

        if ($alumni->status === 'blocked') {
            session()->forget('alumni');
            return redirect()->route('alumni.login')
                ->with('error', 'Your account has been blocked by admin.');
        }
        view()->share('alumni', $alumni);

        return $next($request);
    }
}