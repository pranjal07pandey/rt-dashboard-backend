<?php

namespace App\Http\Controllers;

use App\DocumentTheme;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Helpers\V2\FunctionUtils;

class DocumentThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     *e
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Session::put('navigation','documentThemes');
        Session::put('pageTitle','Document Themes');
        $document_themes  = DocumentTheme::orderBy('created_at', 'ASC')->get();

        return view('dashboard.V2.admin.document_themes.index', compact('document_themes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(DocumentTheme::where('name',$request->name)->count()!=0){
            flash('The Name "'.$request->name.'" has already been taken.','warning');
            return redirect()->back();
        }else{
            $theme                  =    new DocumentTheme();
            $theme->name            =    $request->name;
            $theme->slug            =    str_slug($request->name);
            $theme->description     =    $request->description;
            $theme->type            =    $request->type;
            $theme->paid_free       =    $request->paid_free;
            $theme->price           =    $request->price;
            $theme->web_view_path   =    $request->web_view_path;
            $theme->pdf_view_path   =    $request->pdf_view_path;
            $theme->mobile_view_path   =    $request->mobile_view_path;
            $theme->screenshot        =   $request->screenshot;
            $screenshot              =   Input::file('screenshot');
            $test   =    array();
            if( $request->hasFile('screenshot')) {
                foreach ($request->file('screenshot') as $image) {
                    $dest = 'files/theme';
                    // $ext = $image->getClientOriginalExtension();
                    // $filename =  basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time() . "." . $ext;
                    // $image->move($dest, $filename);
                    // $theme->screenshot = $dest . '/' . $filename;
                    $theme->screenshot = FunctionUtils::imageUpload($dest,$image);
                    $test[] =   $theme->screenshot;

                }
                $serialized_array = serialize($test);
                $theme->screenshot=$serialized_array;
            }

            $preview              =   Input::file('preview');
            if($request->hasFile('preview')) {
                if ($preview->isValid()) {
                    // $ext = $preview->getClientOriginalExtension();
                    // $filename = basename($request->file('preview')->getClientOriginalName(), '.' . $request->file('preview')->getClientOriginalExtension()) . time() . "." . $ext;
                    $dest = 'files/theme';
                    // $preview->move($dest, $filename);
                    // $theme->preview = $dest . '/' . $filename;
                    $theme->preview = FunctionUtils::imageUpload($dest,$preview);
                }
            }

            $theme->save();
            flash('Document Theme ' . $theme->name . ' added successfully.','success');
            return redirect()->back();
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $theme = DocumentTheme::where('id', $id)->first();

        return view('dashboard.V2.admin.document_themes.edit', compact('theme'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $theme = DocumentTheme::where('id', $request->id)->first();

        $theme->name            =    $request->name;
        $theme->slug            =    str_slug($request->name);
        $theme->description     =    $request->description;
        $theme->type            =    $request->type;
        $theme->paid_free       =    $request->paid_free;
        $theme->price           =    $request->price;
        $theme->web_view_path   =    $request->web_view_path;
        $theme->pdf_view_path   =    $request->pdf_view_path;
        $theme->mobile_view_path   = $request->mobile_view_path;

        $preview              =   Input::file('preview');
        if($request->hasFile('preview')) {
            if ($preview->isValid()) {
                // $ext = $preview->getClientOriginalExtension();
                // $filename = basename($request->file('preview')->getClientOriginalName(), '.' . $request->file('preview')->getClientOriginalExtension()) . time() . "." . $ext;
                $dest = 'files/theme';
                // $preview->move($dest, $filename);
                // $theme->preview = $dest . '/' . $filename;
                $theme->preview = FunctionUtils::imageUpload($dest,$preview);
            }
        }

        $theme->update();
        flash('Document Theme ' . $theme->name . ' updated successfully.','success');
        return redirect()->route('document_theme');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $theme = DocumentTheme::where('id', $id)->first();
        $theme->is_active = 0;
        $theme->save();

        flash('Document Theme ' . $theme->name . ' deleted successfully.','success');
        return redirect()->route('document_theme');
    }

    public function restore($id)
    {
        $theme = DocumentTheme::where('id', $id)->first();
        $theme->is_active = 1;
        $theme->save();

        flash('Document Theme ' . $theme->name . ' restored successfully.','success');
        return redirect()->route('document_theme');
    }
}
