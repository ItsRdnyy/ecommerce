<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAllRequestsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log the request and response status/location
        \Log::info('GlobalRequestLogger: ' . $request->method() . ' ' . $request->fullUrl(), [
            'status' => $response->getStatusCode(),
            'location' => $response instanceof \Illuminate\Http\RedirectResponse ? $response->getTargetUrl() : null,
            'auth' => auth()->check(),
            'user' => auth()->check() ? [
                'id' => auth()->user()->id,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
            ] : null,
        ]);

        return $response;
    }
}
