<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Http\Request;
use Response;

class APIAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Auth-Token');
        $user_id  = $request->header('userId');
        $user =  User::where('hashToken','=',$token)->where('id','=',$user_id)->first();
        if(!$user):
            $response = Response::json([
                'status' => false,
                'message' => 'Not authenticated']);

            $response->header('Content-Type', 'application/json');
            return $response;
        endif;

        return $next($request);
    }
}
