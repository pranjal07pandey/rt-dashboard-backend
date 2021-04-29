<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContinueTimerRequest;
use App\Http\Requests\FinishTimerSessioRequest;
use App\Http\Requests\PauseTimerRequest;
use App\Http\Requests\StartNewTimerSessionRequest;
use App\Http\Requests\TimerAttachedTagRequest;
use App\Http\Requests\TimerCommentRequest;
use App\Services\V2\Api\TimerService;

class TimerController extends Controller
{
    protected $timerService;
    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function getcheckOldTimerSession(Request $request){
        return $this->timerService->getcheckOldTimerSession($request);
    }

    public function startNewTimerSession(StartNewTimerSessionRequest $request){
        return $this->timerService->startNewTimerSession($request);
    }

    public function finishTimerSession(FinishTimerSessioRequest $request){
        return $this->timerService->finishTimerSession($request);
    }

    public function getAllSavedTimer(Request $request){
        return $this->timerService->getAllSavedTimer($request);
    }

    public function pauseTimer(PauseTimerRequest $request){
        return $this->timerService->pauseTimer($request);
    }

    public function continueTimer(ContinueTimerRequest $request){
        return $this->timerService->continueTimer($request);
    }

    public function submitTimerComments(TimerCommentRequest $request){
        return $this->timerService->submitTimerComments($request);
    }

    public function searchTimer(Request $request){
        return $this->timerService->searchTimer($request);
    }

    public function timerAttachedTag(TimerAttachedTagRequest $request){
        return $this->timerService->timerAttachedTag($request);
    }

    public function timerDetailsById(Request $request,$id){
        return $this->timerService->timerDetailsById($request,$id);
    }
}
