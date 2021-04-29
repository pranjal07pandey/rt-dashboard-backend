<?php

namespace App\Http\Controllers;

use App\AssignedDocket;
use App\Company;
use App\DefaultCategory;
use App\DefaultDocket;
use App\DefaultDocketCategory;
use App\DefaultDocketField;
use App\DefaultDocketUnitRate;
use App\DefaultTemplate;
use App\Docket;
use App\DocketField;
use App\DocketFiledCategory;
use App\Employee;
use App\SentDockets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Helpers\V2\FunctionUtils;

class DefaultTemplateController extends Controller
{
    public function defaultTemplate(){
        $defaultCategory = DefaultCategory::orderBy('id','desc')->get();
        $defaultTemplate = DefaultDocket::orderBy('id','desc')->get();
        return view('dashboard/admin/defaultTemplate/index',compact('defaultCategory','defaultTemplate'));
    }

    public function defaultTemplateCategory(){
        $category = DefaultCategory::orderBy('id','desc')->get();
        return view('dashboard/admin/defaultTemplate/category',compact('category'));
    }

    public function saveDefaultCataegory(Request $request){
        $this->validate($request,['title'   => 'required']);
        if(DefaultCategory::where('title',$request->title)->count()!=0){
            flash('The title "'.$request->title.'" has already been taken.','warning');
            return redirect('dashboard/defaultTemplate/category');
        }else{
               $category = new DefaultCategory();
               $category->title = $request->title;
               $icon              =   Input::file('icon');
            if($request->hasFile('icon') == "") {
                $category->icon = "";

            }else{
                if ($icon->isValid()) {
                    // $ext = $icon->getClientOriginalExtension();
                    // $filename = basename($request->file('icon')->getClientOriginalName(), '.' . $request->file('icon')->getClientOriginalExtension()). $ext;
                    $dest = 'files/icon';
                    // $icon->move($dest, $filename);
                    // $category->icon = $dest . '/' . $filename;
                    $category->icon = FunctionUtils::imageUpload($dest,$icon);
                }
            }
            $category->save();
            flash('Category add successfully','success');
            return redirect('dashboard/defaultTemplate/category');
        }
    }

    public function deleteCategory(Request $request){
        if(DefaultDocket::where('category_id',$request->id )->count()!=0){
            flash('Invalid attempt! This category is already in use. You can’t delete it.','warning');
            return redirect('dashboard/defaultTemplate/category');
        }
        else {
            $category   =    DefaultCategory::where('id',$request->id)->firstOrFail();
            $category->delete();
            flash('Category deleted successfully.','success');
            return redirect('dashboard/defaultTemplate/category');

        }
    }

    public function updateDefaultCataegory(Request $request){
        $updateCategory              =  DefaultCategory::findOrFail($request->id);
        $updateCategory->title      =   $request->title;
        $icon              =   Input::file('icon');
        if($request->hasFile('icon')) {
            if ($icon->isValid()) {
                // $ext = $icon->getClientOriginalExtension();
                // $filename = basename($request->file('icon')->getClientOriginalName(), '.' . $request->file('icon')->getClientOriginalExtension()). $ext;
                $dest = 'files/icon';
                // $icon->move($dest, $filename);
                // $updateCategory->icon = $dest . '/' . $filename;
                $updateCategory->icon = FunctionUtils::imageUpload($dest,$icon);
            }
        }
        $updateCategory->save();
        flash('Update category successfully','success');
        return redirect('dashboard/defaultTemplate/category');

    }
    public  function deleteDefaultDocket(Request $request){
        $defaultDocket   =    DefaultDocket::where('id',$request->id)->firstOrFail();
        DefaultDocketCategory::where('default_docket_id','=',$request->id)->delete();
        $defaultdocketFields    =   DefaultDocketField::where('default_docket_id',$request->id)->get();
        foreach ($defaultdocketFields as $defaultdocketFields){
            if($defaultdocketFields->docket_field_category_id==7){
                DefaultDocketUnitRate::where('default_docket_field_id',$defaultdocketFields->id)->delete();
                DefaultDocketField::where('id',$defaultdocketFields->id)->delete();
            }
            DefaultDocketField::where('id',$defaultdocketFields->id)->delete();
        }
        $defaultDocket->delete();
        flash('Default Docket deleted successfully.','success');
        return redirect('dashboard/defaultTemplate');
    }

     public function saveDefaultTemplates(Request $request){
         $this->validate($request,['title'   => 'required']);
         if(DefaultDocket::where('title',$request->title)->count()!=0){
             flash('The title "'.$request->title.'" has already been taken.','warning');
             return redirect('dashboard/defaultTemplate');
         }else {
                     $defaultDocket = new DefaultDocket();
                     $defaultDocket->title = $request->title;
                     $defaultDocket->invoiceable = 0;
                     if ($defaultDocket->save()){
                     foreach ($request->category_id as $default_category_ids) {
                         $defaultDocketCategory = new DefaultDocketCategory();
                         $defaultDocketCategory->default_docket_id = $defaultDocket->id;
                         $defaultDocketCategory->default_category_id = $default_category_ids;
                         $defaultDocketCategory->save();
                     }

                      }
                     flash('Add default template successfully!', 'success');
                     return redirect('dashboard/defaultTemplate/designDefaultDocket/'.$defaultDocket->id);
                 }



     }


    public function defaultDocketElementTemplate($fieldType,$tempDocketId){
        $item   =   DefaultDocketField::where('id',$fieldType)->firstOrFail();
        $tempDocket =   DefaultDocket::where('id',$tempDocketId)->firstOrFail();
        $tempDocketFields=  DefaultDocketField::where('default_docket_id',$tempDocketId)->orderBy('id','asc')->get();

        return view('dashboard.admin.defaultTemplate.defaultElementTemplate',compact('item','tempDocket','tempDocketFields'));
    }
    public function insertDefaultDocketTemplate($tempDocketId){
        $tempDocketFields   =   DefaultDocketField::insert([

            ['default_docket_id'    =>  $tempDocketId,
                'docket_field_category_id' =>  '6',
                'order' =>  6,
                'label' => 'Date']

        ]);


        return $tempDocketFields;
    }

     public function designDefaultDocket($tempDocketId){
         $tempDocket =   DefaultDocket::where('id',$tempDocketId)->firstOrFail();
         if(DefaultDocketField::where('default_docket_id',$tempDocketId)->count()==0):
             $tempDocketFields   =   $this->insertDefaultDocketTemplate($tempDocketId);
         endif;

         $tempDocketFields =  DefaultDocketField::where('default_docket_id',$tempDocketId)->orderBy('order','asc')->get();


         return view('dashboard/admin/defaultTemplate/designDefaultDocket', compact('totalAssign','tempDocket','tempDocketFields', 'docketUsedByAdmin','docketUsedByEmployee', 'employeeData', 'companyAdminData', 'employees'));
     }
    public function addDocketField(Request $request, $tempDocketId){
        $this->validate($request,['fieldType'   => 'required']);

            $order  =   DefaultDocketField::where('default_docket_id',$tempDocketId)->count();

            $fieldDetails   =   DocketFiledCategory::where('id',$request->fieldType)->firstOrFail();

            $newField               =   new DefaultDocketField();
            $newField->default_docket_id    =   $tempDocketId;
            $newField->docket_field_category_id =   $request->fieldType;
            $newField->order                    =   $order+1;
            $newField->label                    =   $fieldDetails->title;
            $newField->save();

            //check unit rate field
            if($newField->docket_field_category_id == 7){
                DefaultDocketUnitRate::insert([
                    ['default_docket_field_id'  =>  $newField->id, 'type'  =>  1, 'label'  =>  'Per Unit Rate'],
                    ['default_docket_field_id'  =>  $newField->id, 'type'  =>  2, 'label'  =>  'Total Unit']]);
            }


            return $this->defaultDocketElementTemplate($newField->id, $tempDocketId);

    }

    public function deleteDocketField(Request $request, $tempDocketId){
        $this->validate($request,['fieldId'   => 'required']);
            $docketFieldQuery = DefaultDocketField::where('id',$request->fieldId)->where('default_docket_id',$tempDocketId);
            DefaultDocketUnitRate::where('default_docket_field_id',$docketFieldQuery->first()->id)->delete();
            DefaultDocketField::where('id',$request->fieldId)->where('default_docket_id',$tempDocketId)->delete();

    }

    public function docketFieldUpdatePosition(Request $request, $tempDocketId){
        $this->validate($request,['param'   => 'required']);
        $tempDocket     =   Docket::where('id',$tempDocketId)->firstOrFail();

        if(Company::where('id',$tempDocket->company_id)->first()->user_id==Auth::user()->id || Employee::where('company_id',$tempDocket->company_id)->where('user_id',Auth::user()-id)->count()>0){
            for($i=0;$i<count($request->param);$i++):
                DocketField::where('id',$request->param[$i])->update(['order'=>$i+1]);
            endfor;
        }
        else {
            echo "Unauthorized access!";
        }
    }

    public function docketFieldLabelUpdate(Request $request){
        $this->validate($request,['pk'   => 'required', 'value' =>  'required']);
            DefaultDocketField::where('id',$request->pk)->update(['label'  => $request->value]);

    }

    public function updateTempDocket(Request $request){
        $this->validate($request,['docketId' => 'required','docketName'   => 'required']);
            $docketInfo     =    DefaultDocket::where('id',$request->docketId)->firstOrFail();
            $docketInfo->title  =    $request->docketName;
            $docketInfo->save();
            flash('Docket updated successfully .','success');
            return redirect('dashboard/defaultTemplate/designDefaultDocket/'.$request->docketId);

    }

}
