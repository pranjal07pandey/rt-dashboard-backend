<?php

namespace App\Http\Controllers\DocketManager;

use App\Company;
use App\Docket;
use App\DocketFrequency;
use App\DocumentTheme;
use App\Employee;
use App\Folder;
use App\Support\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DocketTemplateController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            if(Session::get('company_id')==''){
                if(Employee::where('user_id', Auth::user()->id)->count()!=0):
                    $companyId = Employee::where('user_id', Auth::user()->id)->first()->company_id;
                    Session::put('adminType',2);
                else :
                    $companyId   =   Company::where('user_id', Auth::user()->id)->first()->id;
                    Session::put('adminType',1);
                endif;
                Session::put('company_id',$companyId);
            }
            if(!checkProfileComplete()){
                return redirect()->route('companyProfile');
            }

            $status     =   checkSubscription();
            switch ($status){
                case 'noSubscription':
                    return redirect('dashboard/company/profile/selectSubscription');
                    break;

                case 'subscriptionCancel':
//                    return redirect()->route('Company.Subscription.Continue');
                    break;

                case 'past_due':
                    break;

                default:
                    break;
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $dockets    =   Docket::with('assignedDockets','docketFolderAssign','userInfo','templateBank')->where('company_id',Session::get('company_id'))->where('is_archive',0)->orderBy('created_at','desc')->get();
        $folderData =   Folder::where('company_id',Session::get('company_id'))->get();
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
        return view('dashboard.company.docketManager.docket-template.index',compact('dockets','data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
