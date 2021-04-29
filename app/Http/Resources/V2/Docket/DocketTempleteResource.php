<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;

class DocketTempleteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $isDocketNumber,$docketFields;

    public function __construct($resource, $isDocketNumber,$docketFields)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->isDocketNumber = $isDocketNumber;
        $this->docketFields = $docketFields;
    }

    public function toArray($request)
    {
        return [
            'docket' => ['id' => $this->id,
                        'title' => $this->title,
                        'isDocketNumber'=>$this->isDocketNumber,
                        'subTitle' => $this->subTitle
                    ],
            'docket_field'  => $this->docketFields, 
            'invoiceable' => $this->invoiceable,
            'timer_attachement'=>$this->timer_attachement
        ];
    }
}
