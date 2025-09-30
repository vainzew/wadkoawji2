<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Check activation status first with caching to avoid repeated checks
        $cacheKey = 'login_activation_check';
        $activationStatus = cache($cacheKey);
        
        if ($activationStatus === null) {
            $activationStatus = checkActivationStatus();
            // Cache for 5 minutes since activation status doesn't change frequently
            cache([$cacheKey => $activationStatus], now()->addMinutes(5));
        }
        
        if ($activationStatus['status'] !== 'active') {
            return redirect()->route('activation.form')
                ->with('error', 'Aplikasi belum diaktivasi. ' . $activationStatus['message']);
        }

        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}