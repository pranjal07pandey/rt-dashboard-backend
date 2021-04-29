<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Email_Client;

class EmailClientRepository implements IRepository
{
    public function getModel()
    {
        return new Email_Client();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('email_client_id')) {
            $email_client = $this->getModel()->find($request->email_client__id);
        } else {
            $email_client = $this->getModel();
        }

        (!$request->has('email_user_id'))?:                     $email_client->email_user_id   = $request->email_user_id;
        (!$request->has('company_id'))?:                        $email_client->company_id  = $request->company_id;
        (!$request->has('full_name'))?:                         $email_client->full_name  = $request->full_name;
        (!$request->has('company_name'))?:                      $email_client->company_name  = $request->company_name;
        (!$request->has('company_address'))?:                   $email_client->company_address  = $request->company_address;
        (!$request->has('syn_user'))?:                          $email_client->syn_user  = $request->syn_user;
        (!$request->has('xero_contact_id'))?:                   $email_client->xero_contact_id  = $request->xero_contact_id;

        $email_client->save();
        return $email_client;
    }

    public function deleteDataById($request = null)
    {
        $email_client = $this->getModel()->find($request->id);
        $email_client->delete();
        return $email_client;
    }
}
