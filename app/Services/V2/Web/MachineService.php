<?php
namespace App\Services\V2\Web;

use App\Repositories\V2\AssignDocketUserConnectionRepository;
use App\Repositories\V2\MachineRepository;
use Carbon\Carbon;
use Session;
use Image;
use App\Helpers\V2\FunctionUtils;

class MachineService {

    protected $machineRepository,$assignDocketUserConnectionRepository;

    public function __construct(MachineRepository $machineRepository,
        AssignDocketUserConnectionRepository $assignDocketUserConnectionRepository)
    {
        $this->machineRepository = $machineRepository;
        $this->assignDocketUserConnectionRepository = $assignDocketUserConnectionRepository;
    }

    public function index(){
        return $this->machineRepository->getDataWhere([['company_id',auth()->user()->companyInfo->id]])->orderBy('id','desc')->get();
    }

    public function store($request){
        $request['company_id'] = auth()->user()->companyInfo->id;
        if($request->has('image_value')){
            $img = $request->image_value; 
            // $ext = $img->getClientOriginalExtension();
            // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."." . $ext;
            $dest = 'files/machine_images';
            // if(!is_dir($dest)){
            //     mkdir($dest);
            // }
            // $img->move($dest, $filename);

            // $image_resize = Image::make($img->getRealPath());              
            // $image_resize->resize(600, 600, function ($constraint) {
            //     $constraint->aspectRatio();
            // });
            // $image_resize->save(public_path($dest . '/' .$filename),100);
            // $request['image'] = asset($dest . '/' . $filename);

            $request['image'] = FunctionUtils::imageUpload($dest,$img);
        }
        $this->machineRepository->insertAndUpdate($request);
    }

    public function edit($id){
        $machine = $this->machineRepository->getDataWhere([['company_id',auth()->user()->companyInfo->id],['id',$id]])->first();
        return view('dashboard.company.machine.edit',compact('machine'));
    }

    public function update($request){
        $request['company_id'] = auth()->user()->companyInfo->id;
        if($request->has('image_value')){
            $img = $request->image_value; 
            // $ext = $img->getClientOriginalExtension();
            // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."." . $ext;
            $dest = 'files/machine_images';
            // if(!is_dir($dest)){
            //     mkdir($dest);
            // }
            // $img->move($dest, $filename);

            // $image_resize = Image::make($img->getRealPath());              
            // $image_resize->resize(600, 600, function ($constraint) {
            //     $constraint->aspectRatio();
            // });
            // $image_resize->save(public_path($dest . '/' .$filename));
            // $request['image'] = asset($dest . '/' . $filename);

            $request['image'] = FunctionUtils::imageUpload($dest,$img);
        }
        $this->machineRepository->insertAndUpdate($request);
    }


    public function delete($request){
        return $this->machineRepository->deleteDataById($request);
    }

    public function machineAvailability(){
        $machineId = $this->machineRepository->getDataWhere([['company_id',Session::get('company_id')]])->pluck('id');
        $assignDocketUserConnection = $this->assignDocketUserConnectionRepository->getDataWhereIn('machine_id',$machineId)->groupBy('assign_docket_id')
                                            ->with('assignDocketUser','machine')->get();
        $assignDocketUserConnection->map(function ($data) {
            $data['startDate'] = Carbon::parse($data->assignDocketUser->from_date)->toDateString();
            $data['endDate'] = Carbon::parse($data->assignDocketUser->to_date)->toDateString();
            $data['title'] = $data->assignDocketUser->name;
            $data['name'] = $data->machine->name;
            $data['image'] = $data->machine->image;
            $data['customClass'] = 'greenClass';
            return $data;
        });
        $data = json_encode($assignDocketUserConnection);
        return view('dashboard.company.machine.availability',compact('data'));
    }
}