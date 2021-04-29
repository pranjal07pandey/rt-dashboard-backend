<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketPrefillerValue;

class DocketPrefillerValueRepository implements IRepository
{
    public function getModel()
    {
        return new DocketPrefillerValue();
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
        if ($request->has('docket_prefiller_value_id')) {
            $docket_prefiller_value = $this->getModel()->find($request->docket_prefiller_value_id);
        } else {
            $docket_prefiller_value = $this->getModel();
        }

        (!$request->has('docket_prefiller_id'))?:                   $docket_prefiller_value->docket_prefiller_id   = $request->docket_prefiller_id;
        (!$request->has('label'))?:                                 $docket_prefiller_value->label  = $request->label;
        (!$request->has('index'))?:                                 $docket_prefiller_value->index  = $request->index;
        (!$request->has('root_id'))?:                               $docket_prefiller_value->root_id  = $request->root_id;

        $docket_prefiller_value->save();
        return $docket_prefiller_value;
    }

    public function deleteDataById($request = null)
    {
        $docket_prefiller_value = $this->getModel()->find($request->id);
        $docket_prefiller_value->delete();
        return $docket_prefiller_value;
    }
}
