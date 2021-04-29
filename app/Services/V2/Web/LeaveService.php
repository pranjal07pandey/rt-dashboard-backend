<?php
namespace App\Services\V2\Web;

use App\Repositories\V2\AssignDocketUserConnectionRepository;
use App\Repositories\V2\AssignDocketUserRepository;
use App\Repositories\V2\LeaveRepository;
use App\Repositories\V2\EmployeeRepository;
use App\Repositories\V2\CompanyRepository;
use App\Repositories\V2\MachineRepository;
use App\Repositories\V2\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\V2\AmazoneBucket;
class LeaveService {

    protected $leaveRepository,$employeeRepository,$companyRepository,$machineRepository,
        $assignDocketUserRepository,$assignDocketUserConnectionRepository,$userRepository;

    public function __construct(LeaveRepository $leaveRepository,EmployeeRepository $employeeRepository,
        CompanyRepository $companyRepository,MachineRepository $machineRepository,UserRepository $userRepository,
        AssignDocketUserRepository $assignDocketUserRepository, AssignDocketUserConnectionRepository $assignDocketUserConnectionRepository)
    {
        $this->leaveRepository = $leaveRepository;
        $this->employeeRepository = $employeeRepository;
        $this->companyRepository = $companyRepository;
        $this->machineRepository = $machineRepository;
        $this->assignDocketUserRepository = $assignDocketUserRepository;
        $this->assignDocketUserConnectionRepository = $assignDocketUserConnectionRepository;
        $this->userRepository = $userRepository;
    }

    function index(){
        $company = $this->companyRepository->getDataWhere([['user_id',auth()->user()->id]])->first();
        $machines = $this->machineRepository->getDataWhere([['company_id',auth()->user()->companyInfo->id]])->get();
        $employees = $this->employeeRepository->getDataWhere([['employees.company_id',$company->id],['employees.employed',true]])
                                                ->join('users','users.id','employees.user_id')
                                                ->select('employees.user_id as user_id','users.first_name','users.last_name','users.email')->get();

        $data = $this->leaveRepository->getModel()->whereIn('leave_management.user_id',$employees->pluck('user_id'))
                                                ->join('users','users.id','leave_management.user_id')
                                                ->select('leave_management.id','leave_management.from_date as startDate','leave_management.to_date as endDate',
                                                'leave_management.description as title','users.first_name as first_name','users.last_name as last_name','users.image as image')
                                                ->get();
        $machineData = $this->leaveRepository->getModel()->whereIn('leave_management.machine_id',$machines->pluck('id'))
                                                ->join('machines','machines.id','leave_management.machine_id')
                                                ->select('leave_management.id','leave_management.from_date as startDate','leave_management.to_date as endDate',
                                                'leave_management.description as title','machines.name as name','machines.image as image')
                                                ->get();
        $data->map(function ($data) {
            $data['name'] = $data->first_name . ' ' . $data->last_name;
            $data['image'] = AmazoneBucket::url() . $data->image;
            $data['customClass'] = 'greenClass';
            return $data;
        });

        $machineData->map(function ($machineData) {
            $machineData['name'] = $machineData->name;
            $machineData['image'] = AmazoneBucket::url() . $machineData->image;
            $machineData['customClass'] = 'greenClass';
            return $machineData;
        });
        
        $data = $data->merge($machineData);
        $data = json_encode($data); //do order by name;
        return view('dashboard.company.leave.index',compact('data','employees','machines'));
    }

    public function store($request){
        $state = $this->validaiton($request);
        if(!$state){
            return $state;
        }
        $this->leaveRepository->insertAndUpdate($request);
        return true;
    }

    function delete($request){
        return $this->leaveRepository->deleteDataById($request);
    }

    function getEmployeeLeaveByUserId($id){
        return $this->leaveRepository->getDataWhere([['user_id',$id]])->get();
    }

    function getEmployeeLeaveById($id){
        return $this->leaveRepository->getDataWhere([['id',$id]])->first();
    }

    function validaiton($request){
        $state = true;
        if($request->user_id){
            $checkUser = $this->assignDocketUserRepository->getDataWhere([
                    [DB::raw('DATE_FORMAT(assign_docket_user.from_date,"%Y-%m-%d")'), "<=",Carbon::parse($request->from_date)],
                    [DB::raw('DATE_FORMAT(assign_docket_user.to_date,"%Y-%m-%d")'), ">=",Carbon::parse($request->from_date)]])
                    ->join('assign_docket_user_connection','assign_docket_user_connection.assign_docket_id','assign_docket_user.id')
                    ->groupBy('assign_docket_user_connection.assign_docket_id')
                    ->where('assign_docket_user_connection.user_id',$request->user_id)
                    ->first();
            if($checkUser == null){
                $checkUser = $this->assignDocketUserRepository->getDataWhere([
                    [DB::raw('DATE_FORMAT(assign_docket_user.from_date,"%Y-%m-%d")'), "<=",Carbon::parse($request->to_date)],
                    [DB::raw('DATE_FORMAT(assign_docket_user.to_date,"%Y-%m-%d")'), ">=",Carbon::parse($request->to_date)]])
                    ->join('assign_docket_user_connection','assign_docket_user_connection.assign_docket_id','assign_docket_user.id')
                    ->groupBy('assign_docket_user_connection.assign_docket_id')
                    ->where('assign_docket_user_connection.user_id',$request->user_id)
                    ->first();
            }
            if($checkUser){
                $user = $this->userRepository->getDataWhere([['id',$request->user_id]])->select('first_name','last_name')->first();
                toastr()->error($user->first_name. ' ' . $user->last_name . ' employee already have a task from '
                    .Carbon::parse($checkUser->from_date)->format('Y-m-d h:i A').' to ' . Carbon::parse($checkUser->to_date)->format('Y-m-d h:i A'));

                $state = false;
            }
        }
        if($request->machine_id){
            $checkMachine = $this->assignDocketUserRepository->getDataWhere([
                    [DB::raw('DATE_FORMAT(assign_docket_user.from_date,"%Y-%m-%d")'), "<=",Carbon::parse($request->from_date)->format('Y-m-d')],
                    [DB::raw('DATE_FORMAT(assign_docket_user.to_date,"%Y-%m-%d")'), ">=",Carbon::parse($request->from_date)->format('Y-m-d')]])
                    ->join('assign_docket_user_connection','assign_docket_user_connection.assign_docket_id','assign_docket_user.id')
                    ->groupBy('assign_docket_user_connection.assign_docket_id')
                    ->where('assign_docket_user_connection.machine_id',$request->machine_id)
                    ->first();
            if($checkMachine == null){
                $checkMachine = $this->assignDocketUserRepository->getDataWhere([
                    [DB::raw('DATE_FORMAT(assign_docket_user.from_date,"%Y-%m-%d")'), "<=",Carbon::parse($request->to_date)->format('Y-m-d')],
                    [DB::raw('DATE_FORMAT(assign_docket_user.to_date,"%Y-%m-%d")'), ">=",Carbon::parse($request->to_date)->format('Y-m-d')]])
                    ->join('assign_docket_user_connection','assign_docket_user_connection.assign_docket_id','assign_docket_user.id')
                    ->groupBy('assign_docket_user_connection.assign_docket_id')
                    ->where('assign_docket_user_connection.machine_id',$request->machine_id)
                    ->first();
            }
            if($checkMachine){
                $machine = $this->machineRepository->getDataWhere([['id',$request->machine_id]])->select('name')->first();
                toastr()->error($machine->name. ' machine already have a task from '
                    .Carbon::parse($checkMachine->from_date)->format('Y-m-d h:i A').' to ' . Carbon::parse($checkMachine->to_date)->format('Y-m-d h:i A'));

                $state = false;
            }
        }
        return $state;
    }
}