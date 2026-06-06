<?php

namespace App\Providers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        // For BM locale, keep Malay UI strings (JSON keys) instead of falling back to en.json English.
        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale) {
            if ($locale === 'ms' && ! str_contains($key, '.')) {
                return $key;
            }

            return null;
        });

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
                        'label' => __('admin.notification_services_pending'),
                        'count' => $pendingServicesCount,
                        'url' => route('admin.services.pending'),
                    ];
                }

                if ($pendingSellersCount > 0) {
                    $notifications[] = [
                        'label' => __('admin.notification_sellers_pending'),
                        'count' => $pendingSellersCount,
                        'url' => route('admin.seller-requests.pending'),
                    ];
                }

                $pendingRefundsCount = Refund::where('status', 'pending')->count();
                if ($pendingRefundsCount > 0) {
                    $notifications[] = [
                        'label' => __('admin.notification_refunds_pending'),
                        'count' => $pendingRefundsCount,
                        'url' => route('admin.refunds.index', ['status' => 'pending']),
                    ];
                }

                $pendingOrdersCount = Order::where('status', 'pending')->count();
                if ($pendingOrdersCount > 0) {
                    $notifications[] = [
                        'label' => __('admin.notification_orders_pending'),
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
