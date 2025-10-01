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

        // PERFORMANCE OPTIMIZATION: Skip activation check for AJAX requests
        // Activation is already checked on page load, no need to re-check on every AJAX call
        // This significantly speeds up pages with multiple AJAX requests (like transaksi)
        if ($request->ajax() || $request->wantsJson()) {
            // Only check if session doesn't have activation flag
            if (session()->has('activation_verified') && session('activation_verified') === true) {
                return $next($request);
            }
        }

        // Check activation status (with smart caching in helper)
        $status = checkActivationStatus();

        // If not active, redirect to activation form
        if ($status['status'] !== 'active') {
            // Clear activation session flag
            session()->forget('activation_verified');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Application not activated',
                    'message' => $status['message']
                ], 403);
            }

            return redirect()->route('activation.form')
                ->with('error', 'Aplikasi belum diaktivasi. ' . $status['message']);
        }

        // Set session flag for AJAX requests to use
        session(['activation_verified' => true]);

        return $next($request);
    }
}