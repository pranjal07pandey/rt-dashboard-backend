<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketGridPrefiller;

class DocketGridPrefillerRepository implements IRepository
{
    public function getModel()
    {
        return new DocketGridPrefiller();
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
        if ($request->has('docket_grid_prefiller_id')) {
            $docket_grid_prefiller = $this->getModel()->find($request->docket_grid_prefiller_id);
        } else {
            $docket_grid_prefiller = $this->getModel();
        }

        (!$request->has('docket_field_grid_id'))?:                      $docket_grid_prefiller->docket_field_grid_id   = $request->docket_field_grid_id;
        (!$request->has('value'))?:                                     $docket_grid_prefiller->value  = $request->value;
        (!$request->has('index'))?:                                     $docket_grid_prefiller->index  = $request->index;
        (!$request->has('root_id'))?:                                   $docket_grid_prefiller->root_id   = $request->root_id;

        $docket_grid_prefiller->save();
        return $docket_grid_prefiller;
    }

    public function deleteDataById($request = null)
    {
        $docket_grid_prefiller = $this->getModel()->find($request->id);
        $docket_grid_prefiller->delete();
        return $docket_grid_prefiller;
    }
}
