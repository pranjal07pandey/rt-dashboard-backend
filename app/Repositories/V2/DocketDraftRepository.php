<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketDraft;

class DocketDraftRepository implements IRepository
{
    public function getModel()
    {
        return new DocketDraft();
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
        if ($request->has('docket_draft_id')) {
            $docket_draft = $this->getModel()->find($request->docket_draft__id);
        } else {
            $docket_draft = $this->getModel();
        }

        (!$request->has('user_id'))?:                            $docket_draft->user_id   = $request->user_id;
        (!$request->has('docket_id'))?:                          $docket_draft->docket_id  = $request->docket_id;
        (!$request->has('value'))?:                              $docket_draft->value  = $request->value;

        $docket_draft->save();
        return $docket_draft;
    }

    public function deleteDataById($request = null)
    {
        $docket_draft = $this->getModel()->find($request->id);
        $docket_draft->delete();
        return $docket_draft;
    }
}
