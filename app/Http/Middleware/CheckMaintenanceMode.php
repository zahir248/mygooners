<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Routes that remain accessible during maintenance mode.
     */
    protected array $except = [
        'admin',
        'admin/*',
        'up',
        'checkout/toyyibpay/callback',
        'checkout/toyyibpay/return',
        'checkout/toyyibpay/cancel',
        'direct-checkout/toyyibpay/return',
        'direct-checkout/toyyibpay/cancel',
        'stripe/webhook',
        'api/mobile/checkout/toyyibpay/callback',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        try {
            if (!setting('maintenance_mode', false)) {
                return $next($request);
            }
        } catch (\Throwable $e) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => __('maintenance.message'),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->view('maintenance', [], Response::HTTP_SERVICE_UNAVAILABLE);
    }

    protected function inExceptArray(Request $request): bool
    {
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
