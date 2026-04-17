<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            if (Auth::user()->status_id != 1) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is inactive. Please contact the administrator.']);
            }

            $request->session()->regenerate();

            if (Auth::user()->client_id) {
                $client = \App\Models\ClientMaster::find(Auth::user()->client_id);
                if (!$client || $client->plan_type !== 'Premium') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->withErrors(['email' => 'Web portal access is available for Premium plan clients only. Please contact your administrator.']);
                }
                return redirect()->route('client.dashboard');
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
