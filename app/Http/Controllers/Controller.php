<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class Controller extends BaseController
{
    /**
     * @OA\Info(
     *      version="2.0",
     *      title="Record Time",
     *      description="Record Time Api Documantation",
     * )
     *
     * @OA\Server(
     *      url=LOCAL_HOST,
     *      description="API Server"
     * )

     *
     *
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


}
