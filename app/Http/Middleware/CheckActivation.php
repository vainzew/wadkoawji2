<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActivation
{
    public function handle(Request $request, Closure $next)
    {
        // Skip activation check untuk route activation dan login
        $excludedRoutes = [
            'activation.*',
            'login',
            'logout',
            'password.*'
        ];

        foreach ($excludedRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        // Use cached activation status to avoid repeated checks
        $cacheKey = 'middleware_activation_' . $request->ip();
        $status = cache($cacheKey);
        
        if ($status === null) {
            $status = checkActivationStatus();
            // Cache for 2 minutes to reduce overhead
            cache([$cacheKey => $status], now()->addMinutes(2));
        }

        if ($status['status'] !== 'active') {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Application not activated',
                    'message' => $status['message']
                ], 403);
            }

            return redirect()->route('activation.form')
                ->with('error', 'Aplikasi belum diaktivasi. ' . $status['message']);
        }

        return $next($request);
    }
}