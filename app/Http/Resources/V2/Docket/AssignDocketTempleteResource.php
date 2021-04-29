<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignDocketTempleteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $defaultRecipien,$for;

    public function __construct($resource,$for, $defaultRecipien)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->for = $for;
        $this->defaultRecipien = $defaultRecipien;
    }

    public function toArray($request)
    {
        $response = [
            'id'   =>  $this->docket_id,
            'title'    =>  $this->docketInfo->title,
            'docket_approval_type' => $this->docketInfo->docketApprovalType,
            'default_recipient'=> $this->defaultRecipien
        ];
        return $response;
    }
}
