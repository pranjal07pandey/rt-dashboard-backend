<?php

namespace App\Http\Controllers\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V2\Web\SentInvoiceService;

class SentInvoiceController extends Controller
{
    protected $sentInvoiceService;

    public function __construct(SentInvoiceService $sentInvoiceService)
    {
        $this->sentInvoiceService = $sentInvoiceService;
    }

    public function send(Request $request){
        return $this->sentInvoiceService->send($request);
    }
}
