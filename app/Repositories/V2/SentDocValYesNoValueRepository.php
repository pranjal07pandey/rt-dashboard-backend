<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDocValYesNoValue;

class SentDocValYesNoValueRepository implements IRepository
{
    public function getModel()
    {
        return new SentDocValYesNoValue();
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
        if ($request->has('sent_doc_val_yes_no_value_id')) {
            $sent_doc_val_yes_no_value = $this->getModel()->find($request->sent_doc_val_yes_no_value__id);
        } else {
            $sent_doc_val_yes_no_value = $this->getModel();
        }

        (!$request->has('sent_docket_value_id'))?:          $sent_doc_val_yes_no_value->sent_docket_value_id   = $request->sent_docket_value_id;
        (!$request->has('yes_no_docket_field_id'))?:        $sent_doc_val_yes_no_value->yes_no_docket_field_id  = $request->yes_no_docket_field_id;
        (!$request->has('label'))?:                         $sent_doc_val_yes_no_value->label  = $request->label;
        (!$request->has('value'))?:                         $sent_doc_val_yes_no_value->value  = $request->value;
        (!$request->has('required'))?:                      $sent_doc_val_yes_no_value->required  = $request->required;

        $sent_doc_val_yes_no_value->save();
        return $sent_doc_val_yes_no_value;
    }

    public function deleteDataById($request = null)
    {
        $sent_doc_val_yes_no_value = $this->getModel()->find($request->id);
        $sent_doc_val_yes_no_value->delete();
        return $sent_doc_val_yes_no_value;
    }
}
