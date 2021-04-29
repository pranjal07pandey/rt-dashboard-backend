<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketPrefiller;

class DocketPrefillerRepository implements IRepository
{
    public function getModel()
    {
        return new DocketPrefiller();
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
        if ($request->has('docket_prefiller_id')) {
            $docket_prefiller = $this->getModel()->find($request->docket_prefiller_id);
        } else {
            $docket_prefiller = $this->getModel();
        }

        (!$request->has('title'))?:                                 $docket_prefiller->title   = $request->title;
        (!$request->has('company_id'))?:                            $docket_prefiller->company_id  = $request->company_id;
        (!$request->has('user_id'))?:                               $docket_prefiller->user_id  = $request->user_id;
        (!$request->has('type'))?:                                  $docket_prefiller->type  = $request->type;
        (!$request->has('is_integer'))?:                            $docket_prefiller->is_integer   = $request->is_integer;

        $docket_prefiller->save();
        return $docket_prefiller;
    }

    public function deleteDataById($request = null)
    {
        $docket_prefiller = $this->getModel()->find($request->id);
        $docket_prefiller->delete();
        return $docket_prefiller;
    }
}
