<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WriterAccessMiddleware
{
    /**
     * Handle an incoming request.
     * Restricts writers to only access dashboard and articles routes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is a writer, restrict access to only dashboard and articles
        if ($user && $user->role === 'writer') {
            $route = $request->route();
            $routeName = $route ? $route->getName() : null;
            
            // Allowed routes for writers (only articles and profile)
            $allowedRoutes = [
                'admin.profile',
                'admin.profile.update',
                'admin.articles.index',
                'admin.articles.create',
                'admin.articles.store',
                'admin.articles.preview',
                'admin.articles.preview-existing',
                'admin.articles.edit',
                'admin.articles.update',
                'admin.articles.destroy',
                'admin.articles.upload-image',
            ];
            
            // Check if the current route is allowed
            if ($routeName && !in_array($routeName, $allowedRoutes)) {
                return redirect()->route('admin.articles.index')
                    ->with('error', 'Anda tidak mempunyai kebenaran untuk mengakses halaman ini.');
            }
        }
        
        return $next($request);
    }
}
