<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Client users must not access admin routes
        if (!is_null(Auth::user()->client_id)) {
            return redirect()->route('client.dashboard');
        }

        return $next($request);
    }
}
