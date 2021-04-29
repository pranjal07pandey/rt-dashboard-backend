<?php

namespace App\Http\Controllers;

use Session;
use App\DocketFiledCategory;
use Illuminate\Http\Request;

class DocketFieldCategoryController extends Controller
{
    public function index(){
        Session::put('navigation','docketField');
        Session::put('pageTitle','Docket Filed Category');
        $datas = DocketFiledCategory::orderBy('id', 'ASC')->get();
        return view('dashboard.V2.admin.docket-category.index', compact('datas'));
    }

    public function store(Request $request)
    {
        $category = DocketFiledCategory::create($request->all());

        if($category)
        {
            flash('Docket Filed Category added successfully.','success');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        DocketFiledCategory::find($request->id)->update($request->all());
        flash('Docket Filed Category updated successfully.','success');
        return redirect()->back();
    }
}
