<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use overint\MailgunValidator;
use App\Services\V2\Api\LoginService;

class AuthController extends Controller
{
    protected $loginService;
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function validateEmail($key){
        $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
        echo($validator->validate($key));
        if($validator->validate($key)){

        }else{
            echo "false";
        }
    }
    public function login(LoginRequest $request){
        return $this->loginService->login($request);
    }

    public function registration(RegistrationRequest $request){
        return $this->loginService->registration($request);
    }

    public function getAppInfo(){
        $app_infos = $this->loginService->getAppInfo();
        return response()->json($app_infos);
    }











}
