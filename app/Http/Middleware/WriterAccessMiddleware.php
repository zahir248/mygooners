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
     * Restricts writers to articles, article categories, and profile routes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is a writer, restrict access to articles, categories, and profile
        if ($user && $user->role === 'writer') {
            $route = $request->route();
            $routeName = $route ? $route->getName() : null;
            
            // Allowed routes for writers
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
                'admin.article-categories.index',
                'admin.article-categories.create',
                'admin.article-categories.store',
                'admin.article-categories.edit',
                'admin.article-categories.update',
                'admin.article-categories.destroy',
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
