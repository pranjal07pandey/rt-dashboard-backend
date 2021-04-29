<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketFieldFooter;

class DocketFieldFooterRepository implements IRepository
{
    public function getModel()
    {
        return new DocketFieldFooter();
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
        if ($request->has('docket_field_footer_id')) {
            $docket_field_footer = $this->getModel()->find($request->docket_field_footer_id);
        } else {
            $docket_field_footer = $this->getModel();
        }

        (!$request->has('docket_id'))?:                                 $docket_field_footer->docket_id   = $request->docket_id;
        (!$request->has('value'))?:                                     $docket_field_footer->value  = $request->value;
        (!$request->has('field_id'))?:                                  $docket_field_footer->field_id  = $request->field_id;

        $docket_field_footer->save();
        return $docket_field_footer;
    }

    public function deleteDataById($request = null)
    {
        $docket_field_footer = $this->getModel()->find($request->id);
        $docket_field_footer->delete();
        return $docket_field_footer;
    }
}
