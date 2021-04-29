<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketFieldGridValue;

class DocketFieldGridValueRepository implements IRepository
{
    public function getModel()
    {
        return new DocketFieldGridValue();
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
        if ($request->has('docket_field_grid_value_id')) {
            $docket_field_grid_value = $this->getModel()->find($request->docket_field_grid_value_id);
        } else {
            $docket_field_grid_value = $this->getModel();
        }

        (!$request->has('docket_id'))?:                                 $docket_field_grid_value->docket_id   = $request->docket_id;
        (!$request->has('is_email_docket'))?:                           $docket_field_grid_value->is_email_docket  = $request->is_email_docket;
        (!$request->has('docket_field_grid_id'))?:                      $docket_field_grid_value->docket_field_grid_id  = $request->docket_field_grid_id;
        (!$request->has('value'))?:                                     $docket_field_grid_value->value  = $request->value;
        (!$request->has('index'))?:                                     $docket_field_grid_value->index   = $request->index;
        (!$request->has('docket_field_id'))?:                           $docket_field_grid_value->docket_field_id  = $request->docket_field_id;

        $docket_field_grid_value->save();
        return $docket_field_grid_value;
    }

    public function deleteDataById($request = null)
    {
        $docket_field_grid_value = $this->getModel()->find($request->id);
        $docket_field_grid_value->delete();
        return $docket_field_grid_value;
    }
}
