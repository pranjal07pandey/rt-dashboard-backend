<?php

namespace App\Http\Controllers\Docket;

use App\EmailSentDocket;
use App\SentDocketLabel;
use App\SentDockets;
use App\SentEmailDocketLabel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LabelController extends Controller
{
    public function assign(Request $request){
        //old function "FolderController"->folderLabelSave

        if($request->value == null && $request->has('value') == false){
            return response()->json(['status'=>false,'message'=>'Invalid action ! Please select Docket Label.']);
        }else{
            if($request->type == 1){
                $sentDocket     =   SentDockets::where('id',$request->id)->get();
                if($sentDocket->count()==0){
                    return response()->json(['status'=>false,'message'=>'Invalid action ! Docket not found.']);
                }
                $sentDocket =   $sentDocket->first();

                $sentDocketLabels = array();
                foreach ($request->value as $docketLabel) {
                    if(SentDocketLabel::where('sent_docket_id',$sentDocket->id)->where('docket_label_id',$docketLabel)->count()==0) {
                        $sentDocketLabel = new SentDocketLabel();
                        $sentDocketLabel->sent_docket_id = $sentDocket->id;
                        $sentDocketLabel->docket_label_id = $docketLabel;
                        $sentDocketLabel->save();
                        $sentDocketLabels[] = $sentDocketLabel;
                    }
                }
                $type   =   $request->type;
                $html   =  view('dashboard.company.docketManager.partials.table-view.docket-label', compact('sentDocketLabels', 'type'))->render();
                return response()->json(['status'=>true,'message'=>'Docket label attached successfully','html'=> $html,'id'=>"docketLabelIdentify".$sentDocket->id]);
            }else if($request->type == 2){
                $emailSentDocket     =   EmailSentDocket::where('id',$request->id)->get();
                if($emailSentDocket->count()==0){
                    return response()->json(['status'=>false,'message'=>'Invalid action ! Docket not found.']);
                }
                $emailSentDocket =   $emailSentDocket->first();

                $sentDocketLabels = array();
                foreach ($request->value as $docketLabel) {
                    if(SentEmailDocketLabel::where('email_sent_docket_id',$emailSentDocket->id)->where('docket_label_id',$docketLabel)->count()==0) {
                        $sentEmailDocketLabel = new SentEmailDocketLabel();
                        $sentEmailDocketLabel->email_sent_docket_id = $emailSentDocket->id;
                        $sentEmailDocketLabel->docket_label_id = $docketLabel;
                        $sentEmailDocketLabel->save();
                        $sentDocketLabels[] = $sentEmailDocketLabel;
                    }
                }
                $type   =   $request->type;
                $html   =  view('dashboard.company.docketManager.partials.table-view.docket-label', compact('sentDocketLabels', 'type'))->render();
                return response()->json(['status'=>true,'message'=>'Docket label attached successfully','html'=> $html,'id'=>"emailDocketLabelIdentify".$emailSentDocket->id]);
            }
        }
    }

    public function delete(Request $request){
        if($request->type == 1){
            $sentDocketLabel    =   SentDocketLabel::where('id', $request->id)->get();
            if($sentDocketLabel->count()==0){
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
            $sentDocketLabel    =   $sentDocketLabel->first();
            if($sentDocketLabel->docketLabel->company_id!=Auth::user()->company()->id) {
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
            $sentDocketLabel->delete();
            return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.', 'id'=> $request->id ]);
        }else if ($request->type == 2){
            $sentEmailDocketLabel    =   SentEmailDocketLabel::where('id', $request->id)->get();
            if($sentEmailDocketLabel->count()==0){
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
            $sentEmailDocketLabel    =   $sentEmailDocketLabel->first();
            if($sentEmailDocketLabel->docketLabel->company_id!=Session::get('company_id')) {
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
            $sentEmailDocketLabel->delete();
            return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.', 'id'=> $request->id ]);
        }
    }
}
