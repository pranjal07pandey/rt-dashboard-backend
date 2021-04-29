<?php

namespace App\Http\Controllers\InvoiceManager;

use App\Invoice_Label;
use App\SentInvoiceLabel;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\AmazoneBucket;

class InvoiceLabelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoicelabel = Invoice_Label::where('company_id',Session::get('company_id'))->orderby ('id','desc')->get();
        $data =array();
        foreach ($invoicelabel as $invoicelabels){
            $data[] = array(
                'id'=>$invoicelabels->id,
                'title'=>$invoicelabels->title,
                'color'=>$invoicelabels->color,
                'icon'=>  AmazoneBucket::url() . $invoicelabels->icon,

            );
        }
        return response()->json(array('data'=>$data));
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
        $this->validate($request,['title'   => 'required','color'=>'required','icon'=>'mimes:jpeg,jpg,png']);
        if(Invoice_Label::where('company_id',Session::get('company_id'))->where('title',$request->title)->count()!=0){
            flash('The title "'.$request->title.'" has already been taken.','warning');
            return redirect('dashboard/company/invoiceManager/invoices/invoiceLabel');
        }else{
            $labelInvoice             =    new Invoice_Label();
            $labelInvoice->title      =   $request->title;
            $labelInvoice->color      =   $request->color;
            $icon              =   Input::file('icon');


            if($request->hasFile('icon') == "") {
                $labelInvoice->icon = "";

            }else{
                if ($icon->isValid()) {
                    // $ext = $icon->getClientOriginalExtension();
                    // $filename = basename($request->file('icon')->getClientOriginalName(), '.' . $request->file('icon')->getClientOriginalExtension()). $ext;
                    $dest = 'files/icon';
                    // $icon->move($dest, $filename);
                    // $labelInvoice->icon = $dest . '/' . $filename;
                    $labelInvoice->icon = FunctionUtils::imageUpload($dest,$icon);
                }

            }
            $labelInvoice->company_id =   Session::get('company_id');
            $labelInvoice->save();
            flash('Invoice label created successfully.','success');
            return response()->json();
        }
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
        $updateinvoiceLabel              =  Invoice_Label::findOrFail($id);
        $updateinvoiceLabel->title      =   $request->title;
        $updateinvoiceLabel->color      =   $request->color;

        $icon              =   Input::file('icon');
        if($request->hasFile('icon')) {
            if ($icon->isValid()) {
                // $ext = $icon->getClientOriginalExtension();
                // $filename = basename($request->file('icon')->getClientOriginalName(), '.' . $request->file('icon')->getClientOriginalExtension()). $ext;
                $dest = 'files/icon';
                // $icon->move($dest, $filename);
                // $updateinvoiceLabel->icon = $dest . '/' . $filename;
                $updateinvoiceLabel->icon = FunctionUtils::imageUpload($dest,$icon);
            }
        }

        $updateinvoiceLabel->company_id =   Session::get('company_id');
        $updateinvoiceLabel->save();
        return response()->json();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(SentInvoiceLabel::where('invoice_label_id',$id )->count()!=0){
            return response()->json(array('message'=>"Invalid attempt! This invoice label is already in use. You can’t delete it."));
        } else {
            $invoicelabel   =    Invoice_Label::where('id',$id)->firstOrFail();
            $invoicelabel->delete();
            return response()->json();


        }
    }
}
