<?php
namespace App\Services\V2\Api;

use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\StaticValue;
use App\Helpers\V2\MessageDisplay;
use App\Http\Resources\V2\Docket\AssignDocketTempleteResource;
use App\Http\Resources\V2\Docket\DocketCategoryResource;
use App\Http\Resources\V2\Docket\DocketDetailByIdResource;
use App\Http\Resources\V2\Docket\DocketEmailConversationResource;
use App\Http\Resources\V2\Docket\DocketTempleteDetailDocketFieldResource;
use App\Http\Resources\V2\Docket\DocketTempleteDetailDocketGridFieldResource;
use App\Http\Resources\V2\Docket\DocketTempleteResource;
use App\Http\Resources\V2\Invoice\InvoiceFilterDocketResource;
use App\Services\V2\ConstructorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Support\Collection;
use Validator;
use App\Mail\EmailDocket;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCopyEmailDocket;
use App\Mail\SendCopyDocket;
use overint\MailgunValidator;
use App\Helpers\V2\AmazoneBucket;

class DocketService extends ConstructorService {

    public function getDocketTemplateList($request){
        if(auth()->user() != null){
            $userId = auth()->user()->id;
            $userCompany = auth()->user()->companyInfo;
        }else{
            $userId = auth()->user()->id;
            $userCompany = $this->companyRepository->getDataWhere([['id',$request->header('companyId')]])->first();
        }
        $docketTemplate     =  $this->docketRepository->getDataWhere([['company_id',$userCompany->id],['is_archive',0]])
            ->select('id','title','docketApprovalType','created_at')->orderBy('id','desc')->get();
        $date = Carbon::now()->toDateString();
        $docketTemplates   =   array();
        foreach ($docketTemplate as $row ){
            if ($this->sentDocketsRepository->getDataWhere([['docket_id', $row->id],['user_id', $userId]])->whereDate('created_at',$date)->count() == 0) {
                $docketTemplates[] = array('id' => $row->id,
                    'title' => $row->title,
                    'docket_approval_type'=>$row->docketApprovalType,
                );
            }
        }
        return $docketTemplates;
    }

    public function getAssignedDocketTemplateByUserId($request){
        $docketTemplate     =   array();
        if(auth()->user() != null){
            $userId = auth()->user()->id;
        }else{
            $userId = $request->header('userId');
        }
        $employeeLeaveCount = $this->leaveRepository->getDataWhere([['user_id',$userId],[DB::raw('DATE_FORMAT(from_date,"%Y-%m-%d")'), "<=",Carbon::now()],[DB::raw('DATE_FORMAT(to_date,"%Y-%m-%d")'), ">=",Carbon::now()]])->count();
        $docketTemplateQuery    =   $this->assignedDocketRepository->getDataWhere([['user_id',$userId]])->with('docketInfo.defaultRecipient')->orderBy('created_at','desc')->get();
        foreach ($docketTemplateQuery as $row){
            if($row->date_range != null){
                $date_range = explode('-',$row->date_range);
                $employeeLeaveCount = $this->leaveRepository->getDataWhere([['user_id',$userId],[DB::raw('DATE_FORMAT(from_date,"%Y-%m-%d")'), ">=",Carbon::parse($date_range[0])],[DB::raw('DATE_FORMAT(to_date,"%Y-%m-%d")'), "<=",Carbon::parse($date_range[1])]])->count();
            }else{
                $employeeLeaveCount = 0;
            }
            if($row->docketInfo->is_archive == 0 && $employeeLeaveCount == 0){
                if ($row->assign_type == 0){
                    $defaultRecipien= array();
                    if($row->docketInfo->defaultRecipient){
                        $rt_user = array();
                        $email_client = array();
                        foreach ($row->docketInfo->defaultRecipient as $defaultRecipients){
                            if(@$defaultRecipients->user_type== 1){
                                $rt_user[] = $defaultRecipients->userInfo->id;
                            }
                            if(@$defaultRecipients->user_type== 2){
                                $email_client[] = $defaultRecipients->emailUser->id;
                            }
                        }
                        $defaultRecipien = ['rt_user' => $rt_user, 'email_client' => $email_client];
                    }
                    $docketTemplate[] = new AssignDocketTempleteResource($row,'assignDocketTemplete',$defaultRecipien);
                }else{
                    $from = explode(' - ',$row->date_range)[0];
                    $to = explode(' - ',$row->date_range)[1];
                    $startDate = Carbon::parse($from);
                    $endDate = Carbon::parse($to);
                    $check = Carbon::now()->between($startDate,$endDate);
                        if($check){
                            $defaultRecipien= array();
                            if($row->docketInfo->defaultRecipient){
                                $rt_user = array();
                                $email_client = array();
                                foreach ($row->docketInfo->defaultRecipient as $defaultRecipients){
                                    if(@$defaultRecipients->user_type== 1){
                                        $rt_user[] = $defaultRecipients->userInfo->id;
                                    }
                                    if(@$defaultRecipients->user_type== 2){
                                        $email_client[] = $defaultRecipients->emailUser->id;
                                    }
                                }
                                $defaultRecipien = ['rt_user' => $rt_user, 'email_client' => $email_client];
                            }
                            $docketTemplate[] = new AssignDocketTempleteResource($row,'assignDocketTemplete',$defaultRecipien);
                        }
                }
            }
        }
        return $docketTemplate;
    }

    public function getDocketTemplateDetailsById($request,$docketId){
        if(auth()->user() != null){
            $userCompany = auth()->user()->companyInfo;
        }else{
            $userCompany = $this->companyRepository->getDataWhere([['id',$request->header('companyId')]])->first();
        }
        $docketList = $this->docketRepository->getDataWhere([['id',$docketId]])->get();
        if(count($docketList) > 0){
            $docket     =  $docketList->first();
            $docketFieldQuery    =  $this->docketFieldRepository->getDataWhere([['docket_id',$docket->id]])->orderBy('order','asc')->get();
            $docketFields   =   array();
            foreach ($docketFieldQuery as $row){
                $subField   =   array();
                $data['row'] = $row;
                if($row->docket_field_category_id == 7) {
                    $data['repository'] = $this->docketUnitRateRepository;
                    $data['whereArray'] = [["docket_field_id", $row->id]];
                    $data['select'] = ['id', 'type', 'label'];
                    $orderBy[0] = 'type';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $docketFields[] = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id == 13) {
                    $data['repository'] = $this->docketFieldFooterRepository;
                    $data['whereArray'] = [["field_id", $row->id]];
                    $data['select'] = ['id', 'value'];
                    $orderBy[0] = 'id';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $footers = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id == 20) {
                    $data['repository'] = $this->docketManualTimerRepository;
                    $data['whereArray'] = [["docket_field_id", $row->id]];
                    $data['select'] = ['id', 'type', 'label'];
                    $orderBy[0] = 'type';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $data['breakSubField'] = $this->docketManualTimerBreakRepository->getDataWhere([["docket_field_id", $row->id]])->select('id','type', 'label','explanation')->orderBy('type', 'asc')->get();
                    $docketFields[] = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id == 3) {
                    $docketFieldNumber = $this->docketFieldNumberRepository->getDataWhere([["docket_field_id", $row->id]])->select('min', 'max','tolerance')->first();
                    if ($docketFieldNumber == null){
                        $docketFieldNumbers = array(
                            'min' => null,
                            'max' => null,
                            'tolerance' => null,
                        );
                    }else{
                        $docketFieldNumbers= $docketFieldNumber;
                    }

                    if ($row->is_dependent == 1){
                        $docketPrefillerValue = $this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$row->docket_prefiller_id]])->get();

                        $data['docketPreFiller'] = $docketPrefillerValue;
                        $data['docketFieldNumbers'] = $docketFieldNumbers;
                        $data['canAddChildCheck'] = true;
                        $data['repository'] = $this->docketPrefillerValueRepository;
                        $data['parentArray'] = 'getDocketPrefiller';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }else{
                        $data['docketPreFiller'] = $row->docketPreFiller;
                        $data['docketFieldNumbers'] = $docketFieldNumbers;
                        $data['canAddChildCheck'] = false;
                        $data['repository'] = $this->docketFiledPreFillerRepository;
                        $data['parentArray'] = 'getNormalParentData';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }
                }
                elseif ($row->docket_field_category_id == 18) {
                    $subFields = array();
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 1){
                            $subFields[] = $this->docketYesNoFieldLoop($subRow);
                        }
                    endforeach;
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 0){
                            $subFields[] = $this->docketYesNoFieldLoop($subRow);
                        }
                    endforeach;
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 2){
                            $subFields[] = $this->docketYesNoFieldLoop($subRow);
                        }
                    endforeach;
                    $docketFields[] = new DocketTempleteDetailDocketFieldResource($row,$subFields);
                    // $docketFields[] = array('id' => $row->id,
                    //     'docket_field_category_id' => $row->docket_field_category_id,
                    //     'docket_field_category' => $row->fieldCategoryInfo->title,
                    //     'label' => $row->label,
                    //     'order' => $row->order,
                    //     'required' => $row->required,
                    //     'subField' => $subFields,
                    //     'yesNoSubField' => $subFields);

                }
                elseif ($row->docket_field_category_id == 15) {
                    $subFields= array();
                    foreach($row->docketAttached as $subRow):
                        $subFields[]   =     array(
                            'id' => $subRow->id,
                            'name'=>$subRow->name,
                            'url' => AmazoneBucket::url() . $subRow->url);
                    endforeach;
                    $docketFields[] = new DocketTempleteDetailDocketFieldResource($row,$subFields);
                    // $docketFields[] = array('id' => $row->id,
                    //     'docket_field_category_id' => $row->docket_field_category_id,
                    //     'docket_field_category' => $row->fieldCategoryInfo->title,
                    //     'label' => $row->label,
                    //     'order' => $row->order,
                    //     'required'=>$row->required,
                    //     'subField' => $subFields,
                    //     'documentSubField' => $subFields);

                }
                elseif ($row->docket_field_category_id == 9) {
                    if ($row->is_dependent == 1){
                        $docketPrefillerValue = $this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$row->docket_prefiller_id]])->get();
                        $data['docketPreFiller'] = $docketPrefillerValue;
                        $data['canAddChildCheck'] = true;
                        $data['repository'] = $this->docketPrefillerValueRepository;
                        $data['parentArray'] = 'getDocketPrefiller';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }else{
                        $data['docketPreFiller'] = $row->docketPreFiller;
                        $data['repository'] = $this->docketFiledPreFillerRepository;
                        $data['parentArray'] = 'getNormalParentDatas';
                        $data['canAddChildCheck'] = false;
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }
                }
                elseif ($row->docket_field_category_id == 6) {
                    $data['row'] = $row;
                    $data['docketPreFiller'] = $row->docketPreFiller;
                    $data['canAddChildCheck'] = false;
                    $data['repository'] = $this->docketFiledPreFillerRepository;
                    $data['parentArray'] = 'getNormalParentData';
                    $docketFields[] = $this->docketPreFillerRowIndependent($data);
                }
                elseif ($row->docket_field_category_id == 24) {
                    $data['repository'] = $this->docketTallyableUnitRateRepository;
                    $data['whereArray'] = [["docket_field_id", $row->id]];
                    $data['select'] = ['id', 'type', 'label'];
                    $orderBy[0] = 'type';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $docketFields[] = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id == 22) {
                    $modularField  = array();
                    $sumableStatus = false;
                    $canAddChild = true;
                    $isEmailedSubject = false;
                    foreach ($row->girdFields as $gridField){
                        if($gridField->is_emailed_subject == 1){
                            $isEmailedSubject = true;
                        }

                        if ($gridField->docket_field_category_id == 3){
                            if ($gridField->sumable == 1){
                                $sumableStatus = true;
                            }
                        }
                        if ($gridField->is_dependent == 1){
                            $docketPrefillerValue = $this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$gridField->docket_prefiller_id]])->get();

                            $data['docketPreFiller'] = $docketPrefillerValue;
                            $data['gridField'] = $gridField;
                            $data['canAddChildCheck'] = true;
                            $data['repository'] = $this->docketPrefillerValueRepository;
                            $data['parentArray'] = 'getDocketPrefiller';
                            $data['canAddChildCheck'] = true;
                            $docketPreFillerRowIndependent = $this->docketPreFillerRowIndependent($data);
                        }else{
                            $data['docketPreFiller'] = $gridField->gridFieldPreFiller;
                            $data['gridField'] = $gridField;
                            $data['canAddChildCheck'] = true;
                            $data['repository'] = $this->docketGridPrefillerRepository;
                            $data['parentArray'] = 'getParentData';
                            $data['canAddChildCheck'] = true;
                            $docketPreFillerRowIndependent = $this->docketPreFillerRowIndependent($data);
                        }
                        if ($gridField->gridFieldFormula != null){
                            $formulaValue = unserialize($gridField->gridFieldFormula->formula);
                            $formulaArray = array();
                            foreach ($formulaValue as $formulaValues){
                                if (is_numeric($formulaValues)){
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "number",
                                    );
                                }elseif (preg_match("/TDiff/i", $formulaValues)) {
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "function"
                                    );
                                }
                                elseif (preg_match("/cell/i", $formulaValues)) {
                                    $formulaArray[] = array(
                                        "value" => ltrim($formulaValues, 'cell'),
                                        "type" => "cell"
                                    );
                                }
                                else{
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "operator"
                                    );
                                }
                            }
                        }else{
                            $formulaArray = array();
                        }
                        $gridManualTimer  = array();
                        if($gridField->docket_field_category_id == 20){
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 1,
                                'label' => "From"
                            );
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 2,
                                'label' => "To"
                            );
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 3,
                                'label' => "Total Break",
                                'explanation'=> 0
                            );
                        }
                        // $gridData['id'] = $gridField->id;
                        // $gridData['docket_field_id'] = $gridField->docket_field_id;
                        // $gridData['docket_field_category_id'] = $gridField->docketFieldCategory->id;
                        // $gridData['docket_field_category_label'] = $gridField->docketFieldCategory->title;
                        // $gridData['label'] = $gridField->label;
                        // $gridData['order'] = $gridField->order;
                        // $gridData['required'] = $gridField->required;
                        // $gridData['prefiller_data'] =array('autoPrefiller'=>$gridField->auto_field,'isDependent'=>$gridField->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$docketPreFillerRowIndependent['datas']) ;
                        // $gridData['default_value'] = ($docketPreFillerRowIndependent['defaultPrefillerValue']=="")? "" : implode(",",$docketPreFillerRowIndependent['prefillerArray']);
                        // $gridData['subField'] = $gridManualTimer;
                        // $gridData['formula']=  @$formulaArray;
                        // $gridData['sumable'] = ($gridField->sumable== 1)? true : false ;
                        $gridData = new DocketTempleteDetailDocketGridFieldResource($gridField,$canAddChild,$docketPreFillerRowIndependent,$gridManualTimer,$formulaArray);
                        array_push($modularField, $gridData);
                    }
                    $docketFields[] = new DocketTempleteDetailDocketFieldResource($row,$subField,$isEmailedSubject,$modularField,$sumableStatus);
                    // $docketFields[] = array('id' => $row->id,
                    //     'docket_field_category_id' => $row->docket_field_category_id,
                    //     'docket_field_category' => $row->fieldCategoryInfo->title,
                    //     'label' => $row->label,
                    //     'time_required'=>(@$row->docketFieldDateOption->time == null) ? 0: @$row->docketFieldDateOption->time,
                    //     'order' => $row->order,
                    //     'required'=>$row->required,
                    //     'is_emailed_subject'=>($isEmailedSubject == true) ? 1: 0,
                    //     'modularGrid' => $modularField,
                    //     'sumable'=> $sumableStatus,
                    //     'subField'  => $subField);

                }
                elseif ($row->docket_field_category_id == 28) {
                    $data['repository'] = $this->folderRepository;
                    $data['whereArray'] = [['company_id',$userCompany->id],['type',0]];
                    $data['select'] = [];
                    $orderBy[0] = 'name';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $data['templateFolderAssign'] = $this->templateAssignFolderRepository->getDataWhere([['template_id',$docketId]])->get()->first();
                    $docketFields[] = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id != 30) {
                    if ($row->is_dependent == 1){
                        $docketPrefillerValue = $this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$row->docket_prefiller_id]])->get();

                        $data['docketPreFiller'] = $docketPrefillerValue;
                        $data['canAddChildCheck'] = true;
                        $data['repository'] = $this->docketPrefillerValueRepository;
                        $data['parentArray'] = 'getDocketPrefiller';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }else{
                        $data['docketPreFiller'] = $row->docketPreFiller;
                        $data['canAddChildCheck'] = false;
                        $data['repository'] = $this->docketFiledPreFillerRepository;
                        $data['parentArray'] = 'getNormalParentData';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }
                }
            }

            if(@$footers){
                $docketFields[] =   $footers;
            }

            $isDocketNumber = false;
            $company = $this->companyRepository->getDataWhere([['id',$userCompany->id]])->first();
            if($company->number_system == 1){
                $isDocketNumber = false;
            }else{
                if($docket->is_docket_number == 1){
                    $isDocketNumber = true;
                }else{
                    $isDocketNumber = false;
                }
            }
            return response()->json(new DocketTempleteResource($docket,$isDocketNumber,$docketFields),200);
        }else {
            return response()->json(["message"=>'Docket not found!'],500);
        }
    }

    public function getDefaultDocket($request){
        ini_set('memory_limit','256M');
        if(auth()->user() != null){
            $userCompany = auth()->user()->companyInfo;
        }else{
            $userCompany = $this->companyRepository->getDataWhere([['id',$request->header('companyId')]])->first();
        }
        $users_company_id   =  $userCompany->id;
        if($this->docketRepository->getDataWhere([['company_id',$users_company_id]])->count() > 0){
            $docket     =  $this->docketRepository->getDataWhere([['company_id',$users_company_id]])->first();
            $docketFieldQuery    = $this->docketFieldRepository->getDataWhere([['docket_id',$docket->id]])->orderBy('order','asc')->with('docketInfo','fieldCategoryInfo')->get();
            $docketFields   =   array();
            foreach ($docketFieldQuery as $row){
                $subField   =   array();
                if($row->docket_field_category_id == 7){
                    $subField    =  $this->docketUnitRateRepository->getDataWhere([["docket_field_id",$row->id]])->select('id','type','label')->orderBy('type','asc')->get();
                }

                $docketFields[] = new DocketCategoryResource($row,$row->label,$row->value,$subField,$row->fieldCategoryInfo->title); 
            }
            return response()->json(['docket' => array('id' => $docket->id, 'title' => $docket->title), 'docket_field'  => $docketFields],200);
        }else {
            return response()->json(["message"=>'Docket not found!'],500);
        }
    }

    public function docket($request,$id){
        if(auth()->user() != null){
            $userCompany = auth()->user()->companyInfo;
        }else{
            $userCompany = $this->companyRepository->getDataWhere([['id',$request->header('companyId')]])->first();
        }
        $sentDocket = $this->sentDocketsRepository->getDataWhere([['id', $id]]);
        if ($sentDocket->count() == 1){
            $companyId  =    $userCompany->id;

            $validCompanyId     =   array();
            $validCompanyId[]   =   $sentDocket->first()->sender_company_id;

            if($sentDocket->first()->recipientInfo){
                foreach($sentDocket->first()->recipientInfo as $recipient):
                    $company = $this->companyRepository->getDataWhere([["user_id",$recipient->user_id]])->first();
                    if($company != null){
                        $validCompanyId[]   =   $company->id;
                    }else{
                        $validCompanyId[]   =   @$this->employeeRepository->getDataWhere([['user_id',$recipient->user_id]])->first()->company_id;
                    }
                endforeach;
            }
            if(in_array($companyId,$validCompanyId)){
                $sentDocketValueQuery    =   $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$id]])->get();
                $sentDocketValue    = array();
                foreach ($sentDocketValueQuery as $row){
                    if((!$row->docketFieldInfo->is_hidden && $row->sentDocket->sender_company_id!=$companyId) || $row->sentDocket->sender_company_id==$companyId):
                        $subFiled   =   [];
                        if($row->docketFieldInfo->docket_field_category_id==7):
                            foreach($row->sentDocketUnitRateValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'label' => $subFiledRow->docketUnitRateInfo->label,
                                    'value' => $subFiledRow->value);
                            endforeach;
                            $sentDocketValue[] = new DocketCategoryResource($row,$row->label,$row->value,$subFiled);
                            unset($subFiled);
                        elseif($row->docketFieldInfo->docket_field_category_id==9):
                            foreach($row->sentDocketImageValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'value' => AmazoneBucket::url() . $subFiledRow->value);
                            endforeach;
                            $sentDocketValue[] = new DocketCategoryResource($row,$row->label,AmazoneBucket::url() . $row->value,$subFiled);
                        elseif($row->docketFieldInfo->docket_field_category_id==14):
                            foreach($row->sentDocketImageValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'value' => AmazoneBucket::url() . $subFiledRow->value);
                            endforeach;
                            $sentDocketValue[] = new DocketCategoryResource($row,$row->label,AmazoneBucket::url() . $row->value,$subFiled);
                        elseif($row->docketFieldInfo->docket_field_category_id==15):
                            foreach($row->sentDocketAttachment as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'name'=>$subFiledRow->document_name,
                                    'url' => AmazoneBucket::url() . $subFiledRow->url);
                            endforeach;
                            $sentDocketValue[] = new DocketCategoryResource($row,$row->label,AmazoneBucket::url() . $row->url,$subFiled);
                        elseif($row->docketFieldInfo->docket_field_category_id==5):
                            foreach($row->sentDocketImageValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'value' => AmazoneBucket::url() . $subFiledRow->value);
                            endforeach;
                            $sentDocketValue[] = new DocketCategoryResource($row,$row->label,AmazoneBucket::url() . $row->value,$subFiled);
                        elseif($row->docketFieldInfo->docket_field_category_id==6):
                            if ($row->value== "n/a"){
                                $sentDocketValue[] = new DocketCategoryResource($row,$row->label,$row->value,$subFiled);
                            }else {
                                $sentDocketValue[] = new DocketCategoryResource($row,$row->label,Carbon::parse($row->value)->format('d-M-Y'),$subFiled);
                            }
                        elseif($row->docketFieldInfo->docket_field_category_id==18):
                            $sentDocketValue[] = new DocketCategoryResource($row,unserialize($row->label),$row->value,$subFiled);
                        elseif ($row->docketFieldInfo->docket_field_category_id==13):
                            $footers = new DocketCategoryResource($row,$row->label,$row->value,$subFiled);
                        else:
                            $sentDocketValue[] = new DocketCategoryResource($row,$row->label,$row->value,$subFiled);
                        endif;
                    endif;
                }
                if(@$footers){
                    $sentDocketValue[] =   $footers;
                }
                return response()->json(['docket' => $sentDocketValue],200);
            }else{
                return response()->json(['message' => MessageDisplay::UnAuthorized],500);
            }
        }else{
            return response()->json(['message' => MessageDisplay::DocketNotFound],500);
        }
    }

    public function docketUpdate($request,$id){
        try {
            DB::beginTransaction();
            $edited_docket_field_id= $request->edited_docket_field_id;
            $saveDocket = $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$id]])->get();
            foreach ($saveDocket as $rowData){
                if (in_array($rowData->docket_field_id,$edited_docket_field_id)){
                    $editDocketRequest = new Request();
                    $editDocketRequest['sent_docket_value_id'] = $rowData->id;
                    if($rowData->docketFieldInfo->docket_field_category_id == 13){
                        $editDocketRequest['value'] = (Input::has("formField" . $rowData->docket_field_id)) ? Input::get('formField' . $rowData->docket_field_id) : " ";
                    }else{
                        $editDocketRequest['value'] = (Input::has("formField" . $rowData->docket_field_id)) ? Input::get('formField' . $rowData->docket_field_id) : "N/A";
                    }
                    $editDocket = $this->sentDocEditedValueRepository->insertAndUpdate($editDocketRequest);
                    $this->sentDocketsValueRepository->getDataWhere([['id',$rowData->id]])->update(['last_edited_value_id'=>$editDocket->id]);
                }
            }
            DB::commit();
            return MessageDisplay::Success;
        } catch (\Exception $ex) {
            DB::rollback();
            return MessageDisplay::ERROR;
        }

    }

    public function getInvoiceableDocketList($request,$userId){
        $totalSentDocketID  =    array();
        $companyId = auth()->user()->companyInfo->id;
        $receiverCompanyId  =    FunctionUtils::getCompanyId($userId);
        $receiverCompanyUserId  =   FunctionUtils::getCompanyAllUserId($receiverCompanyId);

        $sentDocketQueryTemp    =  $this->sentDocketsRepository->getDataWhere([['sender_company_id',$companyId],['invoiceable',1]])->orderBy('id','desc')->get();
        foreach($sentDocketQueryTemp as $sentDocket){
            if($sentDocket->sentDocketRecipientApproval){
                foreach ($sentDocket->sentDocketRecipientApproval as $approvalUserID){
                    $status     =    true;
                    if(!in_array($approvalUserID->user_id,$receiverCompanyUserId)){
                        $status =   false;
                        break;
                    }
                }
            }
            if($status){
                $totalSentDocketID[]    =   $sentDocket->id;
            }
        }

        $sentDocketQuery    =  $this->sentDocketsRepository->getDataWhereIn('id',$totalSentDocketID)->orderBy('created_at', 'desc')->get();
        $invoiceableDockets =   array();
        if(count($sentDocketQuery) > 0) {
            $resultQuery = $sentDocketQuery;

            foreach ($resultQuery as $result) {
                if ($result->sender_company_id == $companyId):
                    $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                    $senderImage = $result->senderUserInfo->image;
                    $company = $result->senderCompanyInfo->name;

                    $recipientsQuery    =   $result->recipientInfo;
                    $recipientData      =   "";
                    foreach($recipientsQuery as $recipient) {
                        if($recipient->id==$recipientsQuery->first()->id)
                            $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                        else
                            $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                    }
                    $sentDocketRecipientApproval = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]])->get();
                    //approval text
                    $totalRecipientApprovals    =   $sentDocketRecipientApproval->count();
                    $totalRecipientApproved     =   count($sentDocketRecipientApproval->where('status',1));

                    //check is approval
                    $isApproval                 =   0;
                    $isApproved                 =   0;

                    if(count($sentDocketRecipientApproval->where('user_id',auth()->user()->id)) == 1){
                        $isApproval             =   1;
                        //check is approved
                        if(count($sentDocketRecipientApproval->where('user_id',auth()->user()->id)->where('status',1)) == 1){
                            $isApproved             =   1;
                        }
                    }

                    if ($totalRecipientApproved == $totalRecipientApprovals ){
                        $approvalText               =  "Approved";
                    }else{
                        $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                    }

                    $invoiceDescription     =    array();
                    $sentDocketInvoice = $this->sentDocketInvoiceRepository->getDataWhere([['sent_docket_id',$result->id]])->get();
                    $invoiceDescriptionQuery    =  $sentDocketInvoice->where('type',1);
                    foreach($invoiceDescriptionQuery as $description){
                        $invoiceDescription[]   =   array('label'=> $description->sentDocketValueInfo->label,'value' => $description->sentDocketValueInfo->value);
                    }

                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    =    $sentDocketInvoice->where('type',2);
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                        if(is_numeric($unitRate[0]["value"])){
                            $unitRate1= $unitRate[0]["value"];
                        }else{
                            $unitRate1=0;
                        }
                        if(is_numeric($unitRate[1]["value"])){
                            $unitRate2= $unitRate[1]["value"];
                        }else{
                            $unitRate2= 0;
                        }
                        $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
                    }
                    $invoiceableDockets[] = new InvoiceFilterDocketResource($result,'invoiceDocketList',$userName,$company,$invoiceDescription,$invoiceAmount,$recipientData,
                                $senderImage,$approvalText,$isApproval,$isApproved,$result->formatted_id);
                    // $invoiceableDockets[] = array('id' => $result->id,
                    //     'companyDocketId'=>$result->formatted_id,
                    //     'user_id' => $result->user_id,
                    //     'docketName' => $result->docketInfo->title,
                    //     'docketTemplateId' => $result->docketInfo->id,
                    //     'sender' => $userName,
                    //     'company' => $company,
                    //     'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                    //     'invoiceDescription' => $invoiceDescription,
                    //     'invoiceAmount' => $invoiceAmount,
                    //     'recipient'=>$recipientData,
                    //     'senderImage'=>asset($senderImage),
                    //     'status' => $approvalText,
                    //     'isApproval'=>$isApproval,
                    //     'isApproved'=>$isApproved,
                    //     );
                    empty($invoiceDescription);
                    empty($invoiceAmount);
                endif;
            }
            return $invoiceableDockets;
        }
    }

    public function getLatestDockets($request){
        $company = auth()->user()->companyInfo;
        $userId = auth()->user()->id;
        $this->subscriptionCheck($company);
        $jsonData               =    array();
        $latestSentDocketIds        = $this->sentDocketsRepository->getDataWhere([['user_id', $userId]])->orderBy('created_at','desc')->pluck('id')->toArray();
        $latestRecipientSentDocketIds   =  $this->sentDocketRecipientRepository->getDataWhere([['user_id',$userId]])->pluck('sent_docket_id')->toArray();
        $totalSentDocketIds     =   array_merge($latestSentDocketIds, $latestRecipientSentDocketIds);

        $sentDockets            =  $this->sentDocketsRepository->getDataWhereIn('id',$totalSentDocketIds)->orderBy('created_at','desc')->take(30)->get();
        $sentDocketIds 			=	array();

        foreach ($sentDockets as $row):
              $sentDocketIds[] = $row->id;
              $recipientsQuery = $row->recipientInfo;
              $recipientData = "";
              foreach ($recipientsQuery as $recipient) {
                  if ($recipient->id == $recipientsQuery->first()->id)
                      $recipientData = $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                  else
                      $recipientData = $recipientData . ", " . $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
              }
              //check approved or not /.if status == 1 approved

           if ($row->status == 3){
               $status = "Rejected";
           }else{
               if($row->is_cancel == 1){
                   $status = "Cancelled";
               }else{
                   if($row->user_id==$userId){
                       $status     =   "Sent";
                   }else{
                       $status     =   "Received";
                   }
               }
           }
              //approval text
              $sentDocketRecipientApprovalData = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id', $row->id]]);
              $totalRecipientApprovals = $sentDocketRecipientApprovalData->count();
              $totalRecipientApproved = $sentDocketRecipientApprovalData->where('status', 1)->count();

              //check is approval
              $isApproval = 0;
              $isApproved = 0;
              if ($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id', $row->id],['user_id', $userId]])->count() == 1) {
                  $isApproval = 1;

                  //check is approved
                  if ($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id', $row->id],['user_id', $userId]])->where('status', 1)->count() == 1) {
                      $isApproved = 1;
                  }
              }

              if ($totalRecipientApproved == $totalRecipientApprovals) {
                  $approvalText = "Approved";

              } else {
                  $approvalText = $totalRecipientApproved . "/" . $totalRecipientApprovals . " Approved";

              }

            //canreject
            $canRejectDocket = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id', $row->id],['user_id',$userId]]);
            $canReject = 0;
            $isReject = 0;
            if($canRejectDocket->count() > 0 ){
                if ($canRejectDocket->first()->status == 0){
                    if ($row->status == 0) {
                        $canReject = 1;
                    }else{
                        $canReject = 0;
                    }
                }else{
                    $canReject = 0;
                }

                if ($row->status == 3){
                    $isReject = 1;
                }else{
                    $isReject = 0;
                }
            }

            if(($row->is_cancel && $row->user_id == $userId) || !$row->is_cancel){
                $userName = $row->senderUserInfo->first_name . " " . $row->senderUserInfo->last_name;
                $jsonData[] = new InvoiceFilterDocketResource($row,'invoiceLatestDocket',$userName,$row->senderCompanyInfo->name,null,null,$recipientData,
                                $row->senderUserInfo->image,$approvalText,$isApproval,$isApproved,$row->formatted_id,$status,$canReject,$isReject,$row->is_cancel);

                // $jsonData[] = array('id' => $row->id,
                //                     'companyDocketId'=>$row->formatted_id,
                //                     'user_id' => $row->user_id,
                //                     'sender' => $row->senderUserInfo->first_name . " " . $row->senderUserInfo->last_name,
                //                     'profile' => asset($row->senderUserInfo->image),
                //                     'docketName' => $row->docketInfo->title,
                //                     'company' => $row->senderCompanyInfo->name,
                //                     'recipients' => $recipientData,
                //                     'dateAdded' => Carbon::parse($row->created_at)->format('d-M-Y'),
                //                     'dateSorting' => Carbon::parse($row->created_at)->format('d-M-Y H:i:s'),
                //                     'approvalText' => $approvalText,
                //                     'isApproval' => $isApproval,
                //                     'isApproved' => $isApproved,
                //                     'canReject'=>$canReject,
                //                     'isReject' => $isReject,
                //                     'isCancel' => $row->is_cancel,
                //                     'status' => $status);
            }
        endforeach;

        $jsonData = FunctionUtils::conversationArrayDateSorting($jsonData);
        // //conversation sorting according to dateAdded
        // $size = count($jsonData);
        // for($i = 0; $i<$size; $i++){
        //     for ($j=0; $j<$size-1-$i; $j++) {
        //         if (strtotime($jsonData[$j+1]["dateSorting"]) > strtotime($jsonData[$j]["dateSorting"])) {
        //             $tempArray   =    $jsonData[$j+1];
        //             $jsonData[$j+1] = $jsonData[$j];
        //             $jsonData[$j]  =   $tempArray;
        //         }
        //     }
        // }

        //check if subscription was free/count remaining docket left
        if($company->trial_period==3){
            $totalMonthDockets  =  FunctionUtils::checkSubscription($company);
            if($totalMonthDockets>=5){
                $freeSubscriptionStatus    =    array('status' => true, 'message' => 'Please upgrade your subscription. Your active subscription has an ability to send a maximum of 5 dockets per month.');
            }else{
                $freeSubscriptionStatus    =   array('status' => true, 'message' => '');
            }
        }else{
            $freeSubscriptionStatus    =   array('status' => false, 'message' => '');
        }

        $notificationCount  =  $this->userNotificationRepository->getDataWhere([['receiver_user_id',$userId],['status',0]])->count();
        $draftCount = $this->docketDraftRepository->getDataWhere([['user_id',$userId]])->count();
        return response()->json(array('status' => true, 'dockets' =>$jsonData, 'freeSubscriptionStatus' => $freeSubscriptionStatus,'notificationCount' => $notificationCount,'message_status'=>1,'draftCount'=>$draftCount));
    }

    public function getDocketList(){
        $authCompany = auth()->user()->companyInfo;
        $sentDocketQuery    = $this->sentDocketsRepository->getDataWhere([['company_id',$authCompany->id]])->orWhere('sender_company_id',$authCompany->id)
                                                            ->with('senderUserInfo','docketInfo')->orderBy('created_at','desc')->get();
        $sentDocket = array();
        foreach ($sentDocketQuery as $row):
            if($row->company_id == $authCompany->id):
                if($row->status==0):
                    $docketStatus   =   "Received";
                endif;
            else :
                if($row->status==0):
                    $docketStatus   =   "Sent";
                endif;
            endif;
            if($row->status==1)
                $docketStatus ="Approved";

            $userName = $row->senderUserInfo->first_name. " ".$row->senderUserInfo->last_name;
            $sentDocket[] = new DocketEmailConversationResource($row,$docketStatus,$userName,$row->senderCompanyInfo->name);
            // $sentDocket[]   =   array('id' => $row->id,
            //     'docketName' => $row->docketInfo->title,
            //     'sender' => $row->senderUserInfo->first_name. " ".$row->senderUserInfo->last_name,
            //     'company'   =>  $row->senderCompanyInfo->name,
            //     'dateAdded' =>  Carbon::parse($row->created_at)->format('d-M-Y'),
            //     'status'    => $docketStatus);
        endforeach;

        return $sentDocket;
    }

    public function getDocketDetailByIdWebView($request,$id){
        $authCompany = auth()->user()->companyInfo;
        $sentDocket     = $this->sentDocketsRepository->getDataWhere([['id',$id]])->with('recipientInfo.userInfo')->first();
        $docketTimer = $this->sentDocketTimerAttachmentRepository->getDataWhere([['sent_docket_id',$id],['type',1]])->get();
        $sentDocketRecepients = array();

        foreach ($sentDocket->recipientInfo as $sentDocketRecepient){
            if ($sentDocketRecepient->userInfo->employeeInfo){
                $companyNameRecipent = $sentDocketRecepient->userInfo->employeeInfo->companyInfo->name;
            }else if ($sentDocketRecepient->userInfo->companyInfo){
                $companyNameRecipent = $sentDocketRecepient->userInfo->companyInfo->name;
            }
            $sentDocketRecepients[]=array(
                'name'=>$sentDocketRecepient->userInfo->first_name." ".$sentDocketRecepient->userInfo->last_name,
                'company_name'=> $companyNameRecipent,
            );
        }

        $data= (new Collection($sentDocketRecepients))->sortBy('company_name');

        $receiverDetail = array();

        foreach ($data as $datas){
            $receiverDetail[$datas['company_name']][]= $datas['name'];
        }
        if ($sentDocket->theme_document_id == 0){
            if($sentDocket->sender_company_id == $authCompany->id){
                $docketFields   =  $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$sentDocket->id]])->get();
                return (array('docket' => view('dashboard.company.docketManager.preview',compact('sentDocket','docketFields','docketTimer','request','receiverDetail'))->render()));
            }else{
                //get total company employee ids
                $employeeIds    =   $this->employeeRepository->getDataWhere([['company_id',$authCompany->id]])->pluck('user_id');
                $employeeIds[]  =   $authCompany->user_id;
                if($this->sentDocketRecipientRepository->getDataWhere([['sent_docket_id',$id]])->whereIn('user_id',$employeeIds)->count() > 0){
                    $docketFields   =   $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$sentDocket->id]])->get();
                    return (array( 'docket' => view('dashboard.company.docketManager.preview',compact('sentDocket','docketFields','docketTimer','request','receiverDetail'))->render()));
                }else{
                    return response()->json(['message' => MessageDisplay::DocketNotFound],500);
                }
            }
        }else{
            $theme = $this->documentThemeRepository->getDataWhere([['id', $sentDocket->theme_document_id]])->first();
            if($sentDocket->sender_company_id == $authCompany->id){
                $docketFields   =   $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$sentDocket->id]])->get();
                return (array('docket' => view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentDocket','docketFields','docketTimer','employee_name'))->render()));
            }else{
                //get total company employee ids
                $employeeIds    =   $this->employeeRepository->getDataWhere([['company_id',$authCompany->id]])->pluck('user_id');
                $employeeIds[]  =   $authCompany->user_id;
                if(SentDocketRecipient::whereIn('user_id',$employeeIds)->where('sent_docket_id',$id)->count()>0){
                    $docketFields   =   $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$sentDocket->id]])->get();
                    return (array( 'docket' => view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentDocket','docketFields','docketTimer','employee_name'))->render()));
                }else{
                    return response()->json(['message' => MessageDisplay::DocketNotFound],500);
                }
            }
        }
    }

    public function getDocketDetailsById($request, $id){
        $authCompany = auth()->user()->companyInfo;
        $authUserId = auth()->user()->id;
        $sentDocket     = $this->sentDocketsRepository->getDataWhere([['id',$id]]);
        $webView = $this->getDocketDetailByIdWebView($request,$id);
        if($sentDocket->count()==1):
            //check docket associated with user or not
            $companyId  =    $authCompany->id;
            $validCompanyId     =   array();
            $validCompanyId[]   =   $sentDocket->first()->sender_company_id;
            //get recipient company id
            if($sentDocket->first()->recipientInfo){
                foreach($sentDocket->first()->recipientInfo as $recipient):
                    $companyData = $this->companyRepository->getDataWhere([["user_id",$recipient->user_id]])->first();
                    if($companyData != null){
                        $validCompanyId[]   =   $companyData->id;
                    }else{
                        $validCompanyId[]   =   @$this->employeeRepository->getDataWhere([['user_id',$recipient->user_id]])->first()->company_id;
                    }
                endforeach;
            }

            if(in_array($companyId,$validCompanyId)){
                $sentDocketValueQuery    =  $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$id]])
                            ->with('docketFieldInfo','sentDocketImageValue','sentDocket','sentDocketUnitRateValue')->get();
                $sentDocketValue    = array();
                foreach ($sentDocketValueQuery as $row){
                    if((!$row->docketFieldInfo->is_hidden && $row->sentDocket->sender_company_id!=$companyId) || $row->sentDocket->sender_company_id==$companyId):
                        $subFiled   =   [];
                        $value = '';
                        if($row->docketFieldInfo->docket_field_category_id==7):
                            foreach($row->sentDocketUnitRateValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'label' => $subFiledRow->docketUnitRateInfo->label,
                                    'value' => $subFiledRow->value);
                            endforeach;
                            $value = $row->value;
                        elseif($row->docketFieldInfo->docket_field_category_id==9):
                            foreach($row->sentDocketImageValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'value' => AmazoneBucket::url() . $subFiledRow->value);
                            endforeach;
                            $value = AmazoneBucket::url() . $row->value;
                        elseif($row->docketFieldInfo->docket_field_category_id==14):
                            foreach($row->sentDocketImageValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'value' => AmazoneBucket::url() . $subFiledRow->value);
                            endforeach;
                            $value = AmazoneBucket::url() . $row->value;
                        elseif($row->docketFieldInfo->docket_field_category_id==15):
                            foreach($row->sentDocketAttachment as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'name'=>$subFiledRow->document_name,
                                    'url' => AmazoneBucket::url() . $subFiledRow->url);
                            endforeach;
                            $value = AmazoneBucket::url() . $row->url;
                        elseif($row->docketFieldInfo->docket_field_category_id==5):
                            foreach($row->sentDocketImageValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'value' => AmazoneBucket::url() . $subFiledRow->value);
                            endforeach;
                            $value = AmazoneBucket::url() . $row->value;
                        elseif($row->docketFieldInfo->docket_field_category_id==6):
                            if ($row->value== "N/a"){
                                $value = $row->value;
                            }else{
                                $value = Carbon::parse($row->value)->format('d-M-Y');
                            }
                        elseif ($row->docketFieldInfo->docket_field_category_id==13):
                            $value = $row->value;
                        else:
                            $value = $row->value;
                        endif;
                        $sentDocketValue[] = new DocketCategoryResource($row,$row->label,$value,$subFiled);

                        if($row->docketFieldInfo->docket_field_category_id==13){
                            $footers = $sentDocketValue;
                        }
                        unset($subFiled);
                    endif;
                }
                if(@$footers){
                    $sentDocketValue[] =   $footers;
                }
                //approval text
                $sentDocketRecipientApprovalData = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$id]]);
                $totalRecipientApprovalsQuery   =   $sentDocketRecipientApprovalData->get();
                $totalRecipientApprovals    =   $totalRecipientApprovalsQuery->count();
                $totalRecipientApproved     =   $sentDocketRecipientApprovalData->where('status',1)->count();

                $rejectRecipent = array();
                $approvedUsers   =   array();
                $nonApprovedUsers   =   array();
                $canReject = 0;
                $isReject = 0;
                $isApproved = 0;
                foreach ($totalRecipientApprovalsQuery as $recipientApproval){
                    if ($recipientApproval->status == 3){
                        $sentDocketRejectData = $this->sentDocketRejectRepository->getDataWhere([['sent_docket_id',$recipientApproval->sent_docket_id],['user_id',$recipientApproval->user_id]])->first();
                        $rejectRecipent[] = array(
                            'id'=>$recipientApproval->user_id,
                            'user_name'=>$recipientApproval->userInfo->first_name." ".$recipientApproval->userInfo->last_name,
                            'message'=> $sentDocketRejectData->explanation,
                            'time' => Carbon::parse($sentDocketRejectData->updated_at)->format('d/m/Y h:i A')." AEDT",
                        );
                        $isReject = 1;
                    }else{
                        if($recipientApproval->status==1) {
                            $approvedUsers[] = array('id' => $recipientApproval->userInfo->id,
                                'userName' => $recipientApproval->userInfo->first_name . " " . $recipientApproval->userInfo->last_name,
                                'time' => Carbon::parse($recipientApproval->updated_at)->format('d/m/Y h:i A')." AEDT");
                        }else{
                            $nonApprovedUsers[] = array('id' => $recipientApproval->userInfo->id,
                                'userName' => $recipientApproval->userInfo->first_name . " " . $recipientApproval->userInfo->last_name);
                        }
                        //canReject Status
                        if ($recipientApproval->user_id == $authUserId){
                            if ($recipientApproval->status == 0){
                                $canReject = 1;
                            }else{
                                $canReject = 0;
                            }
                        }
                    }
                }

                $recipentId = array();
                foreach ($sentDocket->first()->sentDocketRecipientApproval as $itesm){
                    $recipentId[]= $itesm->userInfo->id;
                }
                //check is approved
                if ($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',  $sentDocket->first()->id],['user_id', $authUserId],['status', 1]])->count() == 1) {
                    $isApproved = 1;
                }
                $docketStatus = new DocketDetailByIdResource($sentDocket->first(),'docket',$totalRecipientApproved, $totalRecipientApprovals, $authUserId, $recipentId, 
                $canReject,$isReject, $isApproved, $approvedUsers, $rejectRecipent, $nonApprovedUsers);
                // $docketStatus   =   array(
                //     'receivedTime'=> Carbon::parse($sentDocket->first()->created_at)->format('d/m/Y h:i A')." AEDT",
                //     'status'=> $totalRecipientApproved."/".$totalRecipientApprovals,
                //     'docketStatus'=> ($sentDocket->first()->user_id == $authUserId)?"sent":"received",
                //     'docket_approval_type'=>$sentDocket->first()->docketApprovalType,
                //     'receiver_id'=>$recipentId,
                //     'isCancelled'=>$sentDocket->first()->is_cancel,
                //     'can_reject'=>$canReject,
                //     'isRejected'=>$isReject,
                //     'isApproved'=>$isApproved,
                //     'approvedUser' => $approvedUsers,
                //     'reject_user'=>$rejectRecipent,
                //     'nonApprovedUser' => $nonApprovedUsers);

                $userNotificationQuery  = $this->userNotificationRepository->getDataWhere([['type',3],['receiver_user_id',$authUserId],['key',$id]]);
                if($userNotificationQuery->count()>0){
                    $userNotificationQuery->update(['status'=>1]);
                }

                $jsonResponse =  ['docketStatus' => $docketStatus, 'docketsValue' => $sentDocketValue,'webView'=>$webView];

                if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$id],['user_id',$authUserId]])->count()==1){
                    $jsonResponse["docketApprovalType"] = $sentDocket->first()->docketApprovalType;
                }
                return response()->json($jsonResponse,200);
            }
            else {
                return response()->json(['message' => MessageDisplay::UnAuthorized],500);
            }
        else:
            return response()->json(['message' => MessageDisplay::DocketNotFound],500);
        endif;
    }

    public function getEmailDocketDetailsByIdWebView($request,$id){
        $sentDocket     =  $this->emailSentDocketRepository->getDataWhere([['id',$id]])->with('recipientInfo')->first();
        $recipientIds   =   $sentDocket->recipientInfo->pluck('email_user_id');
        $companyEmployeeQuery   =   $this->employeeRepository->getDataWhereIn('user_id',$recipientIds)->pluck('company_id');
        $empCompany    =  $this->companyRepository->getDataWhereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
        $adminCompanyQuery   =  $this->companyRepository->getDataWhereIn('user_id',$recipientIds)->pluck('id')->toArray();
        $company    = $this->companyRepository->getDataWhereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
        $authCompanyId = auth()->user()->companyInfo->id;
        if ($sentDocket->theme_document_id == 0){
            $companyName = array();
            foreach ($company as $row){
                $companyName[] = $row->receiver_company_name;
            }
            $receiver_detail = array();
            foreach ($sentDocket->recipientInfo as $row){
                $receiver_detail[] =   $row->emailUserInfo->email;

            }
            $company_name =implode(", ", $companyName);
            $employee_name =implode(", ", $receiver_detail);
            $docketTimer = $this->sentDocketTimerAttachmentRepository->getDataWhere([['sent_docket_id',$id],['type',2]])->get();
            if($sentDocket->company_id == $authCompanyId){
                $docketFields   = $this->emailSentDocketValueRepository->getDataWhere([['email_sent_docket_id',$sentDocket->id]])->get();
                return (array( 'docket' => view('dashboard.company.docketManager.emailPreview',compact('sentDocket','docketFields','company','docketTimer','company_name','employee_name'))->render()));
            }else {
                return response()->json(['message'=>MessageDisplay::InvalidAction],500);
            }
        }else{
            $companyName = array();
            foreach ($company as $row){
                $companyName[] = $row->receiver_company_name;
            }
            $receiver_detail = array();
            foreach ($sentDocket->recipientInfo as $row){
                $receiver_detail[] =   $row->emailUserInfo->email;
            }
            $company_name =implode(", ", $companyName);
            $employee_name =implode(", ", $receiver_detail);
            $docketTimer = $this->sentDocketTimerAttachmentRepository->getDataWhere([['sent_docket_id',$id],['type',2]])->get();
            $theme = $this->documentThemeRepository->getDataWhere([['id', $sentDocket->theme_document_id]])->first();
            if($sentDocket->company_id == $authCompanyId){
                $docketFields   = $this->emailSentDocketValueRepository->getDataWhere([['email_sent_docket_id',$sentDocket->id]])->get();
                return (array( 'docket' => view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentDocket','docketFields','company','docketTimer','company_name','employee_name'))->render()));
            }else {
                return response()->json(['message'=>MessageDisplay::InvalidAction],500);
            }
        }
    }

    public function getEmailDocketDetailsById(Request $request, $id){
        $sentDocket     = $this->emailSentDocketRepository->getDataWhere([['id',$id]]);
        $webView = $this->getEmailDocketDetailsByIdWebView($request, $id);
        if($sentDocket->count()==1):
            //check docket associated with user or not
            $companyId  =  auth()->user()->companyInfo->id;
            if($this->emailSentDocketRepository->getDataWhere([['id',$id],['company_id',$companyId]])->count()>0){
                $sentDocketValueQuery    =  $this->emailSentDocketValueRepository->getDataWhere([['email_sent_docket_id',$id]])->get();
                $sentDocketValue    = array();
                foreach ($sentDocketValueQuery as $row){
                    $subFiled   =   [];
                    $value = '';
                    if($row->docketFieldInfo->docket_field_category_id==7):
                        foreach($row->sentDocketUnitRateValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'label' => $subFiledRow->docketUnitRateInfo->label,
                                'value' => $subFiledRow->value);
                        endforeach;
                        $value = $row->value;
                    elseif($row->docketFieldInfo->docket_field_category_id==9):

                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;
                        $value = AmazoneBucket::url() . $row->value;
                    elseif($row->docketFieldInfo->docket_field_category_id==14):

                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;
                        $value = AmazoneBucket::url() . $row->value;
                    elseif($row->docketFieldInfo->docket_field_category_id==5):
                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;
                        $value = AmazoneBucket::url() . $row->value;
                    elseif ($row->docketFieldInfo->docket_field_category_id==13):
                        $footerDetails = DocketFieldFooter::select('id', 'value')->where("field_id", $row->docket_field_id)->orderBy('id', 'asc')->first();
                        $value = @$footerDetails->value;
                    elseif($row->docketFieldInfo->docket_field_category_id==15):
                        foreach($row->sentEmailAttachment as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'name'=>$subFiledRow->name,
                                'url' => AmazoneBucket::url() . $subFiledRow->url);
                        endforeach;
                        $value = AmazoneBucket::url() . $row->url;
                    else:
                        $value = $row->url;
                    endif;
                    $sentDocketValue[] = new DocketCategoryResource($row,$row->label,$value,$subFiled);
                    
                    unset($subFiled);
                    if($row->docketFieldInfo->docket_field_category_id==13){
                        $footers = $sentDocketValue;
                    }
                }
                if(@$footers){
                    $sentDocketValue[] =   $footers;
                }

                //approval text
                $emailSentDocketRecipientData = $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id',$id],['approval',1]]);
                $totalRecipientApprovalsQuery   =  $emailSentDocketRecipientData->get();
                $totalRecipientApprovals    =   $totalRecipientApprovalsQuery->count();
                $totalRecipientApproved     =  $emailSentDocketRecipientData->where('status',1)->count();

                $approvedUsers   =   array();
                $nonApprovedUsers   =   array();
                foreach ($totalRecipientApprovalsQuery as $recipientApproval){
                    if($recipientApproval->status==1) {
                        $approvedUsers[] = array('id' => $recipientApproval->emailUserInfo->id,
                            'userName' => $recipientApproval->emailUserInfo->email,
                            'time' => Carbon::parse($recipientApproval->updated_at)->format('d/m/Y h:i A')." AEDT");
                    }else{
                        $nonApprovedUsers[] = array('id' => $recipientApproval->emailUserInfo->id,
                            'userName' => $recipientApproval->emailUserInfo->email);
                    }
                }
                $docketStatus = new DocketDetailByIdResource($sentDocket->first(),'emailDocket',$totalRecipientApproved, $totalRecipientApprovals, null, null, 
                                        null,null, null, $approvedUsers, null, $nonApprovedUsers);
                // $docketStatus   =   array('receivedTime'=> Carbon::parse($sentDocket->first()->created_at)->format('d/m/Y h:i A')." AEDT",
                //     'status'=> $totalRecipientApproved."/".$totalRecipientApprovals,
                //     'docketStatus'=> ($sentDocket->first()->status==1)?"Approved":"Sent",
                //     'approvedUser' => $approvedUsers,
                //     'docket_approval_type' =>$sentDocket->first()->docketApprovalType,
                //     'nonApprovedUser' => $nonApprovedUsers);

                $userNotificationQuery  = $this->userNotificationRepository->getDataWhere([['type',5],['receiver_user_id',auth()->user()->id],['key',$id]]);
                if($userNotificationQuery->count()>0){
                    if($userNotificationQuery->first()->status==0){
                        $userNotificationQuery->update(['status'=>1]);
                    }
                }
                return response()->json(['docketStatus' => $docketStatus,'docketsValue' => $sentDocketValue,'docketApprovalType'=>$sentDocket->first()->docketApprovalType,'webView'=>$webView],200);
            } else {
                return response()->json(['message' => MessageDisplay::UnAuthorized],500);
            }
        else:
            return response()->json(['message' => MessageDisplay::DocketNotFound],500);
        endif;

    }

    public function approveDocketById(Request $request){
        $authUserId = auth()->user()->id;
        $sentDocket     = $this->sentDocketsRepository->getDataWhere([['id',$request->sentDocketId]]);
        if($sentDocket->count() == 1):
            $sentDocketRecipientApprovalQuery    = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$request->sentDocketId],['user_id',$authUserId],['status',0]]);
            if($sentDocketRecipientApprovalQuery->count()==1){
                if ($sentDocket->first()->docketApprovalType == 1){
                    $validator  =   Validator::make(Input::all(),['sentDocketId' =>     'required','name' =>     'required','signature' =>     'required']);
                    if ($validator->fails()):
                        foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
                        return response()->json(array('status' => false,'message' => $errors));
                    else:
                        $sentDocketRecipientApprovalRequest = new Request();
                        $sentDocketRecipientApprovalRequest['sent_docket_recipient_approval_id'] = $sentDocketRecipientApprovalQuery->first()->id;
                        $sentDocketRecipientApprovalRequest['status']     =   1;
                        $sentDocketRecipientApprovalRequest['approval_time'] =Carbon::now()->toDateTimeString();
                        $sentDocketRecipientApprovalRequest['name'] =$request->name;
                        $signature              =   Input::file('signature');
                        if($request->hasFile('signature')) {
                            if ($signature->isValid()) {
                                // $ext = $signature->getClientOriginalExtension();
                                // $filename = basename($request->file('signature')->getClientOriginalName(), '.' . $request->file('signature')->getClientOriginalExtension()) . time() . "." . $ext;
                                $dest = 'xfiles/docket/images';
                                // $signature->move($dest, $filename);
                                // $path = $dest . '/' . $filename;

                                $path = FunctionUtils::imageUpload($dest,$signature);

                                $sentDocketRecipientApprovalRequest['signature']=$path;
                            }
                        }
                        $this->sentDocketRecipientApprovalRepository->insertAndUpdate($sentDocketRecipientApprovalRequest);
                    endif;
                }else{
                    $validator  =   Validator::make(Input::all(),['sentDocketId' =>     'required']);
                    if ($validator->fails()):
                        foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
                        return response()->json(array('status' => false,'message' => $errors));
                    else:
                        $sentDocketRecipientApprovalRequest = new Request();
                        $sentDocketRecipientApprovalRequest['sent_docket_recipient_approval_id'] = $sentDocketRecipientApprovalQuery->first()->id;
                        $sentDocketRecipientApprovalRequest['status']     =   1;
                        $sentDocketRecipientApprovalRequest['approval_time'] =Carbon::now()->toDateTimeString();
                        $this->sentDocketRecipientApprovalRepository->insertAndUpdate($sentDocketRecipientApprovalRequest);
                    endif;

                }

                $sentDocketSenderInfo    =   $this->userRepository->getDataWhere([['id',$sentDocket->first()->user_id]])->first();
                $sentDocketReceiverInfo    =   auth()->user();
                if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$request->sentDocketId],['status',0]])->count()==0){
                    $sentDocketUpdateRequest =  new Request();
                    $sentDocketUpdateRequest['send_docket_id']   =   $request->sentDocketId;
                    $sentDocketUpdateRequest['status']   =    1;
                    $sentDocketUpdate = $this->sentDocketsRepository->insertAndUpdate($sentDocketUpdateRequest);

                    if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$request->sentDocketId]])->count()>1) {
                        if ($sentDocketSenderInfo->device_type == 2) {
                            $this->firebaseApi->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
                        }
                        if ($sentDocketSenderInfo->device_type == 1) {
                            $this->firebaseApi->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
                        }
                    }
                }

                if($sentDocketSenderInfo->device_type==2){
                    $this->firebaseApi->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.");
                }
                if($sentDocketSenderInfo->device_type==1){
                    $this->firebaseApi->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.");
                }

                $userNotificationRequest   =    new Request();
                $userNotificationRequest['sender_user_id']   =    $authUserId;
                $userNotificationRequest['receiver_user_id'] = $this->sentDocketsRepository->getDataWhere([['id',$request->sentDocketId]])->first()->user_id;
                $userNotificationRequest['type']     =   3;
                $userNotificationRequest['title']    =   'Docket Approved';
                $userNotificationRequest['message']  =   $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.";
                $userNotificationRequest['key']      =   $request->sentDocketId;
                $userNotificationRequest['status']   =   0;
                $this->userNotificationRepository->insertAndUpdate($userNotificationRequest);

                return response()->json(['message' => MessageDisplay::DocketApproved],200);
            } else {
                return response()->json(['message' => MessageDisplay::DocketAlreadyApproved],500);
            }
        else:
            return response()->json(['message' => MessageDisplay::DocketNotFound],500);
        endif;
    }

    public function draftImageSave($request){
        $number = 0;
        $date = Carbon::now()->format('d-M-Y');
        $response_array = array();
        foreach($request->all() as $key => $imgs){
            $imagePram = intval(explode("-",$key)[1]);
            if (explode("-",$key)[2] == "row"){
                $imageRowId = intval(explode("-",$key)[3]);
                $imageGridId = intval(explode("-",$key)[5]);
                $arrayImage = array();
                $response = $this->draftImageSaveLoop($imgs,$date,$number);
                $number = $response['number'];
                $arrayImage = $response['arrayImage'];
                $response_array[] = array("id" => $imagePram,"row"=>$imageRowId,"sub_field_id"=>$imageGridId, "images" => $arrayImage);
            } elseif (explode("-",$key)[2] == "yesNo"){
                $imageYesNoId = intval(explode("-",$key)[3]);
                $arrayImage = array();
                $response = $this->draftImageSaveLoop($imgs,$date,$number);
                $number = $response['number'];
                $arrayImage = $response['arrayImage'];
                $response_array[] = array("id" => $imagePram,"sub_field_id"=>$imageYesNoId, "images" => $arrayImage);
            }else{
                $arrayImage = array();
                $response = $this->draftImageSaveLoop($imgs,$date,$number);
                $number = $response['number'];
                $arrayImage = $response['arrayImage'];
                $response_array[] = array("id" => $imagePram, "images" => $arrayImage);
            }
        }
        return response()->json($response_array);
    }

    public function saveDocketDraft($request){
        $docketDraftRequest = new Request();
        $docketDraftRequest['user_id'] = auth()->user()->id;
        $docketDraftRequest['docket_id'] = $request->docket_id;
        $docketDraftRequest['value'] = $request->value;
        $docketDraft = $this->docketDraftRepository->insertAndUpdate($docketDraftRequest);
        return $docketDraft;
    }

    public function updateDocketDraft($request){
        try {
            $docketDraftRequest = new Request();
            $docketDraftRequest['docket_draft_id'] = $request->draft_id;
            $docketDraftRequest['value'] = $request->value;
            $this->docketDraftRepository->insertAndUpdate($docketDraftRequest);
            return response()->json(['message' => MessageDisplay::DocketUpdated],200);
        } catch (\Exception $ex) {
            return response()->json(['message' => MessageDisplay::InvalidData],500);
        }
    }

    public function getDocketDraftList(){
        $docketDraft = $this->docketDraftRepository->getDataWhere([['user_id',auth()->user()->id]])->get();
        $docketDraftList = array();
        if (count($docketDraft) > 0){
            foreach ($docketDraft as $row){
                $docketDraftList[] = array(
                    'draft_id' => $row->id,
                    'user_id' => $row->user_id,
                    'docket_id' => $row->docket_id,
                    'value' => $row->value,
                );
            }
        }
        return $docketDraftList;
    }

    public function v1getDocketTemplateDetailsById($id){
        ini_set('memory_limit','256M');
        set_time_limit(0);
        $userCompany = auth()->user()->companyInfo;
        $docket = $this->docketRepository->getDataWhere([['id',$id]])->first();
        if($docket != null){
            $docketFieldQuery    = $this->docketFieldRepository->getDataWhere([['docket_id',$docket->id]])->with('prefillerEcowise')->orderBy('order','asc')->get();
            $docketFields   =   array();
            foreach ($docketFieldQuery as $row){
                $subField   =   array();
                $data['row'] = $row;
                if($row->docket_field_category_id == 7) {
                    $data['repository'] = $this->docketUnitRateRepository;
                    $data['whereArray'] = [["docket_field_id", $row->id]];
                    $data['select'] = ['id', 'type', 'label'];
                    $orderBy[0] = 'type';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $docketFields[] = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id == 13) {
                    $data['repository'] = $this->docketFieldFooterRepository;
                    $data['whereArray'] = [["field_id", $row->id]];
                    $data['select'] = ['id', 'value'];
                    $orderBy[0] = 'id';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $footers = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id == 20) {
                    $data['repository'] = $this->docketManualTimerRepository;
                    $data['whereArray'] = [["docket_field_id", $row->id]];
                    $data['select'] = ['id', 'type', 'label'];
                    $orderBy[0] = 'type';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $data['breakSubField'] = $this->docketManualTimerBreakRepository->getDataWhere([["docket_field_id", $row->id]])->select('id','type', 'label','explanation')->orderBy('type', 'asc')->get();
                    $docketFields[] = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id == 3) {
                    $docketFieldNumber = $this->docketFieldNumberRepository->getDataWhere([["docket_field_id", $row->id]])->select('min', 'max','tolerance')->first();
                    if ($docketFieldNumber == null){
                        $docketFieldNumbers = array(
                            'min' => null,
                            'max' => null,
                            'tolerance' => null,
                        );
                    }else{
                        $docketFieldNumbers= $docketFieldNumber;
                    }

                    if ($row->is_dependent == 1){
                        $docketPrefillerValue = $this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$row->docket_prefiller_id]])->get();

                        $data['docketPreFiller'] = $docketPrefillerValue;
                        $data['docketFieldNumbers'] = $docketFieldNumbers;
                        $data['canAddChildCheck'] = true;
                        $data['repository'] = $this->docketPrefillerValueRepository;
                        $data['parentArray'] = 'getDocketPrefiller';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }else if($row->is_dependent == 2){
                        $data['canAddChild'] = false;
                        $data['row'] = $row;
                        $data['defaultPrefillerValue'] = '';
                        $data['subField'] = $subField;
                        $docketFields[] = $this->docketPreFillerRowDependent2($data);
                    }else{
                        $data['docketPreFiller'] = $row->docketPreFiller;
                        $data['docketFieldNumbers'] = $docketFieldNumbers;
                        $data['canAddChildCheck'] = false;
                        $data['repository'] = $this->docketFiledPreFillerRepository;
                        $data['parentArray'] = 'getNormalParentData';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }
                }
                elseif ($row->docket_field_category_id == 18) {
                    $subFields = array();
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 1){
                            $subFields[] = $this->docketYesNoFieldLoop($subRow);
                        }
                    endforeach;
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 0){
                            $subFields[] = $this->docketYesNoFieldLoop($subRow);
                        }
                    endforeach;
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 2){
                            $subFields[] = $this->docketYesNoFieldLoop($subRow);
                        }
                    endforeach;
                    $docketFields[] = new DocketTempleteDetailDocketFieldResource($row,$subFields);
                    // dump(new DocketTempleteDetailDocketFieldResource($row,$subFields));
                    // $a = array('id' => $row->id,
                    //     'docket_field_category_id' => $row->docket_field_category_id,
                    //     'docket_field_category' => $row->fieldCategoryInfo->title,
                    //     'label' => $row->label,
                    //     'order' => $row->order,
                    //     'required' => $row->required,
                    //     'subField' => $subFields,
                    //     'yesNoSubField' => $subFields);
                    //     dd($a);
                    // $docketFields[] = array('id' => $row->id,
                    //     'docket_field_category_id' => $row->docket_field_category_id,
                    //     'docket_field_category' => $row->fieldCategoryInfo->title,
                    //     'label' => $row->label,
                    //     'order' => $row->order,
                    //     'required' => $row->required,
                    //     'subField' => $subFields,
                    //     'yesNoSubField' => $subFields);

                }
                elseif ($row->docket_field_category_id == 15) {
                    $subFields= array();
                    foreach($row->docketAttached as $subRow):
                        $subFields[]   =     array(
                            'id' => $subRow->id,
                            'name'=>$subRow->name,
                            'url' => AmazoneBucket::url() . $subRow->url);
                    endforeach;
                    $docketFields[] = new DocketTempleteDetailDocketFieldResource($row,$subFields);
                    // $docketFields[] = array('id' => $row->id,
                    //     'docket_field_category_id' => $row->docket_field_category_id,
                    //     'docket_field_category' => $row->fieldCategoryInfo->title,
                    //     'label' => $row->label,
                    //     'order' => $row->order,
                    //     'required'=>$row->required,
                    //     'subField' => $subFields,
                    //     'documentSubField' => $subFields);

                }
                elseif ($row->docket_field_category_id == 9) {
                    if ($row->is_dependent == 1){
                        $docketPrefillerValue = $this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$row->docket_prefiller_id]])->get();
                        $data['docketPreFiller'] = $docketPrefillerValue;
                        $data['canAddChildCheck'] = true;
                        $data['repository'] = $this->docketPrefillerValueRepository;
                        $data['parentArray'] = 'getDocketPrefiller';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }else if($row->is_dependent == 2){
                        $data['canAddChild'] = false;
                        $data['row'] = $row;
                        $data['defaultPrefillerValue'] = '';
                        $data['subField'] = $subField;
                        $docketFields[] = $this->docketPreFillerRowDependent2($data);
                    }else{
                        $data['docketPreFiller'] = $row->docketPreFiller;
                        $data['repository'] = $this->docketFiledPreFillerRepository;
                        $data['parentArray'] = 'getNormalParentDatas';
                        $data['canAddChildCheck'] = false;
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }
                }
                elseif ($row->docket_field_category_id == 6) {
                    $data['row'] = $row;
                    $data['docketPreFiller'] = $row->docketPreFiller;
                    $data['canAddChildCheck'] = false;
                    $data['repository'] = $this->docketFiledPreFillerRepository;
                    $data['parentArray'] = 'getNormalParentData';
                    $docketFields[] = $this->docketPreFillerRowIndependent($data);
                }
                elseif ($row->docket_field_category_id == 24) {
                    $data['repository'] = $this->docketTallyableUnitRateRepository;
                    $data['whereArray'] = [["docket_field_id", $row->id]];
                    $data['select'] = ['id', 'type', 'label'];
                    $orderBy[0] = 'type';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $docketFields[] = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id == 22) {
                    $modularField  = array();
                    $sumableStatus = false;
                    $canAddChild = true;
                    $isEmailedSubject = false;
                    foreach ($row->girdFields as $gridField){
                        if($gridField->is_emailed_subject == 1){
                            $isEmailedSubject = true;
                        }

                        if ($gridField->docket_field_category_id == 3){
                            if ($gridField->sumable == 1){
                                $sumableStatus = true;
                            }
                        }
                        if ($gridField->is_dependent == 1){
                            $docketPrefillerValue = $this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$gridField->docket_prefiller_id]])->get();

                            $data['docketPreFiller'] = $docketPrefillerValue;
                            $data['gridField'] = $gridField;
                            $data['canAddChildCheck'] = true;
                            $data['repository'] = $this->docketPrefillerValueRepository;
                            $data['parentArray'] = 'getDocketPrefiller';
                            $data['canAddChildCheck'] = true;
                            $docketPreFillerRowIndependent = $this->docketPreFillerRowIndependent($data);
                        }elseif($gridField->is_dependent == 2){

                            if ($gridField->auto_field == 1){
                                $data['canAddChild'] = false;
                                $data['row'] = $gridField;
                                $data['defaultPrefillerValue'] = '';
                                $data['subField'] = $subField;
                                $docketPreFillerRowIndependent = $this->docketPreFillerRowDependent2Autofield($data);
                            }else{
                                $data['canAddChild'] = false;
                                $data['row'] = $gridField;
                                $data['defaultPrefillerValue'] = '';
                                $data['subField'] = $subField;
                                $docketPreFillerRowIndependent = $this->docketPreFillerRowDependent2($data);
                            }

                        }else{
                            $data['docketPreFiller'] = $gridField->gridFieldPreFiller;
                            $data['gridField'] = $gridField;
                            $data['canAddChildCheck'] = true;
                            $data['repository'] = $this->docketGridPrefillerRepository;
                            $data['parentArray'] = 'getParentData';
                            $data['canAddChildCheck'] = true;
                            $docketPreFillerRowIndependent = $this->docketPreFillerRowIndependent($data);
                        }
                        if ($gridField->gridFieldFormula != null){
                            $formulaValue = unserialize($gridField->gridFieldFormula->formula);
                            $formulaArray = array();
                            foreach ($formulaValue as $formulaValues){
                                if (is_numeric($formulaValues)){
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "number",
                                    );
                                }elseif (preg_match("/TDiff/i", $formulaValues)) {
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "function"
                                    );
                                }
                                elseif (preg_match("/cell/i", $formulaValues)) {
                                    $formulaArray[] = array(
                                        "value" => ltrim($formulaValues, 'cell'),
                                        "type" => "cell"
                                    );
                                }
                                else{
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "operator"
                                    );
                                }
                            }
                        }else{
                            $formulaArray = array();
                        }
                        $gridManualTimer  = array();
                        if($gridField->docket_field_category_id == 20){
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 1,
                                'label' => "From"
                            );
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 2,
                                'label' => "To"
                            );
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 3,
                                'label' => "Total Break",
                                'explanation'=> 0
                            );
                        }
                        // $gridData['id'] = $gridField->id;
                        // $gridData['docket_field_id'] = $gridField->docket_field_id;
                        // $gridData['docket_field_category_id'] = $gridField->docketFieldCategory->id;
                        // $gridData['docket_field_category_label'] = $gridField->docketFieldCategory->title;
                        // $gridData['label'] = $gridField->label;
                        // $gridData['order'] = $gridField->order;
                        // $gridData['is_emailed_subject'] = $gridField->is_emailed_subject;
                        // $gridData['required'] = $gridField->required;
                        // $gridData['prefiller_data'] =array('autoPrefiller'=>$gridField->auto_field,'isDependent'=>$gridField->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$docketPreFillerRowIndependent['datas']) ;
                        // $gridData['default_value'] = ($docketPreFillerRowIndependent['defaultPrefillerValue']=="")? "" : implode(", ",$docketPreFillerRowIndependent['prefillerArray']);
                        // $gridData['subField'] = $gridManualTimer;
                        // $gridData['manualTimerSubField'] = $gridManualTimer;
                        // $gridData['formula']=  @$formulaArray;
                        // $gridData['sumable'] = ($gridField->sumable== 1)? true : false ;
                        // $gridData['send_copy_docket']=  $gridField->send_copy_docket;
                        $gridData = new DocketTempleteDetailDocketGridFieldResource($gridField,$canAddChild,$docketPreFillerRowIndependent,$gridManualTimer,$formulaArray,'v1DocketTemplete');
                        array_push($modularField, $gridData);
                    }
                    $docketFields[] = new DocketTempleteDetailDocketFieldResource($row,$subField,$isEmailedSubject,$modularField,$sumableStatus);
                    // $docketFields[] = array('id' => $row->id,
                    //     'docket_field_category_id' => $row->docket_field_category_id,
                    //     'docket_field_category' => $row->fieldCategoryInfo->title,
                    //     'label' => $row->label,
                    //     'time_required'=>(@$row->docketFieldDateOption->time == null) ? 0: @$row->docketFieldDateOption->time,
                    //     'order' => $row->order,
                    //     'required'=>$row->required,
                    //     'is_emailed_subject'=>($isEmailedSubject == true) ? 1: 0,
                    //     'modularGrid' => $modularField,
                    //     'sumable'=> $sumableStatus,
                    //     'subField'  => $subField);

                }
                elseif ($row->docket_field_category_id == 28) {
                    $data['repository'] = $this->folderRepository;
                    $data['whereArray'] = [['company_id',$userCompany->id],['type',0]];
                    $data['select'] = [];
                    $orderBy[0] = 'name';
                    $orderBy[1] = 'asc';
                    $data['orderBy'] = $orderBy;
                    $data['templateFolderAssign'] = $this->templateAssignFolderRepository->getDataWhere([['template_id',$id]])->get()->first();
                    $docketFields[] = $this->commonCall($data);
                }
                elseif ($row->docket_field_category_id != 30) {
                    if ($row->is_dependent == 1){
                        $docketPrefillerValue = $this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$row->docket_prefiller_id]])->get();

                        $data['docketPreFiller'] = $docketPrefillerValue;
                        $data['canAddChildCheck'] = true;
                        $data['repository'] = $this->docketPrefillerValueRepository;
                        $data['parentArray'] = 'getDocketPrefiller';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }elseif($row->is_dependent == 2){
                        $data['canAddChild'] = false;
                        $data['row'] = $row;
                        $data['defaultPrefillerValue'] = '';
                        $data['subField'] = $subField;
                        $docketFields[] = $this->docketPreFillerRowDependent2($data);
                    }else{
                        $data['docketPreFiller'] = $row->docketPreFiller;
                        $data['canAddChildCheck'] = false;
                        $data['repository'] = $this->docketFiledPreFillerRepository;
                        $data['parentArray'] = 'getNormalParentData';
                        $docketFields[] = $this->docketPreFillerRowIndependent($data);
                    }
                }
            }

            if(@$footers){
                $docketFields[] =   $footers;
            }

            $isDocketNumber = false;
            $company = $userCompany;
            if($company->number_system == 1){
                $isDocketNumber = false;
            }else{
                if($docket->is_docket_number == 1){
                    $isDocketNumber = true;
                }else{
                    $isDocketNumber = false;
                }
            }

            $template =   array(
                'id'=> $docket->id,
                'title'=>$docket->title,
                'subTitle'=>$docket->subTitle,
                'invoiceable'=>$docket->invoiceable,
                'timer_attachement'=>$docket->timer_attachement,
                'docket_field'=> $docketFields,
                'isDocketNumber'=>  $isDocketNumber,
            );

            if($row->docketInfo->defaultRecipient){
                $rt_user_receivers = array();
                $email_user_receivers = array();
                foreach ($row->docketInfo->defaultRecipient as $defaultRecipients){
                    if(@$defaultRecipients->user_type== 1){
                        $employeeData = $this->employeeRepository->getDataWhere([['user_id', $defaultRecipients->userInfo->id]])->first();
                        if($employeeData != null):
                            $companyId = $employeeData->company_id;
                        else :
                            $companyId   = $this->companyRepository->getDataWhere([['user_id', $defaultRecipients->userInfo->id]])->first()->id;
                        endif;
                        $companyData = $this->companyRepository->getDataWhere([['id',$companyId]])->first();
                        $rt_user_receivers[] = array(
                            'user_id'=> $defaultRecipients->userInfo->id,
                            'company_id'=>$companyId,
                            'company_name'=> $companyData->name,
                            'company_abn'=> $companyData->abn,
                            'first_name'=>$defaultRecipients->userInfo->first_name,
                            'last_name'=>$defaultRecipients->userInfo->last_name,
                            'image'=>AmazoneBucket::url() . $defaultRecipients->userInfo->image
                        );
                    }
                    if(@$defaultRecipients->user_type== 2){
                        if (@$defaultRecipients->emailUser->emailClient->email_user_id == @$defaultRecipients->emailUser->id){
                            $email_user_receivers[] = array(
                                'email_user_id'=>$defaultRecipients->emailUser->id,
                                'email'=> $defaultRecipients->emailUser->email,
                                'full_name'=>$defaultRecipients->emailUser->emailClient->full_name,
                                'company_name'=>$defaultRecipients->emailUser->emailClient->company_name,
                                'company_address'=>$defaultRecipients->emailUser->emailClient->company_address,
                                'saved'=>true
                            );
                        }else{
                            $email_user_receivers[] = array(
                                'email_user_id'=>$defaultRecipients->emailUser->id,
                                'email'=> $defaultRecipients->emailUser->email,
                                'saved'=>false
                            );
                        }
                    }
                }
            }
            return response()->json(['template'=>$template,'rt_user_receivers'=>$rt_user_receivers,'email_user_receivers'=>$email_user_receivers],200);
        }else{
            return response()->json(["message"=> MessageDisplay::DocketNotFound],500);
        }
    }

    public function v1SaveSentDefaultDockets($request){
        //check if subscription was free count remaining docket left
        $company = auth()->user()->companyInfo;
        if ($company->trial_period == 3) {
            //get last subscription created date
            $subscriptionLogQuery = $this->subscriptionLogRepository->getDataWhere([['company_id', $company->id]]);
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
            $sentDockets = $this->sentDocketsRepository->getDataWhere([['sender_company_id', $company->id]])->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();
            $emailDockets = $this->emailSentDocketRepository->getDataWhere([['company_id', $company->id]])->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();

            $totalMonthDockets = $sentDockets + $emailDockets;

            if ($totalMonthDockets >= 5) {
                return response()->json(['message' => MessageDisplay::SubscriptionUpgrade],500);
            }
        }

        try{
            DB::beginTransaction();
            $sentDocketData = json_decode($request->data, true);
            $docketFieldsQuery = $this->docketFieldRepository->getDataWhere([['docket_id',$sentDocketData['template']['id']]])->orderBy('order', 'asc')->get();
            $templateData = $this->docketRepository->getDataWhere([['id', $sentDocketData['template']['id']]])->first();

            foreach ($docketFieldsQuery as $row) {
                if ($row->required) {
                    $searchData = FunctionUtils::searchForId($row->docket_field_category_id,$row->id, $sentDocketData['docket_data']['docket_field_values']);
                    if ($searchData != null) {
                        if ($searchData['category_id'] == 9) {
                            if (count($searchData['signature_value']) == 0) {
                                return response()->json(['message' => 'The ' . $row->label . ' field is required.'],500);
                            }
                        }
                        if ($searchData['category_id'] == 5 || $searchData['category_id'] == 14) {
                            if (count($searchData['image_value']) == 0) {
                                return response()->json(['message' => 'The ' . $row->label . ' field is required.'],500);
                            }
                        }
                        if ($searchData['category_id'] == 7 || $searchData['category_id'] == 24){
                            if (count($searchData['unit_rate_value']) == 0){
                                return response()->json(['message' => 'The ' . $row->label . ' field is required.'],500);
                            }
                        }
                        if ($searchData['category_id'] == 1 || $searchData['category_id'] == 2 || $searchData['category_id'] == 3 || $searchData['category_id'] == 4 || $searchData['category_id'] == 6  || $searchData['category_id'] == 16  || $searchData['category_id']==20   || $searchData['category_id'] == 25  ||  $searchData['category_id'] == 26||  $searchData['category_id'] == 29) {
                            if ($searchData['value'] == "") {
                                return response()->json(['message' => 'The ' . $row->label . ' field is required.'],500);
                            }
                        }

                        if ($searchData['category_id'] == 20 ){
                            foreach ($row->docketManualTimerBreak as $rowDatas) {
                                if ($rowDatas->explanation == 1) {
                                    if($searchData['manual_timer_value']['breakDuration'] != ""){
                                        if ($searchData['manual_timer_value']['explanation'] == "") {
                                            return response()->json(['message' => 'The ' . $row->label . ' field  Explanation is required.'],500);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($sentDocketData['docket_data']['is_email'] == true || count($sentDocketData['email_user_receivers'])!==0){
                $data = $this->saveEmailDockets($sentDocketData, $templateData);
                if($data['status'] == false){
                    DB::rollback();
                    return response()->json(['message' => $data['data']],200);
                }else{
                    DB::commit();
                    return response()->json(['message' => 'Docket successfully sent to '. $data['data']],200);
                }
            }else if($sentDocketData['docket_data']['is_email'] == false || count($sentDocketData['rt_user_receivers'])!==0){
                $data = $this->saveDockets($sentDocketData,$templateData);
                DB::commit();
                return response()->json(['message' => 'Docket successfully sent to '.$data["data"]],200);
            }
        }catch(\Exception $ex){
            DB::rollback();
            return response()->json(['message' => MessageDisplay::ERROR],500);
        }
    }




    public function commonCall($data){
        $responseData = [];
        $responseData1 = [];
        $subField = $data['repository']->getDataWhere($data['whereArray'])
            ->when($data['select'] != null, function ($query) use ($data){
                $query->select($data['select']);
            })->when(empty($data['orderBy']), function ($query) use ($data){
                $query->orderBy($data['orderBy[0]'],$data['orderBy[1]']);
            })->get();

        if($data['row']->docket_field_category_id == 20){
            $merge= array();
            foreach($subField as $rowDtas){
                $merge[]  = array('id' =>$rowDtas->id,
                    'type'=>$rowDtas->type,
                    'label'=>$rowDtas->label,
                );
            }
            foreach($data['breakSubField'] as $rowDta){
                $merge[]  = array('id' =>$rowDta->id,
                    'type'=>$rowDta->type,
                    'label'=>$rowDta->label,
                    'explanation'=>$rowDta->explanation
                );
            }
            $subField = $merge;
        }else if($data['row']->docket_field_category_id == 28){
            $folderList = array();
            foreach($subField as $subRow):
                $folderList[]   =  array(
                    'id'=> $subRow->id,
                    'name'=> $subRow->name,
                    'root_id'=> intval($subRow->root_id),
                );
            endforeach;
            $folderLists = FunctionUtils::folderList($folderList);
            $subField = array();
            $responseData1 = array('folderList'=>$folderLists,
                'default_value'=> ($data['templateFolderAssign'] == null)? "" : $data['templateFolderAssign']->folder_id);
        }else if($data['row']->docket_field_category_id == 13){
            $responseData1 = array('subFieldFooter' => $subField);
        }

        $responseData = array('id' => $data['row']->id,
            'docket_field_category_id' => $data['row']->docket_field_category_id,
            'docket_field_category' => $data['row']->fieldCategoryInfo->title,
            'label' => $data['row']->label,
            'order' => $data['row']->order,
            'required'=>$data['row']->required,
            'subField' => $subField,
            'subFieldUnitRate' => $subField);
        return array_merge($responseData,$responseData1);
    }

    public function docketYesNoFieldLoop($subRow){
        $subDocket = array();
        foreach ($subRow->yesNoDocketsField as $subRowDocket):
            $subDocket[] = array(
                'id' => $subRowDocket->id,
                'docket_field_category_id' => $subRowDocket->docket_field_category_id,
                'order' => $subRowDocket->order,
                'required' => $subRowDocket->required,
                'label' => $subRowDocket->label,
            );
        endforeach;
        return array(
            'id' => $subRow->id,
            'label' => $subRow->label,
            'type' => $subRow->type,
            'colour' => $subRow->colour,
            'explanation' => $subRow->explanation,
            'docket_field_id' => $subRow->docket_field_id,
            'label_icon' => AmazoneBucket::url() . $subRow->icon_image,
            'label_type' => $subRow->label_type,
            'subDocket' => ($subRow->explanation == 0) ? [] : $subDocket,
        );
    }

    public function docketPreFillerRowIndependent($data){
        $prefiller = array();
        $prefillerArray = array();
        $canAddChild = true;
        $subField = [];
        $isBigData = false;
        $datas = [];
        if($data['row']->docket_field_category_id == 22){
            if ($data['gridField']->auto_field == 1){
                if(count($data['docketPreFiller']) > 10){
                    $isBigData = true;
                }else{
                    foreach($data['docketPreFiller'] as $subRow):
                        $prefiller[]   =  array(
                            'id'=> $subRow->id,
                            'value'=> $subRow->label,
                            'index'=> $subRow->index,
                            'docket_field_id'=>$data['gridField']->docket_field_id,
                            'docket_field_grid_id'=>$data['gridField']->id,
                            'root_id'=> intval($subRow->root_id),
                        );
                    endforeach;
                    $datas = FunctionUtils::buildAutoPrefillerTreeArray($prefiller);
                }
            }
            else{
                if(count($data['docketPreFiller']) > 10){
                    $isBigData = true;
                }else{
                    foreach($data['docketPreFiller'] as $subRow):
                        $prefiller[]   =  array(
                            'id'=> $subRow->id,
                            'value'=> $subRow->label,
                            'root_id'=> intval($subRow->root_id),
                            'index'=> intval($subRow->index),
                        );
                    endforeach;
                    $datas = FunctionUtils::buildTreeArray($prefiller);
                }
            }
        }else{
            if(count($data['docketPreFiller']) > 10){
                $isBigData = true;
            }else{
                foreach($data['docketPreFiller'] as $subRow):
                    $prefiller[]   =  array(
                        'id'=> $subRow->id,
                        'value'=> ($data['row']->is_dependent == 1)?$subRow->label : $subRow->value,
                        'root_id'=> intval($subRow->root_id),
                        'index'=> intval($subRow->index),
                    );
                endforeach;
                $datas = FunctionUtils::buildTreeArray($prefiller);
            }
        }
        if($data['row']->docket_field_category_id == 22){
            $row = $data['gridField'];
        }else{
            $row = $data['row'];
        }
        if($row->default_prefiller_id == null){
            $defaultPrefillerValue = "";
        }else{
            $docketFiledPreFiller = $data['repository']->getDataWhereIn('id',unserialize($row->default_prefiller_id))->get();
            if(count($docketFiledPreFiller) == 0){
                $defaultPrefillerValue = "";
            }else{
                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                $parentPrefillers = $data['repository']->getDataWhereIn('id', $defaultPrefillerValue)->select('root_id')->groupBy('root_id')->get();
                $prefillerArray =    array();
                foreach ($parentPrefillers as $prefiller) {
                    $typeColumn = '';
                    if($data['parentArray'] == 'getNormalParentData'){
                        $typeColumn = 'value';
                        $parentArray= FunctionUtils::getNormalParentData($prefiller->root_id,$typeColumn);
                    }elseif($data['parentArray'] == 'getDocketPrefiller'){
                        $typeColumn = 'label';
                        $parentArray= FunctionUtils::getDocketPrefiller($prefiller->root_id,$typeColumn);
                    }elseif($data['parentArray'] == 'getParentData'){
                        $typeColumn = 'value';
                        $parentArray= FunctionUtils::getParentData($prefiller->root_id,$typeColumn);
                    }
                    $value = FunctionUtils::array_values_recursive($parentArray);
                    $defaultValue   =  $docketFiledPreFiller->where('root_id',$prefiller->root_id)->pluck($typeColumn)->toArray();
                    if( count($value) == 0){
                        $prefillerArray[] = implode(',',$defaultValue);
                    }else{
                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                    }
                }
            }
        }

        if($data['canAddChildCheck']){
            $docketPrefillers = $this->docketPrefillerRepository->getDataWhere([['id',$row->docket_prefiller_id]])->first();
            if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                $canAddChild = false;
            }
        }
        
        if($data['row']->docket_field_category_id == 22){
            return ['datas'=>$datas,'defaultPrefillerValue'=>$defaultPrefillerValue,'prefillerArray'=>$prefillerArray];
        }else{
            $array2 = [];
            $array1 = array('id' => $row->id,
                'docket_field_category_id' => $row->docket_field_category_id,
                'docket_field_category' => $row->fieldCategoryInfo->title,
                'label' => $row->label,
                'order' => $row->order,
                'is_emailed_subject' => $row->is_emailed_subject,
                'prefiller_data' => ($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>[]) : array('isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$datas),
                'default_value'=> ($data['row']->docket_field_category_id == 31) ? $row->default_value : (($defaultPrefillerValue == "") ? "" : implode(", ",$prefillerArray)),
                'required'=>$row->required,
                'subField'  => $subField);

            if($data['row']->docket_field_category_id == 3){
                $array2 = ['config' => $data['docketFieldNumbers']];
            }elseif($data['row']->docket_field_category_id == 6){
                $array2 = ['time_required'=>(@$row->docketFieldDateOption->time == null) ? 0: @$row->docketFieldDateOption->time,
                    'prefiller'=>$datas];
            }else if($data['row']->docket_field_category_id == 9){
                $array2 = ['name_required'=> (@$row->docketFieldSignatureOption->name == null) ? 0: @$row->docketFieldSignatureOption->name];
            }

            return array_merge($array1,$array2);
        }
    }

    public function docketPreFillerRowDependent2($data){
        $canAddChild = $data['canAddChild'];
        $isBigData = false;
        $datas = [];
        $prefillerArray = [];
        if($data['row']->selected_index != null) {
            $keyValue = str_replace('_', ' ', $data['row']->selected_index);
            if(array_key_exists($keyValue,json_decode($data['row']->prefillerEcowise->data, true)) == true){
                $arrayData = json_decode($data['row']->prefillerEcowise->data, true)[$keyValue];
                $prefillerArray = array();
                $arrayIndex =   array();
                if(count($arrayData) > 10){
                    $isBigData = true;
                }else{
                    if($data['row']->link_prefiller_filter_label){
                        $filtervalue = json_decode($data['row']->prefillerEcowise->data, true)[$data['row']->link_prefiller_filter_label];
                        foreach ($filtervalue as $keyValue=>$filtervalues){
                            if($data['row']->link_prefiller_filter_value != $filtervalues){
                                $arrayIndex[] = $keyValue;
                            }
                        }
                    }
                    foreach ($arrayData as $KEY=>$arrayDatas){
                        if (!in_array($KEY,$arrayIndex)){
                            $prefillerArray[] = array(
                                'id' => 0,
                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                'index' => 0,
                                'docket_field_id' => $data['row']->id,
                                'root_id' => 0,
                            );
                        }
                    }
                    $datas = $prefillerArray;
                }
            }
        }
        if($data['row']->docket_field_category_id == 22){
            return ['datas'=>$datas,'defaultPrefillerValue'=>$data['defaultPrefillerValue'],'prefillerArray'=>$prefillerArray];
        }else{
            return array('id' => $data['row']->id,
                            'docket_field_category_id' => $data['row']->docket_field_category_id,
                            'docket_field_category' => $data['row']->fieldCategoryInfo->title,
                            'label' => $data['row']->label,
                            'order' => $data['row']->order,
                            'prefiller_data' => ($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$data['row']->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$datas) : array('isDependent'=>$data['row']->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$datas),
                            'is_emailed_subject' => $data['row']->is_emailed_subject,
                            'default_value'=> ($data['defaultPrefillerValue']=="")? "" : implode(", ",$prefillerArray),
                            'required'=>$data['row']->required,
                            'subField'  => $data['subField']);
        }
    }

    public function docketPreFillerRowDependent2Autofield($data){
        if($data['row']->selected_index_value != null){
            $firstIndex = str_replace('_', ' ', $data['row']->selected_index_value);
            $esowise = json_decode($data['row']->prefillerEcowise->data,true);
            if(array_key_exists($firstIndex,$esowise) == true) {
                $firstIndexData = $esowise[$firstIndex];
                $prefillerArray = array();
                foreach ($firstIndexData as $key => $firstIndexDatas) {
                    $prefillerArray[] = array(
                        'id' => strval($key + 1),
                        'value' => (is_array($firstIndexDatas) == 1) ? "" : $firstIndexDatas,
                        'index' => 1,
                        'link_grid_field_id' => $data['row']->id,
                        'root_id' => strval(0),
                    );
                }
            }

            $ecowiseAutoPrefiller = (new Collection($data['row']->gridFieldAutoPreFiller))->sortBy('index');
            foreach ($ecowiseAutoPrefiller as $ecowiseAutoPrefillers){
                if(array_key_exists(str_replace('_', ' ', $ecowiseAutoPrefillers->selected_index),$esowise) == true) {
                    $prefillerData  = $esowise[str_replace('_', ' ', $ecowiseAutoPrefillers->selected_index)];
                    foreach ($prefillerData as $key=> $prefillerDatas){
                        $temp = array(
                            'id'=> ($key+1)."-".$ecowiseAutoPrefillers->index,
                            'value'=> (is_array($prefillerDatas)== 1)? "" : $prefillerDatas,
                            'index'=> $ecowiseAutoPrefillers->index,
                            'link_grid_field_id'=>$ecowiseAutoPrefillers->link_grid_field_id
                        );
                        if($ecowiseAutoPrefillers->index == 2){
                            $temp = array_add($temp,'root_id',strval($key+1));
                        }else{
                            $temp = array_add($temp,'root_id',($key+1)."-".($ecowiseAutoPrefillers->index-1));
                        }
                        $prefillerArray[] = $temp;
                    }
                }
            }
            $datas =   FunctionUtils::findEcowisePrefillerValue($prefillerArray);
        }

        return ['datas'=>$datas,'defaultPrefillerValue'=>$data['defaultPrefillerValue'],'prefillerArray'=>$prefillerArray];
    }

    function draftImageSaveLoop($imgs,$date,$number){
        foreach ($imgs as  $keys => $img){
            // $ext = $img->getClientOriginalExtension();
            // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
            $dest = 'files/draft/images/'.$date;
            // $img->move($dest, $filename);
            // $arrayImage[] = array(
            //     'url' =>  asset($dest . '/' . $filename),
            //     'index'=>$keys,
            // );

            $arrayImage[] = array(
                'url' =>  FunctionUtils::imageUpload($dest,$img,$number),
                'index'=>$keys,
            );
            
            $number++;
        }
        return ['number' => $number, 'arrayImage'=> $arrayImage];
    }

    public function saveEmailDockets($sentDocketData,$templateData){
        $sendDocketCopy = array();
        $folderStatusSave  = false;
        if($sentDocketData['docket_data']['email_subject'] == ""){
            $emailSubject = "";
        }else{
            $emailSubject = $sentDocketData['docket_data']['email_subject'];
        }
        $receiverUserId = $sentDocketData['email_user_receivers'];
        foreach ($receiverUserId as $receiver) {
            $emailUser = $this->emailUserRepository->getDataWhere([['id',$receiver['email_user_id']]])->first();
            $validator = new MailgunValidator(StaticValue::MailgunPubKey());
            if ($validator->validate($emailUser->email)) {
            } else {
                return array('status'=>false,'data'=>$emailUser->email . ' is not valid email.');
            }
        }

        $emailcompany = auth()->user()->companyInfo;
        $company = $emailcompany;
        $emailuserFullname = auth()->user();
        $sentDocketRequest                       =   new Request();
        $sentDocketRequest['user_id']            =   auth()->user()->id;
        $sentDocketRequest['abn']                =   $emailcompany->abn;
        $sentDocketRequest['company_name']       =   $emailcompany->name;
        $sentDocketRequest['company_address']    =   $emailcompany->address;
        $sentDocketRequest['company_logo']       =   $emailcompany->logo;
        $sentDocketRequest['sender_name']        =   $emailuserFullname->first_name.' '.$emailuserFullname->last_name;
        $sentDocketRequest['docket_id']          =   $sentDocketData['template']['id'];
        $sentDocketRequest['invoiceable']        =   $sentDocketData['template']['invoiceable'];
        $sentDocketRequest['theme_document_id']  =   $templateData->theme_document_id;
        $sentDocketRequest['company_id']	     =   $company->id;
        $sentDocketRequest['docketApprovalType'] =   $templateData->docketApprovalType;
        $sentDocketRequest['user_docket_count']  =   0;
        $sentDocketRequest['template_title']     =   $templateData->title;
        if($emailcompany->number_system == 1){
            $emailSentDocketData = $this->emailSentDocketRepository->getDataWhere([['company_id',$emailcompany->id]])->pluck('company_docket_id')->toArray();
            if (count($emailSentDocketData) == 0){
                $sentDocketRequest['company_docket_id'] = 1;
            }else{
                $companyDocketId =  $emailSentDocketData;
                $sentDocketRequest['company_docket_id'] = max($companyDocketId) + 1;
            }
        }else{
            $sentDocketRequest['company_docket_id'] = 0;
        }

        $sentDocketRequest['status']             =   ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1 ) ? 0 : 1;
        $sentDocketRequest['hashKey']            =  ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1 ) ? FunctionUtils::generateRandomString() : "" ;
        $sentDocket = $this->emailSentDocketRepository->insertAndUpdate($sentDocketRequest);

        if($emailcompany->number_system == 1){
            if($templateData->hide_prefix == 1){
                $sentDocket->formatted_id = $sentDocket->company_id.'-'.$sentDocket->company_docket_id ;
            }else{
                $sentDocket->formatted_id = 'rt-'.$sentDocket->company_id.'-edoc-'.$sentDocket->company_docket_id ;
            }
            $sentDocket->update();
        }else{
            $findUserDocketCount = $this->sentDocketsRepository->getDataWhere([['user_id', auth()->user()->id],['sender_company_id', $emailcompany->id],['docket_id',$templateData->id]])->pluck('user_docket_count')->toArray();
            $findUserEmailDocketCount = $this->emailSentDocketRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $emailcompany->id],['docket_id',$templateData->id]])->pluck('user_docket_count')->toArray();
            if(max(array_merge($findUserDocketCount,$findUserEmailDocketCount)) == 0){
                $uniquemax = 0;
            }else{
                $uniquemax = max(array_merge($findUserDocketCount,$findUserEmailDocketCount));
            }
            $sentDocket->user_docket_count = $uniquemax+1;
            $employeeData = $this->employeeRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $emailcompany->id]])->get();
            if($employeeData->count() == 0){
                if($templateData->hide_prefix == 1){
                    $sentDocket->formatted_id = $templateData->id."-1-".($uniquemax+1);
                }else{
                    $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-1-".($uniquemax+1);
                }
            }else{
                if($templateData->hide_prefix == 1){
                    $sentDocket->formatted_id = $templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                }else{
                    $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);

                }
            }
            $sentDocket->update();
        }


        $receiverUserId = $sentDocketData['email_user_receivers'];
        $docketRecipientId = $sentDocketData['email_user_approvers'];

        $this->saveEmailSentDocketRecipient($receiverUserId,$sentDocket,$templateData,$docketRecipientId,$emailcompany);

        $timerAttached = FunctionUtils::findAttachetTimerWithCategoryId($sentDocketData['docket_data']['docket_field_values']);
        if($timerAttached){
            foreach($timerAttached['timer_value'] as $timer_id){
                $timerAttachmentRequest                   = new Request();
                $timerAttachmentRequest['sent_docket_id']   = $sentDocket->id;
                $timerAttachmentRequest['type']             = 2;
                $timerAttachmentRequest['timer_id']         = $timer_id;
                $this->sentDocketTimerAttachmentRepository->insertAndUpdate($timerAttachmentRequest);
                $timerRequest = new Request;
                $timerRequest['timer_id'] = $timer_id;
                $timerRequest['status'] = 2;
                $this->timerRepository->insertAndUpdate($timerRequest);
            }
        }
        $docketFieldsQuery   = $this->docketFieldRepository->getDataWhere([['docket_id',$templateData->id]])->orderBy('order','asc')->get();
        foreach ($docketFieldsQuery as $row){
            $searchData = FunctionUtils::searchForId($row->docket_field_category_id,$row->id, $sentDocketData['docket_data']['docket_field_values']);
            if ($searchData != null) {
                if ($searchData['category_id'] == 9) {
                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,"signature");
                    $this->saveEmailSentDocketImageValueLoop($searchData['signature_value'],$docketFieldValue->id,$searchData['category_id']);
                }else if($searchData['category_id'] == 28){
                    if(count($searchData['folder_value']['folders']) != 0){
                        $folderStatusSave = true;
                        $folderItemRequest = new Request();
                        $folderItemRequest['folder_id'] =  end($searchData['folder_value']['folders'])['id'];
                        $folderItemRequest['ref_id'] = $sentDocket->id;
                        $folderItemRequest['type'] = 3;
                        $folderItemRequest['user_id'] = auth()->user()->id;
                        $folderItemRequest['status'] = 0;
                        $folderItemRequest['company_id'] = $emailcompany->id;
                        $this->folderItemRepository->insertAndUpdate($folderItemRequest);
                        $this->emailSentDocketRepository->getDataWhere([['id', $sentDocket->id]])->update(['folder_status' => 1]);
                    }
                }else if($searchData['category_id'] == 29){
                    $emailArray = array();
                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,serialize($emailArray));
                    if(count($searchData['email_list_value']) != 0){
                        if(count($searchData['email_list_value']['email_list']) != 0){
                            foreach($searchData['email_list_value']['email_list'] as $data){
                                $sendDocketCopy[] = array('email'=>$data['email'],'sendCopy' =>$data['send_copy']);
                            }
                            $docketFieldValue->value = serialize($searchData['email_list_value']['email_list']);
                            $docketFieldValue->update();
                        }
                    }
                }else if($searchData['category_id'] == 5){
                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,"image");
                    $this->saveEmailSentDocketImageValueLoop($searchData['image_value'],$docketFieldValue->id);
                }else if($searchData['category_id'] == 18){
                    $getDataFromYesNoField = $this->yesNoFieldRepository->getDataWhere([['docket_field_id',$row->id]])->get();
                    $arraygetDataFromYesNoField= array();
                    foreach ($getDataFromYesNoField as $test){
                        $arraygetDataFromYesNoField[] = array(
                            'label' => ($test->label_type==0) ? $test->label : $test->icon_image,
                            'colour' => $test->colour,
                            'label_type' => $test->label_type,
                        );
                    }
                    $arrayvalues=array();
                    $arrayvalues["title"] = $row->label;
                    $arrayvalues["label_value"]=$arraygetDataFromYesNoField;
                    $value = (array_key_exists("selected_type",$searchData['yes_no_value'])) ? $searchData['yes_no_value']['selected_type'] : "N/a";

                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,$value,serialize($arrayvalues));

                    if(count($searchData['yes_no_value']['explanation']) !=0){
                        $yesNoDocketField = $searchData['yes_no_value']['selected_id'];
                        if($this->yesNoFieldRepository->getDataWhere([['id',$yesNoDocketField],['explanation',1]])->count() == 1){
                            $items = $this->yesNoDocketsFieldRepository->getDataWhere([['yes_no_field_id',$yesNoDocketField]])->orderBy('order','asc')->get();
                            foreach ($items as $datas){
                                $searchDatas = FunctionUtils::searchForId($datas->docket_field_category_id,$datas->id, $searchData['yes_no_value']['explanation']);
                                if ($searchDatas != null) {
                                    if ($searchDatas['category_id'] == 5) {
                                        $test   =    array();
                                        foreach ($searchDatas['image_value'] as $yesNoDocketexplanations){
                                            $arrayData = explode("/", $yesNoDocketexplanations);
                                            if (count($searchDatas['image_value'])!= 0){
                                                $test[] =   implode("/",array_splice($arrayData, 4, 6));
                                            }
                                        }
                                        $serialized_array = serialize($test);
                                        $value = $serialized_array;
                                    }else if($searchDatas['category_id'] == 1){
                                        $value =$searchDatas['value'];
                                    }else if($searchDatas['category_id'] == 2){
                                        $value =$searchDatas['value'];
                                    }
                                    if ($searchDatas['category_id'] == 5 || $searchDatas['category_id'] == 1 || $searchDatas['category_id'] == 2) {
                                        $yesNoDocketFieldValueRequest = new Request();
                                        $yesNoDocketFieldValueRequest['email_sent_docket_value_id'] = $docketFieldValue->id;
                                        $yesNoDocketFieldValueRequest['yes_no_docket_field_id'] = $datas->id;
                                        $yesNoDocketFieldValueRequest['required'] = $datas->required;
                                        $yesNoDocketFieldValueRequest['label'] = $datas->label;
                                        $yesNoDocketFieldValueRequest['value'] = @$value;
                                        $this->sentEmailDocValYesNoValueRepository->insertAndUpdate($yesNoDocketFieldValueRequest);
                                    }
                                }
                            }
                        }
                    }
                }else if($searchData['category_id'] == 15){
                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,"document");
                    $documentAttachement = $row->docketAttached;
                    foreach ($documentAttachement as $rows){
                        $sentDocketAttachementRepository = new Request();
                        $sentDocketAttachementRepository['sent_email_value_id']    =  $docketFieldValue->id;
                        $sentDocketAttachementRepository['document_name'] = $rows->name;
                        $sentDocketAttachementRepository['url'] =$rows->url;
                        $this->sentEmailAttachmentRepository->insertAndUpdate($sentDocketAttachementRepository);
                    }
                }else if($searchData['category_id'] == 14){
                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,"sketchpad");
                    $this->saveEmailSentDocketImageValueLoop($searchData['image_value'],$docketFieldValue->id);
                }else if($searchData['category_id'] == 13){
                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,$searchData['value']);
                }else if ($searchData['category_id'] == 22 ){
                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,"Grid");
                    foreach ($row->girdFields as $gridField) {
                        $gridFieldLabelRequest = new Request();
                        $gridFieldLabelRequest['docket_id'] = $sentDocket->id;
                        $gridFieldLabelRequest['is_email_docket'] = 1;
                        $gridFieldLabelRequest['docket_field_grid_id'] = $gridField->id;
                        $gridFieldLabelRequest['label'] = $gridField->label;
                        $gridFieldLabelRequest['sumable'] =  $gridField->sumable;
                        $gridFieldLabelRequest['docket_field_id'] =  $row->id;
                        $this->docketFieldGridLabelRepository->insertAndUpdate($gridFieldLabelRequest);
                    }
                    foreach ($row->girdFields as $gridField) {
                        for ($i = 0; $i < count($searchData['grid_value']); $i++) {
                            $gridSearchData = FunctionUtils::searchForId($gridField->docket_field_category_id,$gridField->id, $searchData['grid_value'][$i]);
                            $gridFieldValueRequest = new Request();
                            $gridFieldValueRequest['docket_id'] = $sentDocket->id;
                            $gridFieldValueRequest['is_email_docket'] = 1;
                            $gridFieldValueRequest['docket_field_grid_id'] = $gridField->id;
                            $gridFieldValueRequest['index'] = $i;
                            $gridFieldValueRequest['docket_field_id'] =  $row->id;
                            if ($gridSearchData['category_id'] == 5 || $gridSearchData['category_id'] == 14){
                                $file_values = array();
                                if (count($gridSearchData['image_value'])){
                                    foreach ($gridSearchData['image_value'] as $itemsss){
                                        $arrayData = explode("/", $itemsss);
                                        array_push($file_values, implode("/",array_splice($arrayData, 4, 6)));
                                    }
                                }
                                $value = ($file_values != []) ? serialize($file_values) : "N/a";
                            }else if($gridSearchData['category_id'] == 29){
                                $valueArrayData = array();
                                $value  = serialize($valueArrayData);
                            }elseif ($gridSearchData['category_id'] == 9){
                                $file_values = array();
                                if (count($gridSearchData['signature_value'])){
                                    foreach ($gridSearchData['signature_value'] as $items){
                                        $data = $items['image'];
                                        $arrayData = explode("/", $data);
                                        $file_values[] = array("image" => implode("/",array_splice($arrayData, 4, 6)), "name" => $items['name']);
                                    }
                                }
                                $value = ($file_values != []) ? serialize($file_values) : "N/a";
                            }elseif($gridSearchData['category_id'] == 20){
                                $value =  ($gridSearchData['manual_timer_value'] != null && $gridSearchData['manual_timer_value'] != "" ) ? json_encode($gridSearchData['manual_timer_value']) : "N/a";
                            }else{
                                $value =  ($gridSearchData["value"] != "") ? $gridSearchData["value"] : "N/a";
                            }
                            $gridFieldValueRequest['value'] =  $value;
                            $gridFieldValue = $this->DocketFieldGridValueRepository->insertAndUpdate($gridFieldValueRequest);

                            if($gridSearchData['category_id'] == 29){
                                if(count($gridSearchData['email_list_value']) != 0){
                                    if(count($gridSearchData['email_list_value']['email_list']) != 0){
                                        foreach($gridSearchData['email_list_value']['email_list'] as $data){
                                            $sendDocketCopy[] = array('email'=>$data['email'],'sendCopy' =>$data['send_copy']);
                                        }
                                        $gridFieldValue->value = serialize($gridSearchData['email_list_value']['email_list']);
                                        $gridFieldValue->update();
                                    }
                                }
                            }
                        }
                    }
                }elseif($searchData['category_id'] == 20){
                    $value = (@$searchData['value'] != "") ? FunctionUtils::convertMilisecondtoMinHrs($searchData['value']) : "N/a";
                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,$value);

                    $docketFieldManualTimer = $docketFieldValue->docketManualTimer;
                    foreach ($docketFieldManualTimer as $docketManualTimerRow) {
                        $save = false;
                        if($docketManualTimerRow->type == 1){
                            $save = true;
                            $value = ($searchData['manual_timer_value']['from'] == "") ? 0 : $searchData['manual_timer_value']['from'];
                        }else if($docketManualTimerRow->type == 2){
                            $save = true;
                            $value = ($searchData['manual_timer_value']['to'] == "") ? 0 : $searchData['manual_timer_value']['to'];
                        }
                        if($save){
                            $emailSentDocManualTimerRequest = new Request();
                            $emailSentDocManualTimerRequest['sent_docket_value_id'] = $docketFieldValue->id;
                            $emailSentDocManualTimerRequest['docket_manual_timer_id'] = $docketManualTimerRow->id;
                            $emailSentDocManualTimerRequest['label'] = $docketManualTimerRow->label;
                            $emailSentDocManualTimerRequest['created_at'] = Carbon::now();
                            $emailSentDocManualTimerRequest['updated_at'] = Carbon::now();
                            $emailSentDocManualTimerRequest['value'] = $value;
                            $this->emailSentDocManualTimerRepository->insertAndUpdate($emailSentDocManualTimerRequest);
                        }
                    }
                    empty($docketFieldManualTimer);
                    $docketFieldManualTimerBreak = $docketFieldValue->docketManualTimerBreak;
                    foreach ($docketFieldManualTimerBreak as $docketFieldManualTimerBreakrow) {
                        $breakTimermanualRequest = new Request();
                        $breakTimermanualRequest['sent_docket_value_id'] = $docketFieldValue->id;
                        $breakTimermanualRequest['manual_timer_break_id'] = $docketFieldManualTimerBreakrow->id;
                        $breakTimermanualRequest['label'] = $docketFieldManualTimerBreakrow->label;
                        $breakTimermanualRequest['value'] =  ($searchData['manual_timer_value']['breakDuration'] == "") ? "n/a" : FunctionUtils::convertMilisecondtoMinHrs($searchData['manual_timer_value']['breakDuration']);
                        $breakTimermanualRequest['reason'] = ($searchData['manual_timer_value']['explanation'] == "") ? "n/a" : $searchData['manual_timer_value']['explanation'] ;
                        $this->emailSentDocManualTimerBrkRepository->insertAndUpdate($breakTimermanualRequest);
                    }
                    empty($docketFieldManualTimerBreak);
                }else{
                    dd('else');
                    $value = (@$searchData['value'] != "") ? @$searchData['value'] : "N/a";
                    $docketFieldValue = $this->saveEmailSentDocketValue($sentDocket,$row,$value);
                   
                    if ($searchData['category_id'] == 2 && collect($row->docketInvoiceField)->count() != 0) {
                        $emailSentDocketInvoiceRequest = new Request();
                        $emailSentDocketInvoiceRequest['email_sent_docket_id'] = $sentDocket->id;
                        $emailSentDocketInvoiceRequest['email_sent_docket_value_id'] = $docketFieldValue->id;
                        $emailSentDocketInvoiceRequest['type'] = 1;
                        $this->sentEmailDocketInvoiceRepository->insertAndUpdate($emailSentDocketInvoiceRequest);
                    }

                    if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 7) {
                        //get docket field unit rate id's
                        $docketFieldUnitRate = $docketFieldValue->docketUnitRate;
                        foreach ($docketFieldUnitRate as $unitRateRow) {
                            if (collect($row->docketInvoiceField)->count() != 0) {
                                $emailSentDocketInvoiceRequest = new Request();
                                $emailSentDocketInvoiceRequest['email_sent_docket_id'] = $sentDocket->id;
                                $emailSentDocketInvoiceRequest['email_sent_docket_value_id'] = $docketFieldValue->id;
                                $emailSentDocketInvoiceRequest['type'] = 2;
                                $this->sentEmailDocketInvoiceRepository->insertAndUpdate($emailSentDocketInvoiceRequest);
                            }
                            $save = false;
                            if (count($searchData['unit_rate_value']) != 0) {
                                if($unitRateRow->type == 1){
                                    $save = true;
                                    $value = $searchData['unit_rate_value']['per_unit_rate'];
                                }else if($unitRateRow->type == 2){
                                    $save = true;
                                    $value = $searchData['unit_rate_value']['total_unit'];
                                }
                            }else{
                                if($unitRateRow->type == 1){
                                    $save = true;
                                    $value = 0;
                                }else if($unitRateRow->type == 2){
                                    $save = true;
                                    $value = 0;
                                }
                            }
                            if($save){
                                $emailSentDocketUnitRateValueRequest = new Request();
                                $emailSentDocketUnitRateValueRequest['sent_docket_value_id'] = $docketFieldValue->id;
                                $emailSentDocketUnitRateValueRequest['docket_unit_rate_id'] = $unitRateRow->id;
                                $emailSentDocketUnitRateValueRequest['label'] = $unitRateRow->label;
                                $emailSentDocketUnitRateValueRequest['sent_docket_value_id'] = $docketFieldValue->id;
                                $this->emailSentDocketUnitRateValueRepository->insertAndUpdate($emailSentDocketUnitRateValueRequest);
                            }
                        }
                        empty($docketFieldUnitRate);
                    }

                    if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 24){
                        $docketTallyableUnitRate =  $docketFieldValue->tallyableUnitRate;
                        foreach ($docketTallyableUnitRate as $docketTallyableUnitRates){
                            if (count($searchData['unit_rate_value']) != 0) {
                                $save = false;
                                if($docketTallyableUnitRates->type == 1){
                                    $save = true;
                                    $value = $searchData['unit_rate_value']['per_unit_rate'];
                                }else if($docketTallyableUnitRates->type == 2){
                                    $save = true;
                                    $value = $searchData['unit_rate_value']['total_unit'];
                                }
                            }else{
                                if($docketTallyableUnitRates->type == 1){
                                    $save = true;
                                    $value = 0;
                                }else if($docketTallyableUnitRates->type == 2){
                                    $save = true;
                                    $value = 0;
                                }
                            }
                            if($save){
                                $emailSentDocketTallyUnitRateValRequest = new Request();
                                $emailSentDocketTallyUnitRateValRequest['sent_docket_value_id'] = $docketFieldValue->id;
                                $emailSentDocketTallyUnitRateValRequest['docket_tally_unit_rate_id'] = $docketTallyableUnitRates->id;
                                $emailSentDocketTallyUnitRateValRequest['label'] = $docketTallyableUnitRates->label;
                                $emailSentDocketTallyUnitRateValRequest['value'] = $value;
                                $this->emailSentDocketTallyUnitRateValRepository->insertAndUpdate($emailSentDocketTallyUnitRateValRequest);
                            }

                        }
                        empty($docketTallyableUnitRate);
                    }
                    empty($docketFieldValue);
                }
            }
        }

        $docketProject = $this->docketProjectRepository->getDataWhere([['docket_id', $templateData->id]])->get();
        foreach ($docketProject as $docketProjects){
            if ($docketProjects->project->is_close == 0){
                $sentDocketProjectRequest = new Request();
                $sentDocketProjectRequest['project_id'] = $docketProjects->project_id;
                $sentDocketProjectRequest['sent_docket_id'] = $sentDocket->id;
                $sentDocketProjectRequest['is_email'] = 1;
                $this->sentDocketProjectRepository->insertAndUpdate($sentDocketProjectRequest);
            }
        }

        if($folderStatusSave == false) {
            if (@$templateData->docketFolderAssign!=null){
                $folderItemRequest = new Request();
                $folderItemRequest['folder_id'] = $templateData->docketFolderAssign->folder_id;
                $folderItemRequest['ref_id'] = $sentDocket->id;
                $folderItemRequest['type'] = 3;
                $folderItemRequest['user_id'] = auth()->user()->id;
                $folderItemRequest['status'] = 0;
                $folderItemRequest['company_id'] = $emailcompany->id;
                $this->folderItemRepository->insertAndUpdate($folderItemRequest);
                $this->emailSentDocketRepository->getDataWhere([['id',$sentDocket->id]])->update(['folder_status'=>1]);
            }
        }


        $receiverQuery = $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id', $sentDocket->id]])->get();
        $recipientNames =  "";

        foreach ($receiverQuery as $receiverInfo) {
            $recipientNames = $recipientNames." ".$receiverInfo->emailUserInfo->email;
            if($receiverQuery->count()>1)
                if($receiverQuery->last()->id!=$receiverInfo->id)
                    $recipientNames =   $recipientNames.",";
            if($emailSubject == ""){  $emailSubject = "You’ve got a docket"; };
            Mail::to($receiverInfo->emailUserInfo->email)->send(new EmailDocket($sentDocket,$receiverInfo,$emailSubject));
        }

        if(count($sendDocketCopy)!=0){
            $input = array_map("unserialize", array_unique(array_map("serialize", $sendDocketCopy)));
            foreach($input as $sendDocketCopy){
                $validator = new MailgunValidator(StaticValue::MailgunPubKey());

                if($validator->validate($sendDocketCopy['email'])) {
                    if($sendDocketCopy['sendCopy'] == true){
                        if($emailSubject == ""){  $emailSubject = "You’ve got a email docket copy"; };
                        Mail::to($sendDocketCopy['email'])->send(new SendCopyEmailDocket($sentDocket,$sendDocketCopy,$emailSubject));
                    }
                }
            }
        }
        return array('status'=>true,'data'=>$recipientNames);
    }

    public function saveDockets($sentDocketData, $templateData){
        $sendDocketCopy = array();
        $folderStatusSave  = false;
        $company=auth()->user()->companyInfo;
        $userFullname = $this->userRepository->getDataWhere([['id',auth()->user()->id]])->first();

        $sentDocketRequest                       =       new Request();
        $sentDocketRequest['user_id']            =       auth()->user()->id;
        $sentDocketRequest['abn']                =      $company->abn;
        $sentDocketRequest['company_name']       =      $company->name;
        $sentDocketRequest['company_address']    =      $company->address;
        $sentDocketRequest['company_logo']       =      $company->logo;
        $sentDocketRequest['sender_name']        =      $userFullname->first_name.' '.$userFullname->last_name;
        $sentDocketRequest['docket_id']          =      $sentDocketData['template']['id'];
        $sentDocketRequest['theme_document_id']  =       $templateData->theme_document_id;
        $sentDocketRequest['invoiceable']        =      $sentDocketData['template']['invoiceable'];
        $sentDocketRequest['company_id']         =   0;
        $sentDocketRequest['sender_company_id']	 =   $company->id;
        $sentDocketRequest['template_title']     =       $templateData->title;
        $sentDocketRequest['status']             =   ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1 ) ? 0 : 1;
        $sentDocketRequest['docketApprovalType'] =   $templateData->docketApprovalType;
        $sentDocketRequest['user_docket_count']  =  0;
        if($company->number_system == 1){
            if ($this->sentDocketsRepository->getDataWhere([['sender_company_id',$company->id]])->count()== 0){
                $sentDocketRequest['company_docket_id'] = 1;
            }else{
                $companyDocketId =  $this->sentDocketsRepository->getDataWhere([['sender_company_id',$company->id]])->pluck('company_docket_id')->toArray();
                $sentDocketRequest['company_docket_id'] = max($companyDocketId) + 1;
            }
        }else{
            $sentDocketRequest['company_docket_id']    =   0;
        }

        $sentDocket = $this->sentDocketsRepository->insertAndUpdate($sentDocketRequest);
        if($company->number_system == 1){
            if($templateData->hide_prefix == 1){
                $sentDocket->formatted_id = $sentDocket->sender_company_id.'-'.$sentDocket->company_docket_id ;
            }else{
                $sentDocket->formatted_id = 'rt-'.$sentDocket->sender_company_id.'-doc-'.$sentDocket->company_docket_id ;
            }
            $sentDocket->update();
        }else{
            $findUserDocketCount = $this->sentDocketsRepository->getDataWhere([['user_id', auth()->user()->id],['sender_company_id', $company->id],['docket_id',$templateData->id]])->pluck('user_docket_count')->toArray();
            $findUserEmailDocketCount = $this->emailSentDocketRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $company->id],['docket_id',$templateData->id]])->pluck('user_docket_count')->toArray();
            if(max(array_merge($findUserDocketCount,$findUserEmailDocketCount)) == 0){
                $uniquemax = 0;
                $sentDocket->user_docket_count = $uniquemax+1;
                $employeeData = $this->employeeRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $company->id]])->get();
                if($employeeData->count() == 0){
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-1-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-1-".($uniquemax+1);
                    }
                }else{
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }
                }
                $sentDocket->update();
            }else{
                $uniquemax = max(array_merge($findUserDocketCount,$findUserEmailDocketCount));
                $sentDocket->user_docket_count = $uniquemax+1;
                $employeeData = $this->employeeRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $company->id]])->get();
                if($employeeData->count() == 0){
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-1-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-1-".($uniquemax+1);
                    }
                }else{
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }
                }
                $sentDocket->update();
            }
        }

        $docketFieldsQuery   = $this->docketFieldRepository->getDataWhere([['docket_id',$templateData->id]])->orderBy('order','asc')->get();
        $timerAttached = FunctionUtils::findAttachetTimerWithCategoryId($sentDocketData['docket_data']['docket_field_values']);

        if($timerAttached){
            foreach($timerAttached['timer_value'] as $timer_id){
                $timerAttachmentRequest                     = new Request();
                $timerAttachmentRequest['sent_docket_id']   = $sentDocket->id;
                $timerAttachmentRequest['type']             = 1;
                $timerAttachmentRequest['timer_id']         = $timer_id;
                $this->sentDocketTimerAttachmentRepository->insertAndUpdate($timerAttachmentRequest);
                $timerRequest = new Request();
                $timerRequest['timer_id'] = $timer_id;
                $timerRequest['status'] = 2;
                $this->timerRepository->insertAndUpdate($timerRequest);
            }
        }

        foreach ($docketFieldsQuery as $row){
            $searchData = FunctionUtils::searchForId($row->docket_field_category_id,$row->id, $sentDocketData['docket_data']['docket_field_values']);
            if ($searchData != null) {
                if ($searchData['category_id'] == 9) {
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,'signature');
                    $this->saveSentDocketImageValueLoop($searchData['signature_value'],$docketFieldValue->id,$searchData['category_id']);
                    
                }else if($searchData['category_id'] == 28){
                    if(count($searchData['folder_value']['folders']) != 0){
                        $folderStatusSave = true;
                        $folderItemRequest = new Request();
                        $folderItemRequest['folder_id'] = end($searchData['folder_value']['folders'])['id'];
                        $folderItemRequest['ref_id'] = $sentDocket->id;
                        $folderItemRequest['type'] = 1;
                        $folderItemRequest['user_id'] = auth()->user()->id;
                        $folderItemRequest['status'] = 0;
                        $folderItemRequest['company_id'] = $company->id;
                        $this->folderItemRepository->insertAndUpdate($folderItemRequest);
                        $this->sentDocketsRepository->getDataWhere([['id',$sentDocket->id]])->update(['folder_status'=>1]);
                    }
                }else if($searchData['category_id'] == 29){
                    $emailArray = array();
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,serialize($emailArray));
                    if(count($searchData['email_list_value']) != 0){
                        if(count($searchData['email_list_value']['email_list']) != 0){
                            foreach($searchData['email_list_value']['email_list'] as $data){
                                $sendDocketCopy[] = array('email'=>$data['email'],'sendCopy' =>$data['send_copy']);
                            }
                            $docketFieldValue->value = serialize($searchData['email_list_value']['email_list']);
                            $docketFieldValue->update();
                        }
                    }
                }else if($searchData['category_id'] == 5){
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,'image');
                    $this->saveSentDocketImageValueLoop($searchData['image_value'],$docketFieldValue->id);
                }else if($searchData['category_id'] == 18){
                    $getDataFromYesNoField = $this->yesNoFieldRepository->getDataWhere([['docket_field_id',$row->id]])->get();
                    $arraygetDataFromYesNoField= array();
                    foreach ($getDataFromYesNoField as $test){
                        $arraygetDataFromYesNoField[] = array(
                            'label' => ($test->label_type==0) ? $test->label : $test->icon_image,
                            'colour' => $test->colour,
                            'label_type' => $test->label_type,
                        );
                    }
                    $arrayvalues=array();
                    $arrayvalues["title"] = $row->label;
                    $arrayvalues["label_value"]=$arraygetDataFromYesNoField;
                    $label = serialize($arrayvalues);
                    $value = (array_key_exists("selected_type",$searchData['yes_no_value'])) ? $searchData['yes_no_value']['selected_type'] : "N/a" ;
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,$value,$label);

                    if(count($searchData['yes_no_value']['explanation']) !=0){
                        $yesNoDocketField = $searchData['yes_no_value']['selected_id'];
                        if($this->yesNoFieldsRepository->getDataWhere([['id',$yesNoDocketField],['explanation',1]])->count() == 1){
                            $items = $this->yesNoDocketsFieldRepository->getDataWhere([['yes_no_field_id',$yesNoDocketField]])->orderBy('order','asc')->get();
                            foreach ($items as $datas){
                                $searchDatas = FunctionUtils::searchForId($datas->docket_field_category_id,$datas->id, $searchData['yes_no_value']['explanation']);
                                if ($searchDatas != null) {
                                    $save = false;
                                    if ($searchDatas['category_id'] == 5) {
                                        $save = true;
                                        $test   =    array();
                                        foreach ($searchDatas['image_value'] as $yesNoDocketexplanations){
                                            $arrayData = explode("/", $yesNoDocketexplanations);
                                            if (count($searchDatas['image_value'])!= 0){
                                                $test[] =   implode("/",array_splice($arrayData, 4, 6));
                                            }

                                        }
                                        $serialized_array = serialize($test);
                                        $value = $serialized_array;
                                    }else if($searchDatas['category_id'] == 1){
                                        $save = true;
                                        $value = $searchDatas['value'];
                                    }else if($searchDatas['category_id'] == 2){
                                        $save = true;
                                        $value = $searchDatas['value'];
                                    }

                                    if($save){
                                        $yesNoDocketFieldValueRequest = new Request();
                                        $yesNoDocketFieldValueRequest['sent_docket_value_id'] = $docketFieldValue->id;
                                        $yesNoDocketFieldValueRequest['yes_no_docket_field_id'] = $datas->id;
                                        $yesNoDocketFieldValueRequest['label'] = $datas->label;
                                        $yesNoDocketFieldValueRequest['value'] = $value;
                                        $yesNoDocketFieldValueRequest['required'] = $datas->required;
                                        $this->sentDocValYesNoValueRepository->insertAndUpdate($yesNoDocketFieldValueRequest);
                                    }
                                }
                            }
                        }
                    }
                }else if($searchData['category_id'] == 15){
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,'document');
                    $documentAttachement = $row->docketAttached;
                    foreach ($documentAttachement as $rows){
                        $sentDocketAttachementRequest = new Request();
                        $sentDocketAttachementRequest['sent_dockets_value_id']    =  $docketFieldValue->id;
                        $sentDocketAttachementRequest['document_name'] = $rows->name;
                        $sentDocketAttachementRequest['url'] = $rows->url;
                        $this->sentDocketAttachmentRepository->insertAndUpdate($sentDocketAttachementRequest);
                    }
                }else if($searchData['category_id'] == 14){
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,'sketchpad');
                    $this->saveSentDocketImageValueLoop($searchData['image_value'],$docketFieldValue->id);
                }else if($searchData['category_id'] == 13){
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,$searchData['value']);
                }else if ($searchData['category_id'] == 22 ){
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,'Grid');
                    foreach ($row->girdFields as $gridField) {
                        $gridFieldLabelRequest = new Request();
                        $gridFieldLabelRequest['docket_id'] = $sentDocket->id;
                        $gridFieldLabelRequest['is_email_docket'] = 0;
                        $gridFieldLabelRequest['docket_field_grid_id'] = $gridField->id;
                        $gridFieldLabelRequest['label'] = $gridField->label;
                        $gridFieldLabelRequest['sumable'] =  $gridField->sumable;
                        $gridFieldLabelRequest['docket_field_id'] =  $row->id;
                        $this->docketFieldGridLabelRepository->insertAndUpdate($gridFieldLabelRequest);
                    }

                    foreach ($row->girdFields as $gridField) {
                        for ($i = 0; $i < count($searchData['grid_value']); $i++) {
                            $gridSearchData = FunctionUtils::searchForId($gridField->docket_field_category_id,$gridField->id, $searchData['grid_value'][$i]);
                            $gridFieldValueRequest = new Request();

                            if ($gridSearchData['category_id'] == 5){
                                $file_values = array();
                                if (count($gridSearchData['image_value'])){
                                    foreach ($gridSearchData['image_value'] as $itemsss){
                                        $arrayData = explode("/", $itemsss);
                                        array_push($file_values, implode("/",array_splice($arrayData, 4, 6)));
                                    }
                                }
                                $value = ($file_values != []) ? serialize($file_values) : "N/a";
                           }elseif($gridSearchData['category_id'] == 29){
                                $valueArrayData = array();
                                $value  = serialize($valueArrayData);
                            }elseif($gridSearchData['category_id'] == 14){
                                $file_values = array();
                                if (count($gridSearchData['image_value'])){
                                    foreach ($gridSearchData['image_value'] as $itemsss){
                                        $arrayData = explode("/", $itemsss);
                                        array_push($file_values, implode("/",array_splice($arrayData, 4, 6)));
                                    }
                                }
                                $value = ($file_values != []) ? serialize($file_values) : "N/a";
                            }elseif ($gridSearchData['category_id'] == 9){
                                $file_values = array();
                                if (count($gridSearchData['signature_value'])){
                                    foreach ($gridSearchData['signature_value'] as $items){
                                        $data = $items['image'];
                                        $arrayData = explode("/", $data);
                                        $file_values[] = array("image" => implode("/",array_splice($arrayData, 4, 6)), "name" => $items['name']);
                                    }
                                }
                                $value = ($file_values != []) ? serialize($file_values) : "N/a";
                            }elseif($gridSearchData['category_id'] == 20){
                                $value =  ($gridSearchData['manual_timer_value'] != null && $gridSearchData['manual_timer_value'] != "" ) ? json_encode($gridSearchData['manual_timer_value']) : "N/a";
                            }else{
                                $value =  ($gridSearchData["value"] != "") ? $gridSearchData["value"] : "N/a";
                            }

                            $gridFieldValueRequest['index'] = $i;
                            $gridFieldValueRequest['docket_field_id'] =  $row->id;
                            $gridFieldValueRequest['docket_id'] = $sentDocket->id;
                            $gridFieldValueRequest['is_email_docket'] = 0;
                            $gridFieldValueRequest['value'] = $value;
                            $gridFieldValueRequest['docket_field_grid_id'] = $gridField->id;
                            $gridFieldValue = $this->docketFieldGridValueRepository->insertAndUpdate($gridFieldValueRequest);

                            if($gridSearchData['category_id'] == 29){
                                $valueArrayData = array();
                                $value  = serialize($valueArrayData);
                                if(count($gridSearchData['email_list_value']) != 0){
                                    if(count($gridSearchData['email_list_value']['email_list']) != 0){
                                        foreach($gridSearchData['email_list_value']['email_list'] as $data){
                                            $sendDocketCopy[] = array('email'=>$data['email'],'sendCopy' =>$data['send_copy']);
                                        }
                                        $gridFieldValue->value = serialize($gridSearchData['email_list_value']['email_list']);
                                        $gridFieldValue->update();
                                    }
                                }
                            }
                        }
                    }
                }else if($searchData['category_id'] == 20){
                    $value = (@$searchData['value'] != "") ? FunctionUtils::convertMilisecondtoMinHrs($searchData['value']) : "N/a";
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,$value);
                
                    $docketFieldManualTimer = $docketFieldValue->docketManualTimer;

                    foreach ($docketFieldManualTimer as $docketManualTimerRow) {
                        if($docketManualTimerRow->type == 1){
                            $value = ($searchData['manual_timer_value']['from'] == "") ? 0 : $searchData['manual_timer_value']['from'];
                        }else if($docketManualTimerRow->type == 2){
                            $value = ($searchData['manual_timer_value']['to'] == "") ? 0 : $searchData['manual_timer_value']['to'];
                        }
                        $sentDocketManualTimerRequest = new Request();
                        $sentDocketManualTimerRequest['sent_docket_value_id'] = $docketFieldValue->id;
                        $sentDocketManualTimerRequest['docket_manual_timer_id'] = $docketManualTimerRow->id;
                        $sentDocketManualTimerRequest['label'] = $docketManualTimerRow->label;
                        $sentDocketManualTimerRequest['value'] = $docketFieldValue->id;
                        $sentDocketManualTimerRequest['created_at'] = Carbon::now();
                        $sentDocketManualTimerRequest['updated_at'] = Carbon::now();
                        $this->sentDocketManualTimerRepository->insertAndUpdate($sentDocketManualTimerRequest);
                    }
                    empty($docketFieldManualTimer);
                    $docketFieldManualTimerBreak = $docketFieldValue->docketManualTimerBreak;
                    foreach ($docketFieldManualTimerBreak as $docketFieldManualTimerBreakrow) {
                        $breakTimermanualRequest = new Request();
                        $breakTimermanualRequest['sent_docket_value_id'] =$docketFieldValue->id;
                        $breakTimermanualRequest['manual_timer_break_id'] = $docketFieldManualTimerBreakrow->id;
                        $breakTimermanualRequest['label'] = $docketFieldManualTimerBreakrow->label;
                        $breakTimermanualRequest['value'] =  ($searchData['manual_timer_value']['breakDuration'] == "") ? "n/a" : FunctionUtils::convertMilisecondtoMinHrs($searchData['manual_timer_value']['breakDuration']);
                        $breakTimermanualRequest['reason'] = ($searchData['manual_timer_value']['explanation'] == "" ) ? "n/a" : $searchData['manual_timer_value']['explanation'] ;
                        $this->sentDocketManualTimerBreakRepository->insertAndUpdate($breakTimermanualRequest);
                    }
                    empty($docketFieldManualTimerBreak);
                }else{
                    $value = (@$searchData['value'] != "") ? @$searchData['value'] : "N/a";
                    $docketFieldValue = $this->saveSentDocketsValue($sentDocket,$row,$value);
                   
                    if ($searchData['category_id'] == 2 && collect($row->docketInvoiceField)->count() != 0) {
                        $emailSentDocketInvoiceRequest = new Request();
                        $emailSentDocketInvoiceRequest['sent_docket_id'] = $sentDocket->id;
                        $emailSentDocketInvoiceRequest['sent_docket_value_id'] = $docketFieldValue->id;
                        $emailSentDocketInvoiceRequest['type'] = 1;
                        $this->sentDocketInvoiceRepository->insertAndUpdate($emailSentDocketInvoiceRequest);
                    }

                    if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 7) {
                        $docketFieldUnitRate = $docketFieldValue->docketUnitRate;
                        foreach ($docketFieldUnitRate as $unitRateRow) {
                            if (collect($row->docketInvoiceField)->count() != 0) {
                                $emailSentDocketInvoiceRequest = new Request();
                                $emailSentDocketInvoiceRequest['sent_docket_id'] = $sentDocket->id;
                                $emailSentDocketInvoiceRequest['sent_docket_value_id'] = $docketFieldValue->id;
                                $emailSentDocketInvoiceRequest['type'] = 2;
                                $this->sentDocketInvoiceRepository->insertAndUpdate($emailSentDocketInvoiceRequest);
                            }
                            if (count($searchData['unit_rate_value']) != 0) {
                                if($unitRateRow->type == 1){
                                    $value = $searchData['unit_rate_value']['per_unit_rate'];
                                }else if($unitRateRow->type == 2){
                                    $value = $searchData['unit_rate_value']['total_unit'];
                                }
                            }else{
                                if($unitRateRow->type == 1){
                                    $value = 0;
                                }else if($unitRateRow->type == 2){
                                    $value = 0;
                                }
                            }
                            $sentDocketUnitRateValueRequest = new Request();
                            $sentDocketUnitRateValueRequest['sent_docket_value_id'] = $docketFieldValue->id;
                            $sentDocketUnitRateValueRequest['docket_unit_rate_id'] = $unitRateRow->id;
                            $sentDocketUnitRateValueRequest['label'] = $unitRateRow->label;
                            $sentDocketUnitRateValueRequest['value'] = $value;
                            $this->sentDocketUnitRateValueRepository->insertAndUpdate($sentDocketUnitRateValueRequest);
                        }
                        empty($docketFieldUnitRate);
                    }
                    if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 24){
                        $docketTallyableUnitRate =  $docketFieldValue->tallyableUnitRate;
                        foreach ($docketTallyableUnitRate as $docketTallyableUnitRates){
                            if (count($searchData['unit_rate_value']) != 0) {
                                if($docketTallyableUnitRates->type == 1){
                                    $value = $searchData['unit_rate_value']['per_unit_rate'];
                                }else if($docketTallyableUnitRates->type == 2){
                                    $value = $searchData['unit_rate_value']['total_unit'];
                                }
                            }else{
                                if($docketTallyableUnitRates->type == 1){
                                    $value = 0;
                                }else if($docketTallyableUnitRates->type == 2){
                                    $value = 0;
                                }
                            }
                            $sentDocketTallyUnitRateValRequest = new Request();
                            $sentDocketTallyUnitRateValRequest['sent_docket_value_id'] = $docketFieldValue->id;
                            $sentDocketTallyUnitRateValRequest['docket_tally_unit_rate_id'] = $docketTallyableUnitRates->id;
                            $sentDocketTallyUnitRateValRequest['label'] = $docketTallyableUnitRates->label;
                            $sentDocketTallyUnitRateValRequest['sent_docket_value_id'] = $docketFieldValue->id;
                            $this->sentDocketTallyUnitRateValRepository->insertAndUpdate($sentDocketTallyUnitRateValRequest);
                        }
                        empty($docketTallyableUnitRate);
                    }
                    empty($docketFieldValue);
                }
            }
        }

        if($folderStatusSave == false){

            if (@$templateData->docketFolderAssign!=null){
                $folderItemRequest = new Request();
                $folderItemRequest['folder_id'] = $templateData->docketFolderAssign->folder_id;
                $folderItemRequest['ref_id'] = $sentDocket->id;
                $folderItemRequest['type'] = 1;
                $folderItemRequest['user_id'] = auth()->user()->id;
                $folderItemRequest['status'] = 0;
                $folderItemRequest['company_id'] = $company->id;
                $this->folderItemRepository->insertAndUpdate();
                $this->sentDocketsRepository->getDataWhere([['id',$sentDocket->id]])->update(['folder_status'=>1]);
            }
        }

        $docketProject = $this->docketProjectRepository->getDataWhere([['docket_id', $templateData->id]])->get();
        foreach ($docketProject as $docketProjects){
            if ($docketProjects->project->is_close == 0){
                $sentDocketProjectRequest = new Request();
                $sentDocketProjectRequest['project_id'] = $docketProjects->project_id;
                $sentDocketProjectRequest['sent_docket_id'] = $sentDocket->id;
                $sentDocketProjectRequest['is_email'] = 0;
                $this->sentDocketProjectRepository->insertAndUpdate($sentDocketProjectRequest);
            }
        }
        if ($templateData->xero_timesheet==1) {
            $docketTimesheets = $this->docketTimesheetRepository->getDataWhere([['docket_id',$templateData->id]])->get();
            foreach ($docketTimesheets as $items) {
                $docketTimesheetRequest = new Request();
                $docketTimesheetRequest['sent_docket_id'] = $sentDocket->id;
                $docketTimesheetRequest['docket_field_id'] = $items->docket_field_id;
                $this->sentDocketTimesheetRepository->insertAndUpdate($docketTimesheetRequest);
            }
        }

        //multiple recipient users
        $receiverUserId =  $sentDocketData['rt_user_receivers'];
        $docketRecipientId = $sentDocketData['rt_user_approvers'];
        $sn=1;
        foreach($receiverUserId as $receiver){

            $sentDocketRecipientRequest     =    new Request();
            $sentDocketRecipientRequest['sent_docket_id']    =    $sentDocket->id;
            $sentDocketRecipientRequest['user_id']           =   $receiver['user_id'];
            $docketApproval =   0;
            if ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1) {
                foreach ($docketRecipientId as $recipientId){
                    if($recipientId==$receiver['user_id'])
                        $docketApproval =   1;
                }
            }else{
                $docketApproval =   1;
            }

            $sentDocketRecipientRequest['approval']  =   $docketApproval;
            $sentDocketRecipientRequest['status']   =   0;
            $this->sentDocketRecipientRepository->insertAndUpdate($sentDocketRecipientRequest);

            $sentDocketReceiverInfo    = $this->userRepository->getDataWhere([['id',$receiver['user_id']]])->first();
            $receiverNames = '';
            if($sn==1){
                $receiverNames  =  @$sentDocketReceiverInfo->first_name." ".@$sentDocketReceiverInfo->last_name;
            }elseif($sn==count($receiverUserId)){
                $receiverNames  =  $receiverNames.", ".@$sentDocketReceiverInfo->first_name." ".@$sentDocketReceiverInfo->last_name.".";
            }else{
                $receiverNames  =  $receiverNames.", ".@$sentDocketReceiverInfo->first_name." ".@$sentDocketReceiverInfo->last_name;
            }
            $userNotificationRequest   =    new Request();
            $userNotificationRequest['sender_user_id']   =    auth()->user()->id;
            $userNotificationRequest['receiver_user_id'] =   @$sentDocketReceiverInfo->id;
            $userNotificationRequest['type']     =   3;
            $userNotificationRequest['title']    =   'New Docket';
            $userNotificationRequest['message']  =   "You have received a docket from ".$sentDocket->senderUserInfo->first_name." ".$sentDocket->senderUserInfo->last_name;
            $userNotificationRequest['key']      =   $sentDocket->id;
            $userNotificationRequest['status']   =   0;
            $this->userNotificationRepository->insertAndUpdate();

            if($sentDocketReceiverInfo->device_type == 2){
                $this->firebaseApi->sendiOSNotification($sentDocketReceiverInfo->deviceToken,'New Docket',"You have received a docket from ".$sentDocket->senderUserInfo->first_name." ".$sentDocket->senderUserInfo->last_name,array('type'=>3,'id'=>$sentDocket->id));
            }else if($sentDocketReceiverInfo->device_type == 1){
                $this->firebaseApi->sendAndroidNotification($sentDocketReceiverInfo->deviceToken,'New Docket',"You have received a docket from ".$sentDocket->senderUserInfo->first_name." ".$sentDocket->senderUserInfo->last_name,array('type'=>3,'id'=>$sentDocket->id));
            }
            $sn++;
        }
        //save docket approval users details
        if ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1) {
            foreach($docketRecipientId as $recipient){
                $sentDocketRecipientApprovalRequest    =    new Request();
                $sentDocketRecipientApprovalRequest['sent_docket_id']    =   $sentDocket->id;
                $sentDocketRecipientApprovalRequest['user_id']           =   $recipient['user_id'];
                $sentDocketRecipientApprovalRequest['status']            =   0;
                $sentDocketRecipientApprovalRequest['name'] = "null";
                $sentDocketRecipientApprovalRequest['signature'] = "null";
                $this->sentDocketRecipientApprovalRepository->insertAndUpdate($sentDocketRecipientApprovalRequest);
            }
        }
        if($sentDocket->recipientInfo){
            $emailSubjectFields = $this->docketFieldRepository->getDataWhere([['docket_id',$sentDocket->docket_id],['is_emailed_subject',1]])->orderBy('order','asc')->get();
            $emailSubject   =   "";
            foreach($emailSubjectFields as $subjectField){
                $emailSubjectQuery   = $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$sentDocket->id],['docket_field_id', $subjectField->id]])->get();
                if($emailSubjectQuery->count()>0){
                    if($emailSubjectQuery->first()->value!="") {
                        $emailSubject = $emailSubject . $emailSubjectQuery->first()->label . ": " . $emailSubjectQuery->first()->value . " ";
                    }
                }
            }
        }

        if(count($sendDocketCopy)!=0){
            $input = array_map("unserialize", array_unique(array_map("serialize", $sendDocketCopy)));
            foreach($input as $sendDocketCopy){
                $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');

                if($validator->validate($sendDocketCopy['email'])) {
                    if($sendDocketCopy['sendCopy'] == true){
                        if($emailSubject == ""){  $emailSubject = "You’ve got a docket copy"; };
                        Mail::to($sendDocketCopy['email'])->send(new SendCopyDocket($sentDocket,$sendDocketCopy,$emailSubject));
                    }
                }
            }
        }

        return array('status'=>true,'data'=>$receiverNames);
    }

    function saveEmailSentDocketRecipient($receiverUserId,$sentDocket,$templateData,$docketRecipientId,$emailcompany){
        foreach($receiverUserId as $receiver){
            if($receiver['saved'] == true){
                $sentDocketRecipientRequest                          =    new Request();
                $sentDocketRecipientRequest['email_sent_docket_id']    =    $sentDocket->id;
                $sentDocketRecipientRequest['email_user_id']           =    $receiver['email_user_id'];
                $docketApproval =   0;
                if ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1) {
                    foreach ($docketRecipientId as $recipientId){
                        if($recipientId['email_user_id']==$receiver['email_user_id'])
                            $docketApproval =   1;
                    }
                }else{
                    $docketApproval =   1;
                }
                $sentDocketRecipientRequest['approval']  =   $docketApproval;
                $sentDocketRecipientRequest['status']   =    ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1 ) ? 0 : 1;
                $sentDocketRecipientRequest['hashKey']            =   FunctionUtils::generateRandomString();
                if($receiver['saved'] == true){
                    $sentDocketRecipientRequest['receiver_full_name']        =   ($receiver['full_name'] != "") ? $receiver['full_name'] : "";
                    $sentDocketRecipientRequest['receiver_company_name']	    =   ($receiver['company_name'] != "") ? $receiver['company_name'] : "";
                    $sentDocketRecipientRequest['receiver_company_address']  =   ($receiver['company_address'] != "") ? $receiver['company_address'] : "";
                }else{
                    $emailClient = $this->emailClientRepository->getDataWhere([['company_id', $emailcompany->id],['email_user_id', $receiver['email_user_id']]])->first();

                    $sentDocketRecipientRequest['receiver_full_name'] = @$emailClient->full_name;
                    $sentDocketRecipientRequest['receiver_company_name'] = @$emailClient->company_name;
                    $sentDocketRecipientRequest['receiver_company_address'] = @$emailClient->company_address;
                }
                $this->emailSentDocketRecipientRepository->insertAndUpdate($sentDocketRecipientRequest);
            }else{

                if(count($receiverUserId)==1){
                    $sentDocketRecipientRequest                        =    new Request();
                    $sentDocketRecipientRequest['email_sent_docket_id']  =    $sentDocket->id;
                    $sentDocketRecipientRequest['email_user_id']         =    $receiverUserId[0]['email_user_id'];
                    $sentDocketRecipientRequest['approval']              =    1;
                    $sentDocketRecipientRequest['status']                =    0;
                    $sentDocketRecipientRequest['hashKey']               =    FunctionUtils::generateRandomString();
                    if($receiver['full_name'] !=""){
                        $sentDocketRecipientRequest['receiver_full_name']    =   ($receiver['full_name'] != "") ? $receiver['full_name'] : "";
                        $sentDocketRecipientRequest['receiver_company_name']	 =   ($receiver['company_name'] != "") ? $receiver['company_name'] : "";
                        $sentDocketRecipientRequest['receiver_company_address']  =   ($receiver['company_address'] != "") ? $receiver['company_address'] : "";
                    }else{
                        $emailClient = $this->emailClientRepository->getDataWhere([['company_id', $emailcompany->id],['email_user_id', $receiverUserId[0]["email_user_id"]]])->first();
                        $sentDocketRecipientRequest['receiver_full_name'] = @$emailClient->full_name;
                        $sentDocketRecipientRequest['receiver_company_name'] = @$emailClient->company_name;
                        $sentDocketRecipientRequest['receiver_company_address'] = @$emailClient->company_address;
                    }
                    $this->emailSentDocketRecipientRepository->insertAndUpdate($sentDocketRecipientRequest);
                }else{
                    foreach($receiverUserId as $receiver){
                        $sentDocketRecipientRequest                    =    new Request();
                        $sentDocketRecipientRequest['email_sent_docket_id']    =    $sentDocket->id;
                        $sentDocketRecipientRequest['email_user_id']           =   $receiver['email_user_id'];
                        $docketApproval =   0;
                        foreach ($docketRecipientId as $recipientId){
                            if($recipientId['email_user_id']==$receiver['email_user_id'])
                                $docketApproval =   1;
                        }
                        $sentDocketRecipientRequest['approval']  =   $docketApproval;
                        $sentDocketRecipientRequest['status']   =   0;
                        $sentDocketRecipientRequest['hashKey']            =   FunctionUtils::generateRandomString();
                        $tempCheck = 1;
                        if(count($receiverUserId)==1){
                            if($receiver['full_name'] !=""){
                                $tempCheck = 0;
                                $sentDocketRecipientRequest['receiver_full_name']    = ($receiver['full_name'] != "") ? $receiver['full_name'] : "";
                                $sentDocketRecipientRequest['receiver_company_name']	 =  ($receiver['company_name'] != "") ? $receiver['company_name'] : "";
                                $sentDocketRecipientRequest['receiver_company_address']  =  ($receiver['company_address'] != "") ? $receiver['company_address'] : "";
                            }
                        }
                        if($tempCheck == 1) {
                            $emailClient = $this->emailClientRepository->getDataWhere([['company_id', $emailcompany->id],['email_user_id', $receiver['email_user_id']]])->first();
                            $sentDocketRecipientRequest['receiver_full_name'] = @$emailClient->full_name;
                            $sentDocketRecipientRequest['receiver_company_name'] = @$emailClient->company_name;
                            $sentDocketRecipientRequest['receiver_company_address'] = @$emailClient->company_address;
                        }
                        $this->emailSentDocketRecipientRepository->insertAndUpdate($sentDocketRecipientRequest);
                    }
                }
            }
        }
    }

    function saveEmailSentDocketValue($sentDocket,$row,$value,$label = null){
        $docketFieldValueRequest = new Request();
        $docketFieldValueRequest['email_sent_docket_id'] = $sentDocket->id;
        $docketFieldValueRequest['docket_field_id'] = $row->id;
        $docketFieldValueRequest['label'] = ($label == null) ? $row->label : $label;
        $docketFieldValueRequest['is_hidden'] = $row->is_hidden;
        $docketFieldValueRequest['value'] = $value;
        return $this->emailSentDocketValueRepository->insertAndUpdate($docketFieldValueRequest);
    }

    function saveEmailSentDocketImageValueLoop($image,$docketFieldValueId,$category_id = null){
        foreach ($image as $item){
            $data = ($category_id == 9) ? $item['image'] : $item;
            $arrayData = explode("/", $data);
            $imageValueRequest     =    new Request();
            $imageValueRequest['sent_docket_value_id']    =  $docketFieldValueId;
            if($category_id == 9){
                $imageValueRequest['name'] = $item['name'];
            }
            $imageValueRequest['value'] = implode("/",array_splice($arrayData, 4, 6));
            $this->emailSentDocketImageValueRepository->insertAndUpdate($imageValueRequest);
        }
    }

    function saveSentDocketsValue($sentDocket,$row,$value,$label = null){
        $docketFieldValueRequest = new Request();
        $docketFieldValueRequest['sent_docket_id'] = $sentDocket->id;
        $docketFieldValueRequest['docket_field_id'] = $row->id;
        $docketFieldValueRequest['label']  =  ($label == null) ? $row->label : $label;
        $docketFieldValueRequest['value'] = $value;
        $docketFieldValueRequest['is_hidden'] = $row->is_hidden;
        return $this->sentDocketsValueRepository->insertAndUpdate($docketFieldValueRequest);
    }

    function saveSentDocketImageValueLoop($image,$docketFieldValueId,$category_id = null){
        foreach ($image as $item){
            $data = ($category_id == 9) ? $item['image'] : $item;

            $arrayData = explode("/", $data);
            $imageValueRequest     =    new Request();
            $imageValueRequest['sent_docket_value_id']    =  $docketFieldValueId;
            if($category_id == 9){
                $imageValueRequest['name'] = $item['name'];
            }
            $imageValueRequest['value'] = implode("/",array_splice($arrayData, 4, 6));
            $this->sentDocketImageValueRepository->insertAndUpdate($imageValueRequest);
        }
    }

    function subscriptionCheck($company){
        if($company->trial_period==3) {
            // deactivateAllEmployee($company->id);
        }
    }
}