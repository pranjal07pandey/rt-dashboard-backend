<?php


namespace App\Repositories\V2;

use App\AppInfo;
use App\AppInterface\IRepository;

class AppInfoRepository implements IRepository
{
    public function getModel()
    {
        return new AppInfo();
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
        if ($request->has('app_info_id')) {
            $app_info = $this->getModel()->find($request->app_info_id);
        } else {
            $app_info = $this->getModel();
        }

        (!$request->has('field_name'))?:                                   $app_info->field_name   = $request->field_name;
        (!$request->has('field_slug'))?:                                   $app_info->field_slug  = $request->field_slug;
        (!$request->has('value'))?:                                        $app_info->value  = $request->value;

        $app_info->save();
        return $app_info;
    }

    public function deleteDataById($request = null)
    {
        $app_info = $this->getModel()->find($request->id);
        $app_info->delete();
        return $app_info;
    }
}
