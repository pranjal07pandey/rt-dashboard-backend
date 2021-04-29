<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDockets;

class SentDocketsRepository implements IRepository
{
    public function getModel()
    {
        return new SentDockets();
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
        if ($request->has('send_docket_id')) {
            $send_docket = $this->getModel()->find($request->send_docket_id);
        } else {
            $send_docket = $this->getModel();
        }

        (!$request->has('company_id'))?:                        $send_docket->company_id   = $request->company_id;
        (!$request->has('user_id'))?:                           $send_docket->user_id  = $request->user_id;
        (!$request->has('is_admin'))?:                          $send_docket->is_admin  = $request->is_admin;
        (!$request->has('employed'))?:                          $send_docket->employed  = $request->employed;
        (!$request->has('docket'))?:                            $send_docket->docket   = $request->docket;
        (!$request->has('invoice'))?:                           $send_docket->invoice  = $request->invoice;
        (!$request->has('timer'))?:                             $send_docket->timer  = $request->timer;
        (!$request->has('docket_client'))?:                     $send_docket->docket_client  = $request->docket_client;
        (!$request->has('appear_on_recipient'))?:               $send_docket->appear_on_recipient   = $request->appear_on_recipient;
        (!$request->has('can_self_docket'))?:                   $send_docket->can_self_docket  = $request->can_self_docket;
        (!$request->has('sn'))?:                                $send_docket->sn  = $request->sn;

        $send_docket->save();
        return $send_docket;
    }

    public function deleteDataById($request = null)
    {
        $send_docket = $this->getModel()->find($request->id);
        $send_docket->delete();
        return $send_docket;
    }
}
