<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;

class DocketTempleteDetailDocketFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $subFields,$isEmailedSubject,$modularField,$sumableStatus;

    public function __construct($resource, $subFields,$isEmailedSubject=null,$modularField=null,$sumableStatus=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->subFields = $subFields;
        $this->isEmailedSubject = $isEmailedSubject;
        $this->modularField = $modularField;
        $this->sumableStatus = $sumableStatus;
    }

    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'docket_field_category_id' => $this->docket_field_category_id,
            'docket_field_category' => $this->fieldCategoryInfo->title,
            'label' => $this->label,
            'order' => $this->order,
            'required' => $this->required,
            'subField' => $this->subFields,
        ];

        if($this->docket_field_category_id == 22){
            $response['time_required'] = (@$this->docketFieldDateOption->time == null) ? 0: @$this->docketFieldDateOption->time;
            $response['is_emailed_subject'] = ($this->isEmailedSubject == true) ? 1: 0;
            $response['modularGrid'] =  $this->modularField;
            $response['sumable'] =  $this->sumableStatus;
        }else{
            if($this->docket_field_category_id == 15){
                $response['documentSubField'] = $this->subFields;
            }elseif($this->docket_field_category_id == 20 || $this->docket_field_category_id == 28 || $this->docket_field_category_id == 13){
                $response['subFieldUnitRate'] = $this->subFields;
            }else{
                $response['yesNoSubField'] = $this->subFields;
            }
        }
        return $response;
    }
}
