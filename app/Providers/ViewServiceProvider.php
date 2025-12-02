<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Alumnis;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share alumni data with all views
        View::composer('*', function ($view) {
            $alumniSession = session('alumni');
            
            if ($alumniSession && isset($alumniSession['id'])) {
                $alumni = Alumnis::with(['city', 'occupation'])
                    ->find($alumniSession['id']);
                    if ($alumni && $alumni->status === 'blocked') {
                        session()->flush();
                        redirect()->route('alumni.login')
                            ->with('error', 'Your account has been blocked.')
                            ->send();
                        exit;
                    }
            } else {
                $alumni = null;
            }

            $view->with('alumni', $alumni);
        });
    }
}
