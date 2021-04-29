<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Timer;
use App\Company;
use App\TimerImage;
use App\TimerLog;
use App\TimerClient;
use App\TimerComment;
use App\TimerAttachedTag;
use App\SentDcoketTimerAttachment;
use App\User;
use Carbon\Carbon;
use App\TimerSetting;
use PDF;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\V2\AmazoneBucket;

class TimerController extends Controller
{
    public function index(){
        $companyUsers   =   Auth::user()->company()->allCompanyUsers()->pluck('id')->toArray();
        $timers         =   Timer::whereIn('user_id', $companyUsers)->where('status' ,'!=', '0' )->orderBy('created_at', 'desc')->paginate(10);

        $timerClients   =   TimerClient::whereIn('timer_id',$timers->pluck('id'))->pluck('user_id')->toArray();
        $timersClient   =   User::whereIn('id',array_unique($timerClients))->orderBy('created_at', 'desc')->get();

        $timer_setting  =   Auth::user()->company()->timerSetting();

        return view('dashboard.company.timers.index', compact('timers','timersClient', 'timer_setting'));
    }

    public function nonEmployee(){

        $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
        $employeeIds[]   =    Company::where('id',Session::get('company_id'))->first()->user_id;
        $timerIds         = Timer::whereNotIn('user_id', $employeeIds)->pluck('id');
        $timerClients =  TimerClient::whereIn('timer_id', $timerIds)->whereIn('user_id', $employeeIds)->pluck('timer_id');
        $timerClient =  TimerClient::whereIn('timer_id', $timerIds)->whereIn('user_id', $employeeIds)->pluck('user_id')->toArray();
        $timers = Timer::whereIn('id', $timerClients)->where('status' ,'!=', '0' )->orderBy('created_at', 'desc')->paginate(10);
        $uniqueTimersClient = User::whereIn('id',array_unique($timerClient))->orderBy('created_at', 'desc')->get();

        return view('dashboard.company.timers.nonEmployee', compact('timers','uniqueTimersClient'));
    }

    public function nonEmployeeTemplate(Request $request){

        $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
        $employeeIds[]   =    Company::where('id',Session::get('company_id'))->first()->user_id;
        $timerIds         = Timer::whereNotIn('user_id', $employeeIds)->pluck('id');
        $timerClients =  TimerClient::whereIn('timer_id', $timerIds)->whereIn('user_id', $employeeIds)->pluck('timer_id');
        $timerClient =  TimerClient::whereIn('timer_id', $timerIds)->whereIn('user_id', $employeeIds)->pluck('user_id')->toArray();
        $timers = Timer::whereIn('id', $timerClients)->where('status' ,'!=', '0' )->orderBy('created_at', 'desc')->get();
        $uniqueTimersClient = User::whereIn('id',array_unique($timerClient))->orderBy('created_at', 'desc')->get();
        return view('dashboard.company.timers.nonEmployeeTemplate', compact('timers','uniqueTimersClient'));


    }
    public function nonEmployeeActive(){
        $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
        $employeeIds[]   =    Company::where('id',Session::get('company_id'))->first()->user_id;
        $timerIds         = Timer::whereNotIn('user_id', $employeeIds)->pluck('id');
        $timerClients =  TimerClient::whereIn('timer_id', $timerIds)->whereIn('user_id', $employeeIds)->pluck('timer_id');
        $timerClient =  TimerClient::whereIn('timer_id', $timerIds)->whereIn('user_id', $employeeIds)->pluck('user_id')->toArray();
        $timers = Timer::whereIn('id', $timerClients)->where('status' ,'==', '0' )->orderBy('created_at', 'desc')->get();
        $uniqueTimersClient = User::whereIn('id',array_unique($timerClient))->orderBy('created_at', 'desc')->get();
        return view('dashboard.company.timers.nonEmployeeActive', compact('timers','uniqueTimersClient'));
    }

    public function employeeTemplate(Request $request){
        $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
        $employeeIds[]   =    Company::where('id',Session::get('company_id'))->first()->user_id;
        $timers         = Timer::whereIn('user_id', $employeeIds)->where('status' ,'!=', '0' )->orderBy('created_at', 'desc')->get();
        $timersClients = TimerClient::whereIn('user_id',$employeeIds)->pluck('user_id')->toArray();
        $timersClient = User::whereIn('id',array_unique($timersClients))->orderBy('created_at', 'desc')->get();
        $timer_setting = TimerSetting::where('company_id', session::get('company_id'))->first();

        return view('dashboard.company.timers.employeeTemplate', compact('timers','timersClient','timer_setting'));

    }

    public function employeeActive(){
        $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
        $employeeIds[]   =    Company::where('id',Session::get('company_id'))->first()->user_id;
        $timers         = Timer::whereIn('user_id', $employeeIds)->where('status' ,'==', '0' )->orderBy('created_at', 'desc')->get();
        $timersClients = TimerClient::whereIn('user_id',$employeeIds)->pluck('user_id')->toArray();
        $timersClient = User::whereIn('id',array_unique($timersClients))->orderBy('created_at', 'desc')->get();
        $timer_setting = TimerSetting::where('company_id', session::get('company_id'))->first();
        return view('dashboard.company.timers.employeeActive', compact('timers','timersClient','timer_setting'));
    }

    public function create(){
        $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
        $employeeIds[]  =   Company::where('id',Session::get('company_id'))->first()->user_id;
        $timers         =   Timer::whereIn('user_id', $employeeIds)->where('status', 0)->pluck('user_id');
        $employees      =   Employee::where('company_id',Session::get('company_id'))->whereNotIn('user_id', $timers)->get();

        return view('dashboard.company.timers.create', compact('employees'));
    }

    public function store(Request $request){

        $timer                  = new Timer();
        $timer->user_id         = $request->user_id ;
        $timer->location        = $request->location;
        $timer->latitude        = $request->latitude;
        $timer->longitude       = $request->longitude;
        $timer->time_started    = $request->time_started;
        $timer->status          = 0;

        $timer->save();

        flash('Timer started Sucessfully','success');
        return redirect()->route('timers');

    }

    public function pause($id){

        $timer = Timer::where('id', $id)->first();

        return view('dashboard.company.timers.pause', compact('timer'));

    }

    public function pauseStore(Request $request){


        $timer_log                  = new TimerLog();
        $timer_log->timer_id        = $request->timer_id ;
        $timer_log->location        = $request->location;
        $timer_log->latitude        = $request->latitude;
        $timer_log->longitude       = $request->longitude;
        $timer_log->time_started    = $request->time_started;
        $timer_log->reason          = $request->reason;

        $timer_log->save();

        flash('Timer Paused Sucessfully','success');
        return redirect()->route('timers');
    }

    public function resume($id){

        $timer_log = TimerLog::where('id', $id)->first();

        return view('dashboard.company.timers.resume', compact('timer_log'));

    }

    public function resumeStore(Request $request){

        $timer_log = TimerLog::where('id', $request->timer_log_id)->first();

        $timer_log->time_finished = $request->time_finished;

        $timer_log->update();

        flash('Timer Resumed Sucessfully','success');
        return redirect()->route('timers');
    }

    public function stop($id){

        $timer = Timer::where('id', $id)->first();

        return view('dashboard.company.timers.stop', compact('timer'));

    }

    public function stopStore(Request $request){

        $timer = Timer::where('id', $request->timer_id)->first();

        $timer->time_ended = $request->time_ended;
        $timer->status = 1;

        $timer->update();

        flash('Timer Stopped Sucessfully','success');
        return redirect()->route('timers');
    }

    public  function  checkCompanyId($timer){
        $companyId  =   0;
        if(Employee::where('user_id', $timer->user_id)->count()!=0):
            $companyId = Employee::where('user_id', $timer->user_id)->first()->company_id;
        else :
            $companyId   =   Company::where('user_id', $timer->user_id)->first()->id;
        endif;
        return $companyId;
    }



    public function view($id){
//        $timers = array();
        $timer = Timer::where('id', $id)->first();

        $companyDetail = Company::where('id',$this->checkCompanyId($timer))->first();

        $timer_clients = TimerClient::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();

        $timer_breaks  = TimerLog::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();
        $loginterval = array();
        foreach ($timer_breaks as $row){
            $logdatetime1 = \Carbon\Carbon::parse($row->time_started);
            $logdatetime2 = \Carbon\Carbon::parse($row->time_finished);

            $loginterval[] = $logdatetime2->diffInSeconds($logdatetime1);
        }
        $break_time = array_sum($loginterval);

        $timerTimeline = array();
        foreach ($timer_breaks as $items) {
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
                'time_started' => $items->time_started,
                'time_finished' => $items->time_finished,
                'reason' => $items->reason,
                'images' =>$timer_log_image,
                'dateSorting' => Carbon::parse($items->created_at)->format('d-M-Y g:i A'),
                'type' => 2,

            );

        }
        $timer_comments =  TimerComment::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();
        foreach ($timer_comments as $items) {
            $timer_comment_image= array();
            $timer_comment_imgages = TimerImage::where('key_id', $items->id)->where('type', 1)->get();
            foreach($timer_comment_imgages as $row){
                $timer_comment_image[] = AmazoneBucket::url() . $row->image;
            }
            $timerTimeline[] = array(
                'id' => $items->id,
                'timer_id' => $items->timer_id,
                'user_id' => $items->user_id,
                'time' => $items->time,
                'location' => $items->location,
                'message' => $items->message,
                'longitude' => $items->longitude,
                'latitude' => $items->latitude,
                'images' =>$timer_comment_image,
                'dateSorting' => Carbon::parse($items->created_at)->format('d-M-Y g:i A'),
                'type' => 1,

            );

        }

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
// dd($timerTimeline);
        $timer_tags = TimerAttachedTag::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();

        $timer_dockets = SentDcoketTimerAttachment::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();




        return view('dashboard.company.timers.view', compact('timer', 'timer_clients', 'timer_breaks', 'timer_comments', 'timer_tags', 'timer_tags', 'timer_dockets','timerTimeline','break_time','companyDetail'));
    }

    public function download($id){

        $timer = Timer::where('id', $id)->first();
        $companyDetail = Company::where('id',$this->checkCompanyId($timer))->first();


        $timer_clients = TimerClient::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();

        $timer_breaks  = TimerLog::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();



        $loginterval = array();
        foreach ($timer_breaks as $row){
            $logdatetime1 = \Carbon\Carbon::parse($row->time_started);
            $logdatetime2 = \Carbon\Carbon::parse($row->time_finished);

            $loginterval[] = $logdatetime2->diffInSeconds($logdatetime1);
        }
        $break_time = array_sum($loginterval);

        $timerTimeline = array();
        foreach ($timer_breaks as $items) {
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
                'time_started' => $items->time_started,
                'time_finished' => $items->time_finished,
                'reason' => $items->reason,
                'images' =>$timer_log_image,

                'dateSorting' => Carbon::parse($items->created_at)->format('d-M-Y  H:i:s'),
                'type' => 2,
            );
        }

        $timer_comments =  TimerComment::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();
        foreach ($timer_comments as $items) {
            $timer_comment_image= array();
            $timer_comment_imgages = TimerImage::where('key_id', $items->id)->where('type', 1)->get();
            foreach($timer_comment_imgages as $row){
                $timer_comment_image[] = AmazoneBucket::url() . $row->image;
            }
            $timerTimeline[] = array(
                'id' => $items->id,
                'timer_id' => $items->timer_id,
                'user_id' => $items->user_id,
                'time' => $items->time,
                'location' => $items->location,
                'message' => $items->message,
                'longitude' => $items->longitude,
                'latitude' => $items->latitude,
                'images' =>$timer_comment_image,
                'dateSorting' => Carbon::parse($items->created_at)->format('d-M-Y  H:i:s'),
                'type' => 1,

            );
        }
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


        $timer_tags = TimerAttachedTag::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();
        $timer_dockets = SentDcoketTimerAttachment::where('timer_id', $timer->id)->orderBy('created_at', 'DESC')->get();
        // return view('dashboard.company.timers.pdf',compact('timer', 'timer_clients', 'timer_breaks', 'timer_comments', 'timer_tags', 'timer_tags', 'timer_dockets','timerTimeline','break_time'));
        $pdf = PDF::loadView('dashboard.company.timers.pdf',compact('timer', 'timer_clients', 'timer_breaks', 'timer_comments', 'timer_tags', 'timer_tags', 'timer_dockets','timerTimeline','break_time','companyDetail'));
//        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true, 'isJavascriptEnabled' => true]);
//        $fileName=preg_replace('/\s+/', '-',$timer->id."-".$timer->time_started);
//
//        return $pdf->stream($fileName.'.pdf');
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true, 'isJavascriptEnabled' => true]);
        $fileName=preg_replace('/\s+/', '-',$timer->id."-".$timer->time_started);
        return $pdf->download($fileName.'.pdf');
    }

    public function searchTimer(Request $request){
//        dd($request->client);
        $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
        $employeeIds[]   =    Company::where('id',Session::get('company_id'))->first()->user_id;
        $timersClients = TimerClient::whereIn('user_id',$employeeIds)->pluck('user_id')->toArray();
        $timersClient = User::whereIn('id',array_unique($timersClients))->orderBy('created_at', 'desc')->get();
        $timerQuery =  Timer::whereIn('user_id',$employeeIds)->where('status' ,'!=', '0' );

        $totalSecondDuration = strtok($request->duration*3600, ".");

        if($request->from  && $request->from != ""){
            $timerQuery->whereDate('time_started','>=',Carbon::parse($request->from)->format('Y-m-d'));
        }
        if($request->to  && $request->to != ""){
            $timerQuery->whereDate('time_started','<=',Carbon::parse($request->to)->format('Y-m-d'));

        }
        if($request->location  && $request->location != ""){
            $timerQuery->where('location','like','%'.$request->location.'%');
        }
        $timerQuries = $timerQuery->orderBy('id','desc')->get();

        if ($totalSecondDuration) {
            if ($totalSecondDuration == 36000) {
                $timerQuery = Timer::whereIn('user_id', $employeeIds)->where('status', '!=', '0');
            } else {
//                $h = floor($totalSecondDuration / 3600);
//                $m = floor(($totalSecondDuration % 3600) / 60);
//                $s = $totalSecondDuration - ($h * 3600) - ($m * 60);
//                $durations = sprintf('%02d:%02d:%02d', $h, $m, $s);
//                $timerQuery->whereTime('total_time', '<=', $durations);
                foreach ($timerQuries as $row){
                    $h = floor($totalSecondDuration / 3600);
                    $m = floor(($totalSecondDuration % 3600) / 60);
                    $s = $totalSecondDuration - ($h * 3600) - ($m * 60);
                    $durations = sprintf('%02d:%02d:%02d', $h, $m, $s);
                    $timerQuriess = $timerQuery->whereTime('total_time', '<=', $durations);
                }
                $timerQuries = $timerQuriess->orderBy('id','desc')->get();

            }
        }
        if ($request->client  && $request->client != ""){
            $timer = array();
            foreach ($timerQuries as $row){
                $clients = $row->timerClient->pluck('user_id')->toArray();
                $requestClient =  $request->client;
                $intersect = array_map("strval",array_intersect($clients,$requestClient));
                if ($this->array_equal($intersect,$requestClient)){
                    $timer[] = $row->id;
                }
            }
            $timerQuries = $timerQuery->whereIn('id',$timer)->get();

        }

        if ($request->tags && $request->tags != ""){
            $timers = array();
            foreach ($timerQuries as $items){
                $tags = TimerAttachedTag::where('timer_id',$items->id)->where('tag','like','%'.$request->tags.'%')->count();
                if ($tags >0 ){
                    $timers[] = $items->id;
                    break;
                }
            }
            $timerQuries = $timerQuery->whereIn('id',$timers)->get();
        }


        return view('dashboard.company.timers.filterTimer',compact('timerQuries','timersClient','request'));

    }

    public  function timerFilter(Request $request){
        return view('dashboard.company.timers.filterTimer');
    }




    public function array_equal($a, $b)
    {
        return (
            is_array($a)
            && is_array($b)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    public function storeTimerSettings(Request $request)
    {
        $timer_setting = new TimerSetting();

        $timer_setting->company_id =  Session::get('company_id');
        if($request->comment_image != 0){
            $timer_setting->comment_image = $request->validation_comment;
        }else{

            $timer_setting->comment_image = $request->comment_image;
        }

        if($request->pause_image != 0){
            $timer_setting->pause_image = $request->validation_pause;
        }else{

            $timer_setting->pause_image = $request->pause_image;
        }

        $timer_setting->save();

        flash('Timer settings saved Sucessfully','success');
        return redirect()->back();
    }

    public function updateTimerSettings(Request $request)
    {
        $timer_setting = TimerSetting::where('company_id',Session::get('company_id'))->first();
        $timer_setting->comment_image = $request->comment_image;
        $timer_setting->pause_image = $request->pause_image;
        $timer_setting->update();

        flash('Timer settings updated successfully','success');
        return redirect()->back();
    }

    public function filterNonEmployeeTimer(Request $request){
        $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
        $employeeIds[]   =    Company::where('id',Session::get('company_id'))->first()->user_id;
        $timerIds         = Timer::whereNotIn('user_id', $employeeIds)->pluck('id');
        $timerClients =  TimerClient::whereIn('timer_id', $timerIds)->whereIn('user_id', $employeeIds)->pluck('timer_id');
        $timerClient =  TimerClient::whereIn('timer_id', $timerIds)->whereIn('user_id', $employeeIds)->pluck('user_id')->toArray();
        $uniqueTimersClient = User::whereIn('id',array_unique($timerClient))->orderBy('created_at', 'desc')->get();
        $timerQuery = Timer::whereIn('id', $timerClients)->where('status' ,'!=', '0' );


        $totalSecondDuration = strtok($request->duration*3600, ".");

        if($request->from  && $request->from != ""){
            $timerQuery->whereDate('time_started','>=',Carbon::parse($request->from)->format('Y-m-d'));
        }
        if($request->to  && $request->to != ""){
            $timerQuery->whereDate('time_started','<=',Carbon::parse($request->to)->format('Y-m-d'));

        }
        if($request->location  && $request->location != ""){
            $timerQuery->where('location','like','%'.$request->location.'%');

        }
        $timerQuries = $timerQuery->orderBy('id','desc')->get();

        if ($totalSecondDuration){
            if ($totalSecondDuration == 36000 ){
                $timerQuery = Timer::whereIn('id', $timerClients)->where('status' ,'!=', '0' );
            }else{
                foreach ($timerQuries as $row){
                    $h = floor($totalSecondDuration / 3600);
                    $m = floor(($totalSecondDuration % 3600) / 60);
                    $s = $totalSecondDuration - ($h * 3600) - ($m * 60);
                    $durations = sprintf('%02d:%02d:%02d', $h, $m, $s);
                    $timerQuriess = $timerQuery->whereTime('total_time', '<=', $durations);
                }
                $timerQuries = $timerQuriess->orderBy('id','desc')->get();

            }

        }
        if ($request->client  && $request->client != ""){
            $timer = array();
            foreach ($timerQuries as $row){
                $clients = $row->timerClient->pluck('user_id')->toArray();
                $requestClient =  $request->client;
                $intersect = array_map("strval",array_intersect($clients,$requestClient));
                if ($this->array_equal($intersect,$requestClient)){
                    $timer[] = $row->id;
                }
            }
            $timerQuries = $timerQuery->whereIn('id',$timer)->get();

        }

        if ($request->tags && $request->tags != ""){
            $timers = array();
            foreach ($timerQuries as $items){
                $tags = TimerAttachedTag::where('timer_id',$items->id)->where('tag','like','%'.$request->tags.'%')->count();
                if ($tags >0 ){
                    $timers[] = $items->id;
                    break;
                }
            }
            $timerQuries = $timerQuery->whereIn('id',$timers)->get();
        }

        return view('dashboard.company.timers.filterNonEmployee',compact('timerQuries','uniqueTimersClient','request'));
    }
    public  function filterNonEmployee(Request $request){
        return view('dashboard.company.timers.filterNonEmployee');
    }


}
