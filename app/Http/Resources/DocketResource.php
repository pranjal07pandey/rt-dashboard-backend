<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DocketResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
     public   $defaultRecipient = null;

     public function __construct($resource, $defaultRecipient)
     {
         parent::__construct($resource);
         $this->resource = $resource;
         $this->defaultRecipient = $defaultRecipient;

     }

    public function toArray($request)
    {
            return [
                'id' => $this->id,
                'title' => $this->title,
                'invoiceable'=>$this->invoiceable,
                'timer_attachement'=>$this->timer_attachement,
                'xero_timesheet'=>$this->xero_timesheet,
                'is_archive'=>$this->is_archive,
                'hide_prefix'=>$this->hide_prefix,
                'is_docket_number'=>$this->is_docket_number,
                'docket_approval_type'=>$this->docketApprovalType,
                'default_recipient'=> $this->defaultRecipient
            ];

    }
}
