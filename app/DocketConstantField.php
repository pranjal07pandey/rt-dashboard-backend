<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketConstantField extends Model
{
    protected $visible = ['csv_header','is_show'];

    public function exportMappingFieldCategoryInfo(){
        return $this->hasOne('App\ExportMappingFieldCategory','id','export_mapping_field_category_id');
    }

    public function docketField(){
        return $this->hasOne('App\DocketField','id','docket_field_id');
    }



}
