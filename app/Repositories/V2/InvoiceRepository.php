<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Invoice;

class InvoiceRepository implements IRepository
{
    public function getModel()
    {
        return new Invoice();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('invoice_id')) {
            $invoice = $this->getModel()->find($request->invoice__id);
        } else {
            $invoice = $this->getModel();
        }

        (!$request->has('title'))?:                                     $invoice->title   = $request->title;
        (!$request->has('subTitle'))?:                                  $invoice->subTitle  = $request->subTitle;
        (!$request->has('user_id'))?:                                   $invoice->user_id  = $request->user_id;
        (!$request->has('company_id'))?:                                $invoice->company_id  = $request->company_id;
        (!$request->has('gst'))?:                                       $invoice->gst  = $request->gst;
        (!$request->has('gst_label'))?:                                 $invoice->gst_label  = $request->gst_label;
        (!$request->has('gst_value'))?:                                 $invoice->gst_value  = $request->gst_value;
        (!$request->has('theme_document_id'))?:                         $invoice->theme_document_id  = $request->theme_document_id;
        (!$request->has('preview'))?:                                   $invoice->preview  = $request->preview;
        (!$request->has('prefix'))?:                                    $invoice->prefix  = $request->prefix;
        (!$request->has('hide_prefix'))?:                               $invoice->hide_prefix  = $request->hide_prefix;

        $invoice->save();
        return $invoice;
    }

    public function deleteDataById($request = null)
    {
        $invoice = $this->getModel()->find($request->id);
        $invoice->delete();
        return $invoice;
    }
}
