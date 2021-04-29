<?php

namespace App\Helpers\V2;

use App\DocketFiledPreFiller;
use App\DocketGridAutoPrefiller;
use App\DocketGridPrefiller;
use App\DocketPrefillerValue;
use App\Repositories\V2\SubscriptionLogRepository;
use App\Repositories\V2\SentInvoiceRepository;
use App\Repositories\V2\EmailSentInvoiceRepository;
use App\Repositories\V2\CompanyRepository;
use App\Repositories\V2\EmployeeRepository;
use Carbon\Carbon;
use Storage;

class FunctionUtils
{
    public static function buildTreeArray(array $prefiller, $parentId = 0) {
        $branch = array();
        foreach ($prefiller as $prefillers) {
            if ($prefillers['root_id'] == $parentId) {
                $children = FunctionUtils::buildTreeArray($prefiller, $prefillers['id']);
                if ($children) {
                    $prefillers['prefiller'] = $children;
                }else{
                    $prefillers['prefiller'] =[];
                }
                $branch[] = $prefillers;
            }
        }
        return $branch;
    }

    public static function array_values_recursive($ary){
        $lst = array();
        foreach( array_keys($ary) as $k ){
            $v = $ary[$k];
            if (is_scalar($v)) { $lst[] = $v;}
            elseif (is_array($v)) {
                $lst = array_merge( $lst, FunctionUtils::array_values_recursive($v));
            }
        }
        return $lst;
    }

    public static function buildAutoPrefillerTreeArray(array $prefiller, $parentId = 0){
        $branch = array();
        foreach ($prefiller as $prefillers) {
            $autoPrefillerLinkedGridId =  DocketGridAutoPrefiller::where('grid_field_id',$prefillers['docket_field_grid_id'])->where('docket_field_id',$prefillers['docket_field_id'])->where('index',$prefillers['index'])->get();
            if ($prefillers['root_id'] == $parentId) {
                $children = FunctionUtils::buildAutoPrefillerTreeArray($prefiller, $prefillers['id']);
                if ($children) {
                    $prefillers['prefiller'] = $children;
                }else{
                    $prefillers['prefiller'] =[];
                }
                if($prefillers['root_id']==0){
                    $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>$prefillers['docket_field_grid_id'],'prefiller'=>$prefillers['prefiller']);
                }else{
                    if($autoPrefillerLinkedGridId->first()->link_grid_field_id != null){
                        $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>@$autoPrefillerLinkedGridId->first()->link_grid_field_id,'prefiller'=>$prefillers['prefiller']);
                    }
                }
            }
        }
        return $branch;
    }

    public static function getParentData($data,$type){
        return FunctionUtils::docketPreFiller(new DocketGridPrefiller(),$data,$type);
    }

    public static function getDocketPrefiller($data,$type){
        return FunctionUtils::docketPreFiller(new DocketPrefillerValue(),$data,$type);
    }

    public static function getNormalParentData($data,$type){
        return FunctionUtils::docketPreFiller(new DocketFiledPreFiller(),$data,$type);
    }

    public static function docketPreFiller($model,$data,$type=null){
        $docketPrefillerValues = $model->where('id',$data)->select('id','root_id',$type)->get();
        $child =array();
        if (count($docketPrefillerValues)!=0){
            foreach ($docketPrefillerValues as $datass){
                $child[] = $datass['value'];
                $child[] = FunctionUtils::docketPreFiller($model,$datass->root_id);
            }
        }
        return $child;
    }

    public static function generateRandomString($length = 60) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function array_equal($a, $b) {
        return (
            is_array($a)
            && is_array($b)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    public static function checkSubscription($company){
        $subscriptionLogRepository = new SubscriptionLogRepository();
        $sentInvoiceRepository = new SentInvoiceRepository();
        $emailSentInvoiceRepository = new EmailSentInvoiceRepository();
        $subscriptionLogQuery = $subscriptionLogRepository->getDataWhere([['company_id', $company->id]]);
        if ($subscriptionLogQuery->count() > 0) {
            $lastUpdatedSubscription = $subscriptionLogQuery->orderBy('id', 'desc')->first();
            $monthDay = Carbon::parse($lastUpdatedSubscription->created_at)->format('d');
            $now = Carbon::now();
            $currentMonthStart = Carbon::parse($now->format('Y') . "-" . $now->format('m') . "-" . $monthDay);
            $currentMonthEnd = Carbon::parse($now->format('Y') . "-" . $now->format('m') . "-" . $monthDay)->addDay(30);
        } else {
            $currentMonthStart = new Carbon('first day of this month');
            $currentMonthEnd = new Carbon('last day of this month');
        }

        $sentInvoices = $sentInvoiceRepository->getDataWhere([['company_id', $company->id]])->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();
        $emailInvoices = $emailSentInvoiceRepository->getDataWhere([['company_id', $company->id]])->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();
        return $sentInvoices + $emailInvoices;
    }

    public static function getCompanyId($userId){
        $companyId  =   0;
        $employeeRepository = new EmployeeRepository();
        $companyRepository = new CompanyRepository();
        $company = $employeeRepository->getDataWhere([['user_id', $userId]])->first();
        if($company != 0):
            $companyId = $company->company_id;
        else :
            $companyId   = $companyRepository->getDataWhere([['user_id', $userId]])->first()->id;
        endif;
        return $companyId;
    }

    public static function getCompanyAllUserId($companyId){
        $userId =   array();
        $employeeRepository = new EmployeeRepository();
        $companyRepository = new CompanyRepository();
        $employee  =  $employeeRepository->getDataWhere([['company_id',$companyId]])->pluck('user_id')->toArray();
        $userId     =   array_merge(array($companyRepository->getDataWhere([['id',$companyId]])->first()->user_id),$employee);
        return $userId;
    }

    public static function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    public static function findEcowisePrefillerValue(array $prefiller, $parentId = "0"){
        $branch = array();
        foreach ($prefiller as $prefillers) {
            if ($prefillers['root_id'] == $parentId) {
                $children = FunctionUtils::findEcowisePrefillerValue($prefiller, $prefillers['id']);
                if ($children) {

                    $prefillers['prefiller'] = array(['id' =>0, 'root_id' => 0, 'index' => $children[0]['index'], 'value' => $children[0]['value'], 'link_grid_field_id' => $children[0]['link_grid_field_id'],'prefiller' =>  $children[0]['prefiller']]);
                } else {
                    $prefillers['prefiller'] = [];
                }
                $branch[] = array('id' => 0, 'root_id' => 0, 'index' => $prefillers['index'], 'value' => $prefillers['value'], 'link_grid_field_id' => $prefillers['link_grid_field_id'], 'prefiller' => $prefillers['prefiller']);
            }
        }
        return $branch;
    }

    public static function searchForId($categoryId, $id, $array) {
        foreach ($array as $key => $val) {
            if ($val['category_id'] == $categoryId && $val['form_field_id'] == $id ) {
                return $val;
            }
        }
        return null;
    }

    public static function findAttachetTimerWithCategoryId($data) {
        foreach($data as $index => $datas) {
            if($datas['category_id'] == 17){
                return $datas;
            }
        }
    }

    public static function convertMilisecondtoMinHrs($data){
        $input = $data;
        $input = floor($input / 1000);
        $input = floor($input / 60);
        $minutes = $input % 60;
        $input = floor($input / 60);
        $hour = $input ;
        $hrs = "";
        $min = "";
        if (sprintf('%02d', $hour) == 01 || sprintf('%02d', $hour) == 00 ){
            $hrs = sprintf('%02d', $hour)." hour";
        }else{
            $hrs = sprintf('%02d', $hour)." hours";
        }
        if(sprintf('%02d', $minutes) == 01 || sprintf('%02d', $minutes) == 00 ){
            $min = sprintf('%02d', $minutes)." minute";
        }else {
            $min = sprintf('%02d', $minutes)." minutes";
        }
        return  $hrs." ".$min;
    }

    public static function buildAutoPrefillerTreeArrayList(array $prefiller, $parentId, $rootId,$gridField){
        $branch = array();
        foreach ($prefiller as $prefillers) {
            $autoPrefillerLinkedGridId =  DocketGridAutoPrefiller::where('grid_field_id',$prefillers['docket_field_grid_id'])->where('docket_field_id',$prefillers['docket_field_id'])->where('index',$prefillers['index'])->get();
            if ($prefillers['root_id'] == $parentId) {
                $children = FunctionUtils::buildAutoPrefillerTreeArrayList($prefiller, $prefillers['id'],$rootId,$gridField);
                if ($children) {
                    $prefillers['prefiller'] = $children;
                }else{
                    $prefillers['prefiller'] =[];
                }

                if($gridField->auto_field == 0){
                    if($prefillers['root_id']==0){
                        $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);
                    }elseif($prefillers['root_id'] == $rootId){
                        $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);
                    }else{
                        $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);

                    }
                }else{
                    if($prefillers['root_id']==0){
                        $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>intval($prefillers['docket_field_grid_id']),'prefiller'=>$prefillers['prefiller']);
                    }elseif($prefillers['root_id'] == $rootId){
                        $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>intval($prefillers['docket_field_grid_id']),'prefiller'=>$prefillers['prefiller']);
                    }else{
                        $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>@$autoPrefillerLinkedGridId->first()->link_grid_field_id,'prefiller'=>$prefillers['prefiller']);

                    }
                }

            }
        }
        return $branch;
    }

    public static function buildTreeArrayList(array $prefiller, $parentId, $rootId) {
        $branch = array();
        foreach ($prefiller as $prefillers) {
            if ($prefillers['root_id'] == $parentId) {
                $children = FunctionUtils::buildTreeArrayList($prefiller, $prefillers['id'],$rootId);
                if ($children) {
                    $prefillers['prefiller'] = $children;
                }else{
                    $prefillers['prefiller'] =[];
                }

               if($prefillers['root_id'] == $rootId){
                    $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);
                }else{
                    $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);
                }
            }
        }
        return $branch;
    }

    public static function folderList(array $folderList, $parentId = 0) {
        $branch = array();
        foreach ($folderList as $folderLists) {
            if ($folderLists['root_id'] == $parentId) {
                $children = FunctionUtils::folderList($folderList, $folderLists['id']);
                if ($children) {
                    $folderLists['folder'] = $children;
                }else{
                    $folderLists['folder'] =[];
                }
                $branch[] = $folderLists;
            }
        }
        return $branch;
    }

    public static function convertHrsMin($parameter) {
        $minutes =  ($parameter/(1000*60))%60;
        $hours = ($parameter /(1000*60*60))%1000000;
        $hours = ($hours < 10) ? "0" + $hours : $hours;
        $minutes = ($minutes < 10) ? "0" + $minutes : $minutes;

        if ($hours == 1 || $hours == 0 ){
            $hoursParm = " Hour";
        }else{
            $hoursParm = " Hours";
        }

        if ($minutes == 1 || $minutes == 0 ){
            $minutesParm = " Minute";
        }else{
            $minutesParm = " Minutes";
        }
        return $hours.$hoursParm ." ".$minutes.$minutesParm;
    }

    public static function conversationArrayDateSorting($conversationArray){
        $size = count($conversationArray);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($conversationArray[$j+1]["dateSorting"]) > strtotime($conversationArray[$j]["dateSorting"])) {
                    $tempArray   =    $conversationArray[$j+1];
                    $conversationArray[$j+1] = $conversationArray[$j];
                    $conversationArray[$j]  =   $tempArray;
                }
            }
        }
        return $conversationArray;
    }

    public static function unique_multidim_array_sum($array, $key ,$sumValue) {
        $temp_array = array();
        $i = 0;
        $key_array = array();
        $temp_index = 0;
       
        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $sum = 0;
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
                $i++;
            }
            foreach ($temp_array as $index => $temp) {
                if($temp['id'] == $val['id']){
                    $temp_index = $index;
                    $sum = $sum + (int)$val[$sumValue];
                }
            }
            $temp_array[$temp_index][$sumValue] = $sum;
            
        }
        return $temp_array;
    }

    public static function imageUpload($path,$image,$number=0,$isBlob=false){
        if(parse_url($_SERVER['SERVER_NAME'])['path'] != "127.0.0.1"){
            $imageName = Storage::disk('s3')->put($path, $image,'public');
            if($isBlob){
                // return AmazoneBucket::url() . '/' . $path;
                return $path;
            }else{
                // return AmazoneBucket::url() . '/' . $imageName;
                return $imageName;
            }
        }else{
            if($isBlob){
                \File::put(public_path(''). '/' . $path, base64_decode($image));
                // return asset($path);
                return $path;
            }else{
                $ext = $image->getClientOriginalExtension();
                if($ext == ""){
                    $ext = '.png';
                }
                $filename = basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time()."-".$number."." . $ext;
                if(!is_dir($path)){
                    $tempPath = "";
                    foreach(explode('/',$path) as $key => $shotPath){
                        $tempPath .= (($key == 0) ? '' : '/') . $shotPath;
                        if(!is_dir($tempPath)){
                            mkdir($tempPath);
                        }
                    }
                }
                $image->move($path,$filename);
                // return asset($path . '/' . $filename);
                return $path . '/' . $filename;
            }
        }
    }

    public static function implodeImage(){
        if(parse_url($_SERVER['SERVER_NAME'])['path'] == "test.recordtimeapp.com.au"){
            return 3;
        }else if(parse_url($_SERVER['SERVER_NAME'])['path'] == "127.0.0.1"){
            return 3;
        }else{
            return 4;
        }
    }
}


