<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Alumnis;
use App\Models\Announcements;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $alumniSession = session('alumni');
 
            if (!$alumniSession || !isset($alumniSession['id'])) {
                $view->with('alumni', null);
                $view->with('announcements', collect()); // Empty collection for non-authenticated users
                return;
            }
 
            $alumni = Alumnis::with(['city', 'occupation'])->find($alumniSession['id']);
            
            if (!$alumni) {
                session()->flush();
                return redirect()->route('alumni.login')
                    ->with('error', 'Alumni session is invalid.')
                    ->send();
            }
 
            if ($alumni->status === 'blocked') {
                session()->flush();
                return redirect()->route('alumni.login')
                    ->with('error', 'Your account has been blocked.')
                    ->send();
            }

            // Get active announcements for authenticated alumni
            $announcements = Announcements::where('status', 1)
                ->where(function($query) {
                    $query->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $view->with('alumni', $alumni);
            $view->with('announcements', $announcements);
        });
    }
}
