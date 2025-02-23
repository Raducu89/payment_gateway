<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Block requests without User-Agent (often malicious bots)
        if (!$request->header('User-Agent')) {
            return response()->json(['message' => 'User-Agent header required'], 400);
        }

        // Block requests with too large payload (e.g. DoS attacks)
        if ($request->header('Content-Length') && $request->header('Content-Length') > 1024 * 100) { // 100 KB
            return response()->json(['message' => 'Payload too large'], 413);
        }

        // Permit only JSON as input for API (preventing XSS)
        if ($request->isMethod('POST') || $request->isMethod('PUT')) {
            if ($request->header('Content-Type') !== 'application/json') {
                return response()->json(['message' => 'Only JSON requests are allowed'], 415);
            }
        }

        return $next($request);
    }
}
