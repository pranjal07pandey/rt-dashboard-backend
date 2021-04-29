<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketTallyableUnitRate;

class DocketTallyableUnitRateRepository implements IRepository
{
    public function getModel()
    {
        return new DocketTallyableUnitRate();
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
        if ($request->has('docket_tallyable_unit_rate_id')) {
            $docket_tallyable_unit_rate = $this->getModel()->find($request->docket_tallyable_unit_rate_id);
        } else {
            $docket_tallyable_unit_rate = $this->getModel();
        }

        (!$request->has('docket_field_id'))?:                       $docket_tallyable_unit_rate->docket_field_id   = $request->docket_field_id;
        (!$request->has('label'))?:                                 $docket_tallyable_unit_rate->label  = $request->label;
        (!$request->has('type'))?:                                  $docket_tallyable_unit_rate->type  = $request->type;
        (!$request->has('csv_header'))?:                            $docket_tallyable_unit_rate->csv_header  = $request->csv_header;
        (!$request->has('is_show'))?:                               $docket_tallyable_unit_rate->is_show   = $request->is_show;

        $docket_tallyable_unit_rate->save();
        return $docket_tallyable_unit_rate;
    }

    public function deleteDataById($request = null)
    {
        $docket_tallyable_unit_rate = $this->getModel()->find($request->id);
        $docket_tallyable_unit_rate->delete();
        return $docket_tallyable_unit_rate;
    }
}
