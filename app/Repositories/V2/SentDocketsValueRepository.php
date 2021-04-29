<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDocketsValue;

class SentDocketsValueRepository implements IRepository
{
    public function getModel()
    {
        return new SentDocketsValue();
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
        if ($request->has('sent_dockets_value_id')) {
            $sent_dockets_value = $this->getModel()->find($request->sent_dockets_value__id);
        } else {
            $sent_dockets_value = $this->getModel();
        }

        (!$request->has('sent_docket_id'))?:                 $sent_dockets_value->sent_docket_id   = $request->sent_docket_id;
        (!$request->has('docket_field_id'))?:                $sent_dockets_value->docket_field_id  = $request->docket_field_id;
        (!$request->has('value'))?:                          $sent_dockets_value->value  = $request->value;
        (!$request->has('label'))?:                          $sent_dockets_value->label  = $request->label;
        (!$request->has('last_edited_value_id'))?:           $sent_dockets_value->last_edited_value_id   = $request->last_edited_value_id;

        $sent_dockets_value->save();
        return $sent_dockets_value;
    }

    public function deleteDataById($request = null)
    {
        $sent_dockets_value = $this->getModel()->find($request->id);
        $sent_dockets_value->delete();
        return $sent_dockets_value;
    }
}
