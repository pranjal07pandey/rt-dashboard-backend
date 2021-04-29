<?php

namespace App\Http\Resources\V2\Message;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageGroupUserInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected $profile;
    public function __construct($resource,$profile)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->profile = $profile;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->userInfo->id,
            'name' => $this->userInfo->first_name . ' ' . $this->userInfo->last_name,
            'profile' => $this->profile
        ];
    }
}
