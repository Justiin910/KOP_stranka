<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for Accept-Language header from frontend
        $locale = $request->header('Accept-Language');
        
        // Validate locale is one of supported locales
        $supportedLocales = ['en', 'sk'];
        if ($locale && in_array($locale, $supportedLocales)) {
            app()->setLocale($locale);
        }
        // Otherwise use default locale from config
        
        return $next($request);
    }
}
