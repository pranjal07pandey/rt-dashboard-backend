<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Client;

class ClientRepository implements IRepository
{
    public function getModel()
    {
        return new Client();
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
        if ($request->has('client_id')) {
            $client = $this->getModel()->find($request->client__id);
        } else {
            $client = $this->getModel();
        }

        (!$request->has('company_id'))?:                            $client->company_id   = $request->company_id;
        (!$request->has('user_id'))?:                          $client->user_id  = $request->user_id;
        (!$request->has('requested_company_id'))?:                            $client->requested_company_id  = $request->requested_company_id;
        (!$request->has('accepted_user_id'))?:                  $client->accepted_user_id  = $request->accepted_user_id;

        $client->save();
        return $client;
    }

    public function deleteDataById($request = null)
    {
        $client = $this->getModel()->find($request->id);
        $client->delete();
        return $client;
    }
}
