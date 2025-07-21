<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Service;
use App\Models\Product;

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
        // Share pending services and products count with all admin views
        View::composer('layouts.admin', function ($view) {
            $pendingServicesCount = Service::where('status', 'pending')->count();
            $pendingProductsCount = Product::where('status', 'pending')->count();
            $view->with('stats', [
                'pending_services' => $pendingServicesCount,
                'pending_products' => $pendingProductsCount
            ]);
        });
    }
}
