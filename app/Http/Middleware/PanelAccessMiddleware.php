<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PanelAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // User login nahi hai ya session expire ho gaya
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Account disabled hai
        if ($user->status === 0) {
            Auth::logout();

            abort(403, 'Your account is disabled. You are not allowed to access the admin panel.' . config('app.name'));
        }

        return $next($request);
    }
}