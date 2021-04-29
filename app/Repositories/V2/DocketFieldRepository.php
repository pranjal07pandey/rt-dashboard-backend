<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketField;

class DocketFieldRepository implements IRepository
{
    public function getModel()
    {
        return new DocketField();
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
        if ($request->has('docket_field_id')) {
            $docket_field = $this->getModel()->find($request->docket_field_id);
        } else {
            $docket_field = $this->getModel();
        }

        (!$request->has('docket_id'))?:                                 $docket_field->docket_id   = $request->docket_id;
        (!$request->has('docket_field_category_id'))?:                  $docket_field->docket_field_category_id  = $request->docket_field_category_id;
        (!$request->has('order'))?:                                     $docket_field->order  = $request->order;
        (!$request->has('label'))?:                                     $docket_field->label  = $request->label;
        (!$request->has('required'))?:                                  $docket_field->required   = $request->required;
        (!$request->has('is_hidden'))?:                                 $docket_field->is_hidden  = $request->is_hidden;
        (!$request->has('default_prefiller_id'))?:                      $docket_field->default_prefiller_id  = $request->default_prefiller_id;
        (!$request->has('is_emailed_subject'))?:                        $docket_field->is_emailed_subject  = $request->is_emailed_subject;
        (!$request->has('is_dependent'))?:                              $docket_field->is_dependent   = $request->is_dependent;
        (!$request->has('docket_prefiller_id'))?:                       $docket_field->docket_prefiller_id  = $request->docket_prefiller_id;
        (!$request->has('csv_header'))?:                                $docket_field->csv_header  = $request->csv_header;
        (!$request->has('is_show'))?:                                   $docket_field->is_show  = $request->is_show;
        (!$request->has('send_copy_docket'))?:                          $docket_field->send_copy_docket   = $request->send_copy_docket;
        (!$request->has('echowise_id'))?:                               $docket_field->echowise_id  = $request->echowise_id;
        (!$request->has('selected_index'))?:                            $docket_field->selected_index  = $request->selected_index;
        (!$request->has('time_format'))?:                               $docket_field->time_format  = $request->time_format;

        $docket_field->save();
        return $docket_field;
    }

    public function deleteDataById($request = null)
    {
        $docket_field = $this->getModel()->find($request->id);
        $docket_field->delete();
        return $docket_field;
    }
}
