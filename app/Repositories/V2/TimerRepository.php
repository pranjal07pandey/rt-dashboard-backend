<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Timer;

class TimerRepository implements IRepository
{
    public function getModel()
    {
        return new Timer();
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
        if ($request->has('timer_id')) {
            $timer = $this->getModel()->find($request->timer_id);
        } else {
            $timer = $this->getModel();
        }

        (!$request->has('user_id'))?:                       $timer->user_id   = $request->user_id;
        (!$request->has('location'))?:                      $timer->location  = $request->location;
        (!$request->has('longitude'))?:                     $timer->longitude  = $request->longitude;
        (!$request->has('latitude'))?:                      $timer->latitude   = $request->latitude;
        (!$request->has('time_started'))?:                  $timer->time_started  = $request->time_started;
        (!$request->has('time_ended'))?:                    $timer->time_ended  = $request->time_ended;
        (!$request->has('status'))?:                        $timer->status   = $request->status;
        (!$request->has('total_time'))?:                    $timer->total_time  = $request->total_time;

        $timer->save();
        return $timer;
    }

    public function deleteDataById($request = null)
    {
        $timer = $this->getModel()->find($request->id);
        $timer->delete();
        return $timer;
    }
}
