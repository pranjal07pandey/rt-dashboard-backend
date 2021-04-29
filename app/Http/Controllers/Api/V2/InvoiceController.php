<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Services\V2\Api\InvoiceService;

class InvoiceController extends Controller
{
    protected $invoiceService;
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    
    public function getInvoiceTemplateList(Request $request){
        $invoiceTemplate = $this->invoiceService->getInvoiceTemplateList($request);
        return response()->json(['invoiceTemplate' => $invoiceTemplate],200);
    }

    public function getInvoiceTemplateDetailsById(Request $request, $invoiceId){
        return $this->invoiceService->getInvoiceTemplateDetailsById($request,$invoiceId);
    }

    public function saveSentInvoice(InvoiceRequest $request){
        return $this->invoiceService->saveSentInvoice($request);
    }

    public function getLatestInvoiceHome(Request $request){
        $conversationArray = $this->invoiceService->getLatestInvoiceHome($request);
        return response()->json(['invoices' =>$conversationArray],200);
    }

    public function getLatestInvoiceList(Request $request){
        $conversationArray = $this->invoiceService->getLatestInvoiceList($request);
        return response()->json(['invoices' =>$conversationArray],200);
    }

    public function getConversationInvoiceChatByUserId(Request $request, $userId){
        $conversationArray = $this->invoiceService->getConversationInvoiceChatByUserId($request,$userId);
        return response()->json(['invoices' =>$conversationArray],200);
    }

    public function getInvoiceDetailsById(Request $request, $id){
        return $this->invoiceService->getInvoiceDetailsById($request,$id);
    }

    public function getEmailInvoiceDetailsById(Request $request, $id){
        return $this->invoiceService->getEmailInvoiceDetailsById($request,$id);
    }

    public function getInvoiceTimelineByUserId(Request $request,$id){
        $conversationArray =  $this->invoiceService->getInvoiceTimelineByUserId($request,$id);
        return response()->json(['timeline' => $conversationArray],200);
    }

}
