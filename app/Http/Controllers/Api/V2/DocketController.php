<?php

namespace App\Http\Controllers\Api\V2;

use App\Repositories\Interfaces\DocketRepositoryInterfaces;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class DocketController extends Controller
{

    private $docketRepository;

    public function __construct(DocketRepositoryInterfaces $docketRepository)
    {
        $this->docketRepository = $docketRepository;
    }


    public function index(Request $request){
        $companyId = $request->header('companyId');
        $response = $this->docketRepository->index($companyId);
        return RESPONSEDATA($response);
    }


    public function getAssignedDocketTemplateByUserId(Request $request){
        $userId= $request->header('userId');
        $response = $this->docketRepository->getDocketTemplateByUserId($userId);
        return RESPONSEDATA($response);

    }

    public function show($id){
        $response = $this->docketRepository->show($id);
        return RESPONSEDATA($response);

    }










}
