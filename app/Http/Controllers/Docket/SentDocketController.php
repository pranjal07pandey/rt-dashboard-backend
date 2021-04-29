<?php

namespace App\Http\Controllers\Docket;

use App\Docket;
use App\DocketField;
use App\EmailSentDocket;
use App\Folder;
use App\FolderItem;
use App\SentDockets;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SentDocketController extends Controller
{
    public function cancelDocket(Request $request){
        if ($request->type == 1){
            $sentDocket     =   SentDockets::where('id', $request->id)->get();
            if($sentDocket->count()==0){
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
            $sentDocket =   $sentDocket->first();

            if($sentDocket->sender_company_id==Session::get('company_id') && $sentDocket->is_cancel==0){
                $sentDocket->is_cancel  =   1;
                $sentDocket->save();
                return response()->json(['status'=>true,'message'=>'Docket cancelled successfully.' ,'id'=>$request->id]);
            }else{
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
        }else if ($request->type == 2){
        }
    }


    public function submitDeleteDocket(Request $request)
    {

        if ($request->type == 1) {
            SentDockets::destroy($request->id);
            $folders = Folder::where('company_id', Session::get('company_id'))->where('type', 1)->where('root_id', 0)->get();
            if (count($folders) == 0) {
                $folder = new Folder();
                $folder->name = "Trash (System)";
                $folder->slug = str_slug('Trash (System)');
                $folder->user_id = Auth::user()->id;
                $folder->status = 0;
                $folder->company_id = Session::get('company_id');
                $folder->type = 1;
                if ($folder->save()) {
                    SentDockets::where('id', $request->id)->update(['folder_status'=> 1]);
                    $folderItems = new FolderItem();
                    $folderItems->folder_id = $folder->id;
                    $folderItems->ref_id = $request->id;
                    $folderItems->type = 1;
                    $folderItems->user_id = Auth::user()->id;
                    $folderItems->status = 0;
                    $folderItems->company_id = Session::get('company_id');
                    $folderItems->save();
                }
                $totalItemsss = "";
                if (count($folder->folderItems) != 0) {
                    $totalItemsss = '(' . count($folder->folderItems) . ')';
                }
                return response()->json(['status' => true, 'newFolderId' => $folder->id, 'newFolderName' => $folder->name, 'totalItem' => $totalItemsss]);
            } else {
                EmailSentDocket::where('id', $request->id)->update(['folder_status'=> 1]);
                $folderItems = new FolderItem();
                $folderItems->folder_id = $folders->first()->id;
                $folderItems->ref_id = $request->id;
                $folderItems->type = 1;
                $folderItems->user_id = Auth::user()->id;
                $folderItems->status = 0;
                $folderItems->company_id = Session::get('company_id');
                $folderItems->save();
                $companyFolder=FolderItem::where('company_id',Session::get('company_id'))->where('folder_id',$folders->first()->id)->count();
                return response()->json(['status'=>true ,'type'=>'update','data'=>$companyFolder, 'id'=>$folders->first()->id]);

            }

        }
        else if ($request->type == 2) {
            EmailSentDocket::destroy($request->id);
            $folders = Folder::where('company_id', Session::get('company_id'))->where('type', 1)->where('root_id', 0)->get();
            if (count($folders) == 0) {
                $folder = new Folder();
                $folder->name = "Trash (System)";
                $folder->slug = str_slug('Trash (System)');
                $folder->user_id = Auth::user()->id;
                $folder->status = 0;
                $folder->company_id = Session::get('company_id');
                $folder->type = 1;
                if ($folder->save()) {
                    EmailSentDocket::where('id', $request->id)->update(['folder_status'=> 1]);
                    $folderItems = new FolderItem();
                    $folderItems->folder_id = $folder->id;
                    $folderItems->ref_id = $request->id;
                    $folderItems->type = 3;
                    $folderItems->user_id = Auth::user()->id;
                    $folderItems->status = 0;
                    $folderItems->company_id = Session::get('company_id');
                    $folderItems->save();
                }
                $totalItemsss = "";
                if (count($folder->folderItems) != 0) {
                    $totalItemsss = '(' . count($folder->folderItems) . ')';
                }
                return response()->json(['status' => true, 'type'=>'create', 'newFolderId' => $folder->id, 'newFolderName' => $folder->name, 'totalItem' => $totalItemsss]);
            } else {
                EmailSentDocket::where('id', $request->id)->update(['folder_status'=> 1]);
                $folderItems = new FolderItem();
                $folderItems->folder_id = $folders->first()->id;
                $folderItems->ref_id = $request->id;
                $folderItems->type = 3;
                $folderItems->user_id = Auth::user()->id;
                $folderItems->status = 0;
                $folderItems->company_id = Session::get('company_id');
                $folderItems->save();
                $companyFolder=FolderItem::where('company_id',Session::get('company_id'))->where('folder_id',$folders->first()->id)->count();
                return response()->json(['status'=>true ,'type'=>'update','data'=>$companyFolder, 'id'=>$folders->first()->id]);
            }


        }
    }

    public function docketfieldName(Request $request){
      $this->validate($request,['docketTemplateId'=>'required|int']);
        $categoryId = [5,8,15,14,9,28,30,13,15];
        $docketTemplate = DocketField::where('docket_id',$request->docketTemplateId)->whereNotIn('docket_field_category_id',$categoryId)->get();

        return view('dashboard/company/docketManager/modal-popup/advanced-filter/docket-field-name', compact('docketTemplate'));

    }


}
