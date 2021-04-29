<?php

namespace App\Http\Controllers;

use App\Client;
use App\DocketField;
use App\DocketFieldGrid;
use App\DocketGridPrefiller;
use App\DocketPrefiller;
use App\DocketPrefillerValue;
use App\Email_Client;
use App\Employee;
use App\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use League\Csv\Reader;
use overint\MailgunValidator;
use Session;
use Validator;
use function React\Promise\all;
use App\Helpers\V2\FunctionUtils;


class PrefillerManagerController extends Controller
{
    private $prefillerFirstRow;

    public function index()
    {
        $this->updateDefaultPrefiller();
        $docketPrefiller = DocketPrefiller::where('company_id', Session::get('company_id'))->orderby('id', 'asc')->get();
        $prefillerLabel = DocketPrefillerValue::orderby('id', 'asc')->get();
        return view('dashboard.company.docketManager.prefillerManager.index', compact('docketPrefiller', 'prefillerLabel'));
    }

    public function updateDefaultPrefiller()
    {
        $employeeListQuery = DocketPrefiller::where('company_id', Session::get('company_id'))->where('type', 1);
        $clientListQuery = DocketPrefiller::where('company_id', Session::get('company_id'))->where('type', 2);

        $employeeQuery = Employee::where('company_id', Session::get('company_id'))->get();
        $employeeListArray = array();
        foreach ($employeeQuery as $row) {
            if (@$row->userInfo->isActive == 1)
                $employeeListArray[] = @$row->userInfo->first_name . " " . @$row->userInfo->last_name;
        }
        $clientQuery = Client::where('company_id', Session::get('company_id'))->orWhere('requested_company_id', Session::get('company_id'))->get();
        $emailClientQuery = Email_Client::where('company_id', Session::get('company_id'))->Where('company_name', '!=', "")->get();
        $clientListArray = array();
        $emailClientArray = array();
        foreach ($clientQuery as $client) {
            if ($client->company_id == Session::get('company_id')) {
                $clientListArray[] = $client->requestedCompanyInfo->name;
            }
            if ($client->requested_company_id == Session::get('company_id')) {
                $clientListArray[] = $client->companyInfo->name;
            }
        }
        foreach ($emailClientQuery as $email) {
            $emailClientArray[] = $email->company_name;

        }
        $totalClient = array_merge($clientListArray, $emailClientArray);


        if ($employeeListQuery->count() > 0) {
            $oldPrefillerData = $employeeListQuery->first();
            if (count($oldPrefillerData->docketPrefillerValue) > 0) {
                foreach ($oldPrefillerData->docketPrefillerValue as $oldData) {
                    if (!in_array($oldData->label, $employeeListArray)) {
                        $oldData->delete();
                    } else {
                        $this->deleteElement($oldData->label, $employeeListArray);
                    }
                }
            }
        } else {
            $employeeDocketPrefiller = new DocketPrefiller();
            $employeeDocketPrefiller->title = "Employee List";
            $employeeDocketPrefiller->company_id = Session::get('company_id');
            $employeeDocketPrefiller->type = 1;
            $employeeDocketPrefiller->user_id = Auth::user()->id;
            $employeeDocketPrefiller->save();
            $oldPrefillerData = $employeeDocketPrefiller;
        }

        foreach ($employeeListArray as $employee) {
            $prefillerValue = new DocketPrefillerValue();
            $prefillerValue->docket_prefiller_id = $oldPrefillerData->id;
            $prefillerValue->label = $employee;
            $prefillerValue->save();
        }

        if ($clientListQuery->count() > 0) {
            $oldPrefillerData = $clientListQuery->first();
            if (count($oldPrefillerData->docketPrefillerValue) > 0) {
                foreach ($oldPrefillerData->docketPrefillerValue as $oldData) {
                    if (!in_array($oldData->label, $totalClient)) {
                        $oldData->delete();
                    } else {
                        $this->deleteElement($oldData->label, $totalClient);
                    }
                }
            }

        } else {
            $clientDocketPrefiller = new DocketPrefiller();
            $clientDocketPrefiller->title = "Client List";
            $clientDocketPrefiller->company_id = Session::get('company_id');
            $clientDocketPrefiller->type = 2;
            $clientDocketPrefiller->user_id = Auth::user()->id;
            $clientDocketPrefiller->save();
            $oldPrefillerData = $clientDocketPrefiller;
        }
        foreach ($totalClient as $clients) {
            $prefillerValue = new DocketPrefillerValue();
            $prefillerValue->docket_prefiller_id = $oldPrefillerData->id;
            $prefillerValue->label = $clients;
            $prefillerValue->save();
        }
    }

    function deleteElement($element, &$array)
    {
        $index = array_search($element, $array);
        if ($index !== false) {
            unset($array[$index]);
        }
    }

    public function savePrefillerManager(Request $request)
    {
        $this->validate($request, ['title' => 'required']);
        if (DocketPrefiller::where('title', $request->title)->where('company_id', Session::get('company_id'))->count() != 0) {
            $message = 'The title "' . $request->title . '" has already been taken.';
            return response()->json(['status' => false, 'message' => $message]);
        } else {
            $addprefillerCategory = new DocketPrefiller;
            $addprefillerCategory->title = $request->title;
            $addprefillerCategory->user_id = Auth::user()->id;
            $addprefillerCategory->company_id = Session::get('company_id');
            $addprefillerCategory->is_integer = $request->is_integer;
            $addprefillerCategory->type = 0;
            $addprefillerCategory->save();
            return response()->json(['status' => true, 'label' => $addprefillerCategory->title, 'prefillerManagerId' => $addprefillerCategory->id, 'isInteger' => $addprefillerCategory->is_integer]);
        }
    }


    public function saveParentPrefillerLabel(Request $request)
    {
        $docketPrefillerId = $request->prefillerManagerId;

        $rootId = $request->rootId;
        $index = $request->index;
        $fieldType = $request->fieldType;

        if($fieldType == 0){
            $validator = Validator::make(Input::all(), ['value'   => 'required']);
            if ($validator->fails()) {
                foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                    $errors[] = $messages[0];
                }
                return response()->json(array('status' => false, 'message' => $errors));
            }else{
                $value = $request->value;
            }

        }else if($fieldType == 1){
            $validator = Validator::make(Input::all(), ['value'   => 'required|Int']);
            if ($validator->fails()) {
                foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                    $errors[] = $messages[0];
                }
                return response()->json(array('status' => false, 'message' => $errors));
            }else{
                $value = $request->value;
            }
        }else if($fieldType == 2){
            $validator = Validator::make(Input::all(), ['value'   => 'required|email']);
            if ($validator->fails()) {
                foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                    $errors[] = $messages[0];
                }
                return response()->json(array('status' => false, 'message' => $errors));
            }else{
                $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
                if($validator->validate($request->value)) {
                    $value = $request->value;
                }else{
                    return response()->json(array('status' => false, 'message' => "Invalid Email address."));
                }
            }

        }else{
            return response()->json(['status' => false, 'message' => "Invalid Data"]);

        }
        $docketFiledPreFiller = new DocketPrefillerValue();
        $docketFiledPreFiller->label = $value;
        $docketFiledPreFiller->root_id = $rootId;
        $docketFiledPreFiller->docket_prefiller_id = $docketPrefillerId;
        $docketFiledPreFiller->index = $index + 1;
        $docketFiledPreFiller->save();


        $prefiller = array();
        $docketPreFiller = DocketPrefillerValue::where('docket_prefiller_id', $docketPrefillerId)->get();

        foreach ($docketPreFiller as $subRow):
            $prefiller[] = array(
                'id' => $subRow->id,
                'value' => $subRow->label,
                'root_id' => $subRow->root_id,
                'index' => $subRow->index,
                'docket_prefiller_id' => $subRow->docket_prefiller_id,
            );
        endforeach;


        $urlUpdatePreFiller = url('dashboard/company/docketManager/prefillerManager/updatePrifillerManagerlabel');
        $datas = $this->buildTreeArray($prefiller);
        $finalPrefillerView = array();


        foreach ((new Collection($datas))->sortBy('value') as $row) {
            $prefillerForMaxIndex = DocketPrefillerValue::where('docket_prefiller_id', $row['docket_prefiller_id'])->pluck('index')->toArray();

//            $finalPrefillerMaxIndex = max($prefillerForMaxIndex);
//            $docketField = DocketPrefiller::where('id', $row['docket_prefiller_id'])->first();

//            $defaultPrefillerId =unserialize($docketField->default_prefiller_id);
            $final = "";
            $this->prefillerFirstRow = true;
            $final .= '<tr><td>';
//            if ($finalPrefillerMaxIndex == $row['index']) {
//                $final .= '<div class="prefillercontent"><a href="#" id="shortText" class="editabledocketprefiller" data-type="text" data-pk="' . $row['id'] . '" data-url="' . $urlUpdatePreFiller . '" data-title="Enter Label Text">' . $row['value'] . '</a>   <a    id="deleteprefillerManagerLabel" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '" class="btn btn-raised btn-danger btn-xs btnprefiller"  style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 18px;background: #FF5722;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a>  <a data-toggle="modal" data-target="#addPrefillerValue" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '" data-index="' . $row['index'] . '"  class="btn btn-raised btn-danger btn-xs btnprefiller" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 3px;background: #4395bb;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-plus" aria-hidden="true" /></a></div> </td>';
//            } else {
                $final .= '<div class="prefillercontent"><a href="#" id="shortText" class="editabledocketprefiller" data-type="text" data-pk="' . $row['id'] . '" data-url="' . $urlUpdatePreFiller . '" data-title="Enter Label Text">' . $row['value'] . '</a>   <a   id="deleteprefillerManagerLabel" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '" class="btn btn-raised btn-danger btn-xs btnprefiller"  style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 18px;background: #FF5722;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a> <a data-toggle="modal" data-target="#addPrefillerValue" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '"  data-index="' . $row['index'] . '" class="btn btn-raised btn-danger btn-xs btnprefiller" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 3px;background: #4395bb;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-plus" aria-hidden="true" /></a></div> </td>';
//            }
            if (count($row["prefiller"]) > 0) {
                $final .= $this->getDocketPrefillerChild($row["prefiller"]);
            }

            $finalPrefillerView[] = array(
                'id' => $row['docket_prefiller_id'],
                'final' => $final
            );
        }


        $message = 'Filler Added successfully.';
        return response()->json(['status' => true, 'message' => $message, 'finalPrefillerView' => $finalPrefillerView]);
    }


    public function getDocketPrefillerChild($prefiller)
    {
        $child = "";
        $urlUpdatePreFiller = url('dashboard/company/docketManager/prefillerManager/updatePrifillerManagerlabel');
        foreach ($prefiller as $item) {

//            $prefillerForMaxIndex = DocketPrefillerValue::where('docket_prefiller_id', $item['docket_prefiller_id'])->pluck('index')->toArray();
//            $finalPrefillerMaxIndex = max($prefillerForMaxIndex);
//            if (!$this->prefillerFirstRow) {
//                $child .= "<tr>";
//                for ($i = 1; $i < $item['index']; $i++) {
//                    $child .= "<td></td>";
//                }
//            }
//            if ($finalPrefillerMaxIndex == $item['index']) {
//                $child .= '<td><div class="prefillercontent"> <i style="font-size: 9px;color: #19afba;" class="fa fa-chevron-right" aria-hidden="true"></i> <a href="#" id="shortText" class="editabledocketprefiller" data-type="text" data-pk="' . $item['id'] . '" data-url="' . $urlUpdatePreFiller . '" data-title="Enter Label Text">' . $item['value'] . '</a> <a   id="deleteprefillerManagerLabel" data-id="' . $item['id'] . '" data-docketPrefillerManagerId="' . $item['docket_prefiller_id'] . '"  class="btn btn-raised btn-danger btn-xs btnprefiller"  style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin:11px 18px;background: #FF5722;position: absolute;box-shadow: none;top: 0; right: 0;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a> <a data-toggle="modal" data-target="#addPrefillerValue" data-id="' . $item['id'] . '" data-docketPrefillerManagerId="' . $item['docket_prefiller_id'] . '"   data-index="' . $item['index'] . '"   class="btn btn-raised btn-danger btn-xs btnprefiller"  style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 3px;background: #4395bb;position: absolute;box-shadow: none; top: 0; right: 0"><span class="glyphicon glyphicon-plus" aria-hidden="true" /></a></div>  </td>';
//
//            } else {
                $child .= '<td><div class="prefillercontent"> <i style="font-size: 9px;color: #19afba;" class="fa fa-chevron-right" aria-hidden="true"></i><a href="#" id="shortText" class="editabledocketprefiller" data-type="text" data-pk="' . $item['id'] . '" data-url="' . $urlUpdatePreFiller . '" data-title="Enter Label Text">' . $item['value'] . '</a> <a   id="deleteprefillerManagerLabel" data-id="' . $item['id'] . '" data-docketPrefillerManagerId="' . $item['docket_prefiller_id'] . '"  class="btn btn-raised btn-danger btn-xs btnprefiller"  style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin : 11px 18px;background: #FF5722;position: absolute;box-shadow: none;top: 0; right: 0;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a> <a data-toggle="modal" data-target="#addPrefillerValue" data-id="' . $item['id'] . '" data-docketPrefillerManagerId="' . $item['docket_prefiller_id'] . '"  data-index="' . $item['index'] . '"   class="btn btn-raised btn-danger btn-xs btnprefiller"   style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin:11px 3px;background: #4395bb;position: absolute;box-shadow: none; top: 0; right: 0; "><span class="glyphicon glyphicon-plus" aria-hidden="true" /></a></div>  </td>';

//            }
            if (count($item['prefiller']) > 0) {
                $this->prefillerFirstRow = true;
                $child .= $this->getDocketPrefillerChild($item['prefiller']);
            } else {
                $this->prefillerFirstRow = false;
                $child .= "</tr>";
            }
        }
        return $child;
    }

    public function getChildPrefillerManagerId($root_id)
    {
        $prefillerId = array();
        $prefillerId[] = intval($root_id);
        $query = DocketPrefillerValue::where('root_id', $root_id);
        if ($query->count() > 0) {
            foreach ($query->get() as $items) {
                $prefillerId[] = $items->id;
                if (DocketPrefillerValue::where('root_id', $items->id)->count() > 0) {
                    $prefillerId = array_merge($prefillerId, $this->getChildPrefillerManagerId($items->id));
                }
            }
        } else {
            $prefillerId[] = $root_id;
        }
        return $prefillerId;

    }

    function buildTreeArray(array $prefiller, $parentId = 0)
    {
        $branch = array();
        foreach ($prefiller as $prefillers) {
            if ($prefillers['root_id'] == $parentId) {
                $children = $this->buildTreeArray($prefiller, $prefillers['id']);
                if ($children) {
                    $prefillers['prefiller'] = $children;
                } else {
                    $prefillers['prefiller'] = [];
                }
                $branch[] = $prefillers;
            }
        }
        return $branch;
    }


    public function deletePrefillerManagerlabel(Request $request)
    {
        $this->validate($request, ['prefiller_manager_id' => 'required', 'label_id' => 'required']);
        $root_id = $request->label_id;
        $prefiller_manager_id = $request->prefiller_manager_id;
//            $tempDocketId = DocketFieldGrid::where('id',$request->docket_grid_field_id)->first()->docket_field_id;
        $prefillerId = $this->getChildPrefillerManagerId($root_id);
        DocketPrefillerValue::whereIn('id', array_unique($prefillerId))->delete();

        $docketPreFiller = DocketPrefillerValue::where('docket_prefiller_id', $prefiller_manager_id)->get();
        $prefiller = array();
        foreach ($docketPreFiller as $subRow):
            $prefiller[] = array(
                'id' => $subRow->id,
                'value' => $subRow->label,
                'root_id' => $subRow->root_id,
                'index' => $subRow->index,
                'docket_prefiller_id' => $subRow->docket_prefiller_id,
            );
        endforeach;


        $urlUpdatePreFiller = url('dashboard/company/docketManager/prefillerManager/updatePrifillerManagerlabel');
        $datas = $this->buildTreeArray($prefiller);
        $finalPrefillerView = array();


        foreach ((new Collection($datas))->sortBy('value') as $row) {
            $prefillerForMaxIndex = DocketPrefillerValue::where('docket_prefiller_id', $row['docket_prefiller_id'])->pluck('index')->toArray();

//            $finalPrefillerMaxIndex = max($prefillerForMaxIndex);
//            $docketField = DocketPrefiller::where('id', $row['docket_prefiller_id'])->first();
            $final = "";
            $this->prefillerFirstRow = true;
            $final .= '<tr><td>';
//            if ($finalPrefillerMaxIndex == $row['index']) {
//                $final .= '<div class="prefillercontent"><a href="#" id="shortText" class="editabledocketprefiller" data-type="text" data-pk="' . $row['id'] . '" data-url="' . $urlUpdatePreFiller . '" data-title="Enter Label Text">' . $row['value'] . '</a>   <a    id="deleteprefillerManagerLabel" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '" class="btn btn-raised btn-danger btn-xs btnprefiller"  style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 18px;background: #FF5722;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a>  <a data-toggle="modal" data-target="#addPrefillerValue" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '" data-index="' . $row['index'] . '"  class="btn btn-raised btn-danger btn-xs btnprefiller" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 3px;background: #4395bb;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-plus" aria-hidden="true" /></a></div> </td>';
//            } else {
                $final .= '<div class="prefillercontent"><a href="#" id="shortText" class="editabledocketprefiller" data-type="text" data-pk="' . $row['id'] . '" data-url="' . $urlUpdatePreFiller . '" data-title="Enter Label Text">' . $row['value'] . '</a>   <a   id="deleteprefillerManagerLabel" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '" class="btn btn-raised btn-danger btn-xs btnprefiller"  style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 18px;background: #FF5722;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a> <a data-toggle="modal" data-target="#addPrefillerValue" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '"  data-index="' . $row['index'] . '" class="btn btn-raised btn-danger btn-xs btnprefiller" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 3px;background: #4395bb;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-plus" aria-hidden="true" /></a></div> </td>';
//            }
            if (count($row["prefiller"]) > 0) {
                $final .= $this->getDocketPrefillerChild($row["prefiller"]);
            }

            $finalPrefillerView[] = array(
                'id' => $row['docket_prefiller_id'],
                'final' => $final
            );
        }
        $message = 'Filler Added successfully.';
        return response()->json(['status' => true, 'message' => $message, 'finalPrefillerView' => $finalPrefillerView]);
    }

    public function clearAllPrefillerManager(Request $request){
        $this->validate($request, ['prefillerManagerId' => 'required']);
        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$request->prefillerManagerId)->pluck('id')->toArray();
        if (count($docketPrefillerValue)!= 0){
            DocketPrefillerValue::whereIn('id',$docketPrefillerValue)->delete();
            return response()->json(['status' => true]);
        }else{
            return response()->json(['status' => false]);
        }
    }

    public function deletePrefillerManager(Request $request){
        $this->validate($request, ['id' => 'required']);
        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$request->id)->pluck('id')->toArray();
        if (count($docketPrefillerValue)!= 0){
            DocketPrefillerValue::whereIn('id',$docketPrefillerValue)->delete();
            DocketPrefiller::where('id',$request->id)->delete();
            flash('Prefiller deleted successfully.','success');
            return redirect()->back();
        }else{
            DocketPrefiller::where('id',$request->id)->delete();
            flash('Prefiller deleted successfully.','success');
            return redirect()->back();
        }
    }

    public function checkPrefillerManager(Request $request){
        ini_set('memory_limit','256M');
        set_time_limit(320);


        $this->validate($request, ['prefillerManagerId' => 'required']);
        $docketPrefillerId = $request->prefillerManagerId;
        $prefiller = array();
        $docketPreFiller = DocketPrefillerValue::where('docket_prefiller_id', $docketPrefillerId)->get();

        foreach ($docketPreFiller as $subRow):
            $prefiller[] = array(
                'id' => $subRow->id,
                'value' => $subRow->label,
                'root_id' => $subRow->root_id,
                'index' => $subRow->index,
                'docket_prefiller_id' => $subRow->docket_prefiller_id,
            );
        endforeach;



        $urlUpdatePreFiller = url('dashboard/company/docketManager/prefillerManager/updatePrifillerManagerlabel');
        $datas = $this->buildTreeArray($prefiller);
        $finalPrefillerView = array();

        foreach ((new Collection($datas))->sortBy('value') as $row) {
//            $prefillerForMaxIndex = DocketPrefillerValue::where('docket_prefiller_id', $row['docket_prefiller_id'])->pluck('index')->toArray();
//
//            $finalPrefillerMaxIndex = max($prefillerForMaxIndex);
            $final = "";
            $this->prefillerFirstRow = true;
            $final .= '<tr><td>';
//            if ($finalPrefillerMaxIndex == $row['index']) {
//                $final .= '<div class="prefillercontent"><a href="#" id="shortText" class="editabledocketprefiller" data-type="text" data-pk="' . $row['id'] . '" data-url="' . $urlUpdatePreFiller . '" data-title="Enter Label Text">' . $row['value'] . '</a>   <a    id="deleteprefillerManagerLabel" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '" class="btn btn-raised btn-danger btn-xs btnprefiller"  style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 18px;background: #FF5722;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a>  <a data-toggle="modal" data-target="#addPrefillerValue" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '" data-index="' . $row['index'] . '"  class="btn btn-raised btn-danger btn-xs btnprefiller" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 3px;background: #4395bb;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-plus" aria-hidden="true" /></a></div> </td>';
//            } else {
                $final .= '<div class="prefillercontent"><a href="#" id="shortText" class="editabledocketprefiller" data-type="text" data-pk="' . $row['id'] . '" data-url="' . $urlUpdatePreFiller . '" data-title="Enter Label Text">' . $row['value'] . '</a>   <a   id="deleteprefillerManagerLabel" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '" class="btn btn-raised btn-danger btn-xs btnprefiller"  style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 18px;background: #FF5722;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a> <a data-toggle="modal" data-target="#addPrefillerValue" data-id="' . $row['id'] . '" data-docketPrefillerManagerId ="' . $row['docket_prefiller_id'] . '"  data-index="' . $row['index'] . '" class="btn btn-raised btn-danger btn-xs btnprefiller" style="font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 11px 3px;background: #4395bb;position: absolute;box-shadow: none; top: 0; right: 0;"><span class="glyphicon glyphicon-plus" aria-hidden="true" /></a></div> </td>';
//            }
            if (count($row["prefiller"]) > 0) {
                $final .= $this->getDocketPrefillerChild($row["prefiller"]);
            }

            $finalPrefillerView[] = array(
                'id' => $row['docket_prefiller_id'],
                'final' => $final
            );
        }

        $message = 'Filler Added successfully.';
        return response()->json(['status' => true, 'message' => $message, 'finalPrefillerView' => $finalPrefillerView]);
    }


    public  function updatePrifillerTitle(Request $request){
        $this->validate($request, ['pk' => 'required','value'=>'required']);
        $docketPrefiller = DocketPrefiller::where('id',$request->pk)->update(['title'=>$request->value]);
    }

    public function updatePrifillerManagerlabel(Request $request){
        $this->validate($request, ['pk' => 'required','value'=>'required']);
        $docketPrefiller = DocketPrefillerValue::where('id',$request->pk)->update(['label'=>$request->value]);
    }
    public function prefillerDataUpdate(Request $request){


        $prefillerManager = new DocketPrefiller();
        $prefillerManager->title = "Clients";
        $prefillerManager->company_id = Session::get('company_id');
        $prefillerManager->user_id = Auth::user()->id;
        $prefillerManager->type = 0;
        $prefillerManager->is_integer = 0;
        if ($prefillerManager->save()){
            foreach ($request->data as $data){
                $preval = new DocketPrefillerValue();
                $preval->docket_prefiller_id = $prefillerManager->id;
                $preval->label = $data;
                $preval->index = 0;
                $preval->root_id = 0;
                $preval->save();
            }
        }
        return redirect()->back();
    }
    public  function uploadExcelFile(Request $request){

        $fileid              =   Input::file('fileid');
        // $ext = $fileid->getClientOriginalExtension();
        // $filename = basename($request->file('fileid')->getClientOriginalName(), '.' . $request->file('fileid')->getClientOriginalExtension()).'.'.$ext;
        $dest = 'files/prefiller';
        // $fileid->move($dest, $filename);
        $filePath = FunctionUtils::imageUpload($dest,$fileid);
        // if ( $xls = \SimpleXLSX::parseFile($dest . '/' . $filename) ) {
        if ( $xls = \SimpleXLSX::parseFile($filePath) ) {
            $prefillerManager = new DocketPrefiller();
            $prefillerManager->title = "test";
            $prefillerManager->company_id = Session::get('company_id');
            $prefillerManager->user_id = Auth::user()->id;
            $prefillerManager->type = 0;
            $prefillerManager->is_integer = 0;
            if($prefillerManager->save()){
                $arraydata = array();
                $header = array();
                foreach ($xls->rows() as $key =>$data) {
                    $value = array();
                    if ($key == 0) {
                        foreach ($data as $KEY => $row) {
                            if ($row != "") {
                                $header[] = $KEY;
                            }
                        }
                    }else{
                        foreach ($data as $KEY => $rows) {
                            if(in_array($KEY,$header)){
                                $value[] = $rows;
                            }
                        }
                    }
                    $uniquevalue = array_unique($value);
                    if(count($uniquevalue) == 1){
                        if($uniquevalue[0] != ""){
                            $arraydata[]= $value;
                        }
                    }else{
                        $arraydata[]= $value;
                    }
                }
                foreach ($arraydata as $key=>$arraydatas){
                    $i = 0;
                    $nextrootId = 0;
                    if($key!= 0){
                        foreach ($arraydatas as $row){
                            $preval = new DocketPrefillerValue();
                            $preval->docket_prefiller_id = $prefillerManager->id;
                            $preval->label = $row;
                            $preval->index = $i;
                            $preval->root_id = $nextrootId;
                            $preval->save();
                            $nextrootId = $preval->id;
                            $i++;
                        }
                    }


                }
            }






        } else {
            dd(\SimpleXLSX::parseError());
        }
    }


}
