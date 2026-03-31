<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * @var array<int, string>
     */
    private array $supportedLocales = ['sk', 'en'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->resolveLocaleFromHeader((string) $request->header('Accept-Language', ''));

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    private function resolveLocaleFromHeader(string $acceptLanguageHeader): ?string
    {
        if ($acceptLanguageHeader === '') {
            return null;
        }

        $candidates = explode(',', $acceptLanguageHeader);

        foreach ($candidates as $candidate) {
            $langTag = strtolower(trim((string) explode(';', $candidate)[0]));
            if ($langTag === '') {
                continue;
            }

            // Map regional tags like sk-SK / en-US to base language.
            $baseLang = (string) explode('-', $langTag)[0];

            if (in_array($baseLang, $this->supportedLocales, true)) {
                return $baseLang;
            }
        }

        return null;
    }
}
