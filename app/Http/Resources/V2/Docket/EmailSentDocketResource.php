<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Helpers\V2\AmazoneBucket;
class EmailSentDocketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $userId,$userName,$company,$recipientData,$approvalText,$docketStatus,$companyDocketId;

    public function __construct($resource, $userId,$userName,$company,$recipientData,$approvalText,$docketStatus,$companyDocketId)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->company = $company;
        $this->recipientData = $recipientData;
        $this->approvalText = $approvalText;
        $this->docketStatus = $docketStatus;
        $this->companyDocketId = $companyDocketId;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'companyDocketId'=>$this->companyDocketId,
            'user_id'   =>  $this->userId,
            'docketName' => $this->docketInfo->title,
            'docketId' => $this->docketInfo->id,
            'sender' => $this->userName,
            'profile' => AmazoneBucket::url() . $this->senderUserInfo->image,
            'company'   =>  $this->company,
            'recipient' => $this->recipientData,
            'recipients' => $this->recipientData,
            'dateAdded' =>  Carbon::parse($this->created_at)->format('d-M-Y'),
            'dateSorting' =>  Carbon::parse($this->created_at)->format('d-M-Y H:i:s'),
            'approvalText'  =>  $this->approvalText,
            'isApproved'    =>  $this->status,
            'status'    => $this->docketStatus
        ];
    }
}
