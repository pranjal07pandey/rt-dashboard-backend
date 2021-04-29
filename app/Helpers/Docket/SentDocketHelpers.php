<?php
namespace  App\Helpers\Docket;

use App\Mail\Docket;
use App\SentDockets;
use App\User;
use Illuminate\Support\Facades\Mail;

class SentDocketHelpers
{
    public static function  sentEmail(SentDockets $sentDocket, User $user, $subject){
        Mail::to($user->email)->send(new Docket($sentDocket, $user, $subject));
        return true;
    }
}