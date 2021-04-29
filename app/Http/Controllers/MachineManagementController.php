<?php

namespace App\Http\Controllers;

use App\Helpers\V2\MessageDisplay;
use App\Http\Requests\MachineStoreRequest;
use App\Services\V2\Web\MachineService;
use Illuminate\Http\Request;

class MachineManagementController extends Controller
{
    protected $machineService;
    public function __construct(MachineService $machineService)
    {
        $this->machineService = $machineService;
    }

    public function index(){
        $machines = $this->machineService->index();
        return view('dashboard.company.machine.index',compact('machines'));
    }

    public function store(MachineStoreRequest $request){
        try {
            $this->machineService->store($request);
            toastr()->success(MessageDisplay::MachineAdded);
        } catch (\Exception $ex) {
            toastr()->error(MessageDisplay::ERROR);
        }
        return redirect()->back();
    }

    public function edit($id){
        return $this->machineService->edit($id);
    }

    public function update(MachineStoreRequest $request){
        try {
            $this->machineService->update($request);
            toastr()->success(MessageDisplay::MachineUpdate);
            return redirect()->route('machine_management.index');
        } catch (\Exception $ex) {
            toastr()->error(MessageDisplay::ERROR);
            return redirect()->back();
        }
    }

    public function delete(Request $request){
        try {
            $this->machineService->delete($request);
            toastr()->success(MessageDisplay::MachineDelete);
        } catch (\Exception $ex) {
            toastr()->error(MessageDisplay::ERROR);
        }
        return redirect()->back();
    }

    public function machineAvailability(){
        return $this->machineService->machineAvailability();
    }
}
