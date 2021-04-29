<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketManualTimer;

class DocketManualTimerRepository implements IRepository
{
    public function getModel()
    {
        return new DocketManualTimer();
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
        if ($request->has('docket_manual_timer_id')) {
            $docket_manual_timer = $this->getModel()->find($request->docket_manual_timer_id);
        } else {
            $docket_manual_timer = $this->getModel();
        }

        (!$request->has('docket_field_id'))?:                           $docket_manual_timer->docket_field_id   = $request->docket_field_id;
        (!$request->has('type'))?:                                      $docket_manual_timer->type  = $request->type;
        (!$request->has('label'))?:                                     $docket_manual_timer->label  = $request->label;
        (!$request->has('csv_header'))?:                                $docket_manual_timer->csv_header  = $request->csv_header;
        (!$request->has('is_show'))?:                                   $docket_manual_timer->is_show  = $request->is_show;

        $docket_manual_timer->save();
        return $docket_manual_timer;
    }

    public function deleteDataById($request = null)
    {
        $docket_manual_timer = $this->getModel()->find($request->id);
        $docket_manual_timer->delete();
        return $docket_manual_timer;
    }
}
