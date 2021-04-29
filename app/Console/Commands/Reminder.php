<?php

namespace App\Console\Commands;

use App\DocumentTheme;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class Reminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weekly:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {


//        $documentTheam = new DocumentTheme();
//        $documentTheam->name ="test";
//        $documentTheam->description ="description";
//        $documentTheam->type =1;
//        $documentTheam->preview ="preview";
//        $documentTheam->web_view_path ="web_view_path";
//        $documentTheam->pdf_view_path ="pdf_view_path";
//        $documentTheam->is_active =2;
//        $documentTheam->save();

        $user = User::where('email_verification','!=',"")->get();
        foreach ($user as $users){
            if(Carbon::parse($users->created_at)->format('Y-m-d') == Carbon::now()->subDays(2)->format('Y-m-d')){
                $data = array();
                $data['receiver'] = $users->email;
                $userInfo = $users->email;
                Mail::send('emails.reminder', $data, function ($message) use ($userInfo) {
                    $message->from("signup@recordtimeapp.com.au", "Record Time");
                    $message->to($userInfo)->subject("Reminder");
                });
            }else if(Carbon::parse($users->created_at)->format('Y-m-d') == Carbon::now()->subDays(9)->format('Y-m-d')){
                $data = array();
                $data['receiver'] = $users->email;
                $userInfo = $users->email;
                Mail::send('emails.reminder', $data, function ($message) use ($userInfo) {
                    $message->from("signup@recordtimeapp.com.au", "Record Time");
                    $message->to($userInfo)->subject("Reminder");
                });
            }else if(Carbon::parse($users->created_at)->format('Y-m-d') == Carbon::now()->subDays(16)->format('Y-m-d')){
                $data = array();
                $data['receiver'] = $users->email;
                $userInfo = $users->email;
                Mail::send('emails.reminder', $data, function ($message) use ($userInfo) {
                    $message->from("signup@recordtimeapp.com.au", "Record Time");
                    $message->to($userInfo)->subject("Reminder");
                });
            }else if(Carbon::parse($users->created_at)->format('Y-m-d') == Carbon::now()->subDays(23)->format('Y-m-d')){
                $data = array();
                $data['receiver'] = $users->email;
                $userInfo = $users->email;
                Mail::send('emails.reminder', $data, function ($message) use ($userInfo) {
                    $message->from("signup@recordtimeapp.com.au", "Record Time");
                    $message->to($userInfo)->subject("Reminder");
                });
            }
        }


    }
}
