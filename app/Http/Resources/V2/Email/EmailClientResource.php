<?php

namespace App\Http\Resources\V2\Email;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->resource = $resource;
    }

    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'email_user_id'=>$this->emailUser->id,
            'email'          => $this->emailUser->email,
            'full_name'=> $this->full_name,
            'company_name'         =>  $this->company_name,
            'company_address'  => $this->company_address,
        ];
    }
}
