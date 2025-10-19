<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only refresh CSRF token for successful responses
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            // Regenerate CSRF token for every request to prevent token mismatch
            $request->session()->regenerateToken();
        }

        return $response;
    }
}