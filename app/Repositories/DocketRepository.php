<?php


namespace App\Repositories;


use App\AssignedDocket;
use App\Company;
use App\Docket;
use App\DocketField;
use App\DocketFieldFooter;
use App\DocketFieldNumber;
use App\DocketFiledPreFiller;
use App\DocketGridPrefiller;
use App\DocketManualTimer;
use App\DocketManualTimerBreak;
use App\DocketPrefiller;
use App\DocketPrefillerValue;
use App\DocketTallyableUnitRate;
use App\DocketUnitRate;
use App\Employee;
use App\Folder;
use App\Http\Resources\DefaultUserResource;
use App\Http\Resources\DocketResource;
use App\Repositories\Interfaces\DocketRepositoryInterfaces;
use App\SentDockets;
use App\TemplateAssignFolder;
use Carbon\Carbon;
use http\Env\Request;

class DocketRepository implements DocketRepositoryInterfaces
{

    public function index($companyId){
        $docketTemplateArray = array();
        $docketTemplate=  Docket::where('company_id',$companyId)
            ->where('is_archive',0)
            ->orderBy('id','desc')
            ->get();
        foreach ($docketTemplate as $docketTemplates){
            $defaultRecipien = null;
            $defaultRecipient = new DefaultUserResource($defaultRecipien);
            $docketTemplateArray[] = new DocketResource($docketTemplates,$defaultRecipient);
        }
        return array('data'=>$docketTemplate,'status'=>200);


    }

    public function getDocketTemplateByUserId($userId){
        $docketTemplateQuery    =    AssignedDocket::where('user_id',$userId)
            ->orderBy('created_at','desc')
            ->with('docketInfo')
            ->get();
            $docketTemplate = array();
            foreach ($docketTemplateQuery as $row) {
                if ($row->docketInfo->is_archive == 0) {
                    $defaultRecipien = array();

                    if ($row->docketInfo->defaultRecipient) {
                        $rt_user = array();
                        $email_client = array();
                        foreach ($row->docketInfo->defaultRecipient as $defaultRecipients) {
                            if (@$defaultRecipients->user_type == 1) {
                                $rt_user[] = $defaultRecipients->userInfo->id;
                            }
                            if (@$defaultRecipients->user_type == 2) {
                                $email_client[] = $defaultRecipients->emailUser->id;
                            }
                        }
                        $defaultRecipien = array(
                            'rt_user' => $rt_user,
                            'email_client' => $email_client
                        );
                    }

                    $defaultRecipient = new DefaultUserResource($defaultRecipien);
                    $docketTemplate[] = new DocketResource($row->docketInfo,$defaultRecipient);
                }
            }
        return array('data'=>$docketTemplate,'status'=>200);
    }

    public function show($id){
        $docket     =   Docket::where('id',$id);
        if($docket->count()>0){




        }else{
            return array('message'=>"Docket not found!",'status'=>404);
        }
    }



























}
