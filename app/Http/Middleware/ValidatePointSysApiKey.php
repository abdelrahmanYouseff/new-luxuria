<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePointSysApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key') ?? $request->header('Authorization');

        // Remove 'Bearer ' prefix if present
        if (str_starts_with($apiKey, 'Bearer ')) {
            $apiKey = substr($apiKey, 7);
        }

        $validApiKey = config('services.pointsys.api_key');

        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is invalid or missing'
            ], 401);
        }

        return $next($request);
    }
}
