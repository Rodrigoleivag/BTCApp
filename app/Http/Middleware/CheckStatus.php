<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();
        
        if ($user && $user->phone_verify == 1 && $user->email_verify == 1 && $user->status == 0) {
            return $next($request);
        }
        
        return redirect()->route('user.authorization');
    }
}
