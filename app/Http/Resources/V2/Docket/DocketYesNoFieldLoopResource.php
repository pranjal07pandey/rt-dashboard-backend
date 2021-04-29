<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\V2\AmazoneBucket;
class DocketYesNoFieldLoopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $subDocket;

    public function __construct($resource, $subDocket)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->subDocket = $subDocket;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type,
            'colour' => $this->colour,
            'explanation' => $this->explanation,
            'docket_field_id' => $this->docket_field_id,
            'label_icon' => AmazoneBucket::url() . $this->icon_image,
            'label_type' => $this->label_type,
            'subDocket' => ($this->explanation == 0) ? [] : $this->subDocket,
        ];
    }
}
