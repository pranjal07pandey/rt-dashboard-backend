<?php

namespace App\Http\Resources\V2\Docket;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DocketDetailByIdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $for,$totalRecipientApproved,$totalRecipientApprovals,$authUserId,$recipentId,$canReject,$isReject,
            $isApproved,$approvedUsers,$rejectRecipent,$nonApprovedUsers;

    public function __construct($resource,$for, $totalRecipientApproved, $totalRecipientApprovals, $authUserId, $recipentId, 
                $canReject,$isReject, $isApproved, $approvedUsers, $rejectRecipent, $nonApprovedUsers)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->for = $for;
        $this->totalRecipientApproved = $totalRecipientApproved;
        $this->totalRecipientApprovals = $totalRecipientApprovals;
        $this->authUserId = $authUserId;
        $this->recipentId = $recipentId;
        $this->canReject = $canReject;
        $this->isReject = $isReject;
        $this->isApproved = $isApproved;
        $this->approvedUsers = $approvedUsers;
        $this->rejectRecipent = $rejectRecipent;
        $this->nonApprovedUsers = $nonApprovedUsers;
    }

    public function toArray($request)
    {
        $response = [
            'receivedTime'=> Carbon::parse($this->created_at)->format('d/m/Y h:i A')." AEDT",
            'status'=> $this->totalRecipientApproved."/".$this->totalRecipientApprovals,
            'docketStatus'=> ($this->user_id == $this->authUserId)?"sent":"received",
            'approvedUser' => $this->approvedUsers,
            'docket_approval_type'=>$this->docketApprovalType,
            'nonApprovedUser' => $this->nonApprovedUsers
        ];

        if($this->for == 'docket'){
            $response['receiver_id'] = $this->recipentId;
            $response['isCancelled'] = $this->is_cancel;
            $response['can_reject'] = $this->canReject;
            $response['isRejected'] = $this->isReject;
            $response['isApproved'] = $this->isApproved;
            $response['reject_user'] = $this->rejectRecipent;
        }
        return $response;
    }
}
