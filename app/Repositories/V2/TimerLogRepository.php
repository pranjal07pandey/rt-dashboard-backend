<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\TimerLog;

class TimerLogRepository implements IRepository
{
    public function getModel()
    {
        return new TimerLog();
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
        if ($request->has('timer_log_id')) {
            $timer_log = $this->getModel()->find($request->timer_log_id);
        } else {
            $timer_log = $this->getModel();
        }

        (!$request->has('timer_id'))?:                      $timer_log->timer_id   = $request->timer_id;
        (!$request->has('location'))?:                      $timer_log->location  = $request->location;
        (!$request->has('longitude'))?:                     $timer_log->longitude  = $request->longitude;
        (!$request->has('latitude'))?:                      $timer_log->latitude   = $request->latitude;
        (!$request->has('time_started'))?:                  $timer_log->time_started  = $request->time_started;
        (!$request->has('time_finished'))?:                 $timer_log->time_finished  = $request->time_finished;
        (!$request->has('reason'))?:                        $timer_log->reason   = $request->reason;

        $timer_log->save();
        return $timer_log;
    }

    public function deleteDataById($request = null)
    {
        $timer_log = $this->getModel()->find($request->id);
        $timer_log->delete();
        return $timer_log;
    }
}
