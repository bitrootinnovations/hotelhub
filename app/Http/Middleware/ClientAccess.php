<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if (is_null($user->client_id)) {
            return redirect('/dashboard');
        }

        // Check client's plan — only Premium gets web portal access
        $client = \App\Models\ClientMaster::find($user->client_id);
        if (!$client || $client->plan_type !== 'Premium') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Web portal access is available for Premium plan clients only. Please contact your administrator.']);
        }

        return $next($request);
    }
}
