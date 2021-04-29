<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketFiledPreFiller;

class DocketFiledPreFillerRepository implements IRepository
{
    public function getModel()
    {
        return new DocketFiledPreFiller();
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
        if ($request->has('docket_filed_prefiller_id')) {
            $docket_filed_prefiller = $this->getModel()->find($request->docket_filed_prefiller_id);
        } else {
            $docket_filed_prefiller = $this->getModel();
        }

        (!$request->has('docket_field_id'))?:                           $docket_filed_prefiller->docket_field_id   = $request->docket_field_id;
        (!$request->has('value'))?:                                     $docket_filed_prefiller->value  = $request->value;
        (!$request->has('index'))?:                                     $docket_filed_prefiller->index  = $request->index;
        (!$request->has('root_id'))?:                                   $docket_filed_prefiller->root_id   = $request->root_id;

        $docket_filed_prefiller->save();
        return $docket_filed_prefiller;
    }

    public function deleteDataById($request = null)
    {
        $docket_filed_prefiller = $this->getModel()->find($request->id);
        $docket_filed_prefiller->delete();
        return $docket_filed_prefiller;
    }
}
