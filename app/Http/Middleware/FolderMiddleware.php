<?php

namespace App\Http\Middleware;

use App\ShareableFolder;
use App\ShareableFolderUser;
use Closure;
use Illuminate\Support\Facades\Session;

class FolderMiddleware
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


        $sessionData = Session::get('shareable_folder');
        if($sessionData == null){
            Session::forget('shareable_folder');
            return response()->view('errors.404', [], 404);
        }else{
            $shareableFolder = ShareableFolder::where('link',$sessionData['link'])->first();
            if($shareableFolder == null){
                Session::forget('shareable_folder');
                return response()->view('errors.404', [], 404);
            }else{
                if ($shareableFolder->shareable_type == 'Restricted'){

                    if( array_key_exists("token",$sessionData) == false){

                        Session::forget('shareable_folder');
                        return redirect('/folder/'.$shareableFolder->link);
                    }else{
                        $shareableUser = ShareableFolderUser::where('token',$sessionData['token'])->first();
                        if($shareableUser == null){
                            Session::forget('shareable_folder');
                            return redirect('/folder/'.$shareableFolder->link);
                        }else{
                            return $next($request);
                        }

                    }
                }else if ($shareableFolder->shareable_type  == 'Public'){

                    return $next($request);

                }else if ($shareableFolder->shareable_type  == 'Disabled'){
                    Session::forget('shareable_folder');
                    return response()->view('errors.404', [], 404);
                }
            }
        }

    }
}
