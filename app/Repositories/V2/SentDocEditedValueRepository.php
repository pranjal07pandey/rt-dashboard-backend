<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDocEditedValue;

class SentDocEditedValueRepository implements IRepository
{
    public function getModel()
    {
        return new SentDocEditedValue();
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
        if ($request->has('sent_doc_edited_value_id')) {
            $sent_doc_edited_value = $this->getModel()->find($request->sent_doc_edited_value__id);
        } else {
            $sent_doc_edited_value = $this->getModel();
        }

        (!$request->has('sent_docket_value_id'))?:                  $sent_doc_edited_value->sent_docket_value_id   = $request->sent_docket_value_id;
        (!$request->has('value'))?:                                 $sent_doc_edited_value->value  = $request->value;

        $sent_doc_edited_value->save();
        return $sent_doc_edited_value;
    }

    public function deleteDataById($request = null)
    {
        $sent_doc_edited_value = $this->getModel()->find($request->id);
        $sent_doc_edited_value->delete();
        return $sent_doc_edited_value;
    }
}
