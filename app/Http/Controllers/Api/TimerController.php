<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\Employee;
use App\Events\TimerChanged;
use App\Timer;
use App\TimerAttachedTag;
use App\TimerClient;
use App\TimerComment;
use App\TimerImage;
use App\TimerLog;
use App\TimerSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Helpers\V2\FunctionUtils;
use Validator;
use App\Helpers\V2\AmazoneBucket;

class TimerController extends Controller
{

    public function getcheckOldTimerSession(Request $request){
        $oldSessionCheck = Timer::where('user_id', $request->header('userId'))->where('status', 0)->count();
        $timerSetting = TimerSetting::where('company_id', $request->header('companyId'))->first();
        if($timerSetting){
            $timer_setting_data[] = array(
                'comment_image' => $timerSetting->comment_image,
                'pause_image' => $timerSetting->pause_image,
            );
        }else{
            $timerSetting = new TimerSetting();
            $timerSetting->company_id = $request->header('companyId');
            $timerSetting->comment_image = 1;
            $timerSetting->pause_image = 1;
            $timerSetting->save();
            $timer_setting_data[] = array(
                'comment_image' => $timerSetting->comment_image,
                'pause_image' => $timerSetting->pause_image,
            );
        }

        if($oldSessionCheck == 0){
            return response()->json(array("status" => true, "timer_setting" => $timer_setting_data, "timerStatus" => 0));
        }else{

            $getLastestTimer =  Timer::where('user_id', $request->header('userId'))->where('status',0)->orderBy('created_at', 'DESC')->first();
            $timer_logs =  @@\App\TimerLog::where('timer_id', $getLastestTimer->id)->get();
            $timerComment = TimerComment::where('timer_id',$getLastestTimer->id)->get();

            $timer_comment=array();
            foreach ($timerComment as $row){
                $comment_image_data= array();
                $timer_comment_imgages = TimerImage::where('key_id', $row->id)->where('type', 1)->get();
                foreach($timer_comment_imgages as $items){
                    $comment_image_data[] = AmazoneBucket::url() . $items->image;
                }

                $timer_comment[] = array(
                    'id'=>$row->id,
                    'time'=>$row->time,
                    'message'=>$row->message,
                    'location'=>$row->location,
                    'latitude'   => $row->latitude,
                    'longitude'   => $row->longitude,
                    'images' => $comment_image_data,

                );
            }
            $timer_data = array();
            $totalBreak = 0;
            foreach($timer_logs as $timer_log){
                $break_image_data=array();
                $timer_break_imgages = TimerImage::where('key_id', $timer_log->id)->where('type', 2)->get();
                foreach($timer_break_imgages as $row){
                    $break_image_data[] =  AmazoneBucket::url() . $row->image;
                }
                $timer_data[] = array(
                    'id' => $timer_log->id,
                    'timer_id' => $timer_log->timer_id,
                    'location' => $timer_log->location,
                    'longitude' => $timer_log->longitude,
                    'latitude' => $timer_log->latitude,
                    'time_started' => $timer_log->time_started,
                    'time_finished' => $timer_log->time_finished,
                    'reason' => $timer_log->reason,
                    'images' =>$break_image_data,
                );
                $datetime1 = \Carbon\Carbon::parse($timer_log->time_started);
                if($timer_log->time_finished == NULL){
                    $totalBreak += 0;
                }else{
                    $datetime2 = \Carbon\Carbon::parse($timer_log->time_finished);
                    $break = $datetime2->diffInSeconds($datetime1);
                    $totalBreak += $break;
                }
                $timer_break_imgages = TimerImage::where('key_id', $timer_log->id)->where('type', 2)->get();
                foreach($timer_break_imgages as $row){
                    $break_image_data[] = array(
                        'break_log_id' => $row->key_id,
                        'image' => AmazoneBucket::url() . $row->image,
                    );
                }
            }
            $timerClient =TimerClient::where('timer_id',$getLastestTimer->id)->get();
            $client = array();

            foreach ($timerClient as $items){

                if ($items->user_type == 1){
                    $companyId  =   0;
                    if(Employee::where('user_id', $items->user_id)->count()!=0):
                        $companyId = Employee::where('user_id', $items->user_id)->first()->company_id;
                    else :
                        $companyId   =   Company::where('user_id', $items->user_id)->first()->id;
                    endif;

                    $client[] =   array(
                        'id'            => $items->id,
                        'user_id'     => $items->user_id,
                        'full_name'     => $items->userInfo->first_name." ".$items->userInfo->last_name,
                        'company_name'=>Company::where('id',$companyId)->first()->name


                    );

                }else if ($items->user_type == 2){

                    $client[] =   array(
                        'id'            => $items->id,
                        'user_id'     => $items->user_id,
                        'full_name'     => $items->emailUserInfo->email,
                        'company_name'=>$items->emailUserInfo->emailClient->company_name,


                    );

                }

            }



            $clientsname = array();
            foreach ($timerClient as $timerClients){
                if ($items->user_type == 1) {
                    $clientsname[] = $timerClients->userInfo->first_name . ' ' . $timerClients->userInfo->last_name;
                }else  if ($items->user_type == 2){
                    $clientsname[] = $timerClients->emailUserInfo->email;
                }
            }

            $companyName = array();
            foreach ($timerClient as $row){
                if ($items->user_type == 1) {
                    $companyId = 0;
                    if (Employee::where('user_id', $row->user_id)->count() != 0):
                        $companyId = Employee::where('user_id', $row->user_id)->first()->company_id;
                    else :
                        $companyId = Company::where('user_id', $row->user_id)->first()->id;
                    endif;

                    $companyName[] = Company::where('id', $companyId)->first()->name;
                }elseif ($items->user_type == 2){
                    $companyName[] = $row->emailUserInfo->emailClient->company_name;
                }

            }

            $data = array();
            $data['id'] = $getLastestTimer->id;
            $data['location'] = $getLastestTimer->location;
            $data['longitude'] = $getLastestTimer->longitude;
            $data['latitude'] = $getLastestTimer->latitude;
            $data['time_started'] = $getLastestTimer->time_started;
            $data['time_ended'] = $getLastestTimer->time_ended;
            $data['status'] = $getLastestTimer->status;
            $data['break_log'] = $timer_data;
            $data['comments'] = $timer_comment;
            $data['total_break'] = $totalBreak;
            $data['client'] =$client;
            $data['clients_name'] =implode(", ", $clientsname);
            $data['company_name'] =implode( ", ",array_unique($companyName));

            return response()->json(array("status" => true, "timerStatus" => 1, "timer" => $data , "timer_setting" => $timer_setting_data));
        }

    }


    public function startNewTimerSession(Request $request){
        $validator  =   Validator::make(Input::all(),['location' =>     'required','longitude'  =>  'required', 'latitude' => 'required', 'time_started' => 'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            //check old active timer
            if(Timer::where('user_id',$request->header('userId'))->where('status',0)->count()==0) {
                $clients = Input::get('clients');
                $timer = new Timer();
                $timer->user_id = $request->header('userId');
                $timer->location = $request->location;
                $timer->longitude = $request->longitude;
                $timer->latitude = $request->latitude;
                $timer->time_started = $request->time_started;
                $timer->total_time = "00:00:00";
                $timer->status = 0;
                if ($timer->save()):
                    foreach ($clients as $row) {
                        $timerClient = new TimerClient();
                        $timerClient->timer_id = $timer->id;
                        $timerClient->user_id = $row;
                        $timerClient->user_type = $request->user_type;
                        $timerClient->save();
                    }
                endif;
                $timerClient = TimerClient::where('timer_id', $timer->id)->get();
                $client = array();
                foreach ($timerClient as $items) {
                    if ($items->user_type == 1) {
                        $client[] = array(
                            'id' => $items->id,
                            'user_id' => $items->user_id,
                            'full_name' => $items->userInfo->first_name . " " . $items->userInfo->last_name,
                        );
                    } else if ($items->user_type == 2) {
                        $client[] = array(
                            'id' => $items->id,
                            'user_id' => $items->user_id,
                            'full_name' => $items->emailUserInfo->email,
                        );
                    }

                }


                $clientsname = array();
                foreach ($timerClient as $timerClients) {
                    if ($timerClients->user_type == 1) {
                        $clientsname[] = $timerClients->userInfo->first_name . ' ' . $timerClients->userInfo->last_name;

                    } elseif ($timerClients->user_type == 2) {
                        $clientsname[] = $timerClients->emailUserInfo->email;
                    }
                }
                $companyName = array();
                foreach ($timerClient as $row) {

                    if ($row->user_type == 1) {
                        $companyId = 0;
                        if (Employee::where('user_id', $row->user_id)->count() != 0):
                            $companyId = Employee::where('user_id', $row->user_id)->first()->company_id;
                        else :
                            $companyId = Company::where('user_id', $row->user_id)->first()->id;
                        endif;

                        $companyName[] = Company::where('id', $companyId)->first()->name;
                    } else if ($row->user_type == 2) {

                        $companyName[] = @$row->emailUserInfo->emailClient->company_name;

                    }

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

                return response()->json(array("status" => true, "timer" => $data));
            }else{
                return response()->json(array("status" => false, "message" => "There is already one active timer. Please check first and try again."));
            }
        endif;
    }


    public function finishTimerSession(Request $request){
        ini_set('memory_limit','256M');
        set_time_limit(0);
        $timer = Timer::where('id', $request->timer_id)->where('user_id', $request->header('userId'))->first();
        $timer->time_ended = $request->time_ended;
        $timer->status = 1;
        $timer->update();


        if (TimerLog::where('timer_id',$request->timer_id)->orderBy('id','desc')->count()>0){
            $timerLoglast   =   TimerLog::where('timer_id',$request->timer_id)->orderBy('id','desc')->first();
            if($timerLoglast->time_finished == ""){
                $timerFinnished                 =   TimerLog::findOrFail($timerLoglast->id);
                $timerFinnished->time_finished  =   $request->time_ended;
                $timerFinnished->save();
            }
        }

        if($request->time_ended != NULL){
            $datetime1 = \Carbon\Carbon::parse($timer->time_started);
            $datetime2 = \Carbon\Carbon::parse($timer->time_ended);
            $interval = $datetime2->diffInSeconds($datetime1);
            $loginterval = array();
            $timerLog       =   TimerLog::where('timer_id',$request->timer_id)->get();
            foreach ($timerLog as $row){
                $logdatetime1 = \Carbon\Carbon::parse($row->time_started);
                $logdatetime2 = \Carbon\Carbon::parse($row->time_finished);
                $loginterval[] = $logdatetime2->diffInSeconds($logdatetime1);
            }
            $break_time = array_sum($loginterval);
            $total_times = $interval - $break_time;
            $h   = floor($total_times / 3600);
            $m = floor(($total_times% 3600) / 60);
            $s = $total_times - ($h * 3600) - ($m * 60);
            $total_time= sprintf('%02d:%02d:%02d', $h, $m, $s);
            Timer::where('id', $request->timer_id)->where('user_id', $request->header('userId'))->update(['total_time'=>$total_time]);
        }


        if (Input::has('tag')){
            $tag= Input::get('tag');
            foreach ($tag as $row){
                if(TimerAttachedTag::where('timer_id',$request->timer_id)->where('tag',$row)->count()!=0){
                    continue;
                }else{
                    $timerAttachedTag = new TimerAttachedTag();
                    $timerAttachedTag->timer_id = $request->timer_id;
                    $timerAttachedTag->user_id = $request->header('userId');
                    $timerAttachedTag->tag = $row;
                    $timerAttachedTag->save();
                }
            }
        }

        $timers_tag = TimerAttachedTag::where('timer_id',$request->timer_id)->where('user_id',$request->header('userId'))->get();
        $tag = array();
        foreach ($timers_tag as $items){
            $tag[] =  $items->tag;
        }


        $data = array();

        $data['id'] = $timer->id;
        $data['location'] = $timer->location;
        $data['longitude'] = $timer->longitude;
        $data['latitude'] = $timer->latitude;
        $data['time_started'] = $timer->time_started;
        $data['time_ended'] = $timer->time_ended;
        $data['status'] = $timer->status;
        $data['tag']= implode(", ", $tag);

        return response()->json(array("status" => true, "timer" => $data ));
    }


    public function getAllSavedTimer(Request $request){
        $timer = Timer::where('status','!=', 0)->where('user_id', $request->header('userId'))->orderBy('id','desc')->paginate(10);
        $timers= array();
        if ($timer->count()>0){
            foreach ($timer as $row){
                $timerClient =TimerClient::where('timer_id',$row->id)->get();
                $client = array();
                foreach ($timerClient as $items){

                    if ($items->user_type ==1){
                        $client[] =   array(
                            'id'            => $items->id,
                            'user_id'     => $items->user_id,
                            'full_name'     => $items->userInfo->first_name." ".$items->userInfo->last_name,
                        );
                    }elseif ($items->user_type ==2){
                        $client[] =   array(
                            'id'            => $items->id,
                            'user_id'     => $items->user_id,
                            'full_name'     => $items->emailUserInfo->email,
                        );
                    }

                }
//            dd($client);

                $clientsname = array();
                foreach ($timerClient as $timerClients){
                    if ($timerClients->user_type == 1){
                        $clientsname[] =  $timerClients->userInfo->first_name.' '.$timerClients->userInfo->last_name;
                    }elseif($timerClients->user_type == 2){
                        $clientsname[]  = $timerClients->emailUserInfo->email;
                    }
                }




                $companyName = array();
                foreach ($timerClient as $rows){
                    if ($rows->user_type ==1){
                        $companyId  =   0;
                        if(Employee::where('user_id', $rows->user_id)->count()!=0):
                            $companyId = Employee::where('user_id', $rows->user_id)->first()->company_id;
                        else :
                            $companyId   =   Company::where('user_id', $rows->user_id)->first()->id;
                        endif;

                        $companyName[] = Company::where('id',$companyId)->first()->name;
                    }else if ($rows->user_type ==2){

                        $companyName[] =  @$rows->emailUserInfo->emailClient->company_name;
                    }
                }

                $timerAttachedTag= TimerAttachedTag::where('timer_id',$row->id)->pluck("tag");
                $timers[] =   array(
                    'id'            => $row->id,
                    'location'      => $row->location,
                    'longitude'     => $row->longitude,
                    'latitude'      => $row->latitude,
                    'time_started'  => $row->time_started,
                    'time_ended'    => $row->time_ended,
                    'status'        => $row->status,
                    'clients_name' =>implode(", ", $clientsname),
//                    'company_name' =>implode(", ", $companies),
                    'company_name' =>implode(", ", array_unique($companyName) ),
                    'total_time'    =>substr_replace( $row->total_time ,"",-3),
                    'tags'           =>$timerAttachedTag,
                );

            }

        }
        if ($timer->lastPage() == $timer->currentPage()){
            $has_next = false ;
        }else{
            $has_next= true;
        }

        return response()->json(array("status" => true, "timer" => $timers,'has_next'=>$has_next));
    }

    public function pauseTimer(Request $request){

        $validator  =   Validator::make(Input::all(),['timer_id' => 'required', 'start_time' => 'required' , 'reason' => 'required', 'location' => 'required', 'longitude' => 'required', 'latitude' => 'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:

            $timerLog = new TimerLog();

            $timerLog->timer_id = $request->timer_id;
            $timerLog->location = $request->location;
            $timerLog->longitude = $request->longitude;
            $timerLog->latitude = $request->latitude;
            $timerLog->time_started = $request->start_time;
            $timerLog->reason = $request->reason;

            $timerLog->save();
            $image =  Input::file("images");
            $number = 0;
            if($request->hasFile('images')){
                foreach($image as $img){
                    $timer_image = new TimerImage();
                    $timer_image->key_id = $timerLog->id;
                    $timer_image->type = 2;

                    // $ext = $img->getClientOriginalExtension();
                    // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                    $dest = 'files/timer/log/image';
                    // $img->move($dest, $filename);
                    // $timer_image->image    =    $dest . '/' . $filename;
                    $timer_image->image = FunctionUtils::imageUpload($dest,$img);
                    $timer_image->save();
                    $number++;

                }
            }

            $data = array();
            $timer_log_image = array();
            $timer_break_imgages = TimerImage::where('key_id', $timerLog->id)->where('type', 2)->get();
            foreach($timer_break_imgages as $row){
                $timer_log_image[] = AmazoneBucket::url() . $row->image;
            }
            $data['id'] = $timerLog->id;
            $data['timer_id'] = $timerLog->timer_id;
            $data['location'] = $timerLog->location;
            $data['longitude'] = $timerLog->longitude;
            $data['latitude'] = $timerLog->latitude;
            $data['start_time'] = $timerLog->time_started;
            $data['reason'] = $timerLog->reason;
            $data['end_time'] = $timerLog->time_finished;
            $data['images'] = $timer_log_image;


            return response()->json(array("status" => true, "timerLog" => $data ));
        endif;
    }

    public function continueTimer(Request $request){

        $validator  =   Validator::make(Input::all(),['timer_log_id' => 'required', 'end_time' => 'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:

            $timerLog = TimerLog::where('id', $request->timer_log_id)->first();

            $timerLog->time_finished = $request->end_time;

            $timerLog->update();

            $data = array();

            $data['id'] = $timerLog->id;
            $data['timer_id'] = $timerLog->timer_id;
            $data['location'] = $timerLog->location;
            $data['longitude'] = $timerLog->longitude;
            $data['latitude'] = $timerLog->latitude;
            $data['start_time'] = $timerLog->time_started;
            $data['reason'] = $timerLog->reason;
            $data['end_time'] = $timerLog->time_finished;

            return response()->json(array("status" => true, "timerLog" => $data ));
        endif;
    }


    public function submitTimerComments(Request $request){
        $validator  =   Validator::make(Input::all(),['timer_id' => 'required', 'time' => 'required','message'=>'required','location'=>'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            $timerComment = new TimerComment();
            $timerComment->user_id = $request->header('userId');
            $timerComment->timer_id = $request->timer_id;
            $timerComment->time = $request->time;
            $timerComment->message = $request->message;
            $timerComment->location = $request->location;
            $timerComment->latitude = $request->latitude;
            $timerComment->longitude = $request->longitude;
            $timerComment->save();

            $image =  Input::file("images");
            $number = 0;
            if($request->hasFile('images')){
                foreach($image as $img){
                    $timer_image = new TimerImage();
                    $timer_image->key_id = $timerComment->id;
                    $timer_image->type = 1;
                    // $ext = $img->getClientOriginalExtension();
                    // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                    $dest = 'files/timer/comment/image';
                    // $img->move($dest, $filename);
                    // $timer_image->image    =    $dest . '/' . $filename;

                    $timer_image->image = FunctionUtils::imageUpload($dest,$img);
                    $timer_image->save();
                    $number++;

                }

            }

            $timermessage = TimerComment::where('user_id',$request->header('userId'))->where('timer_id',$request->timer_id)->get();

            $data = array();
            foreach ($timermessage as $row){
                $timer_comment_image= array();
                $timer_comment_imgages = TimerImage::where('key_id', $row->id)->where('type', 1)->get();
                foreach($timer_comment_imgages as $items){
                    $timer_comment_image[] = AmazoneBucket::url() . $items->image;
                }
                $data[] =   array(
                    'id'            => $row->id,
                    'message'      => $row->message,
                    'location'     =>$row->location,
                    'time'   => $row->time,
                    'latitude'   => $row->latitude,
                    'longitude'   => $row->longitude,
                    'images'=> $timer_comment_image
                );

            }

            return response()->json(array("status" => true, "comment" => $data));
        endif;
    }


    public function searchTimer(Request $request){
        $timerQuery =  Timer::where('user_id',$request->header('userId'))->where('status' ,'!=', '0' );
        if($request->from  && $request->from != ""){
            $timerQuery->whereDate('time_started','>=',Carbon::parse($request->from)->format('Y-m-d'));
        }
        if($request->to  && $request->to != ""){
            $timerQuery->whereDate('time_started','<=',Carbon::parse($request->to)->format('Y-m-d'));

        }
        if($request->location  && $request->location != ""){
            $timerQuery->where('location','like','%'.$request->location.'%');
        }
        if ($request->duration){
            $h = floor($request->duration / 3600);
            $m = floor(($request->duration % 3600) / 60);
            $s = $request->duration - ($h * 3600) - ($m * 60);
            $durations = sprintf('%02d:%02d:%02d', $h, $m, $s);
            $timerQuery->whereTime('total_time','<=',$durations);
        }
        $timerQuries = $timerQuery->orderBy('id','desc')->get();

        if($request->user_type ==1){
            if ($request->client  && $request->client != ""){
                $timer = array();
                foreach ($timerQuries as $row){
                    $clientType = $row->timerClient->where('user_type',1);
                    $clients = $clientType->pluck('user_id')->toArray();
                    $requestClient = $request->client;
                    $intersect = array_intersect($clients,$requestClient);
                    if ($this->array_equal($intersect,$requestClient)){
                        $timer[] = $row->id;
                    }

                }
                $timerQuries = $timerQuery->whereIn('id',$timer)->get();
            }
        }elseif ($request->user_type ==2){
            if ($request->client  && $request->client != ""){
                $timer = array();
                foreach ($timerQuries as $row){
                    $clientType = $row->timerClient->where('user_type',2);
                    $clients =$clientType->pluck('user_id')->toArray();
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
                $tags = TimerAttachedTag::where('timer_id',$items->id)->where('tag','like','%'.$request->tag.'%')->count();
                if ($tags >0 ){
                    $timers[] = $items->id;
                    break;
                }
            }
            $timerQuries = $timerQuery->whereIn('id',$timers)->get();

        }
        $data= array();
        foreach ($timerQuery->get() as $row){
            $timerAttachedTag= TimerAttachedTag::where('timer_id',$row->id)->pluck("tag");
            $timerClient =TimerClient::where('timer_id',$row->id)->get();

            $client = array();
            foreach ($timerClient as $items){
                if ($items->user_type == 1){
                    $client[] =   array(
                        'id'            => $items->id,
                        'user_id'     => $items->user_id,
                        'full_name'     => $items->userInfo->first_name." ".$items->userInfo->last_name,
                    );
                }elseif ($items->user_type == 2){
                    $client[] =   array(
                        'id'            => $items->id,
                        'user_id'     => $items->user_id,
                        'full_name'     => $items->emailUserInfo->email,
                    );

                }
            }
//            dd($client);

            $clientsname = array();
            foreach ($timerClient as $timerClients){
                if ($items->user_type == 1){
                    $clientsname[] =  $timerClients->userInfo->first_name.' '.$timerClients->userInfo->last_name;

                }elseif ($items->user_type == 2){
                    $clientsname[] =  $timerClients->emailUserInfo->email;


                }
            }

            $companyName = array();
            foreach ($timerClient as $rows){
                if ($rows->user_type ==1){
                    $companyId  =   0;
                    if(Employee::where('user_id', $rows->user_id)->count()!=0):
                        $companyId = Employee::where('user_id', $rows->user_id)->first()->company_id;
                    else :
                        $companyId   =   Company::where('user_id', $rows->user_id)->first()->id;
                    endif;

                    $companyName[] = Company::where('id',$companyId)->first()->name;
                }else if ($rows->user_type ==2){

                    $companyName[] =  @$rows->emailUserInfo->emailClient->company_name;
                }
            }

            $data[] =   array(
                'id'            => $row->id,
                'location'      => $row->location,
                'longitude'     => $row->longitude,
                'latitude'      => $row->latitude,
                'time_started'  => $row->time_started,
                'time_ended'    => $row->time_ended,
                'status'        => $row->status,
                'total_time'    => substr_replace( $row->total_time ,"",-3),
                'clients_name' =>implode(", ", $clientsname),
                'company_name' =>implode(", ", array_unique($companyName)),
                'tags'          => $timerAttachedTag,

            );

        }
        $timerQuries = $data;

        return response()->json(array("status" => true, "timer" =>$timerQuries ));

    }

    public function timerAttachedTag(Request $request){
        $validator  =   Validator::make(Input::all(),['timer_id' => 'required', 'user_id' => 'required', 'tag' => 'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $errors[]=$messages[0];
            }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            $tag= Input::get('tag');
            foreach ($tag as $row){
                if(TimerAttachedTag::where('timer_id',$request->timer_id)->where('tag',$row)->count()!=0){

                    continue;

                }else{
                    $timerAttachedTag = new TimerAttachedTag();
                    $timerAttachedTag->timer_id = $request->timer_id;
                    $timerAttachedTag->user_id = $request->user_id;
                    $timerAttachedTag->tag = $row;
                    $timerAttachedTag->save();

                }
            }
            $timers = TimerAttachedTag::where('timer_id',$request->timer_id)->where('user_id',$request->user_id)->get();
            $tag = array();
            foreach ($timers as $items){
                $tag[] =  $items->tag;
            }
            return response()->json(array("status" => true, "timer_attached_tag" =>implode(", ", $tag) ));

        endif;

    }


    public function timerDetailsById(Request $request,$id)
    {
        $timer = Timer::where('id', $id)->first();

        $timerClient =TimerClient::where('timer_id',$id)->get();
        $client = array();
        foreach ($timerClient as $items){
            if ($items->usert_type == 1){
                $client[] =   array(
                    'id'            => $items->id,
                    'user_id'     => $items->user_id,
                    'full_name'     => $items->userInfo->first_name." ".$items->userInfo->last_name,
                );
            }elseif($items->usert_type == 2){
                $client[] =   array(
                    'id'            => $items->id,
                    'user_id'     => $items->user_id,
                    'full_name'     => $items->emailUserInfo->email,
                );

            }

        }
        $clientsname = array();
        foreach ($timerClient as $timerClients){
            if ($timerClients->user_type == 1){
                $clientsname[] =  $timerClients->userInfo->first_name.' '.$timerClients->userInfo->last_name;

            }elseif ($timerClients->user_type == 2){
                $clientsname[] =  $timerClients->emailUserInfo->email;
            }
        }
        $companyName = array();
        foreach ($timerClient as $rows) {
            if ($rows->user_type == 1) {
                $companyId = 0;
                if (Employee::where('user_id', $rows->user_id)->count() != 0):
                    $companyId = Employee::where('user_id', $rows->user_id)->first()->company_id;
                else :
                    $companyId = Company::where('user_id', $rows->user_id)->first()->id;
                endif;

                $companyName[] = Company::where('id', $companyId)->first()->name;
            } else if ($rows->user_type == 2) {
                $companyName[] = @$rows->emailUserInfo->emailClient->company_name;
            }
        }





        $timers = array();
        $timerAttachedTag   =   array();
        if(TimerAttachedTag::where('timer_id', $timer->id)->count()>0){
            $timerAttachedTag = TimerAttachedTag::where('timer_id', $timer->id)->pluck("tag");
        }
        $timerComment = TimerComment::where('timer_id', $id)->orderBy('created_at', 'desc')->get();
        $timerTimeline = array();
        foreach ($timerComment as $items) {
            $timer_comment_image= array();
            $timer_comment_imgages = TimerImage::where('key_id', $items->id)->where('type', 1)->get();
            foreach($timer_comment_imgages as $row){
                $timer_comment_image[] = AmazoneBucket::url() . $row->image;
            }

            $timerTimeline[] = array(
                'id' => $items->id,
                'timer_id' => $items->timer_id,
                'user_id' => $items->user_id,
                'time' => Carbon::parse($items->time)->format('d-M-Y g:i A'),
                'location' => $items->location,
                'message' => $items->message,
                'images' =>$timer_comment_image,
                'dateSorting' => Carbon::parse($items->created_at)->format('d-M-Y g:i A'),
                'type' => 1,

            );





        }
        $timerLogs = TimerLog::where('timer_id', $id)->orderBy('created_at', 'desc')->get();
        foreach ($timerLogs as $items) {
            $timer_log_image = array();
            $timer_break_imgages = TimerImage::where('key_id', $items->id)->where('type', 2)->get();

            foreach($timer_break_imgages as $row){
                $timer_log_image[] = AmazoneBucket::url() . $row->image;
            }

            $timerTimeline[] = array(
                'id' => $items->id,
                'timer_id' => $items->timer_id,
                'location' => $items->location,
                'longitude' => $items->longitude,
                'latitude' => $items->latitude,
                'time_started' =>Carbon::parse($items->time_started)->format('d-M-Y g:i A'),
                'time_finished' => Carbon::parse($items->time_finished)->format('d-M-Y g:i A'),
                'reason' => $items->reason,
                'images' =>$timer_log_image,
                'dateSorting' => Carbon::parse($items->created_at)->format('d-M-Y g:i A'),
                'type' => 2,

            );
        }
        //        timer sorting according to dateAdded
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
        $timers['id'] = $timer->id;
        $timers['location'] = $timer->location;
        $timers['longitude'] = $timer->longitude;
        $timers['latitude'] = $timer->latitude;
        $timers['time_started'] =Carbon::parse($timer->time_started)->format('d-M-Y g:i A') ;
        $timers['time_ended'] =Carbon::parse( $timer->time_ended)->format('d-M-Y g:i A');
        $timers['status'] = $timer->status;
        $timers['total_hour'] =$timesss[0] ;
        $timers['total_min'] = $timesss[1];
        $timers['total_sec'] = $timesss[2];
        $timers['total_time'] = substr_replace($timer->total_time, "", -3);
        $timers['tag'] = $timerAttachedTag;
        $timers['clients_name'] =implode(", ", $clientsname);
        $timers['company_name'] =implode(", ", array_unique($companyName));
        $timers['timeline'] = $timerTimeline;
        return response()->json(array("status" => true, "timer" => $timers));
    }






}
