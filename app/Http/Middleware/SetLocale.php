<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supported = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);

        $response = $next($request);

        // Persist the chosen locale in a long-lived cookie
        if ($response instanceof \Illuminate\Http\Response) {
            $response->cookie('app_locale', $locale, 60 * 24 * 365);
        }

        return $response;
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Honour an explicit switch request  (?locale=ar in session)
        if ($request->session()->has('app_locale')) {
            $locale = $request->session()->get('app_locale');
            if (in_array($locale, $this->supported)) {
                return $locale;
            }
        }

        // 2. Cookie preference
        if ($request->hasCookie('app_locale')) {
            $locale = $request->cookie('app_locale');
            if (in_array($locale, $this->supported)) {
                return $locale;
            }
        }

        // 3. Browser Accept-Language header (first visit)
        $browserLocales = $this->parseBrowserLocales($request->header('Accept-Language', ''));
        foreach ($browserLocales as $browserLocale) {
            $lang = strtolower(substr($browserLocale, 0, 2));
            if (in_array($lang, $this->supported)) {
                return $lang;
            }
        }

        // 4. Default
        return config('app.locale', 'en');
    }

    private function parseBrowserLocales(string $header): array
    {
        if (empty($header)) {
            return [];
        }

        $locales = [];
        foreach (explode(',', $header) as $part) {
            $parts = explode(';q=', trim($part));
            $locales[] = trim($parts[0]);
        }

        return $locales;
    }
}
