<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketGridAutoPrefiller;

class DocketGridAutoPrefillerRepository implements IRepository
{
    public function getModel()
    {
        return new DocketGridAutoPrefiller();
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
        if ($request->has('docket_grid_auto_prefiller_id')) {
            $docket_grid_auto_prefiller = $this->getModel()->find($request->docket_grid_auto_prefiller_id);
        } else {
            $docket_grid_auto_prefiller = $this->getModel();
        }

        (!$request->has('grid_field_id'))?:                                     $docket_grid_auto_prefiller->grid_field_id   = $request->grid_field_id;
        (!$request->has('index'))?:                                             $docket_grid_auto_prefiller->index  = $request->index;
        (!$request->has('link_grid_field_id'))?:                                $docket_grid_auto_prefiller->link_grid_field_id  = $request->link_grid_field_id;
        (!$request->has('docket_field_id'))?:                                   $docket_grid_auto_prefiller->docket_field_id   = $request->docket_field_id;
        (!$request->has('docket_field_id'))?:                                   $docket_grid_auto_prefiller->docket_field_id   = $request->docket_field_id;

        $docket_grid_auto_prefiller->save();
        return $docket_grid_auto_prefiller;
    }

    public function deleteDataById($request = null)
    {
        $docket_grid_auto_prefiller = $this->getModel()->find($request->id);
        $docket_grid_auto_prefiller->delete();
        return $docket_grid_auto_prefiller;
    }
}
