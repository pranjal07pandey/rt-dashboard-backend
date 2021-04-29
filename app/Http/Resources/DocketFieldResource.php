<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocketFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'docket'=>$this->docket,
            'docket_field_category_id' => $this->docket_field_category_id,
            'label'=>$this->label,
            'order'=>$this->order,
            'required'=>$this->required,
            'default_prefiller_id'=>$this->default_prefiller_id,
            'is_emailed_subject'=>$this->is_emailed_subject,
            'is_dependent'=>$this->is_dependent,
            'docket_prefiller_id'=>$this->docket_prefiller_id,
            'send_copy_docket'=>$this->send_copy_docket,
        ];
    }
}
