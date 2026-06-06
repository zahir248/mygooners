<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetClientLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        Carbon::setLocale($locale === 'ms' ? 'ms' : 'en');

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $candidates = [];

        $user = $request->user();
        if ($user) {
            $candidates[] = $user->client_locale;
        }

        $candidates[] = $request->session()->get('client_locale');
        $candidates[] = $request->cookie('client_locale');

        foreach ($candidates as $locale) {
            if (in_array($locale, ['ms', 'en'], true)) {
                return $locale;
            }
        }

        return 'ms';
    }
}
