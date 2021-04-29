<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\V2\AmazoneBucket;
class SentDocketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $for,$recipientData,$approvalText,$isApproval,$isApproved,$canReject,$isReject,$status;

    public function __construct($resource,$for,$recipientData,$approvalText,$isApproval,$isApproved,$canReject,$isReject,$status)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->for = $for;
        $this->recipientData = $recipientData;
        $this->approvalText = $approvalText;
        $this->isApproval = $isApproval;
        $this->isApproved = $isApproved;
        $this->canReject = $canReject;
        $this->isReject = $isReject;
        $this->status = $status;
    }

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'companyDocketId'=>$this->formatted_id,
            'name'          => $this->senderUserInfo->first_name." ".$this->senderUserInfo->last_name,
            'company'       => $this->senderCompanyInfo->name,
            'profile'       => AmazoneBucket::url() . $this->senderUserInfo->for,
            'docket'        => $this->docketInfo->title,
            'recipients'    =>  $this->recipientData,
            'addedDate'     => Carbon::parse($this->created_at)->format('d-M-Y'),
            'approvalText'  =>  $this->approvalText,
            'isApproval'    =>  $this->isApproval,
            'isApproved'    =>  $this->isApproved,
            'canReject'     => $this->canReject,
            'isReject'      => $this->isReject,
            'isCancel'      => $this->is_cancel,
            'status'        =>  $this->status
        ];
    }
}
