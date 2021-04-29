<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\AssignDocketUser;

class AssignDocketUserRepository implements IRepository
{
    public function getModel()
    {
        return new AssignDocketUser();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function getDataWhereIn($col,$val)
    {
        return $this->getModel()->whereIn($col,$val);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('assign_docket_user_id')) {
            $assign_docket_user = $this->getModel()->find($request->assign_docket_user_id);
        } else {
            $assign_docket_user = $this->getModel();
        }

        (!$request->has('name'))?:                         $assign_docket_user->name   = $request->name;
        (!$request->has('assigned_by'))?:                  $assign_docket_user->assigned_by  = $request->assigned_by;
        (!$request->has('from_date'))?:                    $assign_docket_user->from_date  = $request->from_date;
        (!$request->has('to_date'))?:                      $assign_docket_user->to_date  = $request->to_date;
        (!$request->has('bgcolor'))?:                      $assign_docket_user->bgcolor  = $request->bgcolor;

        $assign_docket_user->save();
        return $assign_docket_user;
    }

    public function deleteDataById($request = null)
    {
        $assign_docket_user = $this->getModel()->find($request->id);
        $assign_docket_user->delete();
        return $assign_docket_user;
    }
}
