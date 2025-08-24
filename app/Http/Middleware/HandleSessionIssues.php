<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HandleSessionIssues
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log session information for debugging
        Log::info('Session middleware', [
            'session_id' => Session::getId(),
            'session_driver' => config('session.driver'),
            'session_lifetime' => config('session.lifetime'),
            'session_secure' => config('session.secure'),
            'session_same_site' => config('session.same_site'),
            'session_domain' => config('session.domain'),
            'app_env' => config('app.env'),
            'app_url' => config('app.url'),
        ]);

        // Ensure session is started
        if (!Session::isStarted()) {
            Session::start();
        }

        // Set session cookie parameters for better compatibility
        if (php_sapi_name() !== 'cli') {
            session_set_cookie_params([
                'lifetime' => config('session.lifetime') * 60,
                'path' => config('session.path'),
                'domain' => config('session.domain'),
                'secure' => config('session.secure'),
                'httponly' => config('session.http_only'),
                'samesite' => config('session.same_site'),
            ]);
        }

        return $next($request);
    }
}
