<?php
namespace App\Services\V2\Api;

use App\Helpers\V2\MessageDisplay;
use App\Http\Resources\V2\User\EmployeeResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Services\V2\ConstructorService;
use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\AmazoneBucket;

class UserService extends ConstructorService {

    public function changePassword($request){
        try {
            if (Hash::check(Input::get('oldPassword'), $this->userRepository->getDataWhere([['id',auth()->user()->id]])->first()->password)) {
                $userRequest = new Request();
                $userRequest['user_id'] = auth()->user()->id;
                $userRequest['password'] = Hash::make($request['newPassword']); 
                $this->userRepository->insertAndUpdate($userRequest);
                return response()->json(["message"=> MessageDisplay::PasswordChanged],200);
            }else{
                return response()->json(["message"=> MessageDisplay::InvalidPassword],500);
            }
        } catch (\Exception $ex) {
            return response()->json(["message"=> MessageDisplay::ERROR],500);
        }
    }

    public function profileUpdate($request){
        try {
            $profile              =   Input::file('image');
            if($request->hasFile('image')) {
                if ($profile->isValid()) {
                    // $ext = $profile->getClientOriginalExtension();
                    // $filename = basename($request->file('image')->getClientOriginalName(), '.' . $request->file('image')->getClientOriginalExtension()) . time() . "." . $ext;
                    $dest = 'files/profile';
                    // $profile->move($dest, $filename);
                    // $path = $dest . '/' . $filename;
                    $path = FunctionUtils::imageUpload($dest,$profile);
                    
                    $userRequest = new Request();
                    $userRequest['user_id'] = auth()->user()->id;
                    $userRequest['image'] = $path;

                    $this->userRepository->insertAndUpdate($userRequest);
                    return response()->json(["message" => MessageDisplay::ProfileUpdateSuccess,'profile' => AmazoneBucket::url() . $path],200);
                }
            }else{
                return response()->json(["message"=>MessageDisplay::ProfileUpdateError],500);
            }
        } catch (\Exception $ex) {
            return response()->json(["message" => MessageDisplay::ERROR],500);
        }
    }

    public function nameUpdate($request){
        try {
            $userRequest = new Request();
            $userRequest['user_id'] = auth()->user()->id;
            $userRequest['first_name'] = $request->first_name;
            $userRequest['last_name'] = $request->last_name;
            $this->userRepository->insertAndUpdate($userRequest);
            return response()->json(["message" => MessageDisplay::NameUpdated,'first_name' => $request->first_name,'last_name'=>$request->last_name],200);
        } catch (\Exception $ex) {
            return response()->json(["message" => MessageDisplay::ERROR],500);
        }
    }

    public function getEmployeeList($request){
        if(!$request->headers->has('companyId')){
            if(isset(auth()->user()->id)){
                $request->headers->set('userId', auth()->user()->id);
                $request->headers->set('companyId', auth()->user()->companyInfo->id);
                $userId = auth()->user()->id;
                $companyId = auth()->user()->companyInfo->id;
            }
        }else{
            $companyId = $request->header('companyId');
            $userId = $request->header('userId');
        }
        
        $companySuperadmin  = $this->companyRepository->getDataWhere([['id', $companyId]])->with('userInfo')->first();
        
        $added_company_idQuery          =  $this->clientRepository->getDataWhere([["company_id",$companyId]])->orWhere('requested_company_id',$companyId)->get();
        $added_company_id               =   array();
        $employee                       =   array();

        foreach ($added_company_idQuery as $row){
            if($row->company_id == $companyId){
                $added_company_id[] =   $row->requested_company_id;
            }else {
                $added_company_id[] =   $row->company_id;
            }
        }
        if ($companySuperadmin->user_id == $userId){
            if ($companySuperadmin->docket_client ==1) {
                $addCompanySuperadmin = $this->companyRepository->getDataWhereIn('id', $added_company_id)->with('userInfo')->get();
                foreach ($addCompanySuperadmin as $row) {
                    if ($row->userInfo->isActive == 1) {
                        $employee[] = new EmployeeResource($row,'company');
                    }
                }
                $employeeQuery = $this->employeeRepository->getDataWhereIn('company_id', $added_company_id)->with('userInfo')->get();
                foreach ($employeeQuery as $row) {
                    if ($row->userInfo->isActive == 1) {
                        if ($row->employed == 1) {
                            $employee[] = new EmployeeResource($row,'employee');
                        }
                    }
                }
            }
        }else{
            if ($this->employeeRepository->getDataWhere([['user_id',$userId]])->first()->docket_client ==1){
                $addCompanySuperadmin = $this->companyRepository->getDataWhereIn('id', $added_company_id)->with('userInfo')->get();
                foreach ($addCompanySuperadmin as $row){
                    if ($row->userInfo->isActive == 1) {
                        $employee[] = new EmployeeResource($row,'company');
                    }
                }
                $employeeQuery  =  $this->employeeRepository->getDataWhereIn('company_id',$added_company_id)->with('userInfo')->get();
                foreach ($employeeQuery as $row){
                    if ($row->userInfo->isActive == 1) {
                        if($row->employed==1) {
                            $employee[] = new EmployeeResource($row,'employee');
                        }
                    }
                }
            }
        }

        //company superadmin and company employee
        if($companySuperadmin->can_self_docket==1){
            if($userId == $companySuperadmin->user_id){
                $employee[] =  new EmployeeResource($row,'company');
            }
        }
        if($companySuperadmin->appear_on_recipient==1){
            if($userId != $companySuperadmin->user_id){
                $employee[] = new EmployeeResource($row,'company');
            }
        }

        $employeeQuery  =  $this->employeeRepository->getDataWhere([['company_id',$companyId]])->get();
        foreach ($employeeQuery as $row){
            if(@$row->userInfo->isActive==1) {
                if($row->employed==1) {
                    if ($row->can_self_docket == 1) {
                        if ($userId == $row->user_id) {
                            $employee[] = new EmployeeResource($row,'employee');
                        }
                    }
                    if($row->appear_on_recipient==1){
                        if ($userId != $row->user_id) {
                            $employee[] = new EmployeeResource($row,'employee');
                        }
                    }
                }
            }
        }
        return $employee;
    }

    public function getFrequency($request){
        $docketTemplate     =  $this->docketRepository->getDataWhere([['company_id',auth()->user()->companyInfo->id]])->select('id','title','created_at')->orderBy('id','desc')->get(2);
        $docketTemplates   =   array();
        foreach ($docketTemplate as $row ){
         $date = Carbon::parse($row->start_date)->addDay($row->docket_frequency_slug)->toDateString();
            if ($this->sentDocketsRepository->getDataWhere([['docket_id', $row->id],['user_id', auth()->user()->id]])->whereDate('created_at',$date)->count() ==0) {
                $docketTemplates[] = ['id' => $row->id, 'title' => $row->title];
            }
        }
        return $docketTemplates;
    }
}