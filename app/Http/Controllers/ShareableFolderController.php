<?php

namespace App\Http\Controllers;

use App\Company;
use App\EmailSentDocket;
use App\EmailSentDocketRecipient;
use App\EmailSentInvoice;
use App\Employee;
use App\Folder;
use App\FolderItem;
use App\SentDocketRecipientApproval;
use App\SentDocketReject;
use App\SentDockets;
use App\SentDocketsValue;
use App\SentInvoice;
use App\ShareableFolder;
use App\ShareableFolderUser;
use App\Support\Collection;
use App\User;
use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Helpers\V2\AmazoneBucket;


class ShareableFolderController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {

            return $next($request);

        });

    }

    public function folderView(){
        $items= 10;
        $data = Session::get('shareable_folder');

        $shareableFolder = ShareableFolder::where('link',$data['link'])->first();

        if($shareableFolder == null){

        }else{
            if($shareableFolder->shareable_type == "Restricted"){

                $type = "Restricted";
            }else if($shareableFolder->shareable_type == "Public"){
                $type = "Public";

            }

            $companyFolder = Folder::where('id', $shareableFolder->folder->id)->get();
            $folderItems        =   FolderItem::where('folder_id',$shareableFolder->folder->id)->get();
            $sentDockets        =   SentDockets::with('sentDocketLabels.docketLabel','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',1)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $emailSentDockets   =   EmailSentDocket::with('sentEmailDocketLabels.docketLabel','recipientInfo.emailUserInfo','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',3)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $totalDockets       =   $sentDockets->concat($emailSentDockets);
            $sentInvoices       =   SentInvoice::with('sentInvoiceLabels.invoiceLabel','receiverUserInfo','receiverCompanyInfo','invoiceInfo')->whereIn('id',$folderItems->where('type',2)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $emailSentInvoices  =   EmailSentInvoice::whereIn('id',$folderItems->where('type',4)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $totalInvoices      =   $sentInvoices->concat($emailSentInvoices);
            $merged = $totalDockets->concat($totalInvoices);
            $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);
            return view('/shareable-folder/folder/index', compact('type','companyFolder','result','items'));

        }

    }

    public function folderlist(Request $request){
        $folder =  Folder::where('root_id',$request->id)->get();

        $item = array();
        foreach ($folder as $rowData){
            $totalItemsss="";
            if (count($rowData->folderItems)!=0){
                $totalItemsss= '('.count($rowData->folderItems).')';
            }

            $item[] = array(
                'id' => $rowData->id,
                'name' => $rowData->name,
                'totalItems'=>$totalItemsss

            );
        }
        return response()->json(['data'=>$item]);
    }


    public function viewFolderData(Request $request){
        $isValidData = false;
        $isreload = "noreload";
        $items = 10;
        $data = Session::get('shareable_folder');
        $shareableFolder = ShareableFolder::where('link',$data['link'])->first();

        if($shareableFolder == null){

        }else {
            $isValidData = true;
            if($shareableFolder->shareable_type == "Restricted"){
                $shareableFolderUser = ShareableFolderUser::where('token',$data['token'])->get()->first();
                $company = User::where('email',$shareableFolderUser->email)->first()->companyInfo;
                $type = "Restricted";
            }else if($shareableFolder->shareable_type == "Public"){
                $type = "Public";
                $company = null;
            }
            $companyFolder = Folder::where('id', $request->folderId)->get();
            $folderItems        =   FolderItem::where('folder_id',$request->folderId)->get();
            $sentDockets        =   SentDockets::with('sentDocketLabels.docketLabel','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',1)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $emailSentDockets   =   EmailSentDocket::with('sentEmailDocketLabels.docketLabel','recipientInfo.emailUserInfo','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',3)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $totalDockets       =   $sentDockets->concat($emailSentDockets);
            $sentInvoices       =   SentInvoice::with('sentInvoiceLabels.invoiceLabel','receiverUserInfo','receiverCompanyInfo','invoiceInfo')->whereIn('id',$folderItems->where('type',2)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $emailSentInvoices  =   EmailSentInvoice::whereIn('id',$folderItems->where('type',4)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $totalInvoices      =   $sentInvoices->concat($emailSentInvoices);
            $merged = $totalDockets->concat($totalInvoices);
            $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);
            return view('/shareable-folder/folder/folder-contained', compact('type','companyFolder','result','items','isreload','isValidData','company'));
        }

    }

    public function searchFolderById(Request $request){
        $data = Session::get('shareable_folder');
        $shareableFolder = ShareableFolder::where('link',$data['link'])->first();
        $folder = Folder::where('id',$request->id)->get();
        $matchedFolderName= array();
        foreach ($folder as $row) {
            $matchedFolderName[]= $row->id;
        }
        $folderName = Folder::where('id',$shareableFolder->folder->id)->orderBy('name','asc')->get();
        $parentHtml='<ul class="folderRtTree">';

        foreach ($folderName as $folderNames) {
            if($folderNames->type == 0) {
                $totalItemss = "";
                if (count($folderNames->folderItems) != 0) {
                    $totalItemss = '(' . count($folderNames->folderItems) . ')';
                }
                if (!in_array($folderNames->id, $matchedFolderName)) {
                    $parentHtml .= '<li><a href="#"  id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                } else {
                    $parentHtml .= '<li><a href="#" class="active"  id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                }
                if (count($folderNames->childs)) {
                    $parentHtml .= $this->childViewById($folderNames, $matchedFolderName);
                }

                $parentHtml .= '  <div  class="editBtn" id="editBtnId" data-id="' . $folderNames->id . '" data-title="' . $folderNames->name . '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>';
            }
        }

        foreach ($folderName as $folderNames) {
            if($folderNames->type == 1) {
                $totalItemss = "";
                if (count($folderNames->folderItems) != 0) {
                    $totalItemss = '(' . count($folderNames->folderItems) . ')';
                }
                if (!in_array($folderNames->id, $matchedFolderName)) {
                    $parentHtml .= '<li><a href="#"  id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                } else {
                    $parentHtml .= '<li><a href="#" class="active"  id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                }
                if (count($folderNames->childs)) {
                    $parentHtml .= $this->childViewById($folderNames, $matchedFolderName);
                }

                $parentHtml .= '  <div  data-id="' . $folderNames->id . '" data-title="' . $folderNames->name . '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>';
            }
        }
        $parentHtml .='<ul>';
        return response()->json(['status'=>true ,'detail'=>$parentHtml]);
    }

    public function childViewById($folderNames,$matchedFolderName){
        $childHtml ='<ul>';
        foreach ($folderNames->childs as $arr) {
            $totalItemsss="";
            if (count($arr->folderItems)!=0){
                $totalItemsss= '('.count($arr->folderItems).')';
            }
            if(count($arr->childs)){
                if (!in_array($arr->id,$matchedFolderName)){
                    $childHtml .='<li><a href="#"  id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a>  ';
                }else{
                    $childHtml .='<li><a href="#" class="active" id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a>  ';
                }
                $childHtml.= $this->childViewById($arr,$matchedFolderName);
                $childHtml.=  '  <div  class="editBtn" id="editBtnId" data-id="'.$folderNames->id.'" data-title="'.$folderNames->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>';

            }else{

                if (!in_array($arr->id,$matchedFolderName)){
                    $childHtml .='<li><a href="#"  id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'.$arr->id.'" data-title="'.$arr->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>';
                }else{
                    $childHtml .='<li><a href="#" class="active"  id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'.$arr->id.'" data-title="'.$arr->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>';
                }
                $childHtml .="</li>";
            }

        }


        $childHtml .="</ul>";
        return $childHtml;
    }

    public function viewFolderReload(Request $request){
        $isreload = "reload";
        $items = $request->items;
        $data = Session::get('shareable_folder');
        $shareableFolder = ShareableFolder::where('link',$data['link'])->first();
        $isValidData = false;




        if($shareableFolder == null){

        }else {
            $isValidData = true;

            if($shareableFolder->shareable_type == "Restricted"){
                $shareableFolderUser = ShareableFolderUser::where('token',$data['token'])->get()->first();
                $company = User::where('email',$shareableFolderUser->email)->first()->companyInfo;
                $type = "Restricted";
            }else if($shareableFolder->shareable_type == "Public"){
                $type = "Public";
                $company = null;
            }
            if(in_array($request->folderId,$this->searchFolderByIds($request))){
                $companyFolder = Folder::where('id', $request->folderId)->get();
                $folderItems        =   FolderItem::where('folder_id',$request->folderId)->get();
                $sentDockets        =   SentDockets::with('sentDocketLabels.docketLabel','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',1)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
                $emailSentDockets   =   EmailSentDocket::with('sentEmailDocketLabels.docketLabel','recipientInfo.emailUserInfo','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',3)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
                $totalDockets       =   $sentDockets->concat($emailSentDockets);
                $sentInvoices       =   SentInvoice::with('sentInvoiceLabels.invoiceLabel','receiverUserInfo','receiverCompanyInfo','invoiceInfo')->whereIn('id',$folderItems->where('type',2)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
                $emailSentInvoices  =   EmailSentInvoice::whereIn('id',$folderItems->where('type',4)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
                $totalInvoices      =   $sentInvoices->concat($emailSentInvoices);
                $merged = $totalDockets->concat($totalInvoices);
                $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);
                return view('/shareable-folder/folder/folder-contained', compact('type','companyFolder','result','items','isreload','isValidData','company'));
            }else{
                $isValidData = false;
                return view('/shareable-folder/folder/folder-contained', compact('type','items','isreload','isValidData','company'));

            }




        }

    }

    public function searchFolderByIds(Request $request){
        $data = Session::get('shareable_folder');
        $shareableFolder = ShareableFolder::where('link',$data['link'])->first();
        $folder = Folder::where('id',$request->id)->get();
        $matchedFolderName= array();
        foreach ($folder as $row) {
            $matchedFolderName[]= $row->id;
        }
        $folderName = Folder::where('id',$shareableFolder->folder->id)->orderBy('name','asc')->get();
        $parentHtml= "";

        foreach ($folderName as $folderNames) {
            if($folderNames->type == 0) {
                if (!in_array($folderNames->id, $matchedFolderName)) {
                    $parentHtml .= $folderNames->id.',';
                } else {
                    $parentHtml .= $folderNames->id.',';
                }
                if (count($folderNames->childs)) {
                    $parentHtml .= $this->childViewByIds($folderNames, $matchedFolderName);
                }

                $parentHtml .=  $folderNames->id.',';
            }
        }

        foreach ($folderName as $folderNames) {
            if($folderNames->type == 1) {


                if (!in_array($folderNames->id, $matchedFolderName)) {
                    $parentHtml .=  $folderNames->id.',';
                } else {
                    $parentHtml .= $folderNames->id.',' ;
                }
                if (count($folderNames->childs)) {
                    $parentHtml .=  $this->childViewByIds($folderNames, $matchedFolderName);
                }

                $parentHtml .= $folderNames->id.',';
            }
        }
        return explode(",",$parentHtml);
    }

    public function childViewByIds($folderNames,$matchedFolderName){
        $childHtml = "";
        foreach ($folderNames->childs as $arr) {
            if(count($arr->childs)){
                if (!in_array($arr->id,$matchedFolderName)){
                    $childHtml.= $arr->id.',';
                }else{
                    $childHtml .=$arr->id.',';
                }
                $childHtml .= $this->childViewByIds($arr,$matchedFolderName);
                $childHtml .= $arr->id.',';

            }else{

                if (!in_array($arr->id,$matchedFolderName)){
                    $childHtml .= $arr->id.',';
                }else{
                    $childHtml .= $arr->id.',';
                }

            }

        }


        return $childHtml;
    }

    public function companyDocketApprove(Request $request ){
        $sentDocket     =   SentDockets::findOrFail($request->sentDocketId);
//        $sentDocket =SentDockets::where('id',$request->sentDocketId);
        if($sentDocket->sentDocketRecipientApproval) {
            if (in_array($sentDocket->shareFolderUserId()['user']['id'], $sentDocket->sentDocketRecipientApproval->pluck('user_id')->toArray())):
                $sentDocketRecipientApprovalQuery = SentDocketRecipientApproval::where('sent_docket_id', $request->sentDocketId)->Where('user_id', $sentDocket->shareFolderUserId()['user']['id'])->where('status', 0);
                if ($sentDocketRecipientApprovalQuery->count() == 1) {
                    if ($sentDocket->docketApprovalType == 1){
                        $this->validate($request,['sentDocketId' =>     'required','name' =>     'required','signature' =>     'required']);
                        $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
                        $sentDocketRecipientApproval->status     =   1;
                        $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
                        $sentDocketRecipientApproval->name =$request->name;
                        $image = $request->signature;  // your base64 encoded
                        $image = str_replace('data:image/png;base64,', '', $image);
                        $image = str_replace(' ', '+', $image);
                        $imageName = 'signature'.time().'.'.'png';
                        \File::put(base_path('assets'). '/signature/' . $imageName, base64_decode($image));
                        $sentDocketRecipientApproval->signature=$imageName;
                        $sentDocketRecipientApproval->save();

                    }else{
                        $this->validate($request,['sentDocketId' =>     'required']);
                        $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
                        $sentDocketRecipientApproval->status     =   1;
                        $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
                        $sentDocketRecipientApproval->save();

                    }

                    $sentDocketSenderInfo = User::where('id', $sentDocket->user_id)->first();
                    $sentDocketReceiverInfo = User::where('id',$sentDocket->shareFolderUserId()['user']['id'])->first();

                    if (SentDocketRecipientApproval::where('sent_docket_id', $request->sentDocketId)->where('status', 0)->count() == 0) {
                        $sentDocketUpdate = SentDockets::findOrFail($request->sentDocketId);
                        $sentDocketUpdate->status = 1;
                        $sentDocketUpdate->save();

                        if (SentDocketRecipientApproval::where('sent_docket_id', $request->sentDocketId)->count() > 1) {
                            if ($sentDocketSenderInfo->device_type == 2) {
                                $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
                            }
                            if ($sentDocketSenderInfo->device_type == 1) {
                                $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
                            }
                        }
                    }

                    if ($sentDocketSenderInfo->device_type == 2) {
                        $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name . " " . $sentDocketReceiverInfo->last_name . " has approved your docket.");
                    }
                    if ($sentDocketSenderInfo->device_type == 1) {
                        $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name . " " . $sentDocketReceiverInfo->last_name . " has approved your docket.");
                    }

//                    flash('Docket approved successfully.', 'success');
//                    return redirect('dashboard/company/docketBookManager/docket/received');
                    $message= 'Docket approved successfully.';
                    return response()->json(['status' => true, 'message' => $message]);
                } else {
                    flash('Invalid attempt!  approved successfully.', 'success');
                    return redirect()->back();
                }
            else:
                flash('Invalid attempt!  approved successfully.', 'success');
                return redirect()->back();
            endif;
        }else{
            flash('Invalid attempt!  approved successfully.', 'success');
            return redirect()->back();
        }
    }


    public function approvalTypeView($id){
        $sentDocket     =   SentDockets::findOrFail($id);
        $approval_type = array();
        foreach ($sentDocket->sentDocketRecipientApproval as $items){
            $approval_type[] = array(
                'id' => $items->id,
                'status' =>$items->status,
                'full_name' => $items->userInfo->first_name." ".$items->userInfo->last_name,
                'approval_time' =>$items->approval_time,
                'name'=>$items->name,
                'signature'=>AmazoneBucket::url() . $items->signature
            );
        }
        $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
        $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
        $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
        $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
        $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();

//        if($sentDocket->sender_company_id==Session::get('company_id')){
//            $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
//            return view('dashboard.company.docketManager.docket.approvalTypeView',compact('sentDocket','docketFields','company','approval_type '));
//        }else{
        //get total company employee ids
//            $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id');
//            $employeeIds[]  =   Company::find(Session::get('company_id'))->user_id;
//
//            if(SentDocketRecipient::whereIn('user_id',$employeeIds)->where('sent_docket_id',$id)->count()>0){
        $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
        return view('dashboard.company.docketManager.docket.approvalTypeView',compact('sentDocket','docketFields','company','approval_type'));
//            }else{
//                flash('Invalid action !.','warning');
//                return redirect()->back();
//            }
//        }
    }


    public  function  docketReject(Request $request){
        $sentDocket     =   SentDockets::findOrFail($request->docket_id);
        $this->validate($request,['docket_id' =>     'required','explanation'=>'required']);
        $sentDocketRecipientApprovalQuery = SentDocketRecipientApproval::where('sent_docket_id', $request->docket_id)->Where('user_id',$sentDocket->shareFolderUserId()['user']['id'])->where('status', 0);
        $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
        $sentDocketRecipientApproval->status     =   3;
        $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
        if ($sentDocketRecipientApproval->save()){
            $sentDocketExplanation = new SentDocketReject();
            $sentDocketExplanation->sent_docket_id =  $request->docket_id;
            $sentDocketExplanation->explanation =  $request->explanation;
            $sentDocketExplanation->user_id =  $sentDocket->shareFolderUserId()['user']['id'];
            $sentDocketExplanation->save();
        }
        SentDockets::where('id',$request->docket_id)->update(['status'=> 3]);



        // push notification
        $sentDocketRecipientApp= SentDocketRecipientApproval::where('sent_docket_id', $request->docket_id)->pluck('user_id')->toArray();
        if (in_array($sentDocket->shareFolderUserId()['user']['id'], $sentDocketRecipientApp)){
            $companyAdminUser = User::findOrFail($sentDocket->shareFolderUserId()['user']['id']);
            $sentDocket = SentDockets::where('id',$request->docket_id)->first();
            $userNotification   =   new UserNotification();
            $userNotification->sender_user_id   =  $sentDocket->shareFolderUserId()['user']['id'];
            $userNotification->receiver_user_id = $sentDocket->user_id;
            $userNotification->type     =   1;
            $userNotification->title    =   'Docket Rejected';
            $userNotification->message  =   "Your Docket has been rejected by";
            $userNotification->key      =   $request->docket_id;
            $userNotification->status   =   0;
            if ($userNotification->save()) {
                if ($sentDocket->senderUserInfo->deviceToken != "") {
                    if ($sentDocket->senderUserInfo->device_type == 2) {
                        $this->sendiOSNotification($sentDocket->senderUserInfo->deviceToken, $userNotification->title, $userNotification->message);
                    }
                    if ($sentDocket->senderUserInfo->device_type == 1) {
                        $this->sendAndroidNotification($sentDocket->senderUserInfo->deviceToken, $userNotification->title, $userNotification->message);
                    }
                }
            }

            if ($sentDocket->user_id != $companyAdminUser->id){
                $userNotification   =   new UserNotification();
                $userNotification->sender_user_id   =   $sentDocket->shareFolderUserId()['user']['id'];
                $userNotification->receiver_user_id = $companyAdminUser->id;
                $userNotification->type     =   1;
                $userNotification->title    =   'Docket Rejected';
                $userNotification->message  =   "Your Docket has been rejected by ".User::where('id',$sentDocket->shareFolderUserId()['user']['id'])->first_name.' '.User::where('id',$sentDocket->shareFolderUserId()['user']['id'])->last_name ;
                $userNotification->key      =   $request->docket_id;
                $userNotification->status   =   0;
                if ($userNotification->save()) {
                    if ($companyAdminUser->deviceToken != "") {
                        if ($companyAdminUser->device_type == 2) {
                            $this->sendiOSNotification($companyAdminUser->deviceToken, $userNotification->title, $userNotification->message);
                        }
                        if ($companyAdminUser->device_type == 1) {
                            $this->sendAndroidNotification($companyAdminUser->deviceToken, $userNotification->title, $userNotification->message);
                        }
                    }
                }
            }


        }
        $sentDocketReject = SentDocketReject::where('sent_docket_id', $request->docket_id)->get();
        $data ="<h5 style='font-weight: 800;'>Rejected By:</h5><ul>";
        foreach ($sentDocketReject as $sentDocketRejects) {
            $data .='<li><b>'.$sentDocketRejects->userInfo->first_name.' </b>'. $sentDocketRejects->explanation.' '.$sentDocketRejects->created_at.'</li>';
        }
        $data .= "</ul>";
        return response()->json(array('data'=>$data));

    }




    function sendiOSNotification($deviceID, $titles, $message){
        $ch = curl_init("https://fcm.googleapis.com/fcm/send");
        //The device token.
        $token = $deviceID; //token here
        //Title of the Notification.
        $title = $titles;
        //Body of the Notification.
        $body = $message;
        //Creating the notification array.
        $notification = array('title' =>$title , 'text' => $body, 'sound'=>'default', "content_available"=>true);
        //This array contains, the token and the notification. The 'to' attribute stores the token.
        $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
        //Generating JSON encoded string form the above array.
        $json = json_encode($arrayToSend);
        //Setup headers:
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key= AIzaSyBvGkKWzgG0Ah-dw5EDlszZfX6Tiby67po'; // key here
        //Setup curl, add headers and post parameters.
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        curl_close($ch);
    }

    function sendAndroidNotification($deviceId, $titles, $message){
        $registrationIds = array( $deviceId );
        $msg = array
        (
            'message'   => $message,
            'title'     =>$titles,
            'vibrate'   => 1,
            'sound'     => 1
        );
        $fields = array
        (
            'registration_ids'  => $registrationIds,
            'data'          => $msg
        );

        $headers = array
        (
            'Authorization: key= AAAAYXeBuFI:APA91bFidufG2_gC3OOZWz7y37FWQ0B-tIA1OdAa8lu4HYN4wfX8HbNZXa8Wxg76iWgD_VU4kmvAYu71aCeRPmn99jCsMP2f-BVgVhjRcLVypMFSVB5gKXcQS0Prk5088MIDSJ_mrs-E' ,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        // echo $result;
    }

    public function approve(Request $request, $id,$hashKey){


        $emailDocket        =   EmailSentDocket::findOrFail($id);

        $emailRecipient     =   EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('hashKey',$hashKey)->get();


        if($emailRecipient->count()!=1){
            $message    =   "Your link has expired.";
            return view('errors.errorPage', compact('message'));
        }
        $emailRecipient =   $emailRecipient->first();
        if($emailDocket->docketApprovalType == 0){
            if($emailRecipient->hashKey!='' && $emailRecipient->status!='1'){
                $emailRecipient->hashKey = '';
                $emailRecipient->status     =   1;
                $emailRecipient->approval_time =Carbon::now()->toDateTimeString();
                $emailRecipient->save();

                if(EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('approval',1)->where('hashKey','!=','')->count()==0){
                    EmailSentDocket::where('id',$id)->update(['status'=>1]);
                }

                if($emailDocket->senderUserInfo->device_type == 2){
                    sendiOSNotification($emailDocket->senderUserInfo->deviceToken,"Docket Approved", $emailRecipient->emailUserInfo->email." has approved your docket",array('type'=>5));
                }else if($emailDocket->senderUserInfo->device_type == 1){
                    sendAndroidNotification($emailDocket->senderUserInfo->deviceToken,'Docket Approved', $emailRecipient->emailUserInfo->email." has approved your docket",array('type'=>5));
                }

                $userNotification   =    new UserNotification();
                $userNotification->sender_user_id   =   $emailRecipient->email_user_id;
                $userNotification->receiver_user_id =   $emailDocket->user_id;
                $userNotification->type     =   5;
                $userNotification->title    =   'Docket Approved';
                $userNotification->message  =   $emailRecipient->emailUserInfo->email." has approved your docket.";
                $userNotification->key      =   $id;
                $userNotification->status   =   0;
                $userNotification->save();

                $message    =   "Requested docket has been approved successfully.";
//                return view('website.emailDocket.approved', compact('message','emailDocket'));

                return redirect()->back();
            }else {
                $message    =   "Your link has expired.";
                return view('errors.errorPage', compact('message'));
            }
        }else{
            if($emailRecipient->hashKey!='' && $emailRecipient->status!='1'){
                $emailRecipient->hashKey = '';
                $emailRecipient->status     =   1;
                $emailRecipient->approval_time =Carbon::now()->toDateTimeString();
                $emailRecipient->name =$request->name;
                $image = $request->signature;
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'files/docket/images/signature'.time().'.'.'png';
                \File::put(base_path(''). '/' . $imageName, base64_decode($image));
                $emailRecipient->signature=$imageName;
                $emailRecipient->save();

                if(EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('approval',1)->where('hashKey','!=','')->count()==0){
                    EmailSentDocket::where('id',$id)->update(['status'=>1]);
                }

                if($emailDocket->senderUserInfo->device_type == 2){
                    sendiOSNotification($emailDocket->senderUserInfo->deviceToken,"Docket Approved", $emailRecipient->emailUserInfo->email." has approved your docket",array('type'=>5));
                }else if($emailDocket->senderUserInfo->device_type == 1){
                    sendAndroidNotification($emailDocket->senderUserInfo->deviceToken,'Docket Approved', $emailRecipient->emailUserInfo->email." has approved your docket",array('type'=>5));
                }

                $userNotification   =    new UserNotification();
                $userNotification->sender_user_id   =   $emailRecipient->email_user_id;
                $userNotification->receiver_user_id =   $emailDocket->user_id;
                $userNotification->type     =   5;
                $userNotification->title    =   'Docket Approved';
                $userNotification->message  =   $emailRecipient->emailUserInfo->email." has approved your docket.";
                $userNotification->key      =   $id;
                $userNotification->status   =   0;
                $userNotification->save();

                return redirect()->back();
            }

        }
    }




}
