<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\TimerImage;

class TimerImageRepository implements IRepository
{
    public function getModel()
    {
        return new TimerImage();
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
        if ($request->has('timer_image_id')) {
            $timer_image = $this->getModel()->find($request->timer_image__id);
        } else {
            $timer_image = $this->getModel();
        }

        (!$request->has('key_id'))?:                    $timer_image->key_id   = $request->key_id;
        (!$request->has('type'))?:                      $timer_image->type  = $request->type;
        (!$request->has('image'))?:                     $timer_image->image  = $request->image;

        $timer_image->save();
        return $timer_image;
    }

    public function deleteDataById($request = null)
    {
        $timer_image = $this->getModel()->find($request->id);
        $timer_image->delete();
        return $timer_image;
    }
}
