<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentEmailDocValYesNoValue;

class SentEmailDocValYesNoValueRepository implements IRepository
{
    public function getModel()
    {
        return new SentEmailDocValYesNoValue();
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
        if ($request->has('sent_email_doc_val_yes_no_value_id')) {
            $sent_email_doc_val_yes_no_value = $this->getModel()->find($request->sent_email_doc_val_yes_no_value__id);
        } else {
            $sent_email_doc_val_yes_no_value = $this->getModel();
        }

        (!$request->has('email_sent_docket_value_id'))?:        $sent_email_doc_val_yes_no_value->email_sent_docket_value_id   = $request->email_sent_docket_value_id;
        (!$request->has('yes_no_docket_field_id'))?:            $sent_email_doc_val_yes_no_value->yes_no_docket_field_id  = $request->yes_no_docket_field_id;
        (!$request->has('label'))?:                             $sent_email_doc_val_yes_no_value->label  = $request->label;
        (!$request->has('value'))?:                             $sent_email_doc_val_yes_no_value->value  = $request->value;
        (!$request->has('required'))?:                          $sent_email_doc_val_yes_no_value->required  = $request->required;

        $sent_email_doc_val_yes_no_value->save();
        return $sent_email_doc_val_yes_no_value;
    }

    public function deleteDataById($request = null)
    {
        $sent_email_doc_val_yes_no_value = $this->getModel()->find($request->id);
        $sent_email_doc_val_yes_no_value->delete();
        return $sent_email_doc_val_yes_no_value;
    }
}
