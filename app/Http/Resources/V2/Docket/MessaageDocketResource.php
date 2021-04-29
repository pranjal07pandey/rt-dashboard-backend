<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Helpers\V2\AmazoneBucket;
class MessaageDocketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $recipientData,$approvalText,$isApproval,$isApproved,$canReject,$isReject,$status;

    public function __construct($resource,$recipientData,$approvalText,$isApproval,$isApproved,$canReject,$isReject,$status)
    {
        parent::__construct($resource);
        $this->resource = $resource;
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'sender' => $this->senderUserInfo->first_name . " " . $this->senderUserInfo->last_name,
            'profile' => AmazoneBucket::url() . $this->senderUserInfo->image,
            'docketName' => $this->docketInfo->title,
            'company' => $this->senderCompanyInfo->name,
            'recipients' => $this->recipientData,
            'dateAdded' => Carbon::parse($this->created_at)->format('d-M-Y'),
            'dateSorting' => Carbon::parse($this->created_at)->format('d-M-Y H:i:s'),
            'approvalText' => $this->approvalText,
            'isApproval' => $this->isApproval,
            'isApproved' => $this->isApproved,
            'canReject'=>$this->canReject,
            'isReject' => $this->isReject,
            'status' => $this->status
        ];
    }
}
