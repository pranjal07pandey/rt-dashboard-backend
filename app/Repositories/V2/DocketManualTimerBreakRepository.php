<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketManualTimerBreak;

class DocketManualTimerBreakRepository implements IRepository
{
    public function getModel()
    {
        return new DocketManualTimerBreak();
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
        if ($request->has('docket_manual_timer_break_id')) {
            $docket_manual_timer_break = $this->getModel()->find($request->docket_manual_timer_break_id);
        } else {
            $docket_manual_timer_break = $this->getModel();
        }

        (!$request->has('docket_field_id'))?:                          $docket_manual_timer_break->docket_field_id   = $request->docket_field_id;
        (!$request->has('label'))?:                                    $docket_manual_timer_break->label  = $request->label;
        (!$request->has('type'))?:                                     $docket_manual_timer_break->type  = $request->type;
        (!$request->has('explanation'))?:                              $docket_manual_timer_break->explanation  = $request->explanation;
        (!$request->has('csv_header'))?:                               $docket_manual_timer_break->csv_header  = $request->csv_header;
        (!$request->has('is_show'))?:                                  $docket_manual_timer_break->is_show  = $request->is_show;
        
        $docket_manual_timer_break->save();
        return $docket_manual_timer_break;
    }

    public function deleteDataById($request = null)
    {
        $docket_manual_timer_break = $this->getModel()->find($request->id);
        $docket_manual_timer_break->delete();
        return $docket_manual_timer_break;
    }
}
