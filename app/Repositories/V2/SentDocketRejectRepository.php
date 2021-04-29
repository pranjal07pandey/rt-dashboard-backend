<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDocketReject;

class SentDocketRejectRepository implements IRepository
{
    public function getModel()
    {
        return new SentDocketReject();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function getDataWhereIn($col,$value)
    {
        return $this->getModel()->whereIn($col,$value);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('sent_docket_reject_id')) {
            $sent_docket_reject = $this->getModel()->find($request->sent_docket_reject_id);
        } else {
            $sent_docket_reject = $this->getModel();
        }

        (!$request->has('sent_docket_id'))?:                        $sent_docket_reject->sent_docket_id   = $request->sent_docket_id;
        (!$request->has('explanation'))?:                           $sent_docket_reject->explanation  = $request->explanation;
        (!$request->has('user_id'))?:                               $sent_docket_reject->user_id  = $request->user_id;
        $sent_docket_reject->save();
        return $sent_docket_reject;
    }

    public function deleteDataById($request = null)
    {
        $sent_docket_reject = $this->getModel()->find($request->id);
        $sent_docket_reject->delete();
        return $sent_docket_reject;
    }
}
