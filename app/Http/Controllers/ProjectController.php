<?php

namespace App\Http\Controllers;

use App\Docket;
use App\DocketLabel;
use App\DocketProject;
use App\EmailSentDocket;
use App\Project;
use App\SentDocketProject;
use App\SentDockets;
use App\SentDocketsValue;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Support\Collection;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $template = Docket::where('company_id', Session::get('company_id'))->get();
        $project = Project::where('company_id', Session::get('company_id'))->get();

        $docketId = array();
        foreach ( $template as $templates){
            foreach ($templates->docketField as $docketFields){
                if($docketFields->docket_field_category_id == 24 || $docketFields->docket_field_category_id == 25){
                    $docketId[] = $docketFields->docket_id;
                }
            }
        }
        $filterDocketId = array_unique($docketId);
        $docketDetail = Docket::wherein('id',$filterDocketId)->get();
        return view('dashboard/company/docketManager/project/index', compact('docketDetail','project'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $template = Docket::where('company_id', Session::get('company_id'))->get();
        $docketId = array();
        foreach ( $template as $templates){
            foreach ($templates->docketField as $docketFields){
                if($docketFields->docket_field_category_id == 24 || $docketFields->docket_field_category_id == 25){
                    $docketId[] = $docketFields->docket_id;
                }
            }
        }
        $filterDocketId = array_unique($docketId);
        $docketDetail = Docket::wherein('id',$filterDocketId)->get();
        $project = Project::where('id',$request->id)->where('company_id', Session::get('company_id'))->first();
        $templatehtml = '<select id="selectId"  class="form-control" name="docket_id[]" multiple>';
        foreach ($docketDetail as $row){
            if(in_array($row->id,$project->docketProjectInfo->pluck('docket_id')->toArray())){
                $templatehtml .= '<option selected value="'.$row->id.'" >'.$row->title.'</option>';

            }else{
                $templatehtml .= '<option value="'.$row->id.'" >'.$row->title.'</option>';

            }
        }
        $templatehtml .= '</select>';

        return response()->json(['data'=>$templatehtml]);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,['project_name'   => 'required', 'budget' =>  'required','docket_id'=>'required']);
        $project = new Project();
        $project->name = $request->project_name;
        $project->budget = $request->budget;
        $project->company_id = Session::get('company_id');
        $project->user_id = Auth::user()->id;
        if($project->save()){
            foreach ($request->docket_id as $row){
                $docketProject = new DocketProject();
                $docketProject->project_id = $project->id;
                $docketProject->docket_id = $row;
                $docketProject->save();
            }
        }
        flash('Project add successfully.','success');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {


        $emailDocket = array();
        $docket = array();

        foreach ($project->sentDocketProjectInfo as $sentDocketProjects){
            if ($sentDocketProjects->is_email == 0){
                $docket[] = $sentDocketProjects->sent_docket_id;

            }else if ($sentDocketProjects->is_email == 1){
                $emailDocket[] = $sentDocketProjects->sent_docket_id;
            }
        }

        $emailDockets = EmailSentDocket::wherein('id',$emailDocket)->get();
        $dockets = SentDockets::whereIn('id',$docket)->get();


        $merged = $emailDockets->concat($dockets);
        $result = (new Collection($merged))->sortByDesc('created_at');
        $projects = $project;

        $totalValue = $this->tallyableValue($dockets,$emailDockets);


        return view('dashboard/company/docketManager/project/sentDocketList',compact('result','projects','totalValue'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function updates(Request $request){
        $project = Project::find($request->project_id);
        $project->name = $request->project_name;
        $project->budget = $request->budget;
        if ($project->save()){
            foreach ($project->docketProjectInfo as $items){
                DocketProject::where('id',$items->id)->delete();
            }



            foreach ($request->docket_id as $items){
                $docketProject = new DocketProject();
                $docketProject->project_id = $request->project_id;
                $docketProject->docket_id = $items;
                $docketProject->save();

            }


        }
        flash('Project update successfully.','success');
        return redirect()->back();





    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        foreach ($project->sentDocketProjectInfo as $sentDocketProjectInfos){
            SentDocketProject::where('id',$sentDocketProjectInfos->id)->delete();

        }
        foreach ($project->docketProjectInfo as $docketProjectInfos){
            DocketProject::where('id',$docketProjectInfos->id)->delete();

        }

        Project::where('id',$project->id)->delete();

    }




    public function tallyableValue($dockets,$emailDockets){


        $docketTallyValue = array();
        $emailDocketTallyValue = array();
        foreach ($dockets as $items){
            foreach ($items->docketInfo->docketField as $docketField){
                if ($docketField->docket_field_category_id == 24 ){
                    $sn = 1; $total = 0;
                    foreach($docketField->docketFieldValueBySentDocketId($items->id)->first()->sentDocketTallyableUnitRateValue as $row){
                        if($sn == 1){
                            $total = $row->value;
                        }else{
                            $total    =   $total*$row->value;
                        }
                        $sn++;
                    }


                    $docketTallyValue[] =  $total;


                }elseif($docketField->docket_field_category_id == 25 ){

                    $docketTallyValue[] =   intval($docketField->docketFieldValueBySentDocketId($items->id)->first()->value);

                }
            }

        }

        foreach ($emailDockets as $items){
            foreach ($items->docketInfo->docketField as $docketField){
                if ($docketField->docket_field_category_id == 24 ){
                    $sn = 1; $total = 0;
                    foreach($docketField->docketFieldValueByEmailSentDocketId($items->id)->first()->sentDocketTallyableUnitRateValue as $row){
                        if($sn == 1){
                            $total = $row->value;
                        }else{
                            $total    =   $total*$row->value;
                        }
                        $sn++;
                    }


                    $emailDocketTallyValue[] =  $total;


                }elseif($docketField->docket_field_category_id == 25 ){
                    $emailDocketTallyValue[] =   intval($docketField->docketFieldValueByEmailSentDocketId($items->id)->first()->value);

                }
            }

        }
        $totalValue = array_sum(array_merge($emailDocketTallyValue,$docketTallyValue));

        return $totalValue;

    }


    public function closeProject(Request $request){
        $this->validate($request,['id'   => 'required','password'=>'required']);
        $password = $request->password;
        $user= User::where('id',Auth::user()->id)->first();
        if (Auth::attempt(array('email'=>$user->email,'password' => $password))){
            Project::where('id',$request->id)->update(['is_close'=>1]);
            return response()->json(array('status'=>1));
        }else {
            return response()->json(array('status'=>0, 'message'=>'Wrong Credentials. Please try Again.'));

        }




    }



}
