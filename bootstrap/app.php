<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'writer' => \App\Http\Middleware\WriterAccessMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
            \App\Http\Middleware\SetAdminLocale::class,
            \App\Http\Middleware\SetClientLocale::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        // Run after Authenticate so locale comes from the logged-in user's preference.
        $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\SetAdminLocale::class,
            \App\Http\Middleware\SetClientLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/mobile/*')) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    })->create();
