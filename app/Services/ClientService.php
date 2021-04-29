<?php
namespace App\Services;

use App\Client;
use App\Company;
use App\Email_Client;
use App\EmailUser;

class ClientService {
    function clients(Company $company,$joinArray = array()){
        $clients    =   Client::where('company_id',$company->id)
                                ->orWhere('requested_company_id',$company->id);
        if(!empty($joinArray))
            $clients->with($joinArray);

        return $clients->get();
    }

    function emailClient(Company $company, EmailUser $emailUser){
        $emailClient  =    Email_Client::where('email_user_id', $emailUser->id)->where('company_id', $company->id)->first();
        return $emailClient;
    }
}