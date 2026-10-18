<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('admin');

        // Must be signed in through the admin login AND still have the admin role.
        if (!$guard->check() || $guard->user()->role !== 'admin') {
            $guard->logout();

            return redirect()->route('admin.login');
        }

        // Inside admin pages, auth()->user() returns the admin.
        Auth::shouldUse('admin');

        return $next($request);
    }
}