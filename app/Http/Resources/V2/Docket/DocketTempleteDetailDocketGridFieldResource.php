<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;

class DocketTempleteDetailDocketGridFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $canAddChild,$docketPreFillerRowIndependent,$gridManualTimer,$formulaArray,$for;

    public function __construct($resource,$canAddChild,$docketPreFillerRowIndependent,$gridManualTimer,$formulaArray,$for=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->canAddChild = $canAddChild;
        $this->docketPreFillerRowIndependent = $docketPreFillerRowIndependent;
        $this->gridManualTimer = $gridManualTimer;
        $this->gridManualTimer = $gridManualTimer;
        $this->formulaArray = $formulaArray;
        $this->for = $for;
    }

    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'docket_field_id' => $this->docket_field_id,
            'docket_field_category_id' => $this->docketFieldCategory->id,
            'docket_field_category_label' => $this->docketFieldCategory->title,
            'label' => $this->label,
            'order' => $this->order,
            'required' => $this->required,
            'prefiller_data' => [
                'autoPrefiller'=>$this->auto_field,
                'isDependent'=>$this->is_dependent,
                'canAddChild'=>$this->canAddChild,
                'prefiller'=>$this->docketPreFillerRowIndependent['datas']
            ],
            'default_value' => ($this->docketPreFillerRowIndependent['defaultPrefillerValue']=="")? "" : implode(",",$this->docketPreFillerRowIndependent['prefillerArray']),
            'subField' => $this->gridManualTimer,
            'formula'=>  @$this->formulaArray,
            'sumable' => ($this->sumable== 1)? true : false,
        ];
        if($this->for == 'v1DocketTemplete'){
            $response['is_emailed_subject'] = $this->is_emailed_subject;
            $response['manualTimerSubField'] = $this->gridManualTimer;
            $response['send_copy_docket']=  $this->send_copy_docket;
        }
        return $response;
    }
}
