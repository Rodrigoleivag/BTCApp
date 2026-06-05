<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard === 'user') {
                    return redirect('/user/dashboard');
                }
                if ($guard === 'admin') {
                    return redirect('/admin/dashboard');
                }
            }
        }

        return $next($request);
    }
}
