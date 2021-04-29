<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentDocketValue;

class EmailSentDocketValueRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentDocketValue();
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
        if ($request->has('email_sent_docket_values_id')) {
            $email_sent_docket_values = $this->getModel()->find($request->email_sent_docket_values__id);
        } else {
            $email_sent_docket_values = $this->getModel();
        }

        (!$request->has('email_sent_docket_id'))?:                  $email_sent_docket_values->email_sent_docket_id   = $request->email_sent_docket_id;
        (!$request->has('docket_field_id'))?:                       $email_sent_docket_values->docket_field_id  = $request->docket_field_id;
        (!$request->has('value'))?:                                 $email_sent_docket_values->value  = $request->value;
        (!$request->has('label'))?:                                 $email_sent_docket_values->label  = $request->label;
        (!$request->has('is_hidden'))?:                             $email_sent_docket_values->is_hidden  = $request->is_hidden;

        $email_sent_docket_values->save();
        return $email_sent_docket_values;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_docket_values = $this->getModel()->find($request->id);
        $email_sent_docket_values->delete();
        return $email_sent_docket_values;
    }
}
