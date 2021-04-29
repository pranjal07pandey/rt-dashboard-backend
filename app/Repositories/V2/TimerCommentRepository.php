<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\TimerComment;

class TimerCommentRepository implements IRepository
{
    public function getModel()
    {
        return new TimerComment();
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
        if ($request->has('timer_comment_id')) {
            $timer_comment = $this->getModel()->find($request->timer_comment__id);
        } else {
            $timer_comment = $this->getModel();
        }

        (!$request->has('user_id'))?:                           $timer_comment->user_id   = $request->user_id;
        (!$request->has('timer_id'))?:                          $timer_comment->timer_id  = $request->timer_id;
        (!$request->has('time'))?:                              $timer_comment->time  = $request->time;
        (!$request->has('message'))?:                           $timer_comment->message   = $request->message;
        (!$request->has('location'))?:                          $timer_comment->location  = $request->location;
        (!$request->has('latitude'))?:                          $timer_comment->latitude  = $request->latitude;
        (!$request->has('longitude'))?:                         $timer_comment->longitude   = $request->longitude;

        $timer_comment->save();
        return $timer_comment;
    }

    public function deleteDataById($request = null)
    {
        $timer_comment = $this->getModel()->find($request->id);
        $timer_comment->delete();
        return $timer_comment;
    }
}
