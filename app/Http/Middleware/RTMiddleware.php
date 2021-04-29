<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        switch (Auth::user()->user_type){
            case 2:
                return redirect('/dashboard/company');
                break;
            case 1:
                return $next($request);
                break;

            default:
                return redirect('/');
                break;
        }
    }
}
