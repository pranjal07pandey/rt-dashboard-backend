<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\MessagesGroup;

class MessagesGroupRepository implements IRepository
{
    public function getModel()
    {
        return new MessagesGroup();
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
        if ($request->has('messages_group_id')) {
            $messages_group = $this->getModel()->find($request->messages_group__id);
        } else {
            $messages_group = $this->getModel();
        }

        (!$request->has('user_id'))?:              $messages_group->user_id   = $request->user_id;
        (!$request->has('title'))?:                $messages_group->title  = $request->title;
        (!$request->has('slug'))?:                 $messages_group->slug  = $request->slug;
        (!$request->has('is_active'))?:            $messages_group->is_active  = $request->is_active;
        (!$request->has('company_id'))?:           $messages_group->company_id  = $request->company_id;

        $messages_group->save();
        return $messages_group;
    }

    public function deleteDataById($request = null)
    {
        $messages_group = $this->getModel()->find($request->id);
        $messages_group->delete();
        return $messages_group;
    }
}
