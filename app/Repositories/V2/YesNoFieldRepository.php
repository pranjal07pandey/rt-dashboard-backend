<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\YesNoFields;

class YesNoFieldRepository implements IRepository
{
    public function getModel()
    {
        return new YesNoFields();
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
        if ($request->has('yes_no_field_id')) {
            $yes_no_field = $this->getModel()->find($request->yes_no_field_id);
        } else {
            $yes_no_field = $this->getModel();
        }

        (!$request->has('docket_field_id'))?:                           $yes_no_field->docket_field_id   = $request->docket_field_id;
        (!$request->has('label'))?:                                     $yes_no_field->label  = $request->label;
        (!$request->has('type'))?:                                      $yes_no_field->type  = $request->type;
        (!$request->has('explanation'))?:                               $yes_no_field->explanation  = $request->explanation;
        (!$request->has('colour'))?:                                    $yes_no_field->colour   = $request->colour;
        (!$request->has('icon_image'))?:                                $yes_no_field->icon_image  = $request->icon_image;
        (!$request->has('label_type'))?:                                $yes_no_field->label_type  = $request->label_type;
        (!$request->has('csv_header'))?:                                $yes_no_field->csv_header  = $request->csv_header;
        (!$request->has('is_show'))?:                                   $yes_no_field->is_show  = $request->is_show;

        $yes_no_field->save();
        return $yes_no_field;
    }

    public function deleteDataById($request = null)
    {
        $yes_no_field = $this->getModel()->find($request->id);
        $yes_no_field->delete();
        return $yes_no_field;
    }
}
