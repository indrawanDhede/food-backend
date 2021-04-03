<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // verifikasi role admin
        if(Auth::user() && Auth::user()->roles == 'ADMIN') {
            return $next($request);
        } else {
            return redirect('/');
        }

    }
}
