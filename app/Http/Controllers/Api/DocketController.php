<?php

namespace App\Http\Controllers\Api;

use App\DocketFiledPreFiller;
use App\DocketGridAutoPrefiller;
use App\DocketGridPrefiller;
use App\DocketPrefillerValue;
use App\Helpers\V2\MessageDisplay;
use App\Repositories\DocketRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V2\Api\DocketService;

class DocketController extends Controller
{
    /**
     * @var DocketRepository
     */
    protected $docketService;
    public function __construct(DocketService $docketService)
    {
        $this->docketService = $docketService;   
    }

    function buildTreeArray(array $prefiller, $parentId = 0) {
        $branch = array();
        foreach ($prefiller as $prefillers) {
            if ($prefillers['root_id'] == $parentId) {
                $children = $this->buildTreeArray($prefiller, $prefillers['id']);
                if ($children) {
                    $prefillers['prefiller'] = $children;
                }else{
                    $prefillers['prefiller'] =[];
                }
                $branch[] = $prefillers;
            }
        }
        return $branch;
    }

    function buildAutoPrefillerTreeArray(array $prefiller, $parentId = 0){
        $branch = array();
        foreach ($prefiller as $prefillers) {
            $autoPrefillerLinkedGridId =  DocketGridAutoPrefiller::where('grid_field_id',$prefillers['docket_field_grid_id'])->where('docket_field_id',$prefillers['docket_field_id'])->where('index',$prefillers['index'])->get();
            if ($prefillers['root_id'] == $parentId) {
                $children = $this->buildAutoPrefillerTreeArray($prefiller, $prefillers['id']);
                if ($children) {
                    $prefillers['prefiller'] = $children;
                }else{
                    $prefillers['prefiller'] =[];
                }
                if($prefillers['root_id']==0){
                    $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>$prefillers['docket_field_grid_id'],'prefiller'=>$prefillers['prefiller']);
                }else{
                    if($autoPrefillerLinkedGridId->first()->link_grid_field_id != null){
                        $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>@$autoPrefillerLinkedGridId->first()->link_grid_field_id,'prefiller'=>$prefillers['prefiller']);
                    }
                }
            }
        }
        return $branch;
    }

    public function getDocketTemplateList(Request $request){
        return $this->docketService->getDocketTemplateList($request);
    }

    public function getAssignedDocketTemplateByUserId(Request $request){
        $docketTemplate = $this->docketService->getAssignedDocketTemplateByUserId($request);
        return response()->json(array('status' => true, 'docketTemplate' => $docketTemplate,'message_status'=>1));
    }

    function array_values_recursive($ary){
        $lst = array();
        foreach( array_keys($ary) as $k ){
            $v = $ary[$k];
            if (is_scalar($v)) { $lst[] = $v;}
            elseif (is_array($v)) {
                $lst = array_merge( $lst, $this->array_values_recursive($v));
            }
        }
        return $lst;
    }

    public function getParentData($data){
        $docketPrefillerValues = DocketGridPrefiller::where('id',$data)->select('id','root_id','value')->get();
        $child =array();
        if (count($docketPrefillerValues)!=0){
            foreach ($docketPrefillerValues as $datass){
                $child[]    = $datass['value'];
                $child[]= $this->getparentData($datass->root_id);
            }
        }
        return $child;
    }

    public function getDocketPrefiller($data){
        $docketPrefillerValues = DocketPrefillerValue::where('id',$data)->select('id','root_id','label')->get();
        $child =array();
        if (count($docketPrefillerValues)!=0){
            foreach ($docketPrefillerValues as $datass){
                $child[]    = $datass['label'];
                $child[]= $this->getDocketPrefiller($datass->root_id);
            }
        }
        return $child;
    }

    public function getNormalParentData($data){
        $docketPrefillerValues = DocketFiledPreFiller::where('id',$data)->select('id','root_id','value')->get();
        $child =array();
        if (count($docketPrefillerValues)!=0){
            foreach ($docketPrefillerValues as $datass){
                $child[]    = $datass['value'];
                $child[]= $this->getNormalParentData($datass->root_id);
            }
        }
        return $child;
    }

    public function getDocketTemplateDetailsById(Request $request,$docketId){

        return $this->docketService->getDocketTemplateDetailsById($request,$docketId);
    }

    public function getDefaultDocket(Request $request){
        return $this->docketService->getDefaultDocket($request);
    }

    public function docket(Request $request,$id){
        return $this->docketService->docket($request,$id);
    }

    public function update(Request $request,$id){
        $response = $this->docketService->docketUpdate($request,$id);
        if($response == MessageDisplay::ERROR){
            return response()->json(array('status' => false,'message' => MessageDisplay::ERROR));
        }else{
            return response()->json(array('status' => true,'message' => MessageDisplay::DocketUpdated));
        }
    }
}
