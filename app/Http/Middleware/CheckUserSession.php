<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated but session is invalid
        if (Auth::check() && !Session::has('auth')) {
            Auth::logout();
            Session::flush();

            if ($request->header('X-Inertia')) {
                return redirect()->route('login');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
