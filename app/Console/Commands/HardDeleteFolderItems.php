<?php

namespace App\Console\Commands;

use App\DocketFieldGridLabel;
use App\DocketFieldGridValue;
use App\EmailSentDocket;
use App\EmailSentDocketImageValue;
use App\EmailSentDocketRecipient;
use App\EmailSentDocketTallyUnitRateVal;
use App\EmailSentDocketValue;
use App\EmailSentDocManualTimer;
use App\EmailSentDocManualTimerBrk;
use App\EmailSnetDocketUnitRateValue;
use App\Folder;
use App\FolderItem;
use App\SendDocketImageValue;
use App\SentDcoketTimerAttachment;
use App\SentDocketAttachment;
use App\SentDocketInvoiceDetail;
use App\SentDocketLabel;
use App\SentDocketManualTimer;
use App\SentDocketManualTimerBreak;
use App\SentDocketRecipient;
use App\SentDocketRecipientApproval;
use App\SentDocketReject;
use App\SentDockets;
use App\SentDocketsValue;
use App\SentDocketTallyUnitRateVal;
use App\SentDocketTimesheet;
use App\SentDocketUnitRateValue;
use App\SentDocValYesNoValue;
use App\SentEmailAttachment;
use App\SentEmailDocketLabel;
use App\SentEmailDocValYesNoValue;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HardDeleteFolderItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:update';

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

        $folder= Folder::where('type',1)->get();
        foreach ($folder as $folders){
            foreach ($folders->folderItems as $folderItems){
                if($folderItems->type == 1){
                    $sentDocket =SentDockets::where('id',$folderItems->ref_id)->onlyTrashed()->first();
                    if($sentDocket != null){
                        if(Carbon::now()->format('Y-m-d') <= Carbon::parse($sentDocket->deleted_at)->addDays(30)->format('Y-m-d')){
                            SentDocketRecipient::where('sent_docket_id',$sentDocket->id)->delete();
                            SentDocketInvoiceDetail::where('sent_docket_id',$sentDocket->id)->delete();
                            $sentDocketValue = SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();

                            foreach ($sentDocketValue as $sentDocketValues){
                                SentDocketUnitRateValue::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                SendDocketImageValue::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                SentDocketAttachment::where('sent_dockets_value_id',$sentDocketValues->id)->delete();
                                SentDocketManualTimer::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                SentDocketManualTimerBreak::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                SentDocketTallyUnitRateVal::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                SentDocValYesNoValue::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                DocketFieldGridValue::where('docket_id',$sentDocketValues->id)->delete();
                                DocketFieldGridLabel::where('docket_id',$sentDocketValues->id)->delete();
                            }
                            SentDocketsValue::where('sent_docket_id',$sentDocket->id)->delete();
                            SentDocketRecipientApproval::where('sent_docket_id',$sentDocket->id)->delete();
                            SentDocketLabel::where('sent_docket_id',$sentDocket->id)->delete();
                            SentDocketTimesheet::where('sent_docket_id',$sentDocket->id)->delete();
                            SentDcoketTimerAttachment::where('sent_docket_id',$sentDocket->id)->delete();
                            SentDocketReject::where('sent_docket_id',$sentDocket->id)->delete();
                            FolderItem::where('id',$folderItems->id)->delete();

                            $note = SentDockets::onlyTrashed()->find($folderItems->ref_id);
                            $note->forceDelete();

                        }

                    }

                }
                else if($folderItems->type == 3){
                    $emailSentDocket =EmailSentDocket::where('id',$folderItems->ref_id)->onlyTrashed()->first();
                    if($emailSentDocket != null){
                        if(Carbon::now()->format('Y-m-d') <= Carbon::parse($emailSentDocket->deleted_at)->addDays(30)->format('Y-m-d')){
                            SentEmailDocketLabel::where('email_sent_docket_id',$emailSentDocket->id)->delete();
                            $sentDocketValue = EmailSentDocketValue::where('email_sent_docket_id',$emailSentDocket->id)->get();
                            foreach ($sentDocketValue as $sentDocketValues){
                                EmailSnetDocketUnitRateValue::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                EmailSentDocketImageValue::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                SentEmailAttachment::where('sent_email_value_id',$sentDocketValues->id)->delete();
                                SentEmailDocValYesNoValue::where('email_sent_docket_value_id',$sentDocketValues->id)->delete();
                                EmailSentDocManualTimer::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                EmailSentDocManualTimerBrk::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                EmailSentDocketTallyUnitRateVal::where('sent_docket_value_id',$sentDocketValues->id)->delete();
                                DocketFieldGridValue::where('docket_id',$sentDocketValues->id)->delete();
                                DocketFieldGridLabel::where('docket_id',$sentDocketValues->id)->delete();
                            }
                            EmailSentDocketRecipient::where('email_sent_docket_id',$emailSentDocket->id)->delete();
                            EmailSentDocketValue::where('email_sent_docket_id',$emailSentDocket->id)->delete();
                            FolderItem::where('id',$folderItems->id)->delete();

                            $note = EmailSentDocket::onlyTrashed()->find($folderItems->ref_id);
                            $note->forceDelete();
                        }
                    }


                }
            }
        }

    }
}
