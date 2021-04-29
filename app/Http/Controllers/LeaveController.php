<?php

namespace App\Http\Controllers;

use App\Helpers\V2\MessageDisplay;
use App\Http\Requests\LeaveStoreRequest;
use App\Services\V2\Web\LeaveService;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    protected $leaveService;
    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index(){
        return $this->leaveService->index();
    }

    public function store(LeaveStoreRequest $request){
        try {
            $response = $this->leaveService->store($request);
            if($response){
                toastr()->success(MessageDisplay::EmployeeLeaveAdded);
            }            
        } catch (\Exception $ex) {
            toastr()->error(MessageDisplay::ERROR);
        }
        return redirect()->back();
    }

    public function update(LeaveStoreRequest $request){
        try {
            $response = $this->leaveService->store($request);
            if($response){
                toastr()->success(MessageDisplay::EmployeeLeaveUpdate);
            }    
        } catch (\Exception $ex) {
            toastr()->error(MessageDisplay::ERROR);
        }
        return redirect()->back();
    }

    public function delete(Request $request){
        try {
            $this->leaveService->delete($request);
            return response()->json(['status' => true]);
        } catch (\Exception $ex) {
            return response()->json(['status' => false, 'errorMessage'=>MessageDisplay::ERROR]);
        }
    }

    public function getEmployeeLeaveById($id){
        try {
            $employeeLeave = $this->leaveService->getEmployeeLeaveByUserId($id);
            return response()->json(['status'=>true,'employee_leave'=>$employeeLeave]);
        } catch (\Exception $ex) {
            return response()->json(['status'=>false]);
        }
    }

    public function edit($id){
        try {
            $employeeLeave = $this->leaveService->getEmployeeLeaveById($id);
            if($employeeLeave != null){
                return response()->json(['status'=>true,'employee_leave'=>$employeeLeave]);
            }
            return response()->json(['status'=>false]);
        } catch (\Exception $ex) {
            return response()->json(['status'=>false]);
        }
    }
}
