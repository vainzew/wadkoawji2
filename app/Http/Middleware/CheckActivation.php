<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActivation
{
    public function handle(Request $request, Closure $next)
    {
        // ONLY skip activation check for activation routes
        // All other routes including login MUST be protected
        if ($request->routeIs('activation.*')) {
            return $next($request);
        }

        // SECURITY FIX: ALWAYS check activation status, no session bypass
        // Performance is maintained through smart hardware-bound caching in helper
        // Cache hit = ~0.001s, so AJAX requests are still fast but secure
        
        // Check activation status (with hardware-bound caching in helper)
        $status = checkActivationStatus();

        // If not active, redirect to activation form
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