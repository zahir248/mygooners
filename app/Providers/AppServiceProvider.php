<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Service;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share pending services count with all admin views
        View::composer('layouts.admin', function ($view) {
            $pendingServicesCount = Service::where('status', 'pending')->count();
            $view->with('stats', [
                'pending_services' => $pendingServicesCount
            ]);
        });
    }
}
