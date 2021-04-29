<?php

namespace App\Http\Resources\V2\Timer;

use Illuminate\Http\Resources\Json\JsonResource;

class TimerBreakResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $image;

    public function __construct($resource, $image)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->image = $image;
    }

    public function toArray($request)
    {
        // $dockets[]   =   array('id' => $result->id,
        //     'companyDocketId'=>'rt-'.$result->sender_company_id.'-doc-'.$result->company_docket_id,
        //     'user_id'   =>  $userId,
        //     'docketName' => $result->docketInfo->title,
        //     'docketId' => $result->docketInfo->id,
        //     'sender' => $userName,
        //     'profile' => asset($result->senderUserInfo->image),
        //     'company'   =>  $company,
        //     'recipient' => $recipientData,
        //     'recipients' => $recipientData,
        //     'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
        //     'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
        //     'approvalText'  =>  $approvalText,
        //     'isApproval'    =>  $isApproval,
        //     'isApproved'    =>  $isApproved,
        //     'canReject'=>$canReject,
        //     'isReject' => $isReject,
        //     'status'    => $docketStatus);
        return [
            'id' => $this->id,
            'timer_id' => $this->timer_id,
            'location' => $this->location,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'time_started' => $this->time_started,
            'time_finished' => $this->time_finished,
            'reason' => $this->reason,
            'images' =>$this->image,
        ];
    }
}
