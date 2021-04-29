<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\AssignedDocket;

class AssignedDocketRepository implements IRepository
{
    public function getModel()
    {
        return new AssignedDocket();
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
        if ($request->has('assigned_docket_id')) {
            $assigned_docket = $this->getModel()->find($request->assigned_docket_id);
        } else {
            $assigned_docket = $this->getModel();
        }

        (!$request->has('user_id'))?:                                     $assigned_docket->user_id   = $request->user_id;
        (!$request->has('assigned_by'))?:                                 $assigned_docket->assigned_by  = $request->assigned_by;
        (!$request->has('docket_id'))?:                                   $assigned_docket->docket_id  = $request->docket_id;

        $assigned_docket->save();
        return $assigned_docket;
    }

    public function deleteDataById($request = null)
    {
        $assigned_docket = $this->getModel()->find($request->id);
        $assigned_docket->delete();
        return $assigned_docket;
    }
}
