<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WebsiteController extends Controller
{
    public function redirect($key){
//        echo Crypt::encryptString(url('dashboard/company/profile/subscription/upgrade'))."<br/>";
        $url    =    Crypt::decryptString($key);
        return redirect($url);
    }
}
