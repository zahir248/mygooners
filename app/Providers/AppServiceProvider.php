<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Service;
use App\Models\Product;
use App\Models\User;

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
        // Set application locale to Malay
        App::setLocale('ms');
        
        // Share pending counts and navbar notifications with all admin views
        View::composer('layouts.admin', function ($view) {
            $pendingServicesCount = Service::where('status', 'pending')->count();
            $pendingSellersCount = User::where('seller_status', 'pending')->count();

            $view->with('stats', [
                'pending_services' => $pendingServicesCount,
                'pending_sellers' => $pendingSellersCount,
            ]);

            $notifications = [];
            $user = auth()->user();

            if ($user && $user->role !== 'writer') {
                if ($pendingServicesCount > 0) {
                    $notifications[] = [
                        'label' => 'Perkhidmatan menunggu kelulusan',
                        'count' => $pendingServicesCount,
                        'url' => route('admin.services.pending'),
                    ];
                }

                if ($pendingSellersCount > 0) {
                    $notifications[] = [
                        'label' => 'Permohonan penjual menunggu',
                        'count' => $pendingSellersCount,
                        'url' => route('admin.seller-requests.pending'),
                    ];
                }

                $pendingRefundsCount = Refund::where('status', 'pending')->count();
                if ($pendingRefundsCount > 0) {
                    $notifications[] = [
                        'label' => 'Permohonan refund menunggu',
                        'count' => $pendingRefundsCount,
                        'url' => route('admin.refunds.index', ['status' => 'pending']),
                    ];
                }

                $pendingOrdersCount = Order::where('status', 'pending')->count();
                if ($pendingOrdersCount > 0) {
                    $notifications[] = [
                        'label' => 'Pesanan menunggu',
                        'count' => $pendingOrdersCount,
                        'url' => route('admin.orders.index', ['status' => 'pending']),
                    ];
                }
            }

            $view->with('adminNotifications', $notifications);
            $view->with('adminNotificationCount', count($notifications));
        });
    }
}
