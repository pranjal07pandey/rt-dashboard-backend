<?php

namespace App\Http\Controllers;

use App\DocketConstantField;
use App\DocketDraft;
use App\DocketFieldDateOption;
use App\DocketFieldGrid;
use App\DocketFieldNumber;
use App\DocketFiledPreFiller;
use App\DocketGridAutoPrefiller;
use App\DocketGridPrefiller;
use App\DocketInvoiceField;
use App\DocketPreviewField;
use App\DocketTallyableUnitRate;
use App\ExportMapping;
use App\Folder;
use App\GridFieldFormula;
use App\Support\Collection;
use App\TemplateAssignFolder;
use App\YesNoDocketsField;
use App\YesNoFields;
use Illuminate\Http\Request;
use App\AssignedDocket;
use App\DocketField;
use App\Docket;
use App\DocumentTheme;
use App\DocketFrequency;
use App\SentDockets;
use App\DocketFieldFooter;
use App\DocketAttachments;
use App\DocketTimesheet;
use App\DocketManualTimer;
use App\DocketManualTimerBreak;
use App\DocketUnitRate;
use App\EmailSentDocket;
use Illuminate\Support\Facades\Auth;
use App\Employee;
use App\Company;
use Session;

class DocketBookManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(Session::get('company_id')==''){
                if(Employee::where('user_id', Auth::user()->id)->count()!=0):
                    $companyId = Employee::where('user_id', Auth::user()->id)->first()->company_id;
                    Session::put('adminType',2);
                else :
                    $companyId   =   Company::where('user_id', Auth::user()->id)->first()->id;
                    Session::put('adminType',1);
                endif;
                Session::put('company_id',$companyId);
            }
            if(!checkProfileComplete()){
                return redirect()->route('companyProfile');
            }

            $status     =   checkSubscription();
            switch ($status){
                case 'noSubscription':
                    return redirect('dashboard/company/profile/selectSubscription');
                    break;

                case 'subscriptionCancel':
//                    return redirect()->route('Company.Subscription.Continue');
                    break;

                case 'past_due':
                    break;

                default:
                    break;
            }
            return $next($request);
        });
    }

    public function companyDocketTemplates(){
        $dockets    =   Docket::where('company_id',Session::get('company_id'))->where('is_archive',0)->orderBy('created_at','desc')->get();
        $themes = DocumentTheme::where('is_active', 1)->get();
        $docket_frequecies = DocketFrequency::where('company_id',Session::get('company_id'))->orderBy('created_at','desc')->get();

        $folderData= Folder::where('company_id',Session::get('company_id'))->get();
        $treeArray = array();
        foreach ($folderData as $test) {
            $treeArray[] = array(
                'id' => $test->id,
                'parent_id' => $test->root_id,
                'name' => $test->name,
                'slug' => $test->slug,
            );
        }
        $txtTree[0]="";
        foreach($treeArray as $branch){
            if(isset($txtTree[$branch['parent_id']])) {
                $txtTree[$branch['id']] = $txtTree[$branch['parent_id']] . $branch['name'] . "/";
            }
        }

        $datas = array();
        if(@$txtTree){
            foreach ($txtTree as $key => $value){
                if($key != 0) {
                    $datas[] = array(
                        "id"=> $key,
                        "value"=>rtrim($value,'/'),
                        "space" =>str_repeat('&nbsp;', (count(explode('/',$value))-2)*3),
                        'name'=> array_slice(explode('/',$value), -2, 1)
                    );


                }
            }
        }
        $data= (new Collection($datas))->sortBy('value');
        return view('dashboard.company.docketManager.index',compact('dockets', 'docket_frequecies','themes','data'));
    }

    public function docketFieldIsHidden(Request $request){
        $this->validate($request,['data'   => 'required|Int|between:0,1','docketFieldId'   => 'required']);
        $docketField =   DocketField::with('docketInfo')->withTrashed()->findOrFail($request->docketFieldId);
        if($docketField->docketInfo->company_id==Session::get('company_id')):
            $docketField->is_hidden =   $request->data;
            $docketField->save();
        else:
            echo "Invalid attempt!";
        endif;
    }



    public function docketGridFieldIsHidden(Request $request){
        $this->validate($request,['data'   => 'required|Int|between:0,1','requiredDocketFieldId'   => 'required','requiredDocketGridFieldId'   => 'required']);
        $docketField =  DocketFieldGrid::findorFail($request->requiredDocketGridFieldId);
        $docketField->update(['is_hidden'=>$request->data]);
    }

    public function deleteDocketTemplate(Request $request){
        $tempDocket   =   Docket::where('id',$request->id)->firstOrFail();
        if($tempDocket->company_id==Session::get('company_id')){
            //check sentdocket and email docket
            if(SentDockets::where('docket_id',$request->id)->count()>0 || EmailSentDocket::where('docket_id',$request->id)->count()>0){
                flash("Invalid Action. You cannot delete this docket template as it has already been used to create dockets in the past.",'warning');
                return redirect()->route('companyDocketTemplates');
            }else{
                if(AssignedDocket::where('docket_id',$request->id)->count()>0){
                    AssignedDocket::where('docket_id',$request->id)->delete();
                }
                $docketFields    =   DocketField::where('docket_id',$request->id)->withTrashed()->get();




                foreach ($docketFields as $docketField){
                    if($docketField->docket_field_category_id==7){
                        DocketUnitRate::where('docket_field_id',$docketField->id)->delete();
                    }
                    if ($docketField->docket_field_category_id==20){
                        DocketManualTimer::where('docket_field_id',$docketField->id)->delete();
                        DocketManualTimerBreak::where('docket_field_id',$docketField->id)->delete();

                    }
                    if ($docketField->docket_field_category_id==18){
                        YesNoFields::where('docket_field_id',$docketField->id)->delete();

                    }

                    if($docketField->docket_field_category_id == 22){
                        $docketGridField = DocketFieldGrid::where('docket_field_id', $docketField->id)->get();

                        foreach ($docketGridField as $docketGridFields){
                            GridFieldFormula::where('docket_field_grid_id',$docketGridFields->id)->delete();
                        }
                        foreach ($docketGridField as $docketGridFields){
                            DocketGridPrefiller::where('docket_field_grid_id',$docketGridFields->id)->delete();
                        }
                        DocketFieldGrid::where('docket_field_id', $docketField->id)->delete();
                    }

                    DocketFieldFooter::where('field_id',$docketField->id)->where('docket_id',$request->id)->delete();
                    DocketAttachments::where('docket_field_id',$docketField->id)->delete();
                    DocketTimesheet::where('docket_field_id',$docketField->id)->where('docket_id',$request->id)->delete();
                    DocketFiledPreFiller::where('docket_field_id',$docketField->id)->delete();
                    DocketInvoiceField::where('docket_field_id',$docketField->id)->delete();
                    DocketPreviewField::where('docket_field_id',$docketField->id)->delete();
                    DocketUnitRate::where('docket_field_id',$docketField->id)->delete();
                    DocketFieldNumber::where('docket_field_id',$docketField->id)->delete();
                    DocketTallyableUnitRate::where('docket_field_id',$docketField->id)->delete();
                    DocketFieldDateOption::where('docket_field_id',$docketField->id)->delete();
                    ExportMapping::where('docket_field_id',$docketField->id)->delete();

                    DocketField::destroy($docketField->id);
                    $note = DocketField::onlyTrashed()->find($docketField->id);
                    if (!is_null($note)) {
                        $note->forceDelete();
                    }
                }

                Docket::where('id',$request->id)->delete();

                flash('Docket Template Cancel successfully.','warning');
                return redirect()->route('companyDocketTemplates');
            }
        } else {
            echo "<script>alert('Invalid attempt!')</script>";
            return redirect()->back();
        }
    }

    public function archiveDocketTemplete(Request $request){
        $this->validate($request,['id'=>'required|Int']);
        $docket =    Docket::findOrFail($request->id);
        if(count(DocketDraft::where('docket_id',$request->id)->get())== 0){
            if($docket->company_id==Session::get('company_id')){
                if(AssignedDocket::where('docket_id',$request->id)->count()>0){
                    AssignedDocket::where('docket_id',$request->id)->delete();
                }
                if(count($docket->sentDockets) == 0 && count($docket->emailSentDockets) == 0){
                    $docketFields    =   DocketField::where('docket_id',$request->id)->get();

                    foreach ($docketFields as $docketField){
                        DocketFieldDateOption::where('docket_field_id',$docketField->id)->delete();
                        DocketAttachments::where('docket_field_id',$docketField->id)->delete();
                        DocketFieldFooter::where('field_id',$docketField->id)->delete();
                        DocketFiledPreFiller::where('docket_field_id',$docketField->id)->delete();
                        DocketInvoiceField::where('docket_field_id',$docketField->id)->delete();
                        DocketPreviewField::where('docket_field_id',$docketField->id)->delete();
                        DocketTimesheet::where('docket_field_id',$docketField->id)->delete();
                        DocketUnitRate::where('docket_field_id',$docketField->id)->delete();
                        DocketFieldNumber::where('docket_field_id',$docketField->id)->delete();
                        DocketTallyableUnitRate::where('docket_field_id',$docketField->id)->delete();
                        ExportMapping::where('docket_field_id',$docketField->id)->delete();
                        DocketConstantField::where('docket_field_id',$docketField->id)->delete();
                        if($docketField->docket_field_category_id==7){
                            DocketUnitRate::where('docket_field_id',$docketField->id)->delete();
                        }
                        if ($docketField->docket_field_category_id==20){
                            DocketManualTimer::where('docket_field_id',$docketField->id)->delete();
                            DocketManualTimerBreak::where('docket_field_id',$docketField->id)->delete();
                        }
                        if ($docketField->docket_field_category_id==18){
                            YesNoDocketsField::where('yes_no_field_id',YesNoFields::where('docket_field_id',$docketField->id)->first()->id)->delete();
                            YesNoFields::where('docket_field_id',$docketField->id)->delete();
                        }
                        if($docketField->docket_field_category_id == 22){
                            DocketFieldGrid::where('docket_field_id', $docketField->id)->delete();
                            DocketField::where('id',$docketField->id)->where('docket_id',$request->id)->delete();
                        }
                        DocketField::destroy($docketField->id);
                        $note = DocketField::onlyTrashed()->find($docketField->id);
                        if (!is_null($note)) {
                            $note->forceDelete();
                        }
                    }
                    Docket::where('id',$request->id)->delete();
                }else{
                    $docket->is_archive = 1;
                    $docket->save();
                }
                TemplateAssignFolder::where('template_id',$request->id)->where('type',1)->delete();

                flash('Docket Template deleted successfully.','warning');
            }else{
                flash('Invalid attempt!!!', 'warning');
            }
        }else{
            flash('Please remove docket draft first which is using this docket template.', 'danger');
        }

        return redirect()->back();
    }


    public function companyDocketTemplatesArchive(){
        $dockets    =   Docket::where('company_id',Session::get('company_id'))->where('is_archive',1)->orderBy('created_at','desc')->get();
        $themes = DocumentTheme::where('is_active', 1)->get();
        $docket_frequecies = DocketFrequency::where('company_id',Session::get('company_id'))->orderBy('created_at','desc')->get();
        return view('dashboard.company.docketManager.archive',compact('dockets', 'docket_frequecies','themes'));
    }

    public function docketDuplicate(Request $request){
//        dd($request->all());

        if (Docket::where('title',$request->tempate_name)->count()==0){
            $dockets  =   Docket::where('id',$request->id)->first();
            $docket = new Docket();
            $docket->title = $request->tempate_name;
            $docket->subTitle = '';
            $docket->user_id = Auth::user()->id;
            $docket->company_id = Session::get('company_id');
            $docket->invoiceable =$dockets->invoiceable ;
            $docket->docketApprovalType = $dockets->docketApprovalType;
            $docket->timer_attachement = $dockets->timer_attachement;
            $docket->xero_timesheet = $dockets->xero_timesheet;
            $docket->theme_document_id = $dockets->theme_document_id;
            $docket->is_archive = $dockets->is_archive;
            $docket->prefix = $dockets->prefix;

            if ($docket->save()){
                $docketFields = DocketField::where('docket_id',$request->id)->withTrashed()->get();

                foreach ($docketFields as $docketField) {

                    $docket_field = new DocketField();
                    $docket_field->docket_id = $docket->id;
                    $docket_field->docket_field_category_id = $docketField->docket_field_category_id;
                    $docket_field->order = $docketField->order;
                    $docket_field->label = $docketField->label;
                    $docket_field->required = $docketField->required;
                    $docket_field->is_hidden = $docketField->is_hidden;
                    $docket_field->is_emailed_subject = $docketField->is_emailed_subject;
                    $docket_field->default_prefiller_id = $docketField->default_prefiller_id;
                    $docket_field->is_dependent = $docketField->is_dependent;
                    $docket_field->docket_prefiller_id = $docketField->docket_prefiller_id;
                    $docket_field->csv_header = $docketField->csv_header;
                    $docket_field->is_show = $docketField->is_show;
                    if ($docket_field->save()){
                        if($docketField->exportMapping){
                            $exportMapping = new ExportMapping();
                            $exportMapping->docket_field_id = $docket_field->id;
                            $exportMapping->value = $docketField->exportMapping->value;
                            $exportMapping->save();
                        }

                        if($docketField->docketPreFiller){
                            foreach ($docketField->docketPreFiller as $docketPreFillers ){
                                $adddocketPreFiller = new DocketFiledPreFiller();
                                $adddocketPreFiller->docket_field_id = $docket_field->id;
                                $adddocketPreFiller->value = $docketPreFillers->value;
                                $adddocketPreFiller->index = $docketPreFillers->index;
                                $adddocketPreFiller->root_id = $docketPreFillers->root_id;
                                $adddocketPreFiller->save();
                            }



                        }

                        if ($docketField->docketInvoiceField){
                            $docketInvoiceField = new DocketInvoiceField();
                            $docketInvoiceField->docket_field_id =  $docket_field->id;
                            $docketInvoiceField->docket_id = $docket->id;
                            $docketInvoiceField->save();
                        }

                        if ($docketField->docketPreviewField){
                            $docketPreviewFields = new DocketPreviewField();
                            $docketPreviewFields->docket_field_id =  $docket_field->id;
                            $docketPreviewFields->docket_id = $docket->id;
                            $docketPreviewFields->order = $docketField->docketPreviewField->order;
                            $docketPreviewFields->save();
                        }

                        if($docketField->docketFieldDateOption){
                            $adddocketFieldDateOption = new DocketFieldDateOption();
                            $adddocketFieldDateOption->time = $docketField->docketFieldDateOption->time;
                            $adddocketFieldDateOption->date = $docketField->docketFieldDateOption->date;
                            $adddocketFieldDateOption->docket_field_id = $docket_field->id;
                            $adddocketFieldDateOption->save();
                        }

                        if($docketField->docket_field_category_id == 22){

                            foreach ($docketField->girdFields as $docketGridField){
                                $docket_grid_field = new DocketFieldGrid();
                                $docket_grid_field->docket_field_id =$docket_field->id;
                                $docket_grid_field->docket_field_category_id =$docketGridField->docket_field_category_id;
                                $docket_grid_field->order =$docketGridField->order;
                                $docket_grid_field->label =$docketGridField->label;
                                $docket_grid_field->default_prefiller_id =$docketGridField->default_prefiller_id;
                                $docket_grid_field->auto_field =$docketGridField->auto_field;
                                $docket_grid_field->is_dependent =$docketGridField->is_dependent;
                                $docket_grid_field->docket_prefiller_id =$docketGridField->docket_prefiller_id;
                                $docket_grid_field->is_deleted =$docketGridField->is_deleted;
                                $docket_grid_field->sumable =$docketGridField->sumable;
                                $docket_grid_field->csv_header =$docketGridField->csv_header;
                                $docket_grid_field->is_show =$docketGridField->is_show;
                                $docket_grid_field->export_value =$docketGridField->export_value;
                                if($docket_grid_field->save()){

                                    if($docketGridField->docket_field_category_id == 3){


                                        if($docketGridField->gridFieldFormula != null){
                                            $gridFormula = new GridFieldFormula();
                                            $gridFormula->docket_field_grid_id = $docket_grid_field->id;
                                            $gridFormula->user_id = $docketGridField->gridFieldFormula->user_id;
                                            $gridFormula->formula = $docketGridField->gridFieldFormula->formula;
                                            $gridFormula->save();
                                        }


                                        if($docketGridField->gridFieldPreFiller){
                                            foreach ($docketGridField->gridFieldPreFiller as $gridFieldPreFillers){
                                                $gridFieldPreFiller = new DocketGridPrefiller();
                                                $gridFieldPreFiller->docket_field_grid_id = $docket_grid_field->id;
                                                $gridFieldPreFiller->value = $gridFieldPreFillers->value;
                                                $gridFieldPreFiller->index = $gridFieldPreFillers->index;
                                                $gridFieldPreFiller->root_id =  $gridFieldPreFillers->root_id;
                                                $gridFieldPreFiller->save();
                                            }
                                        }
                                        if($docketGridField->gridFieldAutoPreFiller){
                                            foreach ($docketGridField->gridFieldAutoPreFiller as $gridFieldAutoPreFillers){
                                                $gridFieldPreFiller = new DocketGridAutoPrefiller();
                                                $gridFieldPreFiller->grid_field_id = $docket_grid_field->id;
                                                $gridFieldPreFiller->index = $gridFieldAutoPreFillers->index;
                                                $gridFieldPreFiller->link_grid_field_id = $gridFieldAutoPreFillers->link_grid_field_id;
                                                $gridFieldPreFiller->docket_field_id =  $gridFieldAutoPreFillers->docket_field_id;
                                                $gridFieldPreFiller->save();
                                            }
                                        }

                                    }elseif($docketGridField->docket_field_category_id == 1 || $docketGridField->docket_field_category_id == 2 || $docket_grid_field->docket_field_category_id == 4){
                                        if($docketGridField->gridFieldPreFiller){
                                            foreach ($docketGridField->gridFieldPreFiller as $gridFieldPreFillers){
                                                $gridFieldPreFiller = new DocketGridPrefiller();
                                                $gridFieldPreFiller->docket_field_grid_id = $docket_grid_field->id;
                                                $gridFieldPreFiller->value = $gridFieldPreFillers->value;
                                                $gridFieldPreFiller->index = $gridFieldPreFillers->index;
                                                $gridFieldPreFiller->root_id =  $gridFieldPreFillers->root_id;
                                                $gridFieldPreFiller->save();
                                            }
                                        }
                                        if($docket_grid_field->gridFieldAutoPreFiller){
                                            foreach ($docket_grid_field->gridFieldAutoPreFiller as $gridFieldAutoPreFillers){
                                                $gridFieldPreFiller = new DocketGridAutoPrefiller();
                                                $gridFieldPreFiller->grid_field_id = $docket_grid_field->id;
                                                $gridFieldPreFiller->index = $gridFieldAutoPreFillers->index;
                                                $gridFieldPreFiller->link_grid_field_id = $gridFieldAutoPreFillers->link_grid_field_id;
                                                $gridFieldPreFiller->docket_field_id =  $gridFieldAutoPreFillers->docket_field_id;
                                                $gridFieldPreFiller->save();
                                            }
                                        }

                                    }

                                }
                            }

                        }

                        elseif($docketField->docket_field_category_id == 7){
                            foreach ($docketField->unitRate as $row){
                                $docketunitrate = new DocketUnitRate();
                                $docketunitrate->docket_field_id = $docket_field->id;
                                $docketunitrate->label = $row->label;
                                $docketunitrate->type = $row->type;
                                $docketunitrate->csv_header = $row->csv_header;
                                $docketunitrate->is_show = $row->is_show;
                                $docketunitrate->save();
                            }
                        }
                        else if($docketField->docket_field_category_id == 3){
                            if($docketField->docketFieldNumbers){
                                $docketNumberField = new DocketFieldNumber();
                                $docketNumberField->docket_field_id = $docket_field->id;
                                $docketNumberField->min = $docketField->docketFieldNumbers->min;
                                $docketNumberField->max = $docketField->docketFieldNumbers->max;
                                $docketNumberField->tolerance = $docketField->docketFieldNumbers->tolerance;
                                $docketNumberField->save();
                            }
                        }
                        else if($docketField->docket_field_category_id == 24){
                            foreach ($docketField->tallyUnitRate as $tallyableRate){
                                $tallyableUnitRate = new DocketTallyableUnitRate();
                                $tallyableUnitRate->docket_field_id = $docket_field->id;
                                $tallyableUnitRate->label = $tallyableRate->label;
                                $tallyableUnitRate->type = $tallyableRate->type;
                                $tallyableUnitRate->csv_header = $tallyableRate->csv_header;
                                $tallyableUnitRate->is_show = $tallyableRate->is_show;
                                $tallyableUnitRate->save();
                            }
                        }

                        else if($docketField->docket_field_category_id == 20){
                            foreach ($docketField->docketManualTimer as $row){
                                $docketManulaTimer = new DocketManualTimer();
                                $docketManulaTimer->docket_field_id = $docket_field->id;
                                $docketManulaTimer->type = $row->type;
                                $docketManulaTimer->label = $row->label;
                                $docketManulaTimer->csv_header = $row->csv_header;
                                $docketManulaTimer->is_show = $row->is_show;
                                $docketManulaTimer->save();
                            }
                            foreach ($docketField->docketManualTimerBreak as $items){
                                $docketManualTimerBreak = new DocketManualTimerBreak();
                                $docketManualTimerBreak->docket_field_id = $docket_field->id;
                                $docketManualTimerBreak->label = $items->label;
                                $docketManualTimerBreak->type = $items->type;
                                $docketManualTimerBreak->explanation = $items->explanation;
                                $docketManualTimerBreak->csv_header = $items->csv_header;
                                $docketManualTimerBreak->is_show = $items->is_show;
                                $docketManualTimerBreak->save();
                            }
                        }elseif ($docketField->docket_field_category_id == 18){
                            foreach ($docketField->yesNoField as $items){
                                $yesNoField = new YesNoFields();
                                $yesNoField->docket_field_id = $docket_field->id;
                                $yesNoField->label = $items->label;
                                $yesNoField->type = $items->type;
                                $yesNoField->explanation = $items->explanation;
                                $yesNoField->colour = $items->colour;
                                $yesNoField->icon_image = $items->icon_image;
                                $yesNoField->label_type = $items->label_type;
                                $yesNoField->csv_header = $items->csv_header;
                                $yesNoField->is_show = $items->is_show;
                                if ($yesNoField->save()){
                                    foreach ($items->yesNoDocketsField as $row){
                                        $yesNoDocketsField = new  YesNoDocketsField();
                                        $yesNoDocketsField->yes_no_field_id = $yesNoField->id;
                                        $yesNoDocketsField->docket_field_category_id = $row->docket_field_category_id;
                                        $yesNoDocketsField->order = $row->order;
                                        $yesNoDocketsField->required = $row->required;
                                        $yesNoDocketsField->label = $row->label;
                                        $yesNoDocketsField->csv_header = $row->csv_header;
                                        $yesNoDocketsField->is_show = $row->is_show;
                                        $yesNoDocketsField->save();

                                    }
                                }
                            }

                        }
                        elseif ($docketField->docket_field_category_id == 30){
                            if($docketField->docketConstantField){
                                $docketFieldConstant = new DocketConstantField();
                                $docketFieldConstant->docket_field_id = $docket_field->id;
                                $docketFieldConstant->label = $docketField->docketConstantField->label;
                                $docketFieldConstant->csv_header = $docketField->docketConstantField->csv_header;
                                $docketFieldConstant->is_show = $docketField->docketConstantField->is_show;
                                $docketFieldConstant->export_mapping_field_category_id = $docketField->docketConstantField->export_mapping_field_category_id;
                                $docketFieldConstant->save();

                            }

                        }

                    }

                }
            }

            flash("Docket Template Duplicated Successfully.",'success');
            return redirect()->back();

        }else{
            flash("Name must be unique",'warning');
            return redirect()->back();
        }
    }
}
