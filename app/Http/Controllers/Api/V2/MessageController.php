<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\V2\MessageDisplay;
use App\Messages;
use App\MessagesGroup;
use App\MessagesGroupUser;
use App\MessagesRecipients;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageReadRequest;
use App\Services\V2\Api\MessageService;

class MessageController extends Controller
{

    protected $messageService;
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function getMessagesList(Request $request)
    {
        $messagesGroups = $this->messageService->getMessagesList($request);
        return response()->json(["messageGroup" => $messagesGroups],200);
    }

    public function messages(Request $request, $key)
    {
        return $this->messageService->messages($request,$key);
    }

    public function message(Request $request, $key)
    {
        return $this->messageService->message($request,$key);
    }

    public function markAsReads(MessageReadRequest $request)
    {
        $this->messageService->markAsReads($request);
        return response()->json(["message" => MessageDisplay::Success],200);
    }
}