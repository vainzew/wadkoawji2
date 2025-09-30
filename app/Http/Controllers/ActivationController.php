<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivationController extends Controller
{
    public function showActivationForm()
    {
        return view('activation.form');
    }

    public function activate(Request $request)
    {
        $request->validate([
            'activation_code' => 'required|string|size:16'
        ]);

        $result = activateApplication($request->activation_code);

        if ($result['status'] === 'success') {
            return redirect()->route('login')->with('success', 'Aplikasi berhasil diaktivasi! Silakan login.');
        }

        return back()->withErrors(['activation_code' => $result['message']]);
    }

    public function checkStatus()
    {
        $status = checkActivationStatus();
        return response()->json($status);
    }

    public function deactivate()
    {
        $activationFile = storage_path('app/activation.json');

        if (file_exists($activationFile)) {
            unlink($activationFile);
        }

        return redirect()->route('activation.form')->with('success', 'Aplikasi berhasil di-deaktivasi.');
    }
}