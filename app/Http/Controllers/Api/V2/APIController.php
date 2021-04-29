<?php

namespace App\Http\Controllers\Api\V2;

use App\Company;
use App\Docket;
use App\EmailSentDocket;
use App\Helpers\V2\MessageDisplay;
use App\MessagesGroupUser;
use App\SentDockets;
use App\SentInvoice;
use App\DocketDraft;
use App\DocketDraftsAssign;
use Illuminate\Http\Request;
use App\User;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Services\V2\Api\UserService;
use App\Services\V2\Api\DocketService;
use App\Services\V2\Api\ApiService;
use App\Services\V2\Api\SearchService;
use App\Services\V2\Api\MessageService;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\EmailClientStoreRequest;
use App\Http\Requests\FilterDocketRequest;
use App\Http\Requests\NameUpdateRequest;
use App\Http\Requests\PostEmailUserRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\sentDocketRejectRequest;
use App\Services\V2\Api\EmailConversationService;
use App\Services\V2\Api\ForwardService;
use App\Services\V2\Api\InvoiceFilterService;
use App\Http\Requests\SaveDocketDraftRequest;
use App\Http\Requests\UpdateDocketAprovalMethodRequest;
use App\Http\Requests\UpdateDocketDraftRequest;
use App\Http\Requests\v1SaveSentDefaultDocketRequest;
use App\Http\Controllers\APIController as V1ApiController;
use App\Http\Requests\TaskManagementApiRequest;
use App\Http\Resources\V2\Email\EmailClientResource;
use App\Repositories\V2\EmailClientRepository;
use App\Repositories\V2\EmailUserRepository;

class APIController extends Controller
{
    protected $userService,$docketService,$apiService,$emailConversationService,$searchService,$forwardService,
        $messageService,$invoiceFilterService,$emailClientRepository,$emailUserRepository;

    public function __construct(UserService $userService, DocketService $docketService, ApiService $apiService,
        EmailConversationService $emailConversationService, SearchService $searchService, ForwardService $forwardService,
        MessageService $messageService, InvoiceFilterService $invoiceFilterService,EmailClientRepository $emailClientRepository,
        EmailUserRepository $emailUserRepository){
            $this->userService = $userService;
            $this->docketService = $docketService;
            $this->apiService = $apiService;
            $this->emailConversationService = $emailConversationService;
            $this->searchService = $searchService;
            $this->forwardService = $forwardService;
            $this->messageService = $messageService;
            $this->invoiceFilterService = $invoiceFilterService;
            $this->emailClientRepository = $emailClientRepository;
            $this->emailUserRepository = $emailUserRepository;
    }

    public function sentAcivity(Request $request){
        $sendDocket = SentDockets::where('user_id',$request->header('userId'))->count();
        $sendInvoice = SentInvoice::where('user_id',$request->header('userId'))->count();
        $emailDockets = EmailSentDocket::where('user_id',$request->header('userId'))->count();
        return response()->json(["acivity"=>array('dockets' => $sendDocket,'invoice'=> $sendInvoice,'email_docket'=>$emailDockets)],200);
    }

    public function changePassword(ChangePasswordRequest $request){
       return $this->userService->changePassword($request);
    }

    public function profileUpdate(ProfileUpdateRequest $request){
        return $this->userService->profileUpdate($request);
    }

    public function nameUpdate(NameUpdateRequest $request){
        return $this->userService->nameUpdate($request);
    }

    public function getEmployeeList(Request $request){
        $employee = $this->userService->getEmployeeList($request);
        return response()->json(['employee' => $employee],200);
    }

    public function getFrequency(Request $request){
        $docketTemplates = $this->userService->getFrequency($request);
        return response()->json(['docketTemplate' => $docketTemplates],200);
    }

    public function getInvoiceableDocketList(Request $request,$userId){
        $invoiceableDockets = $this->docketService->getInvoiceableDocketList($request,$userId);
        return response()->json(['invoiceableDockets' =>$invoiceableDockets],200);
    }

    public function getLatestConversationList(Request $request){
        $conversationArray = $this->emailConversationService->getLatestConversationList($request);
        return response()->json(['conversation' => $conversationArray],200);
    }

    public function getLatestEmailDocketHome(Request $request){
        $conversationArray = $this->emailConversationService->getLatestEmailDocketHome($request);
        return response()->json(['dockets' => $conversationArray],200);
    }

    public function getLatestEmailInvoiceHome(Request $request){
        $conversationArray = $this->emailConversationService->getLatestEmailInvoiceHome($request);
        return response()->json(['invoices' => $conversationArray],200);
    }

    public function getLatestEmailConversationList(Request $request){
        $conversationArray = $this->emailConversationService->getLatestEmailConversationList($request);
        return response()->json(['dockets' => $conversationArray],200);
    }

    public function getLatestEmailInvoiceConversationList(Request $request){
        $conversationArray = $this->emailConversationService->getLatestEmailInvoiceConversationList($request);
        return response()->json(['invoices' => $conversationArray],200);
    }

    public function getLatestDockets(Request $request){
        return $this->docketService->getLatestDockets($request);
    }

    public function getConversationChatByUserId($userId){
        $conversationArray = $this->emailConversationService->getConversationChatByUserId($userId);
        return response()->json(['dockets' =>$conversationArray],200);
    }

    public function getEmailConversationChatByUserId($userId){
        $conversationArray = $this->emailConversationService->getEmailConversationChatByUserId($userId);
        return response()->json(['dockets' =>$conversationArray],200);
    }

    public function getTimelineChatByRecipients(){
        $conversationArray = $this->emailConversationService->getTimelineChatByRecipients();
        return response()->json(['timeline' => $conversationArray],200);
    }

    public function getEmailTimelineByRecipients(){
        $conversationsArray = $this->emailConversationService->getEmailTimelineByRecipients();
        return response()->json(['timeline' => $conversationsArray],200);
    }

    public function getEmailTimelineByUserId($userId){
        $conversationsArray = $this->emailConversationService->getEmailTimelineByUserId($userId);
        return response()->json(['timeline' => $conversationsArray],200);
    }

    public function getEmailInvoiceTimelineByUserId($userId){
        $conversationsArray = $this->emailConversationService->getEmailInvoiceTimelineByUserId($userId);
        return response()->json(['timeline' => $conversationsArray],200);
    }

    public function getDocketList(){
        $sentDocket = $this->docketService->getDocketList();
        return response()->json(['dockets' => $sentDocket],200);
    }
    public function getDocketDetailByIdWebView(Request $request,$id){
        return $this->docketService->getDocketDetailByIdWebView($request,$id);
    }
    public function getDocketDetailsById(Request $request, $id){
        return $this->docketService->getDocketDetailsById($request,$id);
    }

    public function getEmailDocketDetailsByIdWebView(Request $request,$id){
        return $this->docketService->getEmailDocketDetailsByIdWebView($request,$id);
    }

    public function getEmailDocketDetailsById(Request $request, $id){
        return $this->docketService->getEmailDocketDetailsById($request,$id);
    }

    public function approveDocketById(Request $request){
        return $this->docketService->approveDocketById($request);
    }

    //-----------------email user section ----------------------//
    public function  postEmailUser(PostEmailUserRequest $request){
        return $this->emailConversationService->postEmailUser($request);
        // if(isset($data->original['profile']['id'])){
        //     $emailClient = $this->emailUserRepository->getDataWhere([['email_user_id',$data->original['profile']['id']]])
        //                         ->select('id','email_user_id as id','name as full_name','company_name')->with('emailUser')->first();
        //     $response = new EmailClientResource($emailClient);
        //     return response()->json(["emailClient"=>$response],200);
        // }else{
        //     return $data;
        // }
    }

    public function companyDockets(){
        $activeDocket = $this->apiService->companyDockets();
        return response()->json(['docketTemplate' => $activeDocket],200);
    }

    public function filterDocket(FilterDocketRequest $request){
        return $this->apiService->filterDocket($request);
    }

    function filterDocument(Request $request){
        return $this->apiService->filterDocument($request);
    }

    function searchByKeywordDocket(Request $request){
        $dockets = $this->searchService->searchByKeywordDocket($request);
        return response()->json(['dockets' => $dockets],200);
    }

    function searchByKeywordEmailDocket(Request $request){
        $sentEmailDockets = $this->searchService->searchByKeywordEmailDocket($request);
        return response()->json(['sentEmailDockets' => $sentEmailDockets],200);
    }

    function searchByKeywordInvoice(Request $request){
        $invoices = $this->searchService->searchByKeywordInvoice($request);
        return response()->json(['invoices' => $invoices],200);
    }

    function searchByKeywordEmailInvoice(Request $request){
        $invoices = $this->searchService->searchByKeywordEmailInvoice($request);
        return response()->json(['invoices' => $invoices],200);
    }

    public function forwardDocketById(Request $request,$id){
        return $this->forwardService->forwardDocketById($request,$id);
    }

    public function forwardInvoiceById(Request $request,$id){
        return $this->forwardService->forwardInvoiceById($request,$id);
    }

    public function forwardEmailDocketById(Request $request,$id){
        return $this->forwardService->forwardEmailDocketById($request,$id);
    }

    public function forwardEmailInvoiceById(Request $request,$id){
        return $this->forwardService->forwardEmailInvoiceById($request,$id);
    }

    //user/group notification section
    public function getNotificationList(){
        return $this->messageService->getNotificationList();
    }

    public function getNotificationListUpdateAndroid(Request $request){
        return $this->messageService->getNotificationListUpdateAndroid();
    }

    public function markAsReadNotification($key){
        return $this->messageService->markAsReadNotification($key);
    }

    public function emailUserList(){
        $emailClients = $this->apiService->emailUserList();
        return response()->json(["emailClients"=>$emailClients],200);
    }

    public function  saveEmailClient(EmailClientStoreRequest $request){
        $data = $this->apiService->saveEmailClient($request);
        if(isset($data->original['email_user_id'])){
            $emailClient = $this->emailClientRepository->getDataWhere([['email_user_id',$data->original['email_user_id']]])
                                ->select('id','email_user_id','full_name','company_name','company_address')->with('emailUser')->first();
            $response = new EmailClientResource($emailClient);
            return response()->json(["emailClient"=>$response],200);
        }else{
            return $data;
        }
    }

    //Timer API Endpoints//
    public function getInvoiceableEmailDocketList($key){
        $invoiceableEmailDockets = $this->apiService->getInvoiceableEmailDocketList($key);
        return response()->json(array('status' => true, 'invoiceableEmailDockets' =>$invoiceableEmailDockets));
    }

    public function markAllAsRead(){
        $this->messageService->markAllAsRead();
        return response()->json(["message" => MessageDisplay::Success],200);
    }

    public function approveDocketByEmail(Request $request,$id,$hashKey){
        return $this->apiService->approveDocketByEmail($request,$id,$hashKey);
    }

    public function approvedDocketSignature(Request $request){
        return $this->apiService->approvedDocketSignature($request);
    }

    public  function sentDocketReject(sentDocketRejectRequest $request){
        return $this->apiService->sentDocketReject($request);
    }

    public  function  receiptValidator(Request $request){
        return $this->apiService->receiptValidator($request);
    }

    public function subscriptionStatus(){
        return $this->apiService->subscriptionStatus();
    }
    public  function getInvoiceDocketFilterParameter(Request $request){
        return $this->invoiceFilterService->getInvoiceDocketFilterParameter($request);
    }

    public  function filterInvoiceableDocket(Request $request){
        $invoiceableDockets = $this->invoiceFilterService->filterInvoiceableDocket($request);
        return response()->json(['invoiceableDockets' =>$invoiceableDockets],200);
    }

    public  function getInvoiceEmailDocketFilterParameter(Request $request){
        return $this->invoiceFilterService->getInvoiceEmailDocketFilterParameter($request);
    }

    public  function filterInvoiceableEmailDocket(Request $request){
        $invoiceableEmailDockets = $this->invoiceFilterService->filterInvoiceableEmailDocket($request);
        return response()->json(['invoiceableEmailDockets' =>$invoiceableEmailDockets],200);
    }

    public  function myPermission(){
        return $this->apiService->myPermission();
    }

    public  function draftImageSave(Request $request){
        return $this->docketService->draftImageSave($request);
    }

    public  function saveDocketDraft(SaveDocketDraftRequest $request){
        $docketDraft = $this->docketService->saveDocketDraft($request);
        return response()->json(['message' => MessageDisplay::DocketDraftSave, 'docket_draft' => array('draft_id' => $docketDraft->id)],200);
    }

    public  function updateDocketDraft(UpdateDocketDraftRequest $request){
        return $this->docketService->updateDocketDraft($request);
    }

    public function getDocketDraftList(){
        $docketDraftList = $this->docketService->getDocketDraftList();
        return response()->json(array('docket_draft_list' => $docketDraftList));
    }

    public function updateDeviceToken(Request $request){
        $this->userRepository->getDataWhere([['id',auth()->user()->id]])->update(['deviceToken' => $request->device_token]);
        return response()->json(['message' => MessageDisplay::DeviceTokenUpdated],200);
    }

    public function v1getDocketTemplateDetailsById($id){
        return $this->docketService->v1getDocketTemplateDetailsById($id);
    }

    public function v1SaveSentDefaultDockets(v1SaveSentDefaultDocketRequest $request){
        return $this->docketService->v1SaveSentDefaultDockets($request);
    }

    public function saveGridPrefiller (Request $request){
        return $this->apiService->saveGridPrefiller($request);
    }

    public function savePrefiller (Request $request){
        return $this->apiService->savePrefiller($request);
    }

    public  function deleteDraft(Request $request){
        $this->apiService->deleteDraft($request);
        return response()->json(['message' => MessageDisplay::DocketDraftDelete]);
    }

    public function nextDocketId(Request $request){
        return $this->apiService->nextDocketId($request);
    }

    public function updateDocketAprovalMethod(UpdateDocketAprovalMethodRequest $request){
        $this->apiService->updateDocketAprovalMethod($request);
        return response()->json(['message' => MessageDisplay::DocketApproval],200);
    }

    public function logout(){
        $this->apiService->logout();
        return response()->json(['message' => MessageDisplay::Logout],200);
    }


    public function removeNamespaceFromXML( $xml ){
        // Because I know all of the the namespaces that will possibly appear in
        // in the XML string I can just hard code them and check for
        // them to remove them
        $toRemove = ['rap', 'turss', 'crim', 'cred', 'j', 'rap-code', 'evic'];
        // This is part of a regex I will use to remove the namespace declaration from string
        $nameSpaceDefRegEx = '(\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?';

        // Cycle through each namespace and remove it from the XML string
        foreach( $toRemove as $remove ) {
            // First remove the namespace from the opening of the tag
            $xml = str_replace('<' . $remove . ':', '<', $xml);
            // Now remove the namespace from the closing of the tag
            $xml = str_replace('</' . $remove . ':', '</', $xml);
            // This XML uses the name space with CommentText, so remove that too
            $xml = str_replace($remove . ':commentText', 'commentText', $xml);
            // Complete the pattern for RegEx to remove this namespace declaration
            $pattern = "/xmlns:{$remove}{$nameSpaceDefRegEx}/";
            // Remove the actual namespace declaration using the Pattern
            $xml = preg_replace($pattern, '', $xml, 1);
        }

        // Return sanitized and cleaned up XML with no namespaces
        return $xml;
    }

    public function uploadFiles(Request $request){
        return $this->apiService->uploadFiles($request);
    }

    public function webSendDocket(Request $request){
        $request['template'] = json_decode($request->template,true);
        $temp = [];
        foreach ($request['rt_user_receivers'] as $key => $value) {
            if($value){
                array_push($temp,json_decode($value,true));
            }
        }
        if(count($request['rt_user_receivers'] ) == 1){
            $request['rt_user_approvers'] = json_decode($request['rt_user_receivers'][0],true);
        }
        $request['rt_user_receivers'] = $temp;

        $temp = [];
        foreach ($request['email_user_receivers'] as $key => $value) {
            if($value){
                array_push($temp,json_decode($value,true));
            }
        }
        if(count($request['email_user_receivers'] ) == 1){
            $request['email_user_approvers'] = json_decode($request['email_user_receivers'][0],true);
        }
        $request['email_user_receivers'] = $temp;

        $temp = [];
        if($request['email_user_approvers']){
            foreach ($request['email_user_approvers'] as $key => $value) {
                if($value){
                    array_push($temp,json_decode($value,true));
                }
            }
        }
        $request['email_user_approvers'] = $temp;

        $temp = [];
        if($request['rt_user_approvers']){
            foreach ($request['rt_user_approvers'] as $key => $value) {
                if($value){
                    array_push($temp,json_decode($value,true));
                }
            }
        }

        if(isset($request->docket_data["draft_name"])){  //save to draft
            if(isset($request->docket_data["draft_id"])){
                $draft = DocketDraft::find($request->docket_data["draft_id"]);
                if(!$draft){
                    $draft = new DocketDraft();
                }
            }else{
                $draft = new DocketDraft();
            }
            $draft->user_id = auth()->user()->id;
            $draft->docket_id = $request->template['id'];
            $draft->value = json_encode($request->toArray());
            $draft->is_draft = 1;
            if(isset($request->docket_data['isAdmin'])){
                if($request->docket_data['isAdmin'] == 'true'){
                    $draft->is_admin = 1;
                }
            }
            $draft->save();
            if($request->has('addition_data')){
                if($request->addition_data != "undefined"){
                    $addition_data = json_decode($request->addition_data);
                    $checkDocketDraftsAssign = DocketDraftsAssign::where([['assign_docket_user_id',$addition_data->assign_docket_id],
                                            ['docket_id',$addition_data->docket_template_id],['docket_draft_id',$draft->id]])->first();
                    if($checkDocketDraftsAssign == null){
                        foreach(json_decode($addition_data->machineList) as $machine){
                            $docketDraftsAssign = new DocketDraftsAssign();
                            $docketDraftsAssign->assign_docket_user_id = $addition_data->assign_docket_id;
                            $docketDraftsAssign->docket_id = $addition_data->docket_template_id;
                            $docketDraftsAssign->docket_draft_id = $draft->id;
                            $docketDraftsAssign->machine_id = $machine;
                            $docketDraftsAssign->save();
                        }
                        foreach(json_decode($addition_data->employeeList) as $employee){
                            $docketDraftsAssign = new DocketDraftsAssign();
                            $docketDraftsAssign->assign_docket_user_id = $addition_data->assign_docket_id;
                            $docketDraftsAssign->docket_id = $addition_data->docket_template_id;
                            $docketDraftsAssign->docket_draft_id = $draft->id;
                            $docketDraftsAssign->user_id = $employee;
                            $docketDraftsAssign->save();
                        }
                    }
                }
            }
            
            return response()->json(['message' => MessageDisplay::DocketDraftSave],200);
        }else{
            $request['rt_user_approvers'] = $temp;
            $dataRequest = new Request();
            $dataRequest->headers->set('companyId', auth()->user()->companyInfo->id);
            $dataRequest->headers->set('userId', auth()->user()->id);
            $dataRequest['data'] = json_encode($request->toArray());
            try {
                $apiController = new V1ApiController();
                $apiController->v1SaveSentDefaultDocket($dataRequest);
                // DocketDraft::where([['user_id',auth()->user()->id],['is_draft',1]])->delete();
                return response()->json(['message' => MessageDisplay::DocketAdded],200);
            } catch (\Exception $ex) {
                dd($ex);
                return response()->json(['message' => MessageDisplay::ERROR],500);
            }
        }
        return response()->json(['message' => MessageDisplay::ERROR],500);
    }

    public function taskManagement(TaskManagementApiRequest $request){
        return $this->apiService->taskManagement($request);
    }

    public function taskManagementById(Request $request){
        $employee = $this->userService->getEmployeeList($request);
        $docketTemplate = $this->docketService->getAssignedDocketTemplateByUserId($request);
        $docket_id = $request->template_id;
        return $this->apiService->taskManagementById($request,$docket_id,$employee,$docketTemplate);
    }

    public function taskStatusManagement(Request $request){
        try {
            $this->apiService->taskStatusManagement($request);
            if($request->status == 1){
                $message = "Active";
            }else{
                $message = "Inactive";
            }
            return response()->json(['message' => $message],200);
        } catch (\Exception $ex) {
            dd($ex);
            return response()->json(['message' => MessageDisplay::ERROR],500);
        }
    }
}
