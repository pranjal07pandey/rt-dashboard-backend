<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\TimerSetting;

class TimerSettingRepository implements IRepository
{
    public function getModel()
    {
        return new TimerSetting();
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
        if ($request->has('timer_setting_id')) {
            $timer_setting = $this->getModel()->find($request->timer_setting__id);
        } else {
            $timer_setting = $this->getModel();
        }

        (!$request->has('company_id'))?:                      $timer_setting->company_id   = $request->company_id;
        (!$request->has('comment_image'))?:                   $timer_setting->comment_image  = $request->comment_image;
        (!$request->has('pause_image'))?:                     $timer_setting->pause_image  = $request->pause_image;

        $timer_setting->save();
        return $timer_setting;
    }

    public function deleteDataById($request = null)
    {
        $timer_setting = $this->getModel()->find($request->id);
        $timer_setting->delete();
        return $timer_setting;
    }
}
