<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketFieldGridLabel;

class DocketFieldGridLabelRepository implements IRepository
{
    public function getModel()
    {
        return new DocketFieldGridLabel();
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
        if ($request->has('docket_field_grid_label_id')) {
            $docket_field_grid_label = $this->getModel()->find($request->docket_field_grid_label_id);
        } else {
            $docket_field_grid_label = $this->getModel();
        }

        (!$request->has('docket_id'))?:                                 $docket_field_grid_label->docket_id   = $request->docket_id;
        (!$request->has('is_email_docket'))?:                           $docket_field_grid_label->is_email_docket  = $request->is_email_docket;
        (!$request->has('docket_field_grid_id'))?:                      $docket_field_grid_label->docket_field_grid_id  = $request->docket_field_grid_id;
        (!$request->has('value'))?:                                     $docket_field_grid_label->value  = $request->value;
        (!$request->has('index'))?:                                     $docket_field_grid_label->index   = $request->index;
        (!$request->has('docket_field_id'))?:                           $docket_field_grid_label->docket_field_id  = $request->docket_field_id;

        $docket_field_grid_label->save();
        return $docket_field_grid_label;
    }

    public function deleteDataById($request = null)
    {
        $docket_field_grid_label = $this->getModel()->find($request->id);
        $docket_field_grid_label->delete();
        return $docket_field_grid_label;
    }
}
