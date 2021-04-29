<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DocketEmailConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $docketStatus,$userName,$company;

    public function __construct($resource, $docketStatus,$userName=null,$company=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->docketStatus = $docketStatus;
        $this->userName = $userName;
        $this->company = $company;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id'   =>  $this->receiver_user_id,
            'docketName' => $this->docketInfo->title,
            'sender' => ($this->userName != null) ? $this->userName : $this->receiverUserInfo->email,
            'company'   => ($this->company != null) ? $this->company : "",
            'dateAdded' =>  Carbon::parse($this->created_at)->format('d-M-Y'),
            'dateSorting' =>  Carbon::parse($this->created_at)->format('d-M-Y h:i:s'),
            'status'    => $this->docketStatus
        ];
    }
}
