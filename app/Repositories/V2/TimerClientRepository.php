<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\TimerClient;

class TimerClientRepository implements IRepository
{
    public function getModel()
    {
        return new TimerClient();
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
        if ($request->has('timer_client_id')) {
            $timer_client = $this->getModel()->find($request->timer_client__id);
        } else {
            $timer_client = $this->getModel();
        }

        (!$request->has('user_id'))?:                           $timer_client->user_id   = $request->user_id;
        (!$request->has('timer_id'))?:                          $timer_client->timer_id  = $request->timer_id;
        (!$request->has('user_type'))?:                         $timer_client->user_type   = $request->user_type;

        $timer_client->save();
        return $timer_client;
    }

    public function deleteDataById($request = null)
    {
        $timer_client = $this->getModel()->find($request->id);
        $timer_client->delete();
        return $timer_client;
    }
}
