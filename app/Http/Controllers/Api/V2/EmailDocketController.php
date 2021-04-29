<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V2\Api\EmailDocketService;

class EmailDocketController extends Controller
{
    protected $emailDocketService;
    public function __construct(EmailDocketService $emailDocketService)
    {
        $this->emailDocketService = $emailDocketService;
    }

    public function show(Request $request, $id){
        return $this->emailDocketService->show($request,$id);
    }
    
}
