<?php

namespace App\Http\Middleware;

use App\Models\Alumnis;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlumniAuth
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $alumniId = session('alumni.id');

            if (!$alumniId) {
                return $this->unauthorizedResponse(
                    $request,
                    'Session expired. Please login again.'
                );
            }
            $alumni = Alumnis::with(['city', 'occupation'])->find($alumniId);
            if (!$alumni) {
                session()->forget('alumni');
                return $this->unauthorizedResponse(
                    $request,
                    'Your account does not exist.'
                );
            }
            if ($alumni->status === 'blocked') {
                session()->forget('alumni');
                return $this->unauthorizedResponse(
                    $request,
                    'Your account has been blocked by admin.'
                );
            }
            view()->share('alumni', $alumni);
            return $next($request);
        } catch (\Throwable $e) {
            Log::error('AlumniAuth DB Error: ' . $e->getMessage());
            session()->forget('alumni');
            return $this->unauthorizedResponse(
                $request,
                'Service temporarily unavailable. Please try again later.'
            );
        }
    }
    protected function unauthorizedResponse(Request $request, string $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'redirect' => route('alumni.login')
            ], 401);
        }

        return redirect()->route('alumni.login')
            ->with('error', $message);
    }
}
