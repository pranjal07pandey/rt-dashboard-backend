<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;

class DocketCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $labelData,$valueData,$subFiled,$docketFieldCategory;

    public function __construct($resource, $labelData,$valueData,$subFiled,$docketFieldCategory=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->labelData = $labelData;
        $this->valueData = $valueData;
        $this->subFiled = $subFiled;
        $this->docketFieldCategory = $docketFieldCategory;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'docket_field_category_id' => ($this->docketFieldCategory) ? $this->docket_field_category_id : $this->docketFieldInfo->docket_field_category_id,
            'docket_field_category' => ($this->docketFieldCategory) ? $this->docketFieldCategory : $this->labelData,
            'label' => $this->labelData,
            'value' =>  $this->valueData,
            'subFiled' => $this->subFiled
        ];
    }
}
