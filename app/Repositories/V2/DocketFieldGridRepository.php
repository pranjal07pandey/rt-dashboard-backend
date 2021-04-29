<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\DocketFieldGrid;

class DocketFieldGridRepository implements IRepository
{
    public function getModel()
    {
        return new DocketFieldGrid();
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
        if ($request->has('docket_field_grid_id')) {
            $docket_field_grid = $this->getModel()->find($request->docket_field_grid_id);
        } else {
            $docket_field_grid = $this->getModel();
        }

        (!$request->has('docket_field_id'))?:                                   $docket_field_grid->docket_field_id   = $request->docket_field_id;
        (!$request->has('docket_field_category_id'))?:                          $docket_field_grid->docket_field_category_id  = $request->docket_field_category_id;
        (!$request->has('order'))?:                                             $docket_field_grid->order  = $request->order;
        (!$request->has('label'))?:                                             $docket_field_grid->label  = $request->label;
        (!$request->has('auto_field'))?:                                        $docket_field_grid->auto_field   = $request->auto_field;
        (!$request->has('is_dependent'))?:                                      $docket_field_grid->is_dependent  = $request->is_dependent;
        (!$request->has('docket_prefiller_id'))?:                               $docket_field_grid->docket_prefiller_id  = $request->docket_prefiller_id;
        (!$request->has('default_prefiller_id'))?:                              $docket_field_grid->default_prefiller_id  = $request->default_prefiller_id;
        (!$request->has('sumable'))?:                                           $docket_field_grid->sumable  = $request->sumable;
        (!$request->has('csv_header'))?:                                        $docket_field_grid->csv_header  = $request->csv_header;
        (!$request->has('is_show'))?:                                           $docket_field_grid->is_show  = $request->is_show;
        (!$request->has('export_value'))?:                                      $docket_field_grid->export_value  = $request->export_value;
        (!$request->has('send_copy_docket'))?:                                  $docket_field_grid->send_copy_docket  = $request->send_copy_docket;
        (!$request->has('echowise_id'))?:                                       $docket_field_grid->echowise_id  = $request->echowise_id;
        (!$request->has('selected_index_value'))?:                              $docket_field_grid->selected_index_value  = $request->selected_index_value;
        (!$request->has('is_emailed_subject'))?:                                $docket_field_grid->is_emailed_subject  = $request->is_emailed_subject;
        (!$request->has('time_format'))?:                                       $docket_field_grid->time_format  = $request->time_format;
        (!$request->has('required'))?:                                          $docket_field_grid->required  = $request->required;

        $docket_field_grid->save();
        return $docket_field_grid;
    }

    public function deleteDataById($request = null)
    {
        $docket_field_grid = $this->getModel()->find($request->id);
        $docket_field_grid->delete();
        return $docket_field_grid;
    }
}
