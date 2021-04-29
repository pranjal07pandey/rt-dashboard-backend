<?php

namespace App\Http\Controllers;

use App\Client;
use App\Company;
use App\DefaultDocket;
use App\Docket;
use App\DocketField;
use App\DocketLabel;
use App\EmailSentDocket;
use App\EmailSentDocketRecipient;
use App\EmailSentDocketValue;
use App\EmailSentInvoice;
use App\EmailSentInvoiceDescription;
use App\EmailSentInvoiceLabel;
use App\EmailSentInvoicePaymentDetail;
use App\EmailSentInvoiceValue;
use App\EmailUser;
use App\Employee;
use App\Events\ChatEvent;
use App\Folder;
use App\FolderItem;
use App\Invoice;
use App\Invoice_Label;
use App\SentDcoketTimerAttachment;
use App\SentDocketLabel;
use App\SentDocketRecipient;
use App\SentDockets;
use App\SentDocketsValue;
use App\SentEmailDocketLabel;
use App\SentInvoice;
use App\SentInvoiceDescription;
use App\SentInvoiceLabel;
use App\SentInvoicePaymentDetail;
use App\SentInvoiceValue;
use App\Services\CompanyService;
use App\ShareableFolder;
use App\ShareableFolderUser;
use App\Support\Collection;
use App\TemplateAssignFolder;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use function React\Promise\all;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Helpers\V2\AmazoneBucket;

class FolderController extends Controller
{
    public function index(Request $request){
      $folder =  Folder::where('company_id',Session::get('company_id'))->where('root_id',$request->id)->get();
      $item = array();
      foreach ($folder as $rowData){
          $totalItemsss="";
          if (count($rowData->folderItems)!=0){
              $totalItemsss= '('.count($rowData->folderItems).')';
          }

          $item[] = array(
              'id' => $rowData->id,
              'name' => $rowData->name,
              'totalItems'=>$totalItemsss

          );
      }
       return response()->json(['data'=>$item]);
    }



    public function ajax(Request $request){
        date_default_timezone_set('UTC');
        $company= Company::where('id',Session::get('company_id'))->first();
        $folderData= Folder::where('user_id',$company->user_id)->get();
        $nodes = array();
        foreach ($folderData as $row){
            $nodes[] = array(/* [id,parent,type,name, checked */
                $row['id'], $row['root_id'], 0, $row['name'], false,
            );

        }

//        $nodes = array(/* [id,parent,type,name, checked */
//            array(0, -1, 0, 'Root1', false),
//            array(1, 0, 2, 'Knot1', false),
//            array(2, 0, 2, 'Knot2', false),
//            array(3, 1, 3, 'Leaf1', false),
//            array(4, 2, 3, 'Leaf2', false),
//            array(5, 0, 2, 'Knot3', false),
//            array(6, 5, 3, 'Leaf2', false),
//            array(7, -1, 0, 'Root2', true),
//            array(8, 7, 2, 'Knot4', true)
//        );




        $rq = (object) $_REQUEST;
        if (!isset($rq->ver)) {
            $rq->ver = 1;
        }
        $rq->pfx = 'filer_0_';
        $rq->fid = str_replace($rq->pfx, '', $rq->id);
        $rsp = array('status' => true, 'prompt' => '');

        if ($rq->cmd == 'opn') {
            $rsp['factor'] = $this->Children($nodes, $rq);


        } else if ($rq->cmd == 'sch') {
            $rsp['factor'] = $this->Search($nodes, $rq);

        } else if ($rq->cmd == 'new' || $rq->cmd == 'add') {
            $newFolder = new Folder() ;
            $newFolder->name = str_replace('%20', ' ', $rq->idt);
            $newFolder->slug = str_slug(str_replace('%20', ' ', $rq->idt));
            $newFolder->user_id = Auth::user()->id;
            $newFolder->company_id =Session::get('company_id');
            $newFolder->status = 0;
            $newFolder->root_id = str_replace($rq->pfx, '', $rq->pid);
            $newFolder->save();
            $rsp['factor'] = ['id' => $newFolder->id ];

        } else if ($rq->cmd == 'cpy' || $rq->cmd == 'mve') {
            $rsp['factor'] = [];  /* return old_id => new_id */
            foreach ($rq->id as $id => $ids) {
                if ($rq->cmd == 'cpy') {
                    $rsp['factor'][$id] = $rq->pfx . time();
                } else {
                    $rsp['factor'][$id] = $ids[0];
                }
            }
        } else if ($rq->cmd == 'del') {
            $folderItems = FolderItem::where('folder_id',$rq->fid)->where('company_id',Session::get('company_id'))->where('type',1)->get();
            $folderEmailItems = FolderItem::where('folder_id',$rq->fid)->where('company_id',Session::get('company_id'))->where('type',3)->get();
            foreach ($folderItems as $rowData){
                SentDockets::where('id',$rowData->ref_id)->update(['folder_status'=>0]);
                FolderItem::where('id',$rowData->id)->delete();
            }
            foreach ($folderEmailItems as $data){
                EmailSentDocket::where('id',$data->ref_id)->update(['folder_status'=>0]);
                FolderItem::where('id',$data->id)->delete();
            }
            $docketSetting   =    Folder::where('id',$rq->fid)->firstOrFail();
            $docketSetting->delete();


        }

        else if($rq->cmd == 'ren'){
            $folderRename   =    Folder::where('id',$rq->fid)->firstOrFail();
            $folderRename->name = str_replace('%20', ' ', $rq->idt);
            $folderRename->slug = str_slug(str_replace('%20', ' ', $rq->idt));
            $folderRename->save();
        }
        echo json_encode($rsp);

    }

    public function Children($nodes, $rq) {
        $rlt = array();
        for ($i = 0; $i < count($nodes); $i++) {
            if ($nodes[$i][1] == $rq->fid) {
                foreach ($nodes as $node) {
                    if ($node[1] == $nodes[$i][0]) {
                        if ($nodes[$i][2] != 0) {
                            $nodes[$i][2] = 1;
                        }
                    }
                }
                $sta = array('opened' => false, 'checked' => (isset($rq->ckd) && $nodes[$i][4]));
                $chd = $nodes[$i][2] < 2;
                $r = array('id' => $rq->pfx . $nodes[$i][0], 'text' => $nodes[$i][3], 'state' => $sta, 'children' => $chd, 'type' => $nodes[$i][2]);
                array_push($rlt, $r);
            }
        }
        return $rlt;
//        dd($rlt);
    }


    public function Search($nodes, $rq) {
        $nds = array();
        $this->Find($nodes, $nds, $rq->fid, -1, $rq->fnd);
        $rlt = array();
        $a = array_keys($nds);
        foreach ($nds as $id => $nde) {
            if (!$nde[0]) {
                for ($i = 0; $i < count($a); $i++) {
                    if ($nds[$a[$i]][1] == $id && $nds[$a[$i]][0]) {
                        $nde[0] = true;
                    }
                }
            }
            if ($nde[0]) {
                $rlt[] = $rq->pfx . $id;
            }
        }
        return $rlt;
    }



    public function Find($nodes, &$rlt, $id, $pnt, $cnd)
    {
        foreach ($nodes as $node) {
            if ($node[1] == $id && empty($rlt[$id][0])) {
                $fnd = $cnd['csi'] ? stripos($node[3], $cnd['str']) : strpos($node[3], $cnd['str']);
                $rlt[$id] = array($fnd !== false, $pnt);
                $this->Find($nodes, $rlt, $node[0], $id, $cnd);
            }
        }
    }


    public function getFolderStru(){
        $folderData= Folder::where('company_id',Session::get('company_id'))->where('type',0)->get();
        $treeArray = array();
        foreach ($folderData as $test) {
            $treeArray[] = array(
                'id' => $test->id,
                'parent_id' => $test->root_id,
                'name' => $test->name,
                'slug' => $test->slug,
            );
        }
        $txtTree[0]="";
        foreach($treeArray as $branch){
            if(isset($txtTree[$branch['parent_id']])) {
                $txtTree[$branch['id']] = $txtTree[$branch['parent_id']] . $branch['slug'] . "/";
            }
        }

         $datas = array();
            if(@$txtTree){
                foreach ($txtTree as $key => $value){
                    if($key != 0) {
                        $datas[] = array(
                            "id"=> $key,
                            "value"=>rtrim($value,'/'),
                            "space" =>str_repeat('&nbsp;', count(explode('/',$value))-2),
                            'name'=> array_slice(explode('/',$value), -2, 1)
                        );


                    }
                }
            }

           $data= (new Collection($datas))->sortBy('value');


        return view('dashboard.company.folder.searchValue',compact('data'));

    }


    public  function  saveFolderItems(Request $request){
        if(Input::has("docketId")){
               $selectDocketIds = $request->docketId;
               $checkDocketId = array();
               foreach ($selectDocketIds as $selectDocketId) {
                   $id_get = SentDockets::where('id', $selectDocketId)->pluck('id');
                   $checkDocketId[] = $id_get;
               }

                if (FolderItem::whereIn('ref_id',$checkDocketId)->where('type',1)->where('company_id',Session::get('company_id'))->count()!=0){
                   $updateFolders = FolderItem::whereIn('ref_id',$checkDocketId)->where('type',1)->where('company_id',Session::get('company_id'))->get();
                    foreach ($updateFolders as $rowData){
                        FolderItem::where('id',$rowData->id)->update(['folder_id'  =>  $request->folderId,'user_id'=> Auth::user()->id]);
                    }
                }else{
                    $sentDockets = SentDockets::whereIn('id', array_unique($checkDocketId))->get();
                    foreach ($sentDockets as $item){
                        $folderItem = new FolderItem();
                        $folderItem->folder_id  = $request->folderId;
                        $folderItem->ref_id  = $item->id;
                        $folderItem->type  = 1;
                        $folderItem->user_id  = Auth::user()->id;
                        $folderItem->status  = 0;
                        $folderItem->company_id  = Session::get('company_id');
                        if ($folderItem->save()){
                            SentDockets::where('id',$item->id)->update(['folder_status'  => 1]);
                        }
                    }
                }



        }
        if(Input::has("emailDocketId")){
                $selectEmailDocketIds = $request->emailDocketId;
                $checkEmailDocketId = array();
                foreach ($selectEmailDocketIds as $selectEmailDocketId) {
                    $id_get = EmailSentDocket::where('id', $selectEmailDocketId)->pluck('id');
                    $checkEmailDocketId[] = $id_get;
                }
            if (FolderItem::whereIn('ref_id',$checkEmailDocketId)->where('type',3)->where('company_id',Session::get('company_id'))->count()!=0){
                $updateFolders = FolderItem::whereIn('ref_id',$checkEmailDocketId)->where('type',3)->where('company_id',Session::get('company_id'))->get();
                foreach ($updateFolders as $rowData){
                    FolderItem::where('id',$rowData->id)->update(['folder_id'  =>  $request->folderId,'user_id'=> Auth::user()->id]);
                }

            }else {

                $emailSentDockets = EmailSentDocket::whereIn('id', array_unique($checkEmailDocketId))->get();
                foreach ($emailSentDockets as $items) {
                    $folderItem = new FolderItem();
                    $folderItem->folder_id = $request->folderId;
                    $folderItem->ref_id = $items->id;
                    $folderItem->type = 3;
                    $folderItem->user_id = Auth::user()->id;
                    $folderItem->status = 0;
                    $folderItem->company_id = Session::get('company_id');
                    if ($folderItem->save()) {
                        EmailSentDocket::where('id', $items->id)->update(['folder_status' => 1]);
                    }
                }
            }

        }
        if(Input::has("invoiceId")){
            $selectInvoice = $request->invoiceId;
            $checkInvoiceId = array();
            foreach ($selectInvoice as $selectInvoices) {
                $id_get = SentInvoice::where('id', $selectInvoices)->pluck('id');
                $checkInvoiceId[] = $id_get;
            }

            if (FolderItem::whereIn('ref_id',$checkInvoiceId)->where('type',2)->where('company_id',Session::get('company_id'))->count()!=0){
                $updateFolders = FolderItem::whereIn('ref_id',$checkInvoiceId)->where('type',2)->where('company_id',Session::get('company_id'))->get();
                foreach ($updateFolders as $rowData){
                    FolderItem::where('id',$rowData->id)->update(['folder_id'  =>  $request->folderId,'user_id'=> Auth::user()->id]);
                }
            }else{
                $sentInvoice = SentInvoice::whereIn('id', array_unique($checkInvoiceId))->get();
                foreach ($sentInvoice as $item){
                    $folderItem = new FolderItem();
                    $folderItem->folder_id  = $request->folderId;
                    $folderItem->ref_id  = $item->id;
                    $folderItem->type  = 2;
                    $folderItem->user_id  = Auth::user()->id;
                    $folderItem->status  = 0;
                    $folderItem->company_id  = Session::get('company_id');
                    if ($folderItem->save()){
                        SentInvoice::where('id',$item->id)->update(['folder_status'  => 1]);
                    }
                }
            }
        }
        if( Input::has("emailInvoiceId")){
            $selectEmailInvoiceId = $request->emailInvoiceId;
            $checkEmailInvoiceId = array();
            foreach ($selectEmailInvoiceId as $selectEmailInvoice) {
                $id_get = EmailSentInvoice::where('id', $selectEmailInvoice)->pluck('id');
                $checkEmailInvoiceId[] = $id_get;
            }

            if (FolderItem::whereIn('ref_id',$checkEmailInvoiceId)->where('type',4)->where('company_id',Session::get('company_id'))->count()!=0){
                $updateFolders = FolderItem::whereIn('ref_id',$checkEmailInvoiceId)->where('type',4)->where('company_id',Session::get('company_id'))->get();
                foreach ($updateFolders as $rowData){
                    FolderItem::where('id',$rowData->id)->update(['folder_id'  =>  $request->folderId,'user_id'=> Auth::user()->id]);
                }
            }else{
                $sentEmailInvoice = EmailSentInvoice::whereIn('id', array_unique($checkEmailInvoiceId))->get();
                foreach ($sentEmailInvoice as $item){
                    $folderItem = new FolderItem();
                    $folderItem->folder_id  = $request->folderId;
                    $folderItem->ref_id  = $item->id;
                    $folderItem->type  = 4;
                    $folderItem->user_id  = Auth::user()->id;
                    $folderItem->status  = 0;
                    $folderItem->company_id  = Session::get('company_id');
                    if ($folderItem->save()){
                        EmailSentInvoice::where('id',$item->id)->update(['folder_status'  => 1]);
                    }
                }
            }
        }

        $companyFolder=FolderItem::where('company_id',Session::get('company_id'))->where('folder_id',$request->folderId)->count();
        return response()->json(['status'=>true ,'data'=>$companyFolder, 'id'=>$request->folderId]);
    }

    public function viewFolderData(Request $request){
        $type = "noreload";
        $company            =   Company::with('docketLabels','invoiceLabels')->findOrfail(Session::get('company_id'));
        $folderId   = $request->folderId;
        $folder     = Folder::findOrFail($folderId);

        if($folder->company_id!=Session::get('company_id')){echo "<br>&nbsp;&nbsp;&nbsp;Invalid attempt!";}
        if ($request->items == ""){ $items= 10;}
        else{ $items = $request->items; }
        $folderItems        =   FolderItem::where('company_id',Session::get('company_id'))->where('folder_id',$folderId)->get();


        if($folder->type == 1){

            $sentDockets        =   SentDockets::with('sentDocketLabels.docketLabel','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',1)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->onlyTrashed()->get();
            $emailSentDockets   =   EmailSentDocket::with('sentEmailDocketLabels.docketLabel','recipientInfo.emailUserInfo','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',3)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->onlyTrashed()->get();
            $totalDockets       =   $sentDockets->concat($emailSentDockets);
            $trashFolder = true;

        }else{
            $sentDockets        =   SentDockets::with('sentDocketLabels.docketLabel','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',1)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $emailSentDockets   =   EmailSentDocket::with('sentEmailDocketLabels.docketLabel','recipientInfo.emailUserInfo','docketInfo.previewFields')->whereIn('id',$folderItems->where('type',3)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
            $totalDockets       =   $sentDockets->concat($emailSentDockets);
            $trashFolder = false;
        }




        $sentInvoices       =   SentInvoice::with('sentInvoiceLabels.invoiceLabel','receiverUserInfo','receiverCompanyInfo','invoiceInfo')->whereIn('id',$folderItems->where('type',2)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();
        $emailSentInvoices  =   EmailSentInvoice::whereIn('id',$folderItems->where('type',4)->pluck('ref_id')->toArray())->orderBy('created_at','desc')->get();

        $totalInvoices      =   $sentInvoices->concat($emailSentInvoices);
        $merged = $totalDockets->concat($totalInvoices);
        $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);
        return view('dashboard.company.folder.view',compact('company','items','result','folder','type','trashFolder'));
    }



    public function newFolderCreate(Request $request){
        $this->validate($request,['rootId'   => 'required']);
        if ($request->name == null){
            return response()->json(['status'=>false ,'message'=>"The name field is required."]);
        }else{
            if($request->rootId != 0){
                $rootFolder     =    Folder::findOrFail($request->rootId);
                if($rootFolder->company_id!=Session::get('company_id')){
                    return response()->json(['status'=>false ,'message'=>"Invalid Attempt."]);
                }
            }
            if (Folder::where('name',$request->name)->where('root_id',$request->rootId)->where('company_id',Session::get('company_id'))->count() == 0){
                $newFolder = new Folder() ;
                $newFolder->name =  $request->name;
                $newFolder->slug = str_slug($request->name);
                $newFolder->user_id = Auth::user()->id;
                $newFolder->company_id =Session::get('company_id');
                $newFolder->status = 0;
                $newFolder->root_id = $request->rootId;
                $newFolder->save();

                $totalItemsss="";
                if (count($newFolder->folderItems)!=0){
                    $totalItemsss= '('.count($newFolder->folderItems).')';
                }

                return response()->json(['status'=>true ,'newFolderId'=>$newFolder->id,'newFolderName'=>$newFolder->name,'totalItem'=>$totalItemsss]);
            }else{
                return response()->json(['status'=>false ,'message'=>"Folder name already exists."]);
            }
        }
    }

    public function createFolderSelect(){
        $folderData= Folder::where('company_id',Session::get('company_id'))->where('type',0)->get();
        $treeArray = array();
        foreach ($folderData as $test) {
            $treeArray[] = array(
                'id' => $test->id,
                'parent_id' => $test->root_id,
                'name' => $test->name,
                'slug' => $test->slug,
            );
        }
        $txtTree[0]="";
        foreach($treeArray as $branch){
            if(isset($txtTree[$branch['parent_id']])) {
                $txtTree[$branch['id']] = $txtTree[$branch['parent_id']] . $branch['name'] . "/";
            }
        }

        $datas = array();
        if(@$txtTree){
            foreach ($txtTree as $key => $value){
                if($key != 0) {
                    $datas[] = array(
                        "id"=> $key,
                        "value"=>rtrim($value,'/'),
                        "space" =>str_repeat('&nbsp;', (count(explode('/',$value))-2)*3),
                        'name'=> array_slice(explode('/',$value), -2, 1)
                    );


                }
            }
        }
        $data= (new Collection($datas))->sortBy('value');

        return view('dashboard.company.folder.folderCreateSelect',compact('data'));

    }


     public function getChildFolderId($root_id){
         $folderID  =   array();
         $folderID[]    =   intval($root_id);
         $query= Folder::where('root_id',$root_id);
         if ($query->count()>0){
             foreach ($query->get() as $items){
                 $folderID[]    =   $items->id;
                 if(Folder::where('root_id',$items->id)->count()>0) {
                     $folderID = array_merge($folderID, $this->getChildFolderId($items->id));
                 }
             }
         }else{
             $folderID[]    =     $root_id;
         }
        return $folderID;

     }





    public function removeFolder(Request $request){
       $root_id= $request->id;
       $folder  =   Folder::findOrFail($root_id);
       if($folder->company_id == Session::get('company_id')) {
           $folderId = $this->getChildFolderId($root_id);
           if (FolderItem::whereIn('folder_id', $folderId)->where('company_id', Session::get('company_id'))->where('type', 1)->count() != 0) {
               $folderItems = FolderItem::whereIn('folder_id', $folderId)->where('company_id', Session::get('company_id'))->where('type', 1)->get();
               foreach ($folderItems as $rowData) {
                   SentDockets::where('id', $rowData->ref_id)->update(['folder_status' => 0]);
                   FolderItem::where('id', $rowData->id)->delete();
               }
           }
           if (FolderItem::whereIn('folder_id', $folderId)->where('company_id', Session::get('company_id'))->where('type', 3)->count() != 0) {
               $folderEmailItems = FolderItem::whereIn('folder_id', $folderId)->where('company_id', Session::get('company_id'))->where('type', 3)->get();
               foreach ($folderEmailItems as $data) {
                   EmailSentDocket::where('id', $data->ref_id)->update(['folder_status' => 0]);
                   FolderItem::where('id', $data->id)->delete();
               }
           }
           if (FolderItem::whereIn('folder_id', $folderId)->where('company_id', Session::get('company_id'))->where('type', 2)->count() != 0) {
               $folderInvoiceItem = FolderItem::whereIn('folder_id', $folderId)->where('company_id', Session::get('company_id'))->where('type', 2)->get();
               foreach ($folderInvoiceItem as $folderInvoiceItems) {
                   SentInvoice::where('id', $folderInvoiceItems->ref_id)->update(['folder_status' => 0]);
                   FolderItem::where('id', $folderInvoiceItems->id)->delete();
               }
           }

           if (FolderItem::whereIn('folder_id', $folderId)->where('company_id', Session::get('company_id'))->where('type', 4)->count() != 0) {
               $folderEmailInvoiceItem = FolderItem::whereIn('folder_id', $folderId)->where('company_id', Session::get('company_id'))->where('type', 4)->get();
               foreach ($folderEmailInvoiceItem as $folderEmailInvoiceItems) {
                   EmailSentInvoice::where('id', $folderEmailInvoiceItems->ref_id)->update(['folder_status' => 0]);
                   FolderItem::where('id', $folderEmailInvoiceItems->id)->delete();
               }
           }
           TemplateAssignFolder::whereIn('folder_id', $folderId)->delete();

           Folder::whereIn('id', $folderId)->delete();
           $folder = Folder::where('company_id', Session::get('company_id'))->count();
           return response()->json(['status' => true, 'foldercount' => $folder]);
       }
    }

    public  function updateFolder(Request $request){
        $this->validate($request,['id'   => 'required','title'   => 'required']);
        $folder   =    Folder::findOrFail($request->id);
        if($folder->company_id!=Session::get('company_id')){
            return response()->json(['status'=>false]);
        }
        $folder->name = $request->title;
        $folder->slug = str_slug($request->title);
        $folder->save();

        $totalItemss="";
        if (count($folder->folderItems)!=0){
            $totalItemss= '('.count($folder->folderItems).')';
        }

        return response()->json(['status'=>true ,'title'=>$folder->name,'id'=>$folder->id ,'totalItems'=>$totalItemss]);
    }
    public  function  removeItemsFolder(Request $request){
        if (Input::has("docketId")){
                $folderItems = FolderItem::whereIn('ref_id',$request->docketId)->where('folder_id',$request->folderId)->where('company_id',Session::get('company_id'))->where('type',1)->get();
                foreach ($folderItems as $rowData){
                    SentDockets::where('id',$rowData->ref_id)->update(['folder_status'=>0]);
                    FolderItem::where('id',$rowData->id)->delete();
                }

        }
        if (Input::has("emailDocketId")) {

            $folderEmailItems = FolderItem::whereIn('ref_id', $request->emailDocketId)->where('folder_id', $request->folderId)->where('company_id', Session::get('company_id'))->where('type', 3)->get();
                foreach ($folderEmailItems as $data) {
                    EmailSentDocket::where('id', $data->ref_id)->update(['folder_status' => 0]);
                    FolderItem::where('id', $data->id)->delete();
                }

        }
        if (Input::has("invoiceId")){

            $folderInvoiceItems = FolderItem::whereIn('ref_id', $request->invoiceId)->where('folder_id', $request->folderId)->where('company_id', Session::get('company_id'))->where('type', 2)->get();
            foreach ($folderInvoiceItems as $folderInvoiceItem) {
                EmailSentDocket::where('id', $folderInvoiceItem->ref_id)->update(['folder_status' => 0]);
                FolderItem::where('id', $folderInvoiceItem->id)->delete();
            }

        }
        if (Input::has("emailInvoiceId")){
            $folderEmailInvoiceItems = FolderItem::whereIn('ref_id', $request->emailInvoiceId)->where('folder_id', $request->folderId)->where('company_id', Session::get('company_id'))->where('type', 4)->get();
            foreach ($folderEmailInvoiceItems as $folderEmailInvoiceItem) {
                EmailSentDocket::where('id', $folderEmailInvoiceItem->ref_id)->update(['folder_status' => 0]);
                FolderItem::where('id', $folderEmailInvoiceItem->id)->delete();
            }


        }

        $companyFolder=FolderItem::where('company_id',Session::get('company_id'))->where('folder_id',$request->folderId)->count();
        return response()->json(['status'=>true ,'data'=>$companyFolder, 'id'=>$request->folderId]);



    }


    public  function searchFolder(Request $request){

       $folder = Folder::where('company_id',Session::get('company_id'))->where('name','like','%'.$request->name.'%')->get();
        $matchedFolderName= array();
        foreach ($folder as $row) {
                $matchedFolderName[]= $row->id;
        }
            $folderName = Folder::where('company_id',Session::get('company_id'))->where('root_id', '=', 0)->orderBy('name','asc')->get();
            $parentHtml='<ul class="rtTree">';
            foreach ($folderName as $folderNames) {
                if ($folderNames->type == 0) {
                    $totalItemss = "";
                    if (count($folderNames->folderItems) != 0) {
                        $totalItemss = '(' . count($folderNames->folderItems) . ')';
                    }
                    if (!in_array($folderNames->id, $matchedFolderName)) {
                        $parentHtml .= '<li><a href="#"  id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                    } else {
                        $parentHtml .= '<li><a href="#" style="color: #5f0505;font-weight: 700;" id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                    }
                    if (count($folderNames->childs)) {
                        $parentHtml .= $this->childView($folderNames, $matchedFolderName);
                    }

                    $parentHtml .= '  <div  class="editBtn" id="editBtnId" data-id="' . $folderNames->id . '" data-title="' . $folderNames->name . '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>';


                }
            }


                foreach ($folderName as $folderNames) {
                    if($folderNames->type == 1){
                        $totalItemss="";
                        if (count($folderNames->folderItems)!=0){
                            $totalItemss= '('.count($folderNames->folderItems).')';
                        }
                        if (!in_array($folderNames->id,$matchedFolderName)){
                            $parentHtml .='<li><a href="#"  id="'.$folderNames->id.'">'.$folderNames->name.'<span style="    position: absolute;right: 4px;">'. $totalItemss .'</span></a>';
                        }else{
                            $parentHtml .='<li><a href="#" style="color: #5f0505;font-weight: 700;" id="'.$folderNames->id.'">'.$folderNames->name.'<span style="    position: absolute;right: 4px;">'.$totalItemss.'</span></a>';
                        }
                        if(count($folderNames->childs)) {
                            $parentHtml .=$this->childView($folderNames,$matchedFolderName);
                        }

                        $parentHtml .= '  <div   data-id="'.$folderNames->id.'" data-title="'.$folderNames->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>';


                    }





                }






        $parentHtml .='<ul>';
         return response()->json(['status'=>true ,'detail'=>$parentHtml]);

    }







    public function childView($folderNames,$matchedFolderName){
        $childHtml ='<ul>';
        foreach ($folderNames->childs as $arr) {
            $totalItemsss="";
            if (count($arr->folderItems)!=0){
                $totalItemsss= '('.count($arr->folderItems).')';
            }
            if(count($arr->childs)){
                if (!in_array($arr->id,$matchedFolderName)){
                    $childHtml .='<li><a href="#"  id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a>  ';
                }else{
                    $childHtml .='<li><a href="#" style="color: #5f0505;font-weight: 700;" id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a>  ';

                }
                $childHtml.= $this->childView($arr,$matchedFolderName);
                $childHtml.=  '  <div  class="editBtn" id="editBtnId" data-id="'.$folderNames->id.'" data-title="'.$folderNames->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>';

            }else{
                if (!in_array($arr->id,$matchedFolderName)){
                    $childHtml .='<li><a href="#"  id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'.$arr->id.'" data-title="'.$arr->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>';
                }else{
                    $childHtml .='<li><a href="#" style="color: #5f0505;font-weight: 700;" id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'.$arr->id.'" data-title="'.$arr->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>';

                }
                $childHtml .="</li>";
            }

        }

        $childHtml .="</ul>";
        return $childHtml;
    }

    public  function searchFolderItems(Request $request){
        $folderId   =   Input::get('folderId');
        $folder     =   Folder::findOrFail($folderId);
        if($folder->company_id!=Session::get('company_id')){echo "<br>&nbsp;&nbsp;&nbsp;Invalid attempt!"; exit();}
        $company    =   Company::findOrfail(Session::get('company_id'));

        $searchKey = Input::get('search');
        if($request->items == ""){ $items= 10; }
        else{ $items = $request->items; }

        if(Input::get('search')){
            $searchKey      =   Input::get('search');
            $folderItems    =   FolderItem::where('folder_id',$folderId)->where('company_id',Session::get('company_id'))->get();
            $sentDocketFolderItem       =   $folderItems->where('type',1)->pluck('ref_id')->toArray();
            $sentEmailDocketFolderItem  =   $folderItems->where('type',3)->pluck('ref_id')->toArray();
            $sentInvoiceFolderItem      =   $folderItems->where('type',2)->pluck('ref_id')->toArray();
            $sentEmailInvoiceFolderItem =   $folderItems->where('type',4)->pluck('ref_id')->toArray();

            if($folder->type == 1){
                $sentDocketQuery        =   SentDockets::with('recipientInfo.userInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentDocketFolderItem)->onlyTrashed()->get();
                $sentEmailDocketQuery   =   EmailSentDocket::with('recipientInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentEmailDocketFolderItem)->onlyTrashed()->get();
                $trashFolder = true;
            }else{
                $sentDocketQuery        =   SentDockets::with('recipientInfo.userInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentDocketFolderItem)->get();
                $sentEmailDocketQuery   =   EmailSentDocket::with('recipientInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentEmailDocketFolderItem)->get();
                $trashFolder = false;
            }


            $sentInvoiceQuery       =   SentInvoice::whereIn('id',$sentInvoiceFolderItem)->get();
            $sentEmailInvoiceQuery  =   EmailSentInvoice::whereIn('id',$sentEmailInvoiceFolderItem)->get();

            $sentDockets= array();
            $docketsEmail= array();
            $sentInvoices =array();
            $emailInvoices =array();

            foreach ($sentDocketQuery as $sentDocketQuerys){
                if(preg_match("/".$searchKey."/i",$sentDocketQuerys->sender_name) || preg_match("/".$searchKey."/i",$sentDocketQuerys->company_name)){ $sentDockets [] =  $sentDocketQuerys; continue; }
                if(preg_match("/" . $searchKey . "/i", $sentDocketQuerys->template_title)) { $sentDockets[] = $sentDocketQuerys; continue; }
                if(preg_match("/" . $searchKey . "/i", Carbon::parse($sentDocketQuerys->created_at)->format('d-M-Y'))) { $sentDockets[] = $sentDocketQuerys; continue; }
                if(preg_match("/" . $searchKey . "/i", $sentDocketQuerys->company_docket_id)) { $sentDockets[] = $sentDocketQuerys; continue; }

                if($sentDocketQuerys->recipientInfo){
                    foreach ($sentDocketQuerys->recipientInfo as $recipient):
                        $receiversName = @$recipient->userInfo->first_name . " " . @$recipient->userInfo->last_name;
                        if (preg_match("/" . $searchKey . "/i", $receiversName)) { $sentDockets[] = $sentDocketQuerys; break; }
                        $companyName = "";
                        $employeeQuery = Employee::where('user_id', $recipient->user_id)->get();
                        if($employeeQuery->count() > 0){ $companyName = $employeeQuery->first()->companyInfo->name;}
                        else{ $companyQuery = Company::where('user_id', $recipient->user_id)->get();
                            if ($companyQuery->count() > 0)
                                $companyName = $companyQuery->first()->name;
                        }
                        if (preg_match("/" . $searchKey . "/i", $companyName)) {
                            $sentDockets[] = $sentDocketQuerys;
                            break;
                        }
                    endforeach;
                }

                //  for docket field value
                if($sentDocketQuerys->sentDocketValue) {
                    foreach ($sentDocketQuerys->sentDocketValue as $rowValue) {
                        if (@$rowValue->docketFieldInfo->docket_field_category_id != 5 && @$rowValue->docketFieldInfo->docket_field_category_id != 7 && @$rowValue->docketFieldInfo->docket_field_category_id != 8 && @$rowValue->docketFieldInfo->docket_field_category_id != 9 &&
                            @$rowValue->docketFieldInfo->docket_field_category_id != 12 && @$rowValue->docketFieldInfo->docket_field_category_id != 13 && @$rowValue->docketFieldInfo->docket_field_category_id != 14 && @$rowValue->docketFieldInfo->docket_field_category_id != 22
                        ) {
                            if (preg_match("/" . $searchKey . "/i", $rowValue->value)) {
                                $sentDockets[] = $sentDocketQuerys;
                                break;
                            }
                        }
                    }
                }

                //  for docket field value
                if($sentDocketQuerys->sentDocketValue) {
                    foreach ($sentDocketQuerys->sentDocketValue as $rowValue) {
                        if (@$rowValue->docketFieldInfo->docket_field_category_id != 5 && @$rowValue->docketFieldInfo->docket_field_category_id != 7 && @$rowValue->docketFieldInfo->docket_field_category_id != 8 && @$rowValue->docketFieldInfo->docket_field_category_id != 9 &&
                            @$rowValue->docketFieldInfo->docket_field_category_id != 12 && @$rowValue->docketFieldInfo->docket_field_category_id != 13 && @$rowValue->docketFieldInfo->docket_field_category_id != 14 && @$rowValue->docketFieldInfo->docket_field_category_id != 22
                        ) {
                            if (preg_match("/" . $searchKey . "/i", $rowValue->value)) {
                                $sentDockets[] = $sentDocketQuerys;
                                break;
                            }
                        }
                    }
                }
            }

            foreach ($sentEmailDocketQuery as $sentEmailDocketQuerys){
                if((preg_match("/".$searchKey."/i",$sentEmailDocketQuerys->sender_name) || preg_match("/".$searchKey."/i",$sentEmailDocketQuerys->company_name))){ $docketsEmail [] =  $sentEmailDocketQuerys; continue; }
                if(preg_match("/".$searchKey."/i",$sentEmailDocketQuerys->template_title)){ $docketsEmail[]   =   $sentEmailDocketQuerys; continue; }
                if(preg_match("/".$searchKey."/i",Carbon::parse($sentEmailDocketQuerys->created_at)->format('d-M-Y'))) { $docketsEmail[]   =   $sentEmailDocketQuerys; continue; }
                if(preg_match('/('.$searchKey.')/',  $sentEmailDocketQuerys->company_docket_id)){  $docketsEmail [] =  $sentEmailDocketQuerys; continue; }

                //for receivers Email Company name Company address Company full name
                foreach($sentEmailDocketQuerys->recipientInfo as $recipient){
                    if(preg_match("/".$searchKey."/i",$recipient->emailUserInfo->email)){ $docketsEmail [] =  $sentEmailDocketQuerys; break; }
                    if(preg_match("/".$searchKey."/i",$recipient->receiver_full_name)){ $docketsEmail [] =  $sentEmailDocketQuerys; break; }
                    if(preg_match("/".$searchKey."/i",$recipient->receiver_company_name)){ $docketsEmail [] =  $sentEmailDocketQuerys; break; }
                    if (preg_match("/".$searchKey."/i",$recipient->receiver_company_address)){ $docketsEmail [] =  $sentEmailDocketQuerys; break; }
                }

                //for docket field value
                if($sentEmailDocketQuerys->sentDocketValue){
                    foreach ($sentEmailDocketQuerys->sentDocketValue as $rowValue){
                        if(@$rowValue->docketFieldInfo->docket_field_category_id!=5 && @$rowValue->docketFieldInfo->docket_field_category_id!=7 && @$rowValue->docketFieldInfo->docket_field_category_id!=8 && @$rowValue->docketFieldInfo->docket_field_category_id!=9 &&
                            @$rowValue->docketFieldInfo->docket_field_category_id!=12 && @$rowValue->docketFieldInfo->docket_field_category_id!=13 && @$rowValue->docketFieldInfo->docket_field_category_id!=14 && @$rowValue->docketFieldInfo->docket_field_category_id!=22){
                            if(preg_match("/".$searchKey."/i",$rowValue->value)) {
                                $docketsEmail[]   =  $sentEmailDocketQuerys;
                                break;
                            }
                        }
                    }
                }
            }

            foreach ($sentInvoiceQuery as $sentInvoiceQuerys){
                if(preg_match("/" . $searchKey . "/i", $sentInvoiceQuerys->company_invoice_id)){ $sentInvoices [] =  $sentInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",$sentInvoiceQuerys->invoiceInfo->title)){ $sentInvoices[]   =   $sentInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",Carbon::parse($sentInvoiceQuerys->created_at)->format('d-M-Y'))){ $sentInvoices[]   =   $sentInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",$sentInvoiceQuerys->sender_name)){ $sentInvoices[]   =   $sentInvoiceQuerys;  continue; }
                if(preg_match("/".$searchKey."/i",$sentInvoiceQuerys->sender_name)){ $sentInvoices[]   =   $sentInvoiceQuerys;  continue; }
                if(preg_match("/".$searchKey."/i",$sentInvoiceQuerys->company_name)){ $sentInvoices[]   =   $sentInvoiceQuerys;  continue; }

                $receiverName   =   $sentInvoiceQuerys->receiverUserInfo->first_name." ".$sentInvoiceQuerys->receiverUserInfo->last_name;
                $receiverCompanyName  =   $sentInvoiceQuerys->senderCompanyInfo->name;
                if(preg_match("/".$searchKey."/i",$receiverName) || preg_match("/".$searchKey."/i",$receiverCompanyName)){
                    $sentInvoices[]   =   $sentInvoiceQuerys; continue;
                }
            }

            foreach ($sentEmailInvoiceQuery as $sentEmailInvoiceQuerys){
                if(preg_match("/" . $searchKey . "/i", $sentEmailInvoiceQuerys->company_invoice_id)){  $emailInvoices [] =  $sentEmailInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",Carbon::parse($sentEmailInvoiceQuerys->created_at)->format('d-M-Y'))){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",Carbon::parse($sentEmailInvoiceQuerys->created_at)->format('d M'))){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",Carbon::parse($sentEmailInvoiceQuerys->created_at)->format('F'))){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->template_title)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }

                if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->sender_name)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->company_name)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->company_address)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }

                if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->receiverInfo->email)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->receiver_full_name)){$emailInvoices[]   =   $sentEmailInvoiceQuerys;  continue; }
                if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->receiver_company_name)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys;  continue; }
                if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->receiver_company_address)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys;  continue; }

                if ($sentEmailInvoiceQuerys->invoiceDescription){
                    foreach ($sentEmailInvoiceQuerys->invoiceDescription as $invoiceDescription){
                        if(preg_match("/".$searchKey."/i",$invoiceDescription->description)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys;  break; }
                        if(preg_match("/".$searchKey."/i",$invoiceDescription->amount)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys;  break; }
                    }
                }
            }

            $totalDocket    =   array_merge($sentDockets,$docketsEmail);
            $totalInvoice   =   array_merge($sentInvoices,$emailInvoices);
            $merged         =   array_merge($totalDocket,$totalInvoice);
            $result         =   (new Collection($merged))->sortByDesc('created_at')->paginate($items);
            return view('dashboard.company.folder.viewSearchItem',compact('company','items', 'folder','result','trashFolder'));
        }
        if(Input::get('data')== "all"){
            $folderItems =  FolderItem::where('folder_id',$folderId)->where('company_id',Session::get('company_id'))->get();
            $sentDocketFolderItem       =   $folderItems->where('type',1)->pluck('ref_id')->toArray();
            $sentEmailDocketFolderItem  =   $folderItems->where('type',3)->pluck('ref_id')->toArray();
            $sentInvoiceFolderItem      =   $folderItems->where('type',2)->pluck('ref_id')->toArray();
            $sentEmailInvoiceFolderItem =   $folderItems->where('type',4)->pluck('ref_id')->toArray();
            if($folder->type == 1){
                $sentDocketQuery        =   SentDockets::with('recipientInfo.userInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentDocketFolderItem)->onlyTrashed()->get();
                $sentEmailDocketQuery   =   EmailSentDocket::with('recipientInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentEmailDocketFolderItem)->onlyTrashed()->get();
                $trashFolder = true;
            }else{
                $sentDocketQuery        =   SentDockets::with('recipientInfo.userInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentDocketFolderItem)->get();
                $sentEmailDocketQuery   =   EmailSentDocket::with('recipientInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentEmailDocketFolderItem)->get();
                $trashFolder = false;
            }


            $sentInvoiceQuery       =   SentInvoice::whereIn('id',$sentInvoiceFolderItem)->get();
            $sentEmailInvoiceQuery  =   EmailSentInvoice::whereIn('id',$sentEmailInvoiceFolderItem)->get();

            $totalDocket= $sentDocketQuery->concat($sentEmailDocketQuery);
            $totalInvoice =$sentInvoiceQuery->concat($sentEmailInvoiceQuery);
            $merged = $totalDocket->concat($totalInvoice);
            $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);

            return view('dashboard.company.folder.viewSearchItem',compact('company','items', 'folder','result','trashFolder'));
        }
    }

    public  function showFolderAdvanceFilter(Request $request){
        $type = $request->type;
        if ($type == 1){
            //docket
            $totalCompany   =   Company::get();
            $clients        =   Client::where('company_id',Session::get('company_id'))->orWhere('requested_company_id',Session::get('company_id'))->get();
            $dockets        =   Docket::where('company_id',Session::get('company_id'))->orderBy('id','desc')->get();
            return view('dashboard.company.folder.advanceSearch.docket',compact('type','totalCompany','clients','dockets'));

        }elseif ($type == 2){
            //Email Docket
            $employes = Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
            $company   =   array(Company::where('id',Session::get('company_id'))->first()->user_id);
            $total =  array_merge($company, $employes);
            $docketusedbyemail = EmailSentDocket::select('docket_id')->whereIn('user_id',$total)->groupBy('docket_id')->get();
            return view('dashboard.company.folder.advanceSearch.docket',compact('type','docketusedbyemail'));

        }elseif ($type == 3){
            //invoice
            $invoices   =   Invoice::where('company_id',Session::get('company_id'))->orderBy('id','desc')->get();
            $totalInvoiceLabel =   Invoice_Label::where('company_id',Session::get('company_id'))->count();
            $sentInvoiceLabel=    Invoice_Label::where('company_id',Session::get('company_id'))->orderBy('created_at','desc')->get();
            $clients        =   Client::where('company_id',Session::get('company_id')) ->orWhere('requested_company_id',Session::get('company_id'))->get();
            $totalCompany   =   Company::get();
            return view('dashboard.company.folder.advanceSearch.docket',compact('type','totalCompany','clients','sentInvoiceLabel','totalInvoiceLabel','invoices'));


        }elseif ($type == 4){
            $employes = Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
            $company   =   array(Company::where('id',Session::get('company_id'))->first()->user_id);
            $total =  array_merge($company, $employes);
            $invoiceUsedByEmail = EmailSentInvoice::select('invoice_id')->whereIn('user_id',$total)->groupBy('invoice_id')->get();
            return view('dashboard.company.folder.advanceSearch.docket',compact('type','invoiceUsedByEmail'));

        }


    }

    public function emailFilter($request,$folderItem){


        $sentemailDocketsQuery =  EmailSentDocket::whereIn('id',$folderItem);
        $sentemailDocketsQuery->where('company_id',Session::get('company_id'));

        if($request->invoiceable){
            $sentemailDocketsQuery->where('invoiceable',$request->invoiceable);
        }


        if($request->email){
            $emailUserQuery= EmailUser::where('email',$request->email);
            $emailRecipent= EmailSentDocketRecipient::where('email_user_id', $emailUserQuery->first()->id)->pluck('email_sent_docket_id')->toArray();
            if($emailUserQuery->count()){
                $sentemailDocketsQuery->whereIn('id',$emailRecipent);
            }

        }
        if($request->date){
            if($request->date==1){
                if($request->from)
                    $sentemailDocketsQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));

                if($request->to)
                    $sentemailDocketsQuery->whereDate('created_at','<=',Carbon::parse($request->to)->format('Y-m-d'));
            }
        }
        if($request->TemplateId){
            $sentemailDocketsQuery->where('docket_id',$request->TemplateId);
        }

        if($request->docketFieldValue){
            $emailMatchArray = array();
            foreach ($request->docketFieldValue as $fieldId=>$fieldValues){
                $fieldDatasVal= explode("-",$fieldValues);
                $docketFVal = $fieldDatasVal[1];
                $docketFId = $fieldDatasVal[0];
                if($docketFVal != "null"){
                    foreach ($sentemailDocketsQuery->get() as $row){
                        foreach ($row->sentDocketValue as $sentEDValue){
                            if($sentEDValue->docket_field_id == $docketFId){

                                if($sentEDValue->docketFieldInfo->docket_field_category_id ==7){
                                    if($sentEDValue->sentDocketUnitRateValue){
                                        foreach($sentEDValue->sentDocketUnitRateValue as $sentDURValue){
                                            if(preg_match("/{$docketFVal}/i", $sentDURValue->value)){
                                                $emailMatchArray[] = @$sentDURValue->sentDocketValue->sent_docket_id;
                                            }
                                        }
                                    }
                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->email_sent_docket_id;

                                }elseif($sentEDValue->docketFieldInfo->docket_field_category_id == 23){
                                    if($sentEDValue->sentDocketTallyableUnitRateValue){
                                        foreach($sentEDValue->sentDocketTallyableUnitRateValue as $sentDTURValue){
                                            if(preg_match("/{$docketFVal}/i", $sentDTURValue->value)){
                                                $emailMatchArray[] = @$sentDTURValue->sentDocketValue->email_sent_docket_id;
                                            }
                                        }
                                    }

                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->email_sent_docket_id;
                                }elseif ($sentEDValue->docketFieldInfo->docket_field_category_id == 18){

                                    if($sentEDValue->SentEmailDocValYesNoValueInfo){
                                        foreach($sentEDValue->SentEmailDocValYesNoValueInfo as $SentEDVYNVInfo){
                                            if(preg_match("/{$docketFVal}/i", $SentEDVYNVInfo->value)){
                                                $emailMatchArray[] = @$SentEDVYNVInfo->sentDocketValue->email_sent_docket_id;
                                            }
                                        }
                                    }

                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->email_sent_docket_id;
                                }elseif ($sentEDValue->docketFieldInfo->docket_field_category_id == 20){
                                    if($sentEDValue->emailSentDocManualTimer){
                                        foreach($sentEDValue->emailSentDocManualTimer as $emailSDMTimer){
                                            if(preg_match("/{$docketFVal}/i", $emailSDMTimer->value)){
                                                $emailMatchArray[] = @$emailSDMTimer->sentDocketValue->email_sent_docket_id;
                                            }
                                        }
                                    }

                                    if($sentEDValue->emailSentDocManualTimerBrk){
                                        foreach($sentEDValue->emailSentDocManualTimerBrk as $emailSDMTBrk){
                                            if( preg_match("/{$docketFVal}/i", $emailSDMTBrk->reason)){
                                                $emailMatchArray[] = @$emailSDMTBrk->sentDocketValue->email_sent_docket_id;
                                            }
                                            if( preg_match("/{$docketFVal}/i", $emailSDMTBrk->value)){
                                                $emailMatchArray[] = @$emailSDMTBrk->sentDocketValue->email_sent_docket_id;
                                            }
                                        }
                                    }
                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->email_sent_docket_id;

                                }elseif ($sentEDValue->docketFieldInfo->docket_field_category_id  == 22){

                                    if($sentEDValue->sentDocketFieldGridValues){
                                        foreach($sentEDValue->sentDocketFieldGridValues as $sentDFGValues){
                                            if( preg_match("/{$docketFVal}/i", $sentDFGValues->value)){
                                                $emailMatchArray[] = @$sentDFGValues->docket_id;
                                            }
                                        }
                                    }

                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->email_sent_docket_id;
                                }
                                else{
                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->email_sent_docket_id;
                                }
                            }
                        }
                    }
                }
            }
            if(count($emailMatchArray) != 0){
                $sentemailDocketsQuery->whereIn('id',$emailMatchArray);
            }
        }

        if($request->id){
            $sentemailDocketsQuery->where('id',$request->id);
        }
        if($request->date) {
            if ($request->date == 2) {
                if ($request->from) {
                    $carbonDateFrom = Carbon::parse($request->from);
                    unset($tempSentDocket);
                    $tempSentDocket = array();
                    foreach ($sentemailDocketsQuery->get() as $row) {
                        $flag = false;
                        $docketTemplate = $row->docketInfo();
                        $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                        $getAllSentDocketDateFieldsValues = EmailSentDocketValue::whereIn('docket_field_id', $docketFieldsIds)->where('email_sent_docket_id', $row->id)->get();
                        foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                            try{
                                Carbon::parse($rowValue->value);
                                if ($rowValue->value != "" && $rowValue->value != "null") {
                                    if ($carbonDateFrom->lte(Carbon::parse($rowValue->value)))
                                        $flag = true;
                                }
                                if ($flag == true)
                                    break;
                            }catch(\Exception $e) {
                                break;
                            }
                        }

                        if ($flag == true) {
                            $tempSentDocket[] = $row->id;
                        }
                    }
                    unset($sentDocketsQuery);
                    $sentDocketsQuery = EmailSentDocket::whereIn('id', $tempSentDocket);
                }

                if ($request->to) {
                    $carbonDateTo = Carbon::parse($request->to);


                    unset($tempSentDocket);
                    $tempSentDocket = array();
                    foreach ($sentDocketsQuery->get() as $row) {
                        $flag = false;
                        $docketTemplate = $row->docketInfo();
                        $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                        $getAllSentDocketDateFieldsValues = EmailSentDocketValue::whereIn('docket_field_id', $docketFieldsIds)->where('email_sent_docket_id', $row->id)->get();
                        foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                            if ($rowValue->value != "" && $rowValue->value != "null") {
                                if ($carbonDateTo->gte(Carbon::parse($rowValue->value)))
                                    $flag = true;
                            }
                            if ($flag == true)
                                break;
                        }

                        if ($flag == true) {
                            $tempSentDocket[] = $row->id;
                        }
                    }
                    unset($sentDocketsQuery);
                    $sentemailDocketsQuery = EmailSentDocket::whereIn('id', $tempSentDocket);

                }
            }
        }
        $sentEmailDockets     =   $sentemailDocketsQuery->get();
        return $sentEmailDockets;

    }

    public  function AdvanceFilter(Request $request)
    {
        $type = $request->type;
        $folderId =$request->folder_id;
        if ($type == 1) {

            $totalCompany = Company::get();
            $folderItem = FolderItem::where('type', 1)->where('folder_id',$folderId)->where('company_id', Session::get('company_id'))->pluck('ref_id')->toArray();
            $clients = Client::where('company_id', Session::get('company_id'))->orWhere('requested_company_id', Session::get('company_id'))->get();
            $dockets = Docket::where('company_id', Session::get('company_id'))->orderBy('id', 'desc')->get();
            $sentDocketLabel=    DocketLabel::where('company_id',Session::get('company_id'))->orderBy('created_at','desc')->get();


            $sentDocketsQuery = SentDockets::whereIn('id',$folderItem);
            if ($request->invoiceable) {
                $sentDocketsQuery->where('invoiceable', $request->invoiceable);
            }

            if ($request->TemplateId) {
                $sentDocketsQuery->where('docket_id', $request->TemplateId);
            }

            if($request->docketFieldValue){
                $matchId =array();
                foreach ($request->docketFieldValue as $fieldId=>$fieldValues){
                    $fieldDatasVal= explode("-",$fieldValues);
                    $docketFVal = $fieldDatasVal[1];
                    $docketFId = $fieldDatasVal[0];
                    if($docketFVal != "null"){
                        foreach ($sentDocketsQuery->get() as $ite){
                            foreach ($ite->sentDocketValue as $sentDV){
                                if($sentDV->docket_field_id == $docketFId){
                                    if($sentDV->docketFieldInfo->docket_field_category_id ==7){
                                        if($sentDV->sentDocketUnitRateValue){
                                            foreach($sentDV->sentDocketUnitRateValue as $sentDURValue){
                                                if(preg_match("/{$docketFVal}/i", $sentDURValue->value)){
                                                    $matchId[] = @$sentDURValue->sentDocketValue->sent_docket_id;
                                                }
                                            }
                                        }
                                        $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->sent_docket_id;
                                    }elseif($sentDV->docketFieldInfo->docket_field_category_id == 23){
                                        if($sentDV->sentDocketTallyableUnitRateValue){
                                            foreach($sentDV->sentDocketTallyableUnitRateValue as $sentDTURValue){
                                                if(preg_match("/{$docketFVal}/i", $sentDTURValue->value)){
                                                    $matchId[] = @$sentDTURValue->sentDocketValue->sent_docket_id;
                                                }
                                            }
                                        }
                                        $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->sent_docket_id;
                                    }elseif ($sentDV->docketFieldInfo->docket_field_category_id == 18){
                                        if($sentDV->SentDocValYesNoValueInfo){
                                            foreach($sentDV->SentDocValYesNoValueInfo as $SentDVYNValueInfo){
                                                if(preg_match("/{$docketFVal}/i", $SentDVYNValueInfo->value)){
                                                    $matchId[] = @$SentDVYNValueInfo->sentDocketValue->sent_docket_id;
                                                }
                                            }
                                        }
                                        $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->sent_docket_id;
                                    }elseif ($sentDV->docketFieldInfo->docket_field_category_id == 20){
                                        if($sentDV->sentDocketManualTimer){
                                            foreach($sentDV->sentDocketManualTimer as $sentDMTimer){
                                                if(preg_match("/{$docketFVal}/i", $sentDMTimer->value)){
                                                    $matchId[] = @$sentDMTimer->sentDocketValue->sent_docket_id;
                                                }
                                            }
                                        }
                                        if($sentDV->sentDocketManualTimerBreak){
                                            foreach($sentDV->sentDocketManualTimerBreak as $sentDMTBreak){
                                                if( preg_match("/{$docketFVal}/i", $sentDMTBreak->reason)){
                                                    $matchId[] = @$sentDMTBreak->sentDocketValue->sent_docket_id;
                                                }
                                                if( preg_match("/{$docketFVal}/i", $sentDMTBreak->value)){
                                                    $matchId[] = @$sentDMTBreak->sentDocketValue->sent_docket_id;
                                                }
                                            }
                                        }
                                        $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->sent_docket_id;
                                    }elseif ($sentDV->docketFieldInfo->docket_field_category_id  == 22){
                                        if($sentDV->sentDocketFieldGridValues){
                                            foreach($sentDV->sentDocketFieldGridValues as $sentDFGValues){
                                                if( preg_match("/{$docketFVal}/i", $sentDFGValues->value)){
                                                    $matchId[] = @$sentDFGValues->docket_id;
                                                }
                                            }
                                        }
                                        $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->sent_docket_id;
                                    }else{
                                        $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$docketFId)->where('value','like', '%' . $docketFVal . '%')->first()->sent_docket_id;
                                    }
                                }


                            }
                        }
                    }
                }
                if(count($matchId) != 0){
                    $sentDocketsQuery->whereIn('id',array_unique($matchId));
                }
            }

            if ($request->id) {
                $sentDocketsQuery->where('company_docket_id', $request->id);
            }

            if ($request->date) {
                if ($request->date == 1) {
                    if ($request->from) {
                        $sentDocketsQuery->whereDate('created_at', '>=', Carbon::parse($request->from)->format('Y-m-d'));
                    }
                    if ($request->to) {
                        $sentDocketsQuery->whereDate('created_at', '<=', Carbon::parse($request->to)->format('Y-m-d'));
                    }
                }
            }

//            $sentDockets    =   SentDockets::where('sender_company_id', Session::get('company_id'))->pluck('id')->toArray();
            $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
            $companyAdmin[] =   Company::find(Session::get('company_id'))->user_id;
            $totalemployeId =   array_merge($employeeIds,$companyAdmin);

            //get possible sent docket recipients
            $sentDocketRecipient    =   SentDocketRecipient::whereIn('user_id',$totalemployeId)->pluck('sent_docket_id')->toArray();

            $possibleSentDockets    =   array_unique(array_merge($folderItem,$sentDocketRecipient));
            if($request->company){

                $companyEmployeeId  =   Employee::where('company_id', $request->company)->pluck('user_id')->toArray();
                $companyAdminId[]     =   Company::find($request->company)->user_id;
                $totalCompanyEmployeeID     =   array_merge($companyEmployeeId,$companyAdminId);

                if($request->employee){
                    $totalCompanyEmployeeID  =    array((int)$request->employee);
                }


                $companySentDocketId           =    SentDockets::whereIn('id',$possibleSentDockets)->whereIn('user_id',$totalCompanyEmployeeID)->pluck('id')->toArray();
                $companySentDocketRecipient    =   SentDocketRecipient::whereIn('sent_docket_id',$possibleSentDockets)->whereIn('user_id',$totalCompanyEmployeeID)->pluck('sent_docket_id')->toArray();

                $possibleSentDockets    =   array_unique(array_merge($companySentDocketId,$companySentDocketRecipient));

            }

            $sentDocketsQuery->whereIn('id',$possibleSentDockets);

            //check docket filed date value
            if($request->date) {
                if ($request->date == 2) {
                    if ($request->from) {
                        $carbonDateFrom = Carbon::parse($request->from);
                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                try{
                                    Carbon::parse($rowValue->value);
                                    if ($rowValue->value != "" && $rowValue->value != "null") {
                                        if ($carbonDateFrom->lte(Carbon::parse($rowValue->value)))
                                            $flag = true;
                                    }
                                    if ($flag == true)
                                        break;
                                }catch(\Exception $e) {
                                    break;
                                }
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentDocketsQuery = SentDockets::whereIn('id', $tempSentDocket);
                    }

                    if ($request->to) {
                        $carbonDateTo = Carbon::parse($request->to);


                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                if ($rowValue->value != "" && $rowValue->value != "null") {
                                    if ($carbonDateTo->gte(Carbon::parse($rowValue->value)))
                                        $flag = true;
                                }
                                if ($flag == true)
                                    break;
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentDocketsQuery = SentDockets::whereIn('id', $tempSentDocket);

                    }
                }
            }


            $sentDocket      =  $sentDocketsQuery->get();
            return view('dashboard.company.folder.advanceSearch.filterView',compact('type','sentDocket','dockets','clients','totalCompany','sentDocketLabel'));

        }
        elseif ($type == 2){
            $sentDocketLabel=    DocketLabel::where('company_id',Session::get('company_id'))->orderBy('created_at','desc')->get();
            $folderItem = FolderItem::where('type', 3)->where('folder_id',$folderId)->where('company_id', Session::get('company_id'))->pluck('ref_id')->toArray();
            $dockets        =   EmailSentDocket::whereIn('id',$folderItem)->where('company_id',Session::get('company_id'))->orderBy('id','desc')->get();

            $employes = Employee::where('company_id',Session::get('company_id'))->get()->pluck('user_id')->toArray();
            $company   =   array(Company::where('id',Session::get('company_id'))->first()->user_id);
            $total =  array_merge($company, $employes);
            $docketusedbyemail = EmailSentDocket::select('docket_id')->whereIn('user_id',$total)->groupBy('docket_id')->get();
            $folderItem = FolderItem::where('type', 3)->where('folder_id',$folderId)->where('company_id', Session::get('company_id'))->pluck('ref_id')->toArray();
            $sentEmailDockets = $this->emailFilter($request,$folderItem);
            return view('dashboard.company.folder.advanceSearch.filterView',compact('type','sentEmailDockets','request','dockets','docketusedbyemail','sentDocketLabel'));
        }
        elseif ($type == 3){
            $clients = Client::where('company_id', Session::get('company_id'))->orWhere('requested_company_id', Session::get('company_id'))->get();
            $invoice = Invoice::where('company_id', Session::get('company_id'))->orderBy('id', 'desc')->get();
            $totalCompany = Company::where('id', '!=', Session::get('company_id'))->get();
            $folderItem = FolderItem::where('type', 2)->where('folder_id',$folderId)->where('company_id', Session::get('company_id'))->pluck('ref_id')->toArray();

            $sentInvoiceQuery = SentInvoice::whereIn('id',$folderItem);

            if ($request->date) {
                if ($request->date == 1) {
                    if ($request->from)
                        $sentInvoiceQuery->whereDate('created_at', '>=', Carbon::parse($request->from)->format('Y-m-d'));

                    if ($request->to)
                        $sentInvoiceQuery->whereDate('created_at', '<=', Carbon::parse($request->to)->format('Y-m-d'));
                }
            }

            if ($request->TemplateId) {
                $sentInvoiceQuery->where('invoice_id', $request->TemplateId);
            }
            if($request->id) {
                $sentInvoiceQuery->where('company_invoice_id', $request->id);
            }


            if($request->company){
                $possibleInvoices   =   SentInvoice::whereIn('id',$folderItem)->get();
                $tempPossibleInvoices   = array();

                foreach ($possibleInvoices as $sentInvoice){
                    if($sentInvoice->company_id==$request->company){
                        $tempPossibleInvoices[] = $sentInvoice->id;
                    }elseif($sentInvoice->receiver_company_id==$request->company){
                        $tempPossibleInvoices[] = $sentInvoice->id;
                    }
                }


                $sentInvoiceQuery->whereIn('id',$tempPossibleInvoices);

                if($request->employee){
                    $sentInvoiceQuery->where(function($query) use($request) {
                        $query->where('receiver_user_id',$request->employee)->orWhere('user_id',$request->employee);
                    });
                }
            }

//            if($request->company){
//                $sentInvoiceQuery->where('receiver_company_id',$request->company);
//
//                if($request->employee){
//                    $sentInvoiceQuery->where('receiver_user_id',$request->employee);
//                }
//            }
//            if($request->company){
//                $sentInvoiceQuery->where('company_id',$request->company);
//                if($request->employee){
//                    $sentInvoiceQuery->where('user_id',$request->employee);
//                }
//            }
            $sentInvoice     =   $sentInvoiceQuery->get();

            $invoices   =   Invoice::where('company_id',Session::get('company_id'))->orderBy('id','desc')->get();
            $totalInvoiceLabel =   Invoice_Label::where('company_id',Session::get('company_id'))->count();
            return view('dashboard.company.folder.advanceSearch.filterView',compact('type','invoices','sentInvoice','totalCompany','invoice','clients','totalInvoiceLabel'));

        }elseif ($type == 4){
            $companyFolder=Folder::where('company_id',Session::get('company_id'))->where('root_id',0)->orderBy('name','Asc')->get();
            $folder = Folder::where('company_id',Session::get('company_id'))->get();
            $totalInvoiceLabel =   Invoice_Label::where('company_id',Session::get('company_id'))->count();
            $sentEmailedInvoiceLabel=    Invoice_Label::where('company_id',Session::get('company_id'))->orderBy('created_at','desc')->get();
            $folderItem = FolderItem::where('type', 4)->where('folder_id',$folderId)->where('company_id', Session::get('company_id'))->pluck('ref_id')->toArray();
            $sentInvoiceQuery = EmailSentInvoice::whereIn('id',$folderItem);
            if ($request->date) {
                if ($request->date == 1) {
                    if ($request->from)
                        $sentInvoiceQuery->whereDate('created_at', '>=', Carbon::parse($request->from)->format('Y-m-d'));

                    if ($request->to)
                        $sentInvoiceQuery->whereDate('created_at', '<=', Carbon::parse($request->to)->format('Y-m-d'));
                }
            }
            if ($request->TemplateId) {
                $sentInvoiceQuery->where('invoice_id', $request->TemplateId);
            }
            if($request->id) {
                $sentInvoiceQuery->where('company_invoice_id', $request->id);
            }
            if($request->email){
                $emailUserQuery= EmailUser::where('email',$request->email);
                if($emailUserQuery->count()){
                    $sentInvoiceQuery->where('receiver_user_id', $emailUserQuery->first()->id);
                }else{

                }
            }

            $sentEmailInvoice     =   $sentInvoiceQuery->get();

            return view('dashboard.company.folder.advanceSearch.filterView',compact('type','companyFolder','folder','totalInvoiceLabel','sentEmailedInvoiceLabel','sentEmailInvoice'));

        }



    }


    public  function folderLabelSave(Request $request){

     if($request->value== null ){
         return response()->json(['status'=>false,'message'=>'Invalid action ! Please select Docket Label.']);
     }else{
         if ($request->type == 1){
             $folder_item_id = $request->id;
             $data = array();
             foreach ($request->value as $assigndocketlabel) {
                 if(SentDocketLabel::where('sent_docket_id',$folder_item_id)->where('docket_label_id',$assigndocketlabel)->count()==0) {
                     $sentDocketLabel = new SentDocketLabel ();
                     $sentDocketLabel->sent_docket_id = $folder_item_id;
                     $sentDocketLabel->docket_label_id = $assigndocketlabel;
                     $sentDocketLabel->save();
                     $data [] = array(
                         'id' => $sentDocketLabel->id,
                         'title' => $sentDocketLabel->docketLabel->title,
                         'icon' => AmazoneBucket::url() . $sentDocketLabel->docketLabel->icon,
                         'colour' => $sentDocketLabel->docketLabel->color,
                     );
                 }
             }
             $html = array();
             foreach ($data as $items ){
                 $html[] = '<span style=" background:'.$items['colour'].';display:inline-block;font-size:10px;padding-right: 23px;padding-bottom: 5px;margin-left: 0px;margin-right: 5px" class="badge badge-pill badge-primary docketDelete'.$items['id'].'"><img style="margin-right: 4px" src="'.$items['icon'].'" height="10" width="10">'.$items['title'].'<button  data-toggle="modal" data-target="#deleteLabel" data-type="1" data-id="'.$items['id'].'"  class="btn btn-raised btn-danger btn-xs"  style="    margin: -1px 0px 0 6px;font-size: 9px;border-radius: 34px;width: 15px;height: 14px;padding: 1px 3px 0px 3px;background: #5a5a5a;position: absolute;"><span  class="glyphicon glyphicon-remove" aria-hidden="true"  /> </button></span>';
             }
             return response()->json(['status'=>true,'message'=>'Docket label attached successfully','labelData'=>implode(" ",$html),'id'=>"docketLabelIdentify".$folder_item_id]);
         }
         else if($request->type == 2){
             $folder_item_id = $request->id;
             $data = array();
             foreach ($request->value as $assigndocketlabel) {
                 if (SentEmailDocketLabel::where('email_sent_docket_id', $folder_item_id)->where('docket_label_id', $assigndocketlabel)->count() == 0) {
                     $sentDocketLabel = new SentEmailDocketLabel ();
                     $sentDocketLabel->email_sent_docket_id = $folder_item_id;
                     $sentDocketLabel->docket_label_id = $assigndocketlabel;
                     $sentDocketLabel->save();

                     $data [] = array(
                         'id' => $sentDocketLabel->id,
                         'title' => $sentDocketLabel->docketLabel->title,
                         'icon' => AmazoneBucket::url() . $sentDocketLabel->docketLabel->icon,
                         'colour' => $sentDocketLabel->docketLabel->color,
                     );
                 }
             }
             $html = array();
             foreach ($data as $items ){
                 $html[] = '<span style=" background:'.$items['colour'].';display:inline-block;font-size:10px;padding-right: 23px;padding-bottom: 5px;margin-left: 0px;margin-right: 5px" class="badge badge-pill badge-primary emailDocketDelete'.$items['id'].'"><img style="margin-right: 4px" src="'.$items['icon'].'" height="10" width="10">'.$items['title'].'<button  data-toggle="modal" data-target="#deleteLabel" data-type="2" data-id="'.$items['id'].'"  class="btn btn-raised btn-danger btn-xs"  style="   margin: -1px 0px 0 6px;font-size: 9px;border-radius: 34px;width: 15px;height: 14px;padding: 1px 3px 0px 3px;background: #5a5a5a;position: absolute;"><span  class="glyphicon glyphicon-remove" aria-hidden="true"  /> </button></span>';
             }
             return response()->json(['status'=>true,'message'=>'Docket label attached successfully','labelData'=>implode(" ",$html),'id'=>'emailDocketLabelIdentify'.$folder_item_id]);

         }
     }




    }

    public  function folderInvoiceLabelSave(Request $request){
        if($request->value== null ){
            return response()->json(['status'=>false,'message'=>'Invalid action ! Please select Invoice Label.']);
        }else {
            if ($request->type == 3) {
                $folder_item_id = $request->id;
                $data = array();
                foreach ($request->value as $assignInvoicelabel) {
                    if (SentInvoiceLabel::where('sent_invoice_id', $folder_item_id)->where('invoice_label_id', $assignInvoicelabel)->count() == 0) {
                        $sentinvoiceLabel = new SentInvoiceLabel();
                        $sentinvoiceLabel->sent_invoice_id = $folder_item_id;
                        $sentinvoiceLabel->invoice_label_id = $assignInvoicelabel;
                        $sentinvoiceLabel->save();

                        $data [] = array(
                            'id' => $sentinvoiceLabel->id,
                            'title' => $sentinvoiceLabel->invoiceLabel->title,
                            'icon' => AmazoneBucket::url() . $sentinvoiceLabel->invoiceLabel->icon,
                            'colour' => $sentinvoiceLabel->invoiceLabel->color,
                        );
                    }
                }

                $html = array();
                foreach ($data as $items) {
                    $html[] = '<span style=" background:' . $items['colour'] . ';display:inline-block;font-size:10px;padding-right: 23px;padding-bottom: 5px;margin-left: 0px;margin-right: 5px" class="badge badge-pill badge-primary invoiceDelete' . $items['id'] . '"><img style="margin-right: 4px" src="' . $items['icon'] . '" height="10" width="10">' . $items['title'] . '<button  data-toggle="modal" data-target="#deleteLabel" data-type="3" data-id="' . $items['id'] . '"  class="btn btn-raised btn-danger btn-xs"  style="   margin: -1px 0px 0 6px;font-size: 9px;border-radius: 34px;width: 15px;height: 14px;padding: 1px 3px 0px 3px;background: #5a5a5a;position: absolute;"><span  class="glyphicon glyphicon-remove" aria-hidden="true"  /> </button></span>';
                }
                return response()->json(['status' => true, 'message' => 'Invoice label attached successfully', 'labelData' => implode(" ", $html), 'id' => 'invoiceLabelIdentify' . $folder_item_id]);


                //                else{
                //
                //                    $data = array();
                //                    $labelAlreadyTaken = SentInvoiceLabel::where('sent_invoice_id',$folder_item_id)->whereIn('invoice_label_id',$request->value)->get();
                //                    foreach ($labelAlreadyTaken as $items){
                //                        $data [] = $items->invoiceLabel->title;
                //                    }
                //                    return response()->json(['status'=>false,'message'=>'"'. implode(", ",$data ) .'"'.' Already used please reselect label.' ]);
                //
                //                }

            } else if ($request->type == 4) {
                $folder_item_id = $request->id;
                $data = array();
                foreach ($request->value as $assignInvoicelabel) {
                    if (EmailSentInvoiceLabel::where('email_sent_id', $folder_item_id)->where('invoice_label_id', $assignInvoicelabel)->count() == 0) {
                        $EmailSentInvoiceLabel = new EmailSentInvoiceLabel();
                        $EmailSentInvoiceLabel->email_sent_id = $folder_item_id;
                        $EmailSentInvoiceLabel->invoice_label_id = $assignInvoicelabel;
                        $EmailSentInvoiceLabel->save();

                        $data [] = array(
                            'id' => $EmailSentInvoiceLabel->id,
                            'title' => $EmailSentInvoiceLabel->invoiceLabel->title,
                            'icon' => AmazoneBucket::url() . $EmailSentInvoiceLabel->invoiceLabel->icon,
                            'colour' => $EmailSentInvoiceLabel->invoiceLabel->color,
                        );
                    }
                }
                $html = array();
                foreach ($data as $items) {
                    $html[] = '<span  style=" background:' . $items['colour'] . ';display:inline-block;font-size:10px;padding-right: 23px;padding-bottom: 5px;margin-left: 0px;margin-right: 5px" class="badge badge-pill badge-primary emailInvoiceDelete' . $items['id'] . '"><img style="margin-right: 4px" src="' . $items['icon'] . '" height="10" width="10">' . $items['title'] . '<button  data-toggle="modal" data-target="#deleteLabel" data-type="4" data-id="' . $items['id'] . '"  class="btn btn-raised btn-danger btn-xs"  style="   margin: -1px 0px 0 6px;font-size: 9px;border-radius: 34px;width: 15px;height: 14px;padding: 1px 3px 0px 3px;background: #5a5a5a;position: absolute;"><span  class="glyphicon glyphicon-remove" aria-hidden="true"  /> </button></span>';
                }
                return response()->json(['status' => true, 'message' => 'Email Sent Invoice label attached successfully', 'labelData' => implode(" ", $html), 'id' => 'emailInvoiceLabelIdentify' . $folder_item_id]);

                //                else{
                //                    $data = array();
                //                    $labelAlreadyTaken = EmailSentInvoiceLabel::where('email_sent_id',$folder_item_id)->whereIn('invoice_label_id',$request->value)->get();
                //                    foreach ($labelAlreadyTaken as $items){
                //                        $data [] = $items->invoiceLabel->title;
                //                    }
                //                    return response()->json(['status'=>false,'message'=>'"'. implode(", ",$data ) .'"'.' Already used please reselect label.' ]);
                //
                //                }
            }
        }

    }


    public function deleteAssignLable(Request $request){
        if($request->type == 1) {
            if ( $deleteAssignLabel = SentDocketLabel::where('id', $request->id)->count()!=0){
                $deleteAssignLabel = SentDocketLabel::where('id', $request->id)->firstOrFail();
                $deleteAssignLabel->delete();
                return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.', 'id'=>'docketDelete'.$request->id ]);
            }else{
                return response()->json(['status'=>false,'message'=>'Invalid action ! Please try with valid action.']);
            }
        }else if ($request->type == 2){
            if ( $deleteAssignLabel = SentEmailDocketLabel::where('id', $request->id)->count()!=0){
                $deleteAssignLabel = SentEmailDocketLabel::where('id', $request->id)->firstOrFail();
                $deleteAssignLabel->delete();
                return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.', 'id'=>'emailDocketDelete'.$request->id ]);
            }else{
                return response()->json(['status'=>false,'message'=>'Invalid action ! Please try with valid action.']);
            }
        }else if($request->type == 3){
            if ( $deleteAssignLabel = SentInvoiceLabel::where('id', $request->id)->count()!=0){
                $deleteAssignLabel = SentInvoiceLabel::where('id', $request->id)->firstOrFail();
                $deleteAssignLabel->delete();
                return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.', 'id'=>'invoiceDelete'.$request->id ]);
            }else{
                return response()->json(['status'=>false,'message'=>'Invalid action ! Please try with valid action.']);
            }
        }else if($request->type == 4){
            if ( $deleteAssignLabel = EmailSentInvoiceLabel::where('id', $request->id)->count()!=0){
                $deleteAssignLabel = EmailSentInvoiceLabel::where('id', $request->id)->firstOrFail();
                $deleteAssignLabel->delete();
                return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.', 'id'=>'emailInvoiceDelete'.$request->id ]);
            }else{
                return response()->json(['status'=>false,'message'=>'Invalid action ! Please try with valid action.']);
            }

        }

    }


    public function assignTemplateFolder(Request $request){
       if ($request->folderId=="" || $request->type=="" || $request->templateId==""){
           return response()->json(['status'=>false,'message'=>'Invalid action ! Please try with valid action.']);
       }else{
           if (TemplateAssignFolder::where('folder_id',$request->folderId)->where('template_id',$request->templateId)->where('type', $request->type)->count()==0){
               $templateAssignFolder = new TemplateAssignFolder();
               $templateAssignFolder->folder_id = $request->folderId;
               $templateAssignFolder->type = $request->type;
               $templateAssignFolder->template_id = $request->templateId;
               $templateAssignFolder->save();

               $folderData= Folder::where('company_id',Session::get('company_id'))->get();
               $treeArray = array();
               foreach ($folderData as $test) {
                   $treeArray[] = array(
                       'id' => $test->id,
                       'parent_id' => $test->root_id,
                       'name' => $test->name,
                       'slug' => $test->slug,
                   );
               }
               $txtTree[0]="";
               foreach($treeArray as $branch){
                   if(isset($txtTree[$branch['parent_id']])) {
                       $txtTree[$branch['id']] = $txtTree[$branch['parent_id']] . $branch['slug'] . "/";
                   }
               }

               $datas = array();
               if(@$txtTree){
                   foreach ($txtTree as $key => $value){
                       if($key != 0) {
                           $datas[] = array(
                               "id"=> $key,
                               "value"=>rtrim($value,'/'),
                               "space" =>str_repeat('&nbsp;', count(explode('/',$value))-2),
                               'name'=> array_slice(explode('/',$value), -2, 1)
                           );


                       }
                   }
               }

               $data= (new Collection($datas))->sortBy('value');

               foreach ($data as $items){
                   if ($items['id'] == $request->folderId){
                       $folderName = $items['value'];
                   }
               }
               $buttonConfig = array();
               $buttonConfig[]= '<a  data-toggle="modal" data-target="#unassignFolderModal" data-id="'.$templateAssignFolder->template_id.'" data-name="'.$request->name.'" data-folder="'.$templateAssignFolder->folder_id.'" data-folderId="'.$templateAssignFolder->id.'" style="background-color: #ff5722;" class="btn btn-raised btn-info btn-xs buttonChanger'.$templateAssignFolder->template_id.'" ><i class="fa fa-folder-o" aria-hidden="true"></i> UnAssign Folder</a>';
               $buttonConfig[]='.buttonChanger'.$templateAssignFolder->template_id;
               $buttonConfig[]='.assignedFolderLink'.$templateAssignFolder->template_id;
               $buttonConfig[]='<div class="assignedFolderLink'. $templateAssignFolder->template_id.'"> <i style="color: #EFCE4A;" class="fa fa-folder" aria-hidden="true"></i> '.$folderName.'</div>';

               \Event::fire(new ChatEvent(['buttonConfig'=>$buttonConfig]));

               return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.','buttonConfig'=>$buttonConfig]);
           }else{
               return response()->json(['status'=>false,'message'=>'Invalid action ! This Folder already Assign.']);
           }
       }
    }

    public  function unassignTemplateFolder(Request $request){
        if ($request->folderId == null){
              return response()->json(['status'=>false,'message'=>'Invalid action ! Please try with valid action.']);
        }else{
            if ( TemplateAssignFolder::where('id',$request->folderId)->first()->count()==0){
                return response()->json(['status'=>false,'message'=>'Invalid action ! Please try with valid action.']);
            }else{
                $templateAssignFolder   =    TemplateAssignFolder::where('id',$request->folderId)->firstOrFail();
                $templateAssignFolder->delete();
                $buttonConfig = array();
                $buttonConfig[]= '<a  data-toggle="modal" data-target="#assignFolderModal" data-id="'.$request->templateId.'" data-name="'.$request->templateName.'" style="background-color: #ff9b00;" class="btn btn-raised btn-info btn-xs buttonChanger'.$request->templateId.'" ><i class="fa fa-folder-o" aria-hidden="true"></i> Assign Folder</a>';
                $buttonConfig[]='.buttonChanger'.$templateAssignFolder->template_id;
                $buttonConfig[]='.assignedFolderLink'.$templateAssignFolder->template_id;
                $buttonConfig[]='<div class="assignedFolderLink'. $templateAssignFolder->template_id.'">Not assigned yet.</div>';
                return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.','buttonConfig'=>$buttonConfig]);
            }
        }
    }


    public function cancelRtItems(Request $request){
        if ($request->type == 1){
            if(SentDockets::where('id',$request->id)->where('sender_company_id',Session::get('company_id'))->where('is_cancel',0)->count()==1){
                SentDockets::where('id',$request->id)->where('sender_company_id',Session::get('company_id'))->where('is_cancel',0)->update(['is_cancel'=>1]);
                return response()->json(['status'=>true,'message'=>'Docket Cancel successfully.' ,'id'=>$request->id]);
            }
        }else if ($request->type == 2){
            if(Invoice::where('id',$request->id)->where('company_id',Session::get('company_id'))->where('is_cancel',0)->count()==1){
                Invoice::where('id',$request->id)->where('company_id',Session::get('company_id'))->where('is_cancel',0)->update(['is_cancel'=>1]);
                return response()->json(['status'=>true,'message'=>'Invoice Cancel successfully.','id'=>$request->id]);
            }
        }

    }

    public  function viewFolderReload(Request $request){
        $type = "reload";
        $folderId   =   Input::get('folderId');

        $folder     =   Folder::where('id', $folderId)->get()->first();


      if($folder != null){
          if($folder->company_id!=Session::get('company_id')){echo "<br>&nbsp;&nbsp;&nbsp;Invalid attempt!"; exit();}
          $company    =   Company::findOrfail(Session::get('company_id'));

          $searchKey = Input::get('search');
          if($request->items == ""){ $items= 10; }
          else{ $items = $request->items; }

          if(Input::get('search')){
              $searchKey      =   Input::get('search');
              $folderItems    =   FolderItem::where('folder_id',$folderId)->where('company_id',Session::get('company_id'))->get();
              $sentDocketFolderItem       =   $folderItems->where('type',1)->pluck('ref_id')->toArray();
              $sentEmailDocketFolderItem  =   $folderItems->where('type',3)->pluck('ref_id')->toArray();
              $sentInvoiceFolderItem      =   $folderItems->where('type',2)->pluck('ref_id')->toArray();
              $sentEmailInvoiceFolderItem =   $folderItems->where('type',4)->pluck('ref_id')->toArray();

              if($folder->type == 1){
                  $sentDocketQuery        =   SentDockets::with('recipientInfo.userInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentDocketFolderItem)->onlyTrashed()->get();
                  $sentEmailDocketQuery   =   EmailSentDocket::with('recipientInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentEmailDocketFolderItem)->onlyTrashed()->get();
                  $trashFolder = true;
              }else{
                  $sentDocketQuery        =   SentDockets::with('recipientInfo.userInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentDocketFolderItem)->get();
                  $sentEmailDocketQuery   =   EmailSentDocket::with('recipientInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentEmailDocketFolderItem)->get();
                  $trashFolder = false;
              }


              $sentInvoiceQuery       =   SentInvoice::whereIn('id',$sentInvoiceFolderItem)->get();
              $sentEmailInvoiceQuery  =   EmailSentInvoice::whereIn('id',$sentEmailInvoiceFolderItem)->get();

              $sentDockets= array();
              $docketsEmail= array();
              $sentInvoices =array();
              $emailInvoices =array();

              foreach ($sentDocketQuery as $sentDocketQuerys){
                  if(preg_match("/".$searchKey."/i",$sentDocketQuerys->sender_name) || preg_match("/".$searchKey."/i",$sentDocketQuerys->company_name)){ $sentDockets [] =  $sentDocketQuerys; continue; }
                  if(preg_match("/" . $searchKey . "/i", $sentDocketQuerys->template_title)) { $sentDockets[] = $sentDocketQuerys; continue; }
                  if(preg_match("/" . $searchKey . "/i", Carbon::parse($sentDocketQuerys->created_at)->format('d-M-Y'))) { $sentDockets[] = $sentDocketQuerys; continue; }
                  if(preg_match("/" . $searchKey . "/i", $sentDocketQuerys->company_docket_id)) { $sentDockets[] = $sentDocketQuerys; continue; }

                  if($sentDocketQuerys->recipientInfo){
                      foreach ($sentDocketQuerys->recipientInfo as $recipient):
                          $receiversName = @$recipient->userInfo->first_name . " " . @$recipient->userInfo->last_name;
                          if (preg_match("/" . $searchKey . "/i", $receiversName)) { $sentDockets[] = $sentDocketQuerys; break; }
                          $companyName = "";
                          $employeeQuery = Employee::where('user_id', $recipient->user_id)->get();
                          if($employeeQuery->count() > 0){ $companyName = $employeeQuery->first()->companyInfo->name;}
                          else{ $companyQuery = Company::where('user_id', $recipient->user_id)->get();
                              if ($companyQuery->count() > 0)
                                  $companyName = $companyQuery->first()->name;
                          }
                          if (preg_match("/" . $searchKey . "/i", $companyName)) {
                              $sentDockets[] = $sentDocketQuerys;
                              break;
                          }
                      endforeach;
                  }

                  //  for docket field value
                  if($sentDocketQuerys->sentDocketValue) {
                      foreach ($sentDocketQuerys->sentDocketValue as $rowValue) {
                          if (@$rowValue->docketFieldInfo->docket_field_category_id != 5 && @$rowValue->docketFieldInfo->docket_field_category_id != 7 && @$rowValue->docketFieldInfo->docket_field_category_id != 8 && @$rowValue->docketFieldInfo->docket_field_category_id != 9 &&
                              @$rowValue->docketFieldInfo->docket_field_category_id != 12 && @$rowValue->docketFieldInfo->docket_field_category_id != 13 && @$rowValue->docketFieldInfo->docket_field_category_id != 14 && @$rowValue->docketFieldInfo->docket_field_category_id != 22
                          ) {
                              if (preg_match("/" . $searchKey . "/i", $rowValue->value)) {
                                  $sentDockets[] = $sentDocketQuerys;
                                  break;
                              }
                          }
                      }
                  }

                  //  for docket field value
                  if($sentDocketQuerys->sentDocketValue) {
                      foreach ($sentDocketQuerys->sentDocketValue as $rowValue) {
                          if (@$rowValue->docketFieldInfo->docket_field_category_id != 5 && @$rowValue->docketFieldInfo->docket_field_category_id != 7 && @$rowValue->docketFieldInfo->docket_field_category_id != 8 && @$rowValue->docketFieldInfo->docket_field_category_id != 9 &&
                              @$rowValue->docketFieldInfo->docket_field_category_id != 12 && @$rowValue->docketFieldInfo->docket_field_category_id != 13 && @$rowValue->docketFieldInfo->docket_field_category_id != 14 && @$rowValue->docketFieldInfo->docket_field_category_id != 22
                          ) {
                              if (preg_match("/" . $searchKey . "/i", $rowValue->value)) {
                                  $sentDockets[] = $sentDocketQuerys;
                                  break;
                              }
                          }
                      }
                  }
              }

              foreach ($sentEmailDocketQuery as $sentEmailDocketQuerys){
                  if((preg_match("/".$searchKey."/i",$sentEmailDocketQuerys->sender_name) || preg_match("/".$searchKey."/i",$sentEmailDocketQuerys->company_name))){ $docketsEmail [] =  $sentEmailDocketQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",$sentEmailDocketQuerys->template_title)){ $docketsEmail[]   =   $sentEmailDocketQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",Carbon::parse($sentEmailDocketQuerys->created_at)->format('d-M-Y'))) { $docketsEmail[]   =   $sentEmailDocketQuerys; continue; }
                  if(preg_match('/('.$searchKey.')/',  $sentEmailDocketQuerys->company_docket_id)){  $docketsEmail [] =  $sentEmailDocketQuerys; continue; }

                  //for receivers Email Company name Company address Company full name
                  foreach($sentEmailDocketQuerys->recipientInfo as $recipient){
                      if(preg_match("/".$searchKey."/i",$recipient->emailUserInfo->email)){ $docketsEmail [] =  $sentEmailDocketQuerys; break; }
                      if(preg_match("/".$searchKey."/i",$recipient->receiver_full_name)){ $docketsEmail [] =  $sentEmailDocketQuerys; break; }
                      if(preg_match("/".$searchKey."/i",$recipient->receiver_company_name)){ $docketsEmail [] =  $sentEmailDocketQuerys; break; }
                      if (preg_match("/".$searchKey."/i",$recipient->receiver_company_address)){ $docketsEmail [] =  $sentEmailDocketQuerys; break; }
                  }

                  //for docket field value
                  if($sentEmailDocketQuerys->sentDocketValue){
                      foreach ($sentEmailDocketQuerys->sentDocketValue as $rowValue){
                          if(@$rowValue->docketFieldInfo->docket_field_category_id!=5 && @$rowValue->docketFieldInfo->docket_field_category_id!=7 && @$rowValue->docketFieldInfo->docket_field_category_id!=8 && @$rowValue->docketFieldInfo->docket_field_category_id!=9 &&
                              @$rowValue->docketFieldInfo->docket_field_category_id!=12 && @$rowValue->docketFieldInfo->docket_field_category_id!=13 && @$rowValue->docketFieldInfo->docket_field_category_id!=14 && @$rowValue->docketFieldInfo->docket_field_category_id!=22){
                              if(preg_match("/".$searchKey."/i",$rowValue->value)) {
                                  $docketsEmail[]   =  $sentEmailDocketQuerys;
                                  break;
                              }
                          }
                      }
                  }
              }

              foreach ($sentInvoiceQuery as $sentInvoiceQuerys){
                  if(preg_match("/" . $searchKey . "/i", $sentInvoiceQuerys->company_invoice_id)){ $sentInvoices [] =  $sentInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",$sentInvoiceQuerys->invoiceInfo->title)){ $sentInvoices[]   =   $sentInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",Carbon::parse($sentInvoiceQuerys->created_at)->format('d-M-Y'))){ $sentInvoices[]   =   $sentInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",$sentInvoiceQuerys->sender_name)){ $sentInvoices[]   =   $sentInvoiceQuerys;  continue; }
                  if(preg_match("/".$searchKey."/i",$sentInvoiceQuerys->sender_name)){ $sentInvoices[]   =   $sentInvoiceQuerys;  continue; }
                  if(preg_match("/".$searchKey."/i",$sentInvoiceQuerys->company_name)){ $sentInvoices[]   =   $sentInvoiceQuerys;  continue; }

                  $receiverName   =   $sentInvoiceQuerys->receiverUserInfo->first_name." ".$sentInvoiceQuerys->receiverUserInfo->last_name;
                  $receiverCompanyName  =   $sentInvoiceQuerys->senderCompanyInfo->name;
                  if(preg_match("/".$searchKey."/i",$receiverName) || preg_match("/".$searchKey."/i",$receiverCompanyName)){
                      $sentInvoices[]   =   $sentInvoiceQuerys; continue;
                  }
              }

              foreach ($sentEmailInvoiceQuery as $sentEmailInvoiceQuerys){
                  if(preg_match("/" . $searchKey . "/i", $sentEmailInvoiceQuerys->company_invoice_id)){  $emailInvoices [] =  $sentEmailInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",Carbon::parse($sentEmailInvoiceQuerys->created_at)->format('d-M-Y'))){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",Carbon::parse($sentEmailInvoiceQuerys->created_at)->format('d M'))){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",Carbon::parse($sentEmailInvoiceQuerys->created_at)->format('F'))){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->template_title)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }

                  if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->sender_name)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->company_name)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->company_address)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }

                  if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->receiverInfo->email)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys; continue; }
                  if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->receiver_full_name)){$emailInvoices[]   =   $sentEmailInvoiceQuerys;  continue; }
                  if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->receiver_company_name)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys;  continue; }
                  if(preg_match("/".$searchKey."/i",$sentEmailInvoiceQuerys->receiver_company_address)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys;  continue; }

                  if ($sentEmailInvoiceQuerys->invoiceDescription){
                      foreach ($sentEmailInvoiceQuerys->invoiceDescription as $invoiceDescription){
                          if(preg_match("/".$searchKey."/i",$invoiceDescription->description)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys;  break; }
                          if(preg_match("/".$searchKey."/i",$invoiceDescription->amount)){ $emailInvoices[]   =   $sentEmailInvoiceQuerys;  break; }
                      }
                  }
              }

              $totalDocket    =   array_merge($sentDockets,$docketsEmail);
              $totalInvoice   =   array_merge($sentInvoices,$emailInvoices);
              $merged         =   array_merge($totalDocket,$totalInvoice);
              $result         =   (new Collection($merged))->sortByDesc('created_at')->paginate($items);
              return view('dashboard.company.folder.view',compact('company','items', 'folder','result','type','trashFolder'));
          }

          if(Input::get('search')== null){
              $folderItems =  FolderItem::where('folder_id',$folderId)->where('company_id',Session::get('company_id'))->get();
              $sentDocketFolderItem       =   $folderItems->where('type',1)->pluck('ref_id')->toArray();
              $sentEmailDocketFolderItem  =   $folderItems->where('type',3)->pluck('ref_id')->toArray();
              $sentInvoiceFolderItem      =   $folderItems->where('type',2)->pluck('ref_id')->toArray();
              $sentEmailInvoiceFolderItem =   $folderItems->where('type',4)->pluck('ref_id')->toArray();
              if($folder->type == 1){
                  $sentDocketQuery        =   SentDockets::with('recipientInfo.userInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentDocketFolderItem)->onlyTrashed()->get();
                  $sentEmailDocketQuery   =   EmailSentDocket::with('recipientInfo','sentDocketValue.docketFieldInfo')->whereIn('id',$sentEmailDocketFolderItem)->onlyTrashed()->get();
                  $trashFolder = true;
              }else {
                  $sentDocketQuery = SentDockets::with('recipientInfo.userInfo', 'sentDocketValue.docketFieldInfo')->whereIn('id', $sentDocketFolderItem)->get();
                  $sentEmailDocketQuery = EmailSentDocket::with('recipientInfo', 'sentDocketValue.docketFieldInfo')->whereIn('id', $sentEmailDocketFolderItem)->get();
                  $trashFolder = false;
              }


              $sentInvoiceQuery       =   SentInvoice::whereIn('id',$sentInvoiceFolderItem)->get();
              $sentEmailInvoiceQuery  =   EmailSentInvoice::whereIn('id',$sentEmailInvoiceFolderItem)->get();

              $totalDocket= $sentDocketQuery->concat($sentEmailDocketQuery);
              $totalInvoice =$sentInvoiceQuery->concat($sentEmailInvoiceQuery);
              $merged = $totalDocket->concat($totalInvoice);
              $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);

              return view('dashboard.company.folder.view',compact('company','items', 'folder','result','type','trashFolder'));

          }
      }



    }

    public function searchFolderById(Request $request){
        $folder = Folder::where('company_id',Session::get('company_id'))->where('id',$request->id)->get();
        $matchedFolderName= array();
        foreach ($folder as $row) {
            $matchedFolderName[]= $row->id;
        }
        $folderName = Folder::where('company_id',Session::get('company_id'))->where('root_id', '=', 0)->orderBy('name','asc')->get();
        $parentHtml='<ul class="rtTree">';

        foreach ($folderName as $folderNames) {
            if($folderNames->type == 0) {
                $totalItemss = "";
                if (count($folderNames->folderItems) != 0) {
                    $totalItemss = '(' . count($folderNames->folderItems) . ')';
                }
                if (!in_array($folderNames->id, $matchedFolderName)) {
                    $parentHtml .= '<li><a href="#"  id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                } else {
                    $parentHtml .= '<li><a href="#" class="active"  id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                }
                if (count($folderNames->childs)) {
                    $parentHtml .= $this->childViewById($folderNames, $matchedFolderName);
                }

                $parentHtml .= '  <div  class="editBtn" id="editBtnId" data-id="' . $folderNames->id . '" data-title="' . $folderNames->name . '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>';
            }
        }

        foreach ($folderName as $folderNames) {
            if($folderNames->type == 1) {
                $totalItemss = "";
                if (count($folderNames->folderItems) != 0) {
                    $totalItemss = '(' . count($folderNames->folderItems) . ')';
                }
                if (!in_array($folderNames->id, $matchedFolderName)) {
                    $parentHtml .= '<li><a href="#"  id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                } else {
                    $parentHtml .= '<li><a href="#" class="active"  id="' . $folderNames->id . '">' . $folderNames->name . '<span style="    position: absolute;right: 4px;">' . $totalItemss . '</span></a>';
                }
                if (count($folderNames->childs)) {
                    $parentHtml .= $this->childViewById($folderNames, $matchedFolderName);
                }

                $parentHtml .= '  <div  data-id="' . $folderNames->id . '" data-title="' . $folderNames->name . '" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>';
            }
        }
        $parentHtml .='<ul>';
        return response()->json(['status'=>true ,'detail'=>$parentHtml]);
    }


    public function childViewById($folderNames,$matchedFolderName){
        $childHtml ='<ul>';
        foreach ($folderNames->childs as $arr) {
            $totalItemsss="";
            if (count($arr->folderItems)!=0){
                $totalItemsss= '('.count($arr->folderItems).')';
            }
            if(count($arr->childs)){
                if (!in_array($arr->id,$matchedFolderName)){
                    $childHtml .='<li><a href="#"  id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a>  ';
                }else{
                    $childHtml .='<li><a href="#" class="active" id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a>  ';
                }
                $childHtml.= $this->childViewById($arr,$matchedFolderName);
                $childHtml.=  '  <div  class="editBtn" id="editBtnId" data-id="'.$folderNames->id.'" data-title="'.$folderNames->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>';

            }else{

                if (!in_array($arr->id,$matchedFolderName)){
                    $childHtml .='<li><a href="#"  id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'.$arr->id.'" data-title="'.$arr->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>';
                }else{
                    $childHtml .='<li><a href="#" class="active"  id="'.$arr->id.'">'.$arr->name.'<span style="    position: absolute;right: 4px;">'.$totalItemsss.'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'.$arr->id.'" data-title="'.$arr->name.'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>';
                }
                $childHtml .="</li>";
            }

        }


        $childHtml .="</ul>";
        return $childHtml;
    }



    public function downloadPdf(Request $request){
        $folder = Folder::where('company_id',Session::get('company_id'))->where('id',$request->folderId)->get()->first();
        $selectDocketIds = array();
        $selectEmailDocketIds = array();
        $selectInvoiceIds= array();
        $selectEmailInvoice = array();

        foreach ($folder->folderItems as $folderItems){
            if($folderItems->type == 1){
                //docket
                $selectDocketIds[] = $folderItems->ref_id;
            }else if($folderItems->type == 2){
                //invoice
                $selectInvoiceIds[] = $folderItems->ref_id;
            }
            else if($folderItems->type == 3){
                //emailDocket
                $selectEmailDocketIds[] = $folderItems->ref_id;
            }
            else if($folderItems->type == 4){
                //emailInvoice
                $selectEmailInvoice[] = $folderItems->ref_id;
            }
        }

        $dir =  'files/folder/pdf/'.str_replace(" ","-",Carbon::now());
        $result = File::makeDirectory($dir);

        if($selectDocketIds){
            $checkDocketId = array();
            foreach ($selectDocketIds as $selectDocketId) {
                $id_get = SentDockets::where('id', $selectDocketId)->pluck('id');
                $checkDocketId[] = $id_get;
            }
            $sentDockets = SentDockets::whereIn('id', array_unique($checkDocketId))->get();
            foreach ($sentDockets as $sentDocket) {
                $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
                $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
                $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
                $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
                $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
                $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
                $pdf = PDF::loadView('pdfTemplate.docketForward',compact('sentDocket','company','docketFields'))->setOptions(['dpi'=> 150,'isRemoteEnabled'=>true]);
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                $fileName=preg_replace('/\s+/', '', $sentDocket->docketInfo->title."".str_replace("-","",$sentDocket->formatted_id));
                $output = $pdf->output();
                $path = base_path($dir.'/'.$fileName.'.pdf');
                file_put_contents($path, $output);
            }
        }


        if($selectEmailDocketIds){
            $checkDocketId = array();
            foreach ($selectEmailDocketIds as $selectDocketId) {
                $id_get = EmailSentDocket::where('id', $selectDocketId)->where('company_id', Session::get('company_id'))->pluck('id');
                $checkDocketId[] = $id_get;
            }
            $sentDockets = EmailSentDocket::whereIn('id', array_unique($checkDocketId))->get();

            foreach ($sentDockets as $sentDocket) {
                $approval_type = array();
                foreach ($sentDocket->recipientInfo as $items){
                    $approval_type[] = array(
                        'id' => $items->id,
                        'status' =>$items->status,
                        'email' => $items->emailUserInfo->email,
                        'approval_time' =>$items->approval_time,
                        'name'=>$items->name,
                        'signature'=>AmazoneBucket::url() . $items->signature
                    );
                }
                $docketFields   =   EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
                $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$sentDocket->id)->where('type',2)->get();
                $pdf = PDF::loadView('pdfTemplate.emailedDocketForward',compact('sentDocket','docketFields','docketTimer','approval_type'))->setOptions(['dpi'=> 150,'isRemoteEnabled'=>true]);
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                $fileName=preg_replace('/\s+/', '', $sentDocket->docketInfo->title."".str_replace("-","",$sentDocket->formatted_id));
                $output = $pdf->output();
                $path = base_path($dir.'/'.$fileName.'.pdf');
                file_put_contents($path, $output);
            }
        }

        if($selectEmailInvoice){
            $checkInvoiceId = array();
            foreach ($selectEmailInvoice as $selectInvoiceId) {
                $id_get = EmailSentInvoice::where('id', $selectInvoiceId)->where('company_id', Session::get('company_id'))->pluck('id');
                $checkInvoiceId[] = $id_get;
            }
            $sentInvoices = EmailSentInvoice::whereIn('id', array_unique($checkInvoiceId))->get();

            foreach ($sentInvoices as $sentInvoice) {
                $sentInvoiceValueQuery    =    EmailSentInvoiceValue::where('email_sent_invoice_id',$selectEmailInvoice)->get();
                $sentInvoiceValue    = array();
                foreach ($sentInvoiceValueQuery as $row){
                    $subFiled   =   [];
                    $sentInvoiceValue[]    =     array('id' => $row->id,
                        'invoice_field_category_id'  =>  $row->invoiceFieldInfo->invoice_field_category_id,
                        'invoice_field_category' =>  $row->label,
                        'label' => $row->label,
                        'value' => $row->value,
                        'subFiled' => $subFiled);
                }
                $invoice     =     EmailSentInvoice::where('id',$selectEmailInvoice)->first();
                $companyDetails =   Company::where('id',$sentInvoice->company_id)->first();
                $invoiceDescription     =    EmailSentInvoiceDescription::where('email_sent_invoice_id',$sentInvoice->id)->get();
                $invoiceSetting =   array();
                //check invoice payment info
                if(EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$sentInvoice->id)->count()==1){
                    $invoiceSetting =   EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$sentInvoice->id)->first();
                }

                $pdf = PDF::loadView('pdfTemplate.emailedInvoiceForward',compact('sentInvoiceValue','companyDetails','sentInvoice','invoiceDescription','invoiceSetting','invoice'));
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                $fileName=preg_replace('/\s+/', '',$sentInvoice->invoiceInfo->title."".str_replace("-","",$sentInvoice->formatted_id));
                $output = $pdf->output();
                $path = base_path($dir.'/'.$fileName.'.pdf');
                file_put_contents($path, $output);
            }

        }

        if($selectInvoiceIds){

            $checkInvoiceId = array();
            foreach ($selectInvoiceIds as $selectInvoiceId) {
                $id_get = SentInvoice::where('id', $selectInvoiceId)->pluck('id');
                $checkInvoiceId[] = $id_get;
            }
            $sentInvoices = SentInvoice::whereIn('id', array_unique($checkInvoiceId))->get();
            foreach ($sentInvoices as $sentInvoice) {
                $invoiceDescription     =    SentInvoiceDescription::where('sent_invoice_id',$sentInvoice->id)->get();
                $sentInvoiceValueQuery    =    SentInvoiceValue::where('sent_invoice_id',$sentInvoice->id)->get();
                $sentInvoiceValue    = array();
                foreach ($sentInvoiceValueQuery as $row){
                    $subFiled   =   [];
                    $sentInvoiceValue[]    =     array('id' => $row->id,
                        'invoice_field_category_id'  =>  $row->invoiceFieldInfo->invoice_field_category_id,
                        'invoice_field_category' =>  $row->label,
                        'label' => $row->label,
                        'value' => $row->value,
                        'subFiled' => $subFiled);
                }

                $invoiceSetting =   array();
                if(SentInvoicePaymentDetail::where('sent_invoice_id',$selectInvoiceIds)->count()==1){
                    $invoiceSetting =   SentInvoicePaymentDetail::where('sent_invoice_id',$sentInvoice->id)->first();
                }
                $pdf = PDF::loadView('pdfTemplate.invoiceForward',compact('invoiceSetting','sentInvoice','invoiceDescription','sentInvoiceValue'));
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                $fileName=preg_replace('/\s+/', '',$sentInvoice->invoiceInfo->title."".str_replace("-","",$sentInvoice->formatted_id));
                $output = $pdf->output();
                $path = base_path($dir.'/'.$fileName.'.pdf');
                file_put_contents($path, $output);
            }
        }


        $files = base_path($dir.'/');
        $now = Carbon::now();
        $zipper = new \Chumper\Zipper\Zipper;
        $zipper->make('zipFile/'.$now.'/record-time-docktes.zip')->add($files)->close();
        File::deleteDirectory(base_path($dir));
        return response()->json(array("status" => true, "messages" =>$now."/record-time-folder.zip"));
    }


    public function recoverFolderItem(Request $request){

        if ($request->type == 1){
            $folderitems = FolderItem::where('folder_id',$request->folderId)->where('ref_id',$request->id)->where('type',1)->get();
            if(count($folderitems) != 0){
                FolderItem::where('id',$folderitems->first()->id)->delete();
                $emaildocket = SentDockets::where('id',$request->id)->onlyTrashed()->get()->first();
                $emaildocket->folder_status = 0;
                $emaildocket->deleted_at = null;
                $emaildocket->update();
            }
        }else if($request->type == 2){
            $folderitems = FolderItem::where('folder_id',$request->folderId)->where('ref_id',$request->id)->where('type',3)->get();
            if(count($folderitems) != 0){
                FolderItem::where('id',$folderitems->first()->id)->delete();
                $emaildocket = EmailSentDocket::where('id',$request->id)->onlyTrashed()->get()->first();
                $emaildocket->folder_status = 0;
                $emaildocket->deleted_at = null;
                $emaildocket->update();

            }
        }

        $companyFolder=FolderItem::where('company_id',Session::get('company_id'))->where('folder_id',$request->folderId)->count();
        return response()->json(['status'=>true ,'data'=>$companyFolder, 'id'=>$request->folderId]);
    }

    public function saveShareableUsers(Request $request){
        $validator  =   Validator::make($request->all(),['folder_id'=>'required','email'=>'required|email','password'=>'required|min:6']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){
            $errors[]=$messages[0];
             }
            return response()->json(array('status' => false,'message' => $errors[0]));
        else:
             $folder = Folder::where('id',$request->folder_id)->where('company_id',Session::get('company_id'))->first();
              if($folder!= null){
                if($folder->shareableFolder != null){
                    $shareableFolder = $folder->shareableFolder;
                    $shareableFolderUsers = ShareableFolderUser::where('email',$request->email)->where('shareable_folder_id',$shareableFolder->id)->get();
                     if(count($shareableFolderUsers)!=0){
                         return response()->json(array('status' => false,'message' => "Email already exist."));
                     }else{
                         $shareableFolderUser = new ShareableFolderUser();
                         $shareableFolderUser->shareable_folder_id = $folder->shareableFolder->id;
                         $shareableFolderUser->email = $request->email;
                         $shareableFolderUser->password =  Hash::make($request->password);
                         $shareableFolderUser->save();

                         $folders = Folder::where('id',$request->folder_id)->where('company_id',Session::get('company_id'))->first();
                         $shareablefolder = "data";
                         return view('/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-contain',compact('folders','shareablefolder'));
                     }
                }else{
                    //add
                    $shareableFolder = new ShareableFolder();
                    $shareableFolder->folder_id = $folder->id;
                    $shareableFolder->shareable_type = "Restricted";
                    if($shareableFolder->save()){
                        $shareableFolderUser = new ShareableFolderUser();
                        $shareableFolderUser->shareable_folder_id = $shareableFolder->id;
                        $shareableFolderUser->email = $request->email;
                        $shareableFolderUser->password =  Hash::make($request->password);
                        $shareableFolderUser->save();
                    }

                    $shareableFolder->link = Crypt::encrypt($shareableFolder->id);
                    $shareableFolder->update();

                    $folders = Folder::where('id',$request->folder_id)->where('company_id',Session::get('company_id'))->first();
                    $shareablefolder = "data";
                    return view('/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-contain',compact('folders','shareablefolder'));

                }

              }else{
                  return response()->json(array('status' => false,'message' => "Invalid Folder id."));
              }
        endif;


    }

    public function viewShareableData(Request $request){
        $this->validate($request,['folder_id'   => 'required']);
        $folders = Folder::where('id',$request->folder_id)->where('company_id',Session::get('company_id'))->first();
        if($folders!= null){

            if($folders->shareableFolder == null){
                $shareablefolder = "data";
                $shareableFolders = new ShareableFolder();
                $shareableFolders->folder_id = $folders->id;
                $shareableFolders->shareable_type = "Restricted";
                if($shareableFolders->save()){
                    $shareableFolders->link = Crypt::encrypt($shareableFolders->id);
                    $shareableFolders->update();
                }
                $folders = $shareableFolders->folder;
                return view('/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-contain',compact('folders','shareablefolder'));

            }else{
                $shareablefolder = "data";
                return view('/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-contain',compact('folders','shareablefolder'));
            }
        }

    }

    public function updateShareableType(Request $request){
        $this->validate($request,['folder_id'   => 'required']);
        $folder = Folder::where('id',$request->folder_id)->where('company_id',Session::get('company_id'))->first();
        if($folder!= null){
            if($folder->shareableFolder == null){
                return response()->json(array('status' => false,'message' => "Please add an e-mail and password first"));
            }else{
                if(count($folder->shareableFolder->shareableFolderUsers) == 0){
                    if($request->value == "Restricted"){
                        return response()->json(array('status' => false,'message' => "Please add an e-mail and password first"));
                    }else{
                        ShareableFolder::where('id',$folder->shareableFolder->id)->update(['shareable_type'=>$request->value]);
                        foreach ($folder->shareableFolder->shareableFolderUsers as $shareableFolderUsers){
                            $shareableFolderUsers->update(['token'=>null]);
                        }
                        return response()->json(array('status' => true));
                    }
                }else{
                    ShareableFolder::where('id',$folder->shareableFolder->id)->update(['shareable_type'=>$request->value]);
                    foreach ($folder->shareableFolder->shareableFolderUsers as $shareableFolderUsers){
                        $shareableFolderUsers->update(['token'=>null]);
                    }
                    return response()->json(array('status' => true));
                }


            }
        }else{
            return response()->json(array('status' => false,'message' => "Invalid Folder id."));
        }
    }


    public function deleteShareableUser(Request $request){
        $validator  =   Validator::make($request->all(),['id'=>'required|int']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){
                $errors[]=$messages[0];
            }
            return response()->json(array('status' => false,'message' => $errors[0]));
        else:
            $shareableUser = ShareableFolderUser::where('id',$request->id)->first();
            if($shareableUser != null){
                $shareableUser->delete();

                $folders =$shareableUser->shareableFolder->folder;
                $shareablefolder = "data";
                return view('/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-contain',compact('folders','shareablefolder'));
            }else{
                return response()->json(array('status' => false,'message' => "Invalid data."));
            }

        endif;

    }

    public function updateShareableUser(Request $request){
        $validator  =   Validator::make($request->all(),['id'=>'required|int','password'=>'required|min:6']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){
                $errors[]=$messages[0];
            }
            return response()->json(array('status' => false,'message' => $errors[0]));
        else:
            $shareableUser = ShareableFolderUser::where('id',$request->id)->first();
            if($shareableUser != null){
                    $shareableUser->update(['password'=>Hash::make($request->password)]);
                    $folders =$shareableUser->shareableFolder->folder;
                    $shareablefolder = "data";
                    return view('/dashboard/company/folder-management/popup-modal/shareable-folder/shareable-contain',compact('folders','shareablefolder'));
            }else{
                return response()->json(array('status' => false,'message' => "Invalid data."));
            }


        endif;
    }





}
