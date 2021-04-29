<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketDraftsAssign;

class DocketDraftsAssignRepository implements IRepository
{
    public function getModel()
    {
        return new DocketDraftsAssign();
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
        if ($request->has('docket_draft_assign_id')) {
            $docket_draft_assign = $this->getModel()->find($request->docket_draft_assign__id);
        } else {
            $docket_draft_assign = $this->getModel();
        }

        (!$request->has('assign_docket_user_id'))?:              $docket_draft_assign->assign_docket_user_id   = $request->assign_docket_user_id;
        (!$request->has('docket_id'))?:                          $docket_draft_assign->docket_id  = $request->docket_id;
        (!$request->has('docket_draft_id'))?:                    $docket_draft_assign->docket_draft_id  = $request->docket_draft_id;
        (!$request->has('user_id'))?:                            $docket_draft_assign->user_id  = $request->user_id;
        (!$request->has('machine_id'))?:                         $docket_draft_assign->machine_id  = $request->machine_id;

        $docket_draft_assign->save();
        return $docket_draft_assign;
    }

    public function deleteDataById($request = null)
    {
        $docket_draft_assign = $this->getModel()->find($request->id);
        $docket_draft_assign->delete();
        return $docket_draft_assign;
    }
}
