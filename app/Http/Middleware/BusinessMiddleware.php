<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BusinessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('BusinessMiddleware checking request: ' . $request->fullUrl(), [
            'auth_check' => auth()->check(),
            'user' => auth()->check() ? [
                'id' => auth()->user()->id,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
                'status' => auth()->user()->status,
            ] : null,
        ]);

        if (!auth()->check() || !auth()->user()->isBusiness()) {
            \Log::info('BusinessMiddleware redirecting to / because user is not authorized.');
            return redirect('/')->with('error', 'Unauthorized.');
        }

        return $next($request);
    }
}
