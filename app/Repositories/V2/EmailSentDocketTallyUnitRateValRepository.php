<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentDocketTallyUnitRateVal;

class EmailSentDocketTallyUnitRateValRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentDocketTallyUnitRateVal();
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
        if ($request->has('email_sent_docket_tally_unit_rate_val_id')) {
            $email_sent_docket_tally_unit_rate_val = $this->getModel()->find($request->email_sent_docket_tally_unit_rate_val_id);
        } else {
            $email_sent_docket_tally_unit_rate_val = $this->getModel();
        }

        (!$request->has('sent_docket_value_id'))?:                             $email_sent_docket_tally_unit_rate_val->sent_docket_value_id   = $request->sent_docket_value_id;
        (!$request->has('docket_tally_unit_rate_id'))?:                              $email_sent_docket_tally_unit_rate_val->docket_tally_unit_rate_id  = $request->docket_tally_unit_rate_id;
        (!$request->has('value'))?:                                            $email_sent_docket_tally_unit_rate_val->value  = $request->value;
        (!$request->has('label'))?:                                            $email_sent_docket_tally_unit_rate_val->label  = $request->label;

        $email_sent_docket_tally_unit_rate_val->save();
        return $email_sent_docket_tally_unit_rate_val;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_docket_tally_unit_rate_val = $this->getModel()->find($request->id);
        $email_sent_docket_tally_unit_rate_val->delete();
        return $email_sent_docket_tally_unit_rate_val;
    }
}
