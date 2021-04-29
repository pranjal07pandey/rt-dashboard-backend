<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\YesNoDocketsField;

class YesNoDocketsFieldRepository implements IRepository
{
    public function getModel()
    {
        return new YesNoDocketsField();
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
        if ($request->has('yes_no_dockets_field_id')) {
            $yes_no_dockets_field = $this->getModel()->find($request->yes_no_dockets_field_id);
        } else {
            $yes_no_dockets_field = $this->getModel();
        }

        (!$request->has('yes_no_field_id'))?:                           $yes_no_dockets_field->yes_no_field_id   = $request->yes_no_field_id;
        (!$request->has('docket_field_category_id'))?:                  $yes_no_dockets_field->docket_field_category_id  = $request->docket_field_category_id;
        (!$request->has('order'))?:                                     $yes_no_dockets_field->order  = $request->order;
        (!$request->has('required'))?:                                  $yes_no_dockets_field->required  = $request->required;
        (!$request->has('label'))?:                                     $yes_no_dockets_field->label   = $request->label;
        (!$request->has('csv_header'))?:                                $yes_no_dockets_field->csv_header  = $request->csv_header;
        (!$request->has('is_show'))?:                                   $yes_no_dockets_field->is_show  = $request->is_show;

        $yes_no_dockets_field->save();
        return $yes_no_dockets_field;
    }

    public function deleteDataById($request = null)
    {
        $yes_no_dockets_field = $this->getModel()->find($request->id);
        $yes_no_dockets_field->delete();
        return $yes_no_dockets_field;
    }
}
