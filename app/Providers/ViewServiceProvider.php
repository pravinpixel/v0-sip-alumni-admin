<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Alumnis;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $alumniSession = session('alumni');
 
            if (!$alumniSession || !isset($alumniSession['id'])) {
                $view->with('alumni', null);
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
            $view->with('alumni', $alumni);
        });
    }
}
