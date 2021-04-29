<?php

namespace App\Http\Resources\V2\Timer;

use Illuminate\Http\Resources\Json\JsonResource;

class TimerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected $breakLog,$comment,$totalBreak,$client,$clientsName,$companyName,$timers_tag,$total_time,
        $timer_log_image,$timerTimeline,$time;

    public function __construct($resource,$breakLog=null,$comment=null,$totalBreak=null,$client=null,
            $clientsName=null,$companyName=null,$timers_tag=null,$total_time=null,$timer_log_image=null,
            $timerTimeline=null,$time=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->breakLog = $breakLog;
        $this->comment = $comment;
        $this->totalBreak = $totalBreak;
        $this->client = $client;
        $this->clientsName = $clientsName;
        $this->companyName =$companyName;
        $this->timers_tag = $timers_tag;
        $this->total_time = $total_time;
        $this->timer_log_image = $timer_log_image;
        $this->timerTimeline = $timerTimeline;
        $this->time = $time;
    }


    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'timer_id' => $this->timer_id,
            'location' => $this->location,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'time_started' => $this->time_started,
            'time_ended' => $this->time_ended,
            'reason' => $this->reason,
            'status' => $this->status,
        ];
        is_array($this->comment) ? $response['comments'] = $this->comment : "";
        is_array($this->client) ? $response['client'] = $this->client : "";
        is_array($this->breakLog) ? $response['break_log'] = $this->breakLog : "";
        is_array($this->timers_tag) ? $response['tag'] = implode(", ", $this->timers_tag) : "";
        is_array($this->clientsName) ? $response['clients_name'] = implode(", ", $this->clientsName) : "";
        is_array($this->companyName) ? $response['company_name'] = implode( ", ",array_unique($this->companyName)) : "";
        is_array($this->timer_log_image) ? $response['images'] = $this->timer_log_image : "";
        is_array($this->timerTimeline) ? $response['timeline'] = $this->timerTimeline : "";
        if(is_array($this->time)){
            $response['total_hour'] = $this->time[0];
            $response['total_min'] = $this->time[1];
            $response['total_sec'] = $this->time[2];
        }

        ($this->totalBreak != null) ? $response['total_break'] = $this->totalBreak : "";

        $response['total_time'] = ($this->total_time != null) ?  substr_replace($this->total_time ,"",-3) : "";


        return $response;
    }
}
