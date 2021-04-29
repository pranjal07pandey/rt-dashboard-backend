<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\TimerAttachedTag;

class TimerAttachedTagRepository implements IRepository
{
    public function getModel()
    {
        return new TimerAttachedTag();
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
        if ($request->has('timer_attached_tag_id')) {
            $timer_attached_tag = $this->getModel()->find($request->timer_attached_tag__id);
        } else {
            $timer_attached_tag = $this->getModel();
        }

        (!$request->has('user_id'))?:                           $timer_attached_tag->user_id   = $request->user_id;
        (!$request->has('timer_id'))?:                          $timer_attached_tag->timer_id  = $request->timer_id;
        (!$request->has('tag'))?:                               $timer_attached_tag->tag   = $request->tag;

        $timer_attached_tag->save();
        return $timer_attached_tag;
    }

    public function deleteDataById($request = null)
    {
        $timer_attached_tag = $this->getModel()->find($request->id);
        $timer_attached_tag->delete();
        return $timer_attached_tag;
    }
}
