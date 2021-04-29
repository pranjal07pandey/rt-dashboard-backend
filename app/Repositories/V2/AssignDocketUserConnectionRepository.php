<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\AssignDocketUserConnection;

class AssignDocketUserConnectionRepository implements IRepository
{
    public function getModel()
    {
        return new AssignDocketUserConnection();
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
        if ($request->has('assign_docket_user_connection_id')) {
            $assign_docket_user_connection = $this->getModel()->find($request->assign_docket_user_connection_id);
        } else {
            $assign_docket_user_connection = $this->getModel();
        }

        (!$request->has('assign_docket_id'))?:                         $assign_docket_user_connection->assign_docket_id   = $request->assign_docket_id;
        (!$request->has('user_id'))?:                                  $assign_docket_user_connection->user_id  = $request->user_id;
        (!$request->has('machine_id'))?:                               $assign_docket_user_connection->machine_id  = $request->machine_id;
        (!$request->has('docket_id'))?:                                $assign_docket_user_connection->docket_id  = $request->docket_id;

        $assign_docket_user_connection->save();
        return $assign_docket_user_connection;
    }

    public function deleteDataById($request = null)
    {
        $assign_docket_user_connection = $this->getModel()->find($request->id);
        $assign_docket_user_connection->delete();
        return $assign_docket_user_connection;
    }
}
