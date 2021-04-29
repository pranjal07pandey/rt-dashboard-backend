<?php
namespace App\Services\V2\Api;

use App\Helpers\V2\MessageDisplay;
use App\Http\Resources\V2\Timer\TimerBreakResource;
use App\Http\Resources\V2\Timer\TimerCommentResource;
use App\Http\Resources\V2\Timer\TimerResource;
use Illuminate\Http\Request;
use App\Events\TimerChanged;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\V2\ConstructorService;
use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\AmazoneBucket;

class TimerService extends ConstructorService {

    public function getcheckOldTimerSession($request){
        $companyId = auth()->user()->companyInfo->id;
        $oldSessionCheck = $this->timerRepository->getDataWhere([['user_id', auth()->user()->id],['status', 0]])->first();
        $timerSetting = $this->timerSettingRepository->getDatawhere([['company_id', $companyId]])->first();

        if($timerSetting == null){
            $timerSettingRequest = new Request();
            $timerSettingRequest['company_id'] = $companyId;
            $timerSettingRequest['comment_image'] = 1;
            $timerSettingRequest['pause_image'] = 1;
            $timerSetting = $this->timerSettingRepository->insertAndUpdate($timerSettingRequest);
        }
        $timer_setting_data = array(
            'comment_image' => $timerSetting->comment_image,
            'pause_image' => $timerSetting->pause_image,
        );
        if($oldSessionCheck == null){
            return response()->json(["timer_setting" => $timer_setting_data, "timerStatus" => 0],200);
        }else{
            $getLastestTimer =  $oldSessionCheck;
            $timer_logs =  @@$this->timerLogRepository->getDataWhere([['timer_id', $getLastestTimer->id]])->get();
            $timerComment = $this->timerCommentRepository->getDataWhere([['timer_id',$getLastestTimer->id]])->get();
            $timer_comment=array();
            foreach ($timerComment as $row){
                $comment_image_data= array();
                $timer_comment_imgages = $this->timerImageRepository->getDataWhere([['key_id', $row->id],['type',1]])->get();
                foreach($timer_comment_imgages as $items){
                    $comment_image_data[] = AmazoneBucket::url() . $items->image;
                }
                $timer_comment[] = new TimerCommentResource($row,$comment_image_data);
            }
            $timer_data = array();
            $totalBreak = 0;
            foreach($timer_logs as $timer_log){
                $break_image_data=array();
                $timer_break_imgages = $this->timerImageRepository->getDataWhere([['key_id', $timer_log->id],['type',2]])->get();
                foreach($timer_break_imgages as $row){
                    $break_image_data[] =  AmazoneBucket::url() . $row->image;
                }
                $timer_data[] = new TimerBreakResource($timer_log,$break_image_data);
                $datetime1 = Carbon::parse($timer_log->time_started);
                if($timer_log->time_finished == NULL){
                    $totalBreak += 0;
                }else{
                    $datetime2 = Carbon::parse($timer_log->time_finished);
                    $break = $datetime2->diffInSeconds($datetime1);
                    $totalBreak += $break;
                }
                foreach($timer_break_imgages as $row){
                    $break_image_data[] = array(
                        'break_log_id' => $row->key_id,
                        'image' => AmazoneBucket::url() . $row->image,
                    );
                }
            }
            $timerClient = $this->timerClientRepository->getDataWhere([['timer_id',$getLastestTimer->id]])->with('userInfo','emailUserInfo.emailClient')->get();
            $client = array();
            $clientsname = array();
            $companyName = array();
            foreach ($timerClient as $items){
                $temp = ['id' => $items->id,'user_id' => $items->user_id];
                if ($items->user_type == 1){
                    $companyId  =   0;
                    $employee = $this->employeeRepository->getDataWhere([['user_id', $items->user_id]])->first();
                    if($employee != null):
                        $companyId = $employee->company_id;
                    else :
                        $companyId   =  $this->companyRepository->getDataWhere([['user_id', $items->user_id]])->first()->id;
                    endif;
                    $companyName[] = $this->companyRepository->getDataWhere([['id',$companyId]])->first()->name;
                    $temp = array_add($temp,'full_name',$items->userInfo->first_name." ".$items->userInfo->last_name);
                    $temp = array_add($temp,'company_name',$this->companyRepository->getDataWhere([['id',$companyId]])->first()->name);
                    $clientsname[] = $items->userInfo->first_name . ' ' . $items->userInfo->last_name;
                }else if ($items->user_type == 2){
                    $companyName[] = $items->emailUserInfo->emailClient->company_name;
                    $temp = array_add($temp,'full_name',$items->emailUserInfo->email);
                    $temp = array_add($temp,'company_name',$items->emailUserInfo->emailClient->company_name);
                    $clientsname[] = $items->emailUserInfo->email;
                }
                $client[] = $temp;
            }
            $data = new TimerResource($getLastestTimer,$timer_data,$timer_comment,$totalBreak,$client,$clientsname,$companyName);
            return response()->json(["timerStatus" => 1, "timer" => $data , "timer_setting" => $timer_setting_data],200);
        }
    }

    public function startNewTimerSession($request){
        try {
            DB::beginTransaction();
            //check old active timer
            if($this->timerRepository->getDataWhere([['user_id',auth()->user()->id],['status',0]])->count() == 0) {
                $clients = Input::get('clients');
                $timerRequest = new Request();
                $timerRequest['user_id'] = auth()->user()->id;
                $timerRequest['location'] = $request->location;
                $timerRequest['longitude'] = $request->longitude;
                $timerRequest['latitude'] = $request->latitude;
                $timerRequest['time_started'] = $request->time_started;
                $timerRequest['total_time'] = "00:00:00";
                $timerRequest['status'] = 0;
                $timer = $this->timerRepository->insertAndUpdate($timerRequest);
                if($clients != null){
                    foreach ($clients as $row) {
                        $timerClientRequest = new Request();
                        $timerClientRequest['timer_id'] = $timer->id;
                        $timerClientRequest['user_id'] = $row;
                        $timerClientRequest['user_type'] = $request->user_type;
                        $this->timerClientRepository->insertAndUpdate($timerClientRequest);
                    }
                }
                $timerClient = $this->timerClientRepository->getDataWhere([['timer_id', $timer->id]])->with('userInfo','emailUserInfo')->get();
                $client = array();
                $clientsname = array();
                $companyName = array();
                foreach ($timerClient as $items) {
                    $temp = ['id' => $items->id,'user_id' => $items->user_id];
                    if ($items->user_type == 1) {
                        $temp = array_add($temp,'full_name',$items->userInfo->first_name . " " . $items->userInfo->last_name);
                        $clientsname[] = $items->userInfo->first_name . ' ' . $items->userInfo->last_name;
                        $employee = $this->employeeRepository->getDataWhere([['user_id', $row->user_id]])->first();
                        if ($employee != null):
                            $companyId = $employee->company_id;
                        else :
                            $companyId = $this->companyRepository->getDataWhere([['user_id', $row->user_id]])->first()->id;
                        endif;

                        $companyName[] = $this->companyRepository->getDataWhere([['id', $companyId]])->first()->name;
                    } else if ($items->user_type == 2) {
                        $temp = array_add($temp,'full_name',$items->emailUserInfo->email);
                        $clientsname[] = $items->emailUserInfo->email;
                        $companyName[] = @$row->emailUserInfo->emailClient->company_name;
                    }
                    $client[] = $temp;
                }

                $data = array();

                $data['id'] = $timer->id;
                $data['location'] = $timer->location;
                $data['longitude'] = $timer->longitude;
                $data['latitude'] = $timer->latitude;
                $data['time_started'] = $timer->time_started;
                $data['time_ended'] = $timer->time_ended;
                $data['status'] = $timer->status;
                $data['client'] = $client;
                $data['clients_name'] = implode(", ", $clientsname);
                $data['company_name'] = implode(", ", array_unique($companyName));

                event(new TimerChanged($timer));
                DB::commit();
                return response()->json(["timer" => $data],200);
            }else{
                DB::rollback();
                return response()->json(["message" => MessageDisplay::ActiveTimerExist],500);
            }
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(["message" => MessageDisplay::ERROR],500);
        }
    }

    public function finishTimerSession($request){
        try {
            ini_set('memory_limit','256M');
            set_time_limit(0);
            DB::beginTransaction();
            $timer = $this->timerRepository->getDataWhere([['id', $request->timer_id]])->first();
            $timer->time_ended = $request->time_ended;
            $timer->status = 1;
            $timer->update();

            $timerLog = $this->timerLogRepository->getDataWhere([['timer_id',$request->timer_id]])->orderBy('id','desc')->first();
            if ($timerLog != null){
                $timerLoglast   =   $timerLog;
                if($timerLoglast->time_finished == ""){
                    $timerFinnishedRequest = new Request();
                    $timerFinnishedRequest['timer_log_id']  =   $timerLoglast->id;
                    $timerFinnishedRequest['time_finished']  =   $request->time_ended;
                    $this->timerLogRepository->insertAndUpdate($timerFinnishedRequest);
                }
            }

            if($request->time_ended != NULL){
                $datetime1 = Carbon::parse($timer->time_started);
                $datetime2 = Carbon::parse($timer->time_ended);
                $interval = $datetime2->diffInSeconds($datetime1);
                $loginterval = array();
                $timerLog       =  $this->timerLogRepository->getDataWhere([['timer_id',$request->timer_id]])->get();
                foreach ($timerLog as $row){
                    $logdatetime1 = Carbon::parse($row->time_started);
                    $logdatetime2 = Carbon::parse($row->time_finished);
                    $loginterval[] = $logdatetime2->diffInSeconds($logdatetime1);
                }
                $break_time = array_sum($loginterval);
                $total_times = $interval - $break_time;
                $h   = floor($total_times / 3600);
                $m = floor(($total_times % 3600) / 60);
                $s = $total_times - ($h * 3600) - ($m * 60);
                $total_time= sprintf('%02d:%02d:%02d', $h, $m, $s);
                $timer->update(['total_time'=>$total_time]);
            }

            if (Input::has('tag')){
                $tag= Input::get('tag');
                foreach ($tag as $row){
                    if($this->timerAttachedTagRepository->getDataWhere([['timer_id',$request->timer_id],['tag',$row]])->count()!=0){
                        continue;
                    }else{
                        $timerAttachedTagRequest = new Request();
                        $timerAttachedTagRequest['timer_id'] = $request->timer_id;
                        $timerAttachedTagRequest['user_id'] = auth()->user()->id;
                        $timerAttachedTagRequest['tag'] = $row;
                        $this->timerAttachedTagRepository->insertAndUpdate($timerAttachedTagRequest);
                    }
                }
            }

            $timers_tag = $this->timerAttachedTagRepository->getDataWhere([['timer_id',$request->timer_id],['user_id',auth()->user()->id]])->get()->pluck('tag')->toArray();
            $data = new TimerResource($timer,null,null,null,null,null,null,$timers_tag);
            DB::commit();
            return response()->json(["timer" => $data] ,200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['message' => MessageDisplay::Error ], 500);
        }
    }

    public function getAllSavedTimer($request){
        $timer = $this->timerRepository->getDataWhere([['status','!=', 0],['user_id', auth()->user()->id]])->orderBy('id','desc')->paginate(10);
        $timers= array();
        if ($timer->count()>0){
            foreach ($timer as $row){
                $timerClient = $this->timerClientRepository->getDataWhere([['timer_id',$row->id]])->with('userInfo','emailUserInfo')->get();
                $client = array();
                $clientsname = array();
                $companyName = array();
                foreach ($timerClient as $items){
                    $temp = ['id' => $items->id, 'user_id' => $items->user_id];
                    if ($items->user_type == 1){
                        $temp = array_add($temp,'full_name',$items->userInfo->first_name." ".$items->userInfo->last_name);
                        $clientsname[] =  $items->userInfo->first_name.' '.$items->userInfo->last_name;
                        $employee = $this->employeeRepository->getDataWhere([['user_id', $items->user_id]])->first();
                        if($employee != null):
                            $companyId = $employee->company_id;
                        else :
                            $companyId   =  $this->companyRepository->getDataWhere([['user_id', $items->user_id]])->first()->id;
                        endif;
                        $companyName[] = $this->companyRepository->getDataWhere([['id',$companyId]])->first()->name;
                    }elseif ($items->user_type == 2){
                        $temp = array_add($temp,'full_name',$items->emailUserInfo->email);
                        $clientsname[]  = $items->emailUserInfo->email;
                        $companyName[] =  @$items->emailUserInfo->emailClient->company_name;
                    }
                    $client[] = $temp;
                }

                $timerAttachedTag = $this->timerAttachedTagRepository->getDataWhere([['timer_id',$row->id]])->get()->pluck("tag")->toArray();
                $timers[] = new TimerResource($row,null,null,null,null,$clientsname,$companyName,$timerAttachedTag,$row->total_time);
            }
        }
        if ($timer->lastPage() == $timer->currentPage()){
            $has_next = false ;
        }else{
            $has_next= true;
        }
        return response()->json(["timer" => $timers,'has_next'=>$has_next],200);
    }

    public function pauseTimer($request){
        try {
            DB::beginTransaction();
            $timerLogRequest = new Request();
            $timerLogRequest['timer_id'] = $request->timer_id;
            $timerLogRequest['location'] = $request->location;
            $timerLogRequest['longitude'] = $request->longitude;
            $timerLogRequest['latitude'] = $request->latitude;
            $timerLogRequest['time_started'] = $request->start_time;
            $timerLogRequest['reason'] = $request->reason;
            $timerLog = $this->timerLogRepository->insertAndUpdate($timerLogRequest);
            $image =  Input::file("images");
            $number = 0;
            if($request->hasFile('images')){
                foreach($image as $img){
                    $timerImageRequest = new Request();
                    $timerImageRequest['key_id'] = $timerLog->id;
                    $timerImageRequest['type'] = 2;

                    // $ext = $img->getClientOriginalExtension();
                    // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                    $dest = 'files/timer/log/image';
                    // $img->move($dest, $filename);
                    // $timerImageRequest['image']    =    $dest . '/' . $filename;

                    $timerImageRequest['image'] = FunctionUtils::imageUpload($dest,$img,$number);
                    $this->timerImageRepository->insertAndUpdate($timerImageRequest);
                    $number++;
                }
            }

            $data = array();
            $timer_log_image = array();
            $timer_break_imgages = $this->timerImageRepository->getDataWhere([['key_id', $timerLog->id],['type', 2]])->get();
            foreach($timer_break_imgages as $row){
                $timer_log_image[] = AmazoneBucket::url() . $row->image;
            }
            $data = new TimerResource($timerLog,null,null,null,null,null,null,null,null,$timer_log_image);
            DB::commit();
            return response()->json(["timerLog" => $data ],200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(["message" => MessageDisplay::ERROR],500);
        }
    }

    public function continueTimer($request){
        try {
            $timerLogRequest = new Request();
            $timerLogRequest['timer_log_id'] = $request->timer_log_id;
            $timerLogRequest['time_finished'] = $request->end_time;
            $timerLog = $this->timerLogRepository->insertAndUpdate($timerLogRequest);
            $data = new TimerResource($timerLog);

            return response()->json(["timerLog" => $data],200);
        } catch (\Exception $ex) {
            return response()->json(["message" => MessageDisplay::ERROR],500);
        }
    }

    public function submitTimerComments($request){
        try {
            DB::beginTransaction();
            $timerCommentRequest = new Request();
            $timerCommentRequest['user_id'] = auth()->user()->id;
            $timerCommentRequest['timer_id'] = $request->timer_id;
            $timerCommentRequest['time'] = $request->time;
            $timerCommentRequest['message'] = $request->message;
            $timerCommentRequest['location'] = $request->location;
            $timerCommentRequest['latitude'] = $request->latitude;
            $timerCommentRequest['longitude'] = $request->longitude;
            $timerComment = $this->timerCommentRepository->insertAndUpdate($timerCommentRequest);

            $image =  Input::file("images");
            $number = 0;
            if($request->hasFile('images')){
                foreach($image as $img){
                    $timerImageRequest = new Request();
                    $timerImageRequest['key_id'] = $timerComment->id;
                    $timerImageRequest['type'] = 1;
                    // $ext = $img->getClientOriginalExtension();
                    // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                    $dest = 'files/timer/comment/image';
                    // $img->move($dest, $filename);
                    // $timerImageRequest['image']    =    $dest . '/' . $filename;

                    $timerImageRequest['image'] = FunctionUtils::imageUpload($dest,$img,$number);
                    $this->timerImageRepository->insertAndUpdate($timerImageRequest);
                    $number++;
                }
            }

            $timermessage = $this->timerCommentRepository->getDataWhere([['user_id',auth()->user()->id],['timer_id',$request->timer_id]])->get();

            $data = array();
            foreach ($timermessage as $row){
                $timer_comment_image= array();
                $timer_comment_imgages = $this->timerImageRepository->getDataWhere([['key_id', $row->id],['type', 1]])->get();
                foreach($timer_comment_imgages as $items){
                    $timer_comment_image[] = AmazoneBucket::url() . $items->image;
                }
                $data[] = new TimerCommentResource($row,$timer_comment_image);
            }
            DB::commit();
            return response()->json(["comment" => $data],200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(["message" => MessageDisplay::ERROR],500);
        }
    }

    public function searchTimer($request){
        try {
            $timerQuery = $this->timerRepository->getDataWhere([['user_id',auth()->user()->id],['status' ,'!=', '0']])
                                                ->when($request->from  && $request->from != "",function($query) use($request){
                                                    $query->whereDate('time_started','>=',Carbon::parse($request->from)->format('Y-m-d'));
                                                })
                                                ->when($request->to  && $request->to != "",function($query) use($request){
                                                    $query->whereDate('time_started','<=',Carbon::parse($request->to)->format('Y-m-d'));
                                                })
                                                ->when($request->location  && $request->location != "",function($query) use($request){
                                                    $query->where('location','like','%'.$request->location.'%');
                                                })
                                                ->when($request->duration,function($query) use($request){
                                                    $h = floor($request->duration / 3600);
                                                    $m = floor(($request->duration % 3600) / 60);
                                                    $s = $request->duration - ($h * 3600) - ($m * 60);
                                                    $durations = sprintf('%02d:%02d:%02d', $h, $m, $s);
                                                    $query->whereTime('total_time','<=',$durations);
                                                });

            $timerQuries = $timerQuery->with('timerClient')->orderBy('id','desc')->get();

            if($request->user_type == 1 || $request->user_type == 2){
                if ($request->client  && $request->client != ""){
                    $timer = array();
                    foreach ($timerQuries as $row){
                        $clientType = $row->timerClient->where('user_type',$request->user_type);
                        $clients = $clientType->pluck('user_id')->toArray();
                        $requestClient = $request->client;
                        $intersect = array_intersect($clients,$requestClient);
                        if ($this->array_equal($intersect,$requestClient)){
                            $timer[] = $row->id;
                        }
                    }
                    $timerQuries = $timerQuery->whereIn('id',$timer)->get();
                }
            }

            if ($request->tag && $request->tag != ""){
                $timers = array();
                foreach ($timerQuries as $items){
                    $tags = $this->timerAttachedTagRepository->getDataWhere([['timer_id',$items->id],['tag','like','%'.$request->tag.'%']])->count();
                    if ($tags > 0 ){
                        $timers[] = $items->id;
                        break;
                    }
                }
                $timerQuries = $timerQuery->whereIn('id',$timers)->get();
            }

            $data= array();
            foreach ($timerQuery->get() as $row){
                $timerAttachedTag = $this->timerAttachedTagRepository->getDataWhere([['timer_id',$row->id]])->pluck("tag")->toArray();
                $timerClient = $this->timerClientRepository->getDataWhere([['timer_id',$row->id]])->with('userInfo','emailUserInfo')->get();
                $client = array();
                $clientsname = array();
                $companyName = array();
                foreach ($timerClient as $items){
                    $temp = ['id' => $items->id, 'user_id' => $items->user_id];
                    if ($items->user_type == 1){
                        $temp = array_add($temp,'full_name',$items->userInfo->first_name." ".$items->userInfo->last_name);
                        $clientsname[] =  $items->userInfo->first_name.' '.$items->userInfo->last_name;
                        $employee = $this->employeeRepository->getDataWhere([['user_id', $items->user_id]])->first();
                        if($employee != null):
                            $companyId = $employee->company_id;
                        else :
                            $companyId   =  $this->companyRepository->getDataWhere([['user_id', $items->user_id]])->first()->id;
                        endif;

                        $companyName[] = $this->companyRepository->getDataWhere([['id',$companyId]])->first()->name;
                    }elseif ($items->user_type == 2){
                        $temp = array_add($temp,'full_name',$items->emailUserInfo->email);
                        $clientsname[] =  $items->emailUserInfo->email;
                        $companyName[] =  @$items->emailUserInfo->emailClient->company_name;
                    }
                    $client[] = $temp;
                }
                $data[] = new TimerResource($row,null,null,null,null,$clientsname,$companyName,$timerAttachedTag,$row->total_time);
            }
            $timerQuries = $data;

            return response()->json(["timer" =>$timerQuries],200);
        } catch (\Exception $ex) {
            return response()->json(["message" =>MessageDisplay::ERROR],500);
        }
    }

    public function timerAttachedTag($request){
        try {
            DB::beginTransaction();
            $tag= Input::get('tag');
            foreach ($tag as $row){
                if($this->timerAttachedTagRepository->getDataWhere([['timer_id',$request->timer_id],['tag',$row]])->count() != 0){
                    continue;
                }else{
                    $timerAttachedTagRequest = new Request();
                    $timerAttachedTagRequest['timer_id'] = $request->timer_id;
                    $timerAttachedTagRequest['user_id'] = $request->user_id;
                    $timerAttachedTagRequest['tag'] = $row;
                    $this->timerAttachedTagRepository->insertAndUpdate($timerAttachedTagRequest);
                }
            }
            $timers = $this->timerAttachedTagRepository->getDataWhere([['timer_id',$request->timer_id],['user_id',$request->user_id]])->get();
            $tag = array();
            foreach ($timers as $items){
                $tag[] =  $items->tag;
            }
            DB::commit();
            return response()->json(["timer_attached_tag" =>implode(", ", $tag) ],200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(["message" => MessageDisplay::ERROR ],500);
        }
    }

    public function timerDetailsById($request,$id){
        try {
            $timer = $this->timerRepository->getDataWhere([['id', $id]])->first();

            $timerClient = $this->timerClientRepository->getDataWhere([['timer_id',$id]])->with('userInfo','emailUserInfo')->get();;
            $client = array();
            $clientsname = array();
            $companyName = array();
            foreach ($timerClient as $items){
                $temp = ['id' => $items->id,'user_id' => $items->user_id];
                if ($items->usert_type == 1){
                    $temp = array_add($temp,'full_name',$items->userInfo->first_name." ".$items->userInfo->last_name);
                    $clientsname[] =  $items->userInfo->first_name.' '.$items->userInfo->last_name;

                    $employee = $this->employeeRepository->getDataWhere([['user_id', $items->user_id]])->first();
                    if ($employee != null):
                        $companyId = $employee->company_id;
                    else :
                        $companyId = $this->companyRepository->getDataWhere([['user_id', $items->user_id]])->first()->id;
                    endif;

                    $companyName[] = $this->companyRepository->getDataWhere([['id', $companyId]])->first()->name;
                }elseif($items->usert_type == 2){
                    $temp = array_add($temp,'full_name',$items->emailUserInfo->email);
                    $clientsname[] =  $items->emailUserInfo->email;
                    $companyName[] = @$items->emailUserInfo->emailClient->company_name;
                }
                $client[] = $temp;
            }

            $timers = array();
            $timerAttachedTag   =   array();
            $timerAttachedTagList = $this->timerAttachedTagRepository->getDataWhere([['timer_id', $timer->id]]);
            if($timerAttachedTagList->count() > 0){
                $timerAttachedTag = $timerAttachedTagList->pluck("tag");
            }
            $timerComment = $this->timerCommentRepository->getDataWhere([['timer_id', $id]])->orderBy('created_at', 'desc')->get();
            $timerTimeline = array();
            foreach ($timerComment as $items) {
                $timer_comment_image= array();
                $timer_comment_imgages = $this->timerImageRepository->getDataWhere([['key_id', $items->id],['type', 1]])->get();
                foreach($timer_comment_imgages as $row){
                    $timer_comment_image[] = AmazoneBucket::url() . $row->image;
                }
                $type = 1;
                $timerTimeline[] = new TimerCommentResource($items,$timer_comment_image,$type);
            }
            $timerLogs = $this->timerLogRepository->getDataWhere([['timer_id', $id]])->orderBy('created_at', 'desc')->get();
            foreach ($timerLogs as $items) {
                $timer_log_image = array();
                $timer_break_imgages = $this->timerImageRepository->getDataWhere([['key_id', $items->id],['type', 2]])->get();
                foreach($timer_break_imgages as $row){
                    $timer_log_image[] = AmazoneBucket::url() . $row->image;
                }
                $time_started = Carbon::parse($items->time_started)->format('d-M-Y g:i A');
                $time_finished = Carbon::parse($items->time_finished)->format('d-M-Y g:i A');
                $type = 2;
                $timerTimeline[] = new TimerCommentResource($items,$timer_log_image,$type,$time_started,$time_finished);
            }

            // timer sorting according to dateAdded
            $size = count($timerTimeline);
            for ($i = 0; $i < $size; $i++) {
                for ($j = 0; $j < $size - 1 - $i; $j++) {
                    if (strtotime($timerTimeline[$j + 1]["dateSorting"]) > strtotime($timerTimeline[$j]["dateSorting"])) {
                        $tempArray = $timerTimeline[$j + 1];
                        $timerTimeline[$j + 1] = $timerTimeline[$j];
                        $timerTimeline[$j] = $tempArray;
                    }
                }
            }
            $timesss = (explode(":",$timer->total_time));
            $timers = new TimerResource($timer,null,null,null,null,$clientsname,$companyName,$timerAttachedTag,$timer->total_time,null,$timerTimeline,$timesss);
            return response()->json(["timer" => $timers],200);
        } catch (\Exception $ex) {
            return response()->json(["timer" => $timers],500);
        }
    }
}