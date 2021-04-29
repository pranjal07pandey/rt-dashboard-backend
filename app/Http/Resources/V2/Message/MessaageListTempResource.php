<?php

namespace App\Http\Resources\V2\Message;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class MessaageListTempResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected $groupProfile,$groupTitle,$memberNumber,$lastMessages,$dateSorting;
    public function __construct($resource,$groupProfile,$groupTitle,$memberNumber,$lastMessages=null,$dateSorting=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->groupProfile = $groupProfile;
        $this->groupTitle = $groupTitle;
        $this->memberNumber = $memberNumber;
        $this->lastMessages = $lastMessages;
        $this->dateSorting = $dateSorting;
    }

    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'date' => Carbon::parse($this->created_date)->format('d-M-Y'),
            'profile' => $this->groupProfile,
            'title' => $this->groupTitle,
            'member' => $this->memberNumber,
        ];

        ($this->lastMessages != null) ? $response['last_messages'] = $this->lastMessages : "";
        ($this->dateSorting != null) ? $response['sortingDate'] = $this->dateSorting : "";

        if ($this->title == null) {
            $response['type'] = 1;
        } else {
            $response['type'] = 2;
        }

        return $response;
    }
}
