<?php
namespace Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/auth/login');
        }

        if (!empty($roles) && !in_array(Auth::user()->role, $roles)) {
            return redirect('/'); // Redirect to home if user does not have the required role
        }

        return $next($request);
    }
}