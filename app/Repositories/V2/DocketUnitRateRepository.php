<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketUnitRate;

class DocketUnitRateRepository implements IRepository
{
    public function getModel()
    {
        return new DocketUnitRate();
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
        if ($request->has('docket_unit_rate_id')) {
            $docket_unit_rate = $this->getModel()->find($request->docket_unit_rate_id);
        } else {
            $docket_unit_rate = $this->getModel();
        }

        (!$request->has('docket_field_id'))?:                         $docket_unit_rate->docket_field_id   = $request->docket_field_id;
        (!$request->has('type'))?:                                    $docket_unit_rate->type  = $request->type;
        (!$request->has('label'))?:                                   $docket_unit_rate->label  = $request->label;
        (!$request->has('csv_header'))?:                              $docket_unit_rate->csv_header   = $request->csv_header;
        (!$request->has('is_show'))?:                                 $docket_unit_rate->is_show  = $request->is_show;

        $docket_unit_rate->save();
        return $docket_unit_rate;
    }

    public function deleteDataById($request = null)
    {
        $docket_unit_rate = $this->getModel()->find($request->id);
        $docket_unit_rate->delete();
        return $docket_unit_rate;
    }
}
