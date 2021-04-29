<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Company;
use Illuminate\Support\Facades\Session;
use App\PaymentLog;

class CompanyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->email_verification == "") {
            switch (Auth::user()->user_type) {
                case 1:
                    return redirect('/dashboard');
                    break;
                case 2:
                    return $next($request);
                    break;

                default:
                    return redirect('/');
                    break;
            }
        } else {
            Auth::logout();
            return redirect('login')->with('message', 'Please check your email and activate your account first.');
        }
    }
}
