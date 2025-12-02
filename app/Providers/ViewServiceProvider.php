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

            if ($alumniSession && isset($alumniSession['id'])) {
                $alumni = Alumnis::with(['city', 'occupation'])
                    ->find($alumniSession['id']);

                if ($alumni && $alumni->status === 'blocked') {
                    session()->forget('alumni'); // Clear session
                    return redirect()->route('alumni.login')
                        ->with('error', 'Your profile has been blocked. Please contact Admin.')
                        ->send();
                }
            } else {
                $alumni = null;
            }

            $view->with('alumni', $alumni);
        });
    }
}
