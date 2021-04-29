<?php

namespace App\Http\Resources\V2\Timer;
use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

class TimerCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected $image,$type,$time_started,$time_finished;

    public function __construct($resource,$image=null,$type = null,$time_started=null,$time_finished=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->image = $image;
        $this->type = $type;
        $this->time_started = $time_started;
        $this->time_finished = $time_finished;
    }

    public function toArray($request)
    {
        $response = [
            'id'=>$this->id,
            'timer_id' => $this->timer_id,
            'user_id' => $this->user_id,
            'time'=>$this->time,
            'message'=>$this->message,
            'location'=>$this->location,
            'latitude'   => $this->latitude,
            'longitude'   => $this->longitude,
            'dateSorting' => Carbon::parse($this->created_at)->format('d-M-Y g:i A'),
            'images' => $this->image,
        ];

        if($this->type != null) {
            $response['type'] = $this->type ;
            $response['reason'] = $this->reason;   
        }
        ($this->time_started != null) ? $response['time_started'] = $this->time_started : "";
        ($this->time_finished != null) ? $response['time_finished'] = $this->time_finished : "";
        return $response;
    }
}
