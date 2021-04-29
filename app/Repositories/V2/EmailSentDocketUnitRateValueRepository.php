<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSnetDocketUnitRateValue;

class EmailSentDocketUnitRateValueRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSnetDocketUnitRateValue();
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
        if ($request->has('email_sent_docket_unit_rate_value_id')) {
            $email_sent_docket_unit_rate_value = $this->getModel()->find($request->email_sent_docket_unit_rate_value_id);
        } else {
            $email_sent_docket_unit_rate_value = $this->getModel();
        }

        (!$request->has('sent_docket_value_id'))?:                             $email_sent_docket_unit_rate_value->sent_docket_value_id   = $request->sent_docket_value_id;
        (!$request->has('docket_unit_rate_id'))?:                              $email_sent_docket_unit_rate_value->docket_unit_rate_id  = $request->docket_unit_rate_id;
        (!$request->has('value'))?:                                            $email_sent_docket_unit_rate_value->value  = $request->value;
        (!$request->has('label'))?:                                            $email_sent_docket_unit_rate_value->label  = $request->label;

        $email_sent_docket_unit_rate_value->save();
        return $email_sent_docket_unit_rate_value;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_docket_unit_rate_value = $this->getModel()->find($request->id);
        $email_sent_docket_unit_rate_value->delete();
        return $email_sent_docket_unit_rate_value;
    }
}
