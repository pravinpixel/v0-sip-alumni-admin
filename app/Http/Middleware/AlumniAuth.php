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
            return $this->unauthorizedResponse($request, 'Please login to continue.');
        }

        $alumni = Alumnis::with(['city', 'occupation'])->find($alumniId);
        if (!$alumni) {
            session()->forget('alumni');
            return $this->unauthorizedResponse($request, 'Your account does not exist.');
        }

        if ($alumni->status === 'blocked') {
            session()->forget('alumni');
            return $this->unauthorizedResponse($request, 'Your account has been blocked by admin.');
        }
        view()->share('alumni', $alumni);

        return $next($request);
    }
    protected function unauthorizedResponse(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 401);
        }

        return redirect()->route('alumni.login')
            ->with('error', $message);
    }
}
