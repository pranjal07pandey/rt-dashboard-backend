<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyXero;
use App\EmailSentInvoice;
use App\Employee;
use App\SentEmailInvoiceXero;
use App\SentInvoice;
use App\SentInvoiceXero;
use App\SentXeroInvoiceSetting;
use App\SynXeroContact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use DateTime;
use App\User;
use overint\MailgunValidator;
use App\EmailUser;
use App\Client;
use App\Email_Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use XeroPHP\Application\PrivateApplication;
use XeroPHP\Exception;
use XeroPHP\Models\Accounting\Contact;
use XeroPHP\Models\Accounting\Organisation;

class XeroController extends Controller
{
    private $xero;
    public function __construct()
    {
        $this->xero = xero();
    }
    public function index()
    {

        $company = CompanyXero::where('company_id', Session::get('company_id'))->where('status',1)->first();
        //These are the minimum settings - for more options, refer to examples/config.php
        $config = [
            'oauth' => [
                'callback' => 'https://recordtime.dev/dashboard/company/xero/',
                'consumer_key' => $company->consumer_key,
                'consumer_secret' => $company->consumer_secret,
                'rsa_private_key' => 'file://' . $company->rsa_private_key,
            ],
        ];
        $xero = new PrivateApplication($config);

    }

    public function xeroContactGet()
    {
        if(CompanyXero::where('company_id', Session::get('company_id'))->where('status',1)->count()>0){
                $companyXero =CompanyXero::where('company_id', Session::get('company_id'))->where('status',1)->first();
                $synXeroContact = SynXeroContact::where('company_xero_id',  $companyXero->id)->pluck('email')->toArray();
                $employee = Employee::where('company_id', Session::get('company_id'))->get();
                $employeDetail = array();
                foreach ($employee as $items) {
                    $employeDetail[] = array(
                        'id' => $items->id,
                        'email' => $items->userInfo->email,
                        'first_name' => $items->userInfo->first_name,
                        'last_name' => $items->userInfo->last_name,
                        'contact_name' => $items->userInfo->first_name . " " . $items->userInfo->last_name,
                        'flag'=> in_array($items->userInfo->email, $synXeroContact)? "true":"false",
                    );
                }
                $emailClient = Email_Client::where('company_id', Session::get('company_id'))->get();
                foreach ($emailClient as $item) {
                    $name = trim($item->full_name);
                    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
                    $employeDetail[] = array(
                        'id' => $item->id,
                        'email' => $item->emailUser->email,
                        'first_name' => trim(preg_replace('#' . $last_name . '#', '', $name)),
                        'last_name' => $last_name,
                        'contact_name' => $item->full_name,
                        'flag'=> in_array($item->emailUser->email, $synXeroContact)? "true":"false",
                    );
                }

                $time=\Carbon\Carbon::parse( @Session::get('xero_oauth')->expires) ;
                return view('dashboard.company.xero.index', compact('synXeroContact', 'employeDetail','time'));
            }else{

                $employee = Employee::where('company_id', Session::get('company_id'))->get();
                $employeDetail = array();
                foreach ($employee as $items) {
                    $employeDetail[] = array(
                        'id' => $items->id,
                        'email' => $items->userInfo->email,
                        'first_name' => $items->userInfo->first_name,
                        'last_name' => $items->userInfo->last_name,
                        'contact_name' => $items->userInfo->first_name . " " . $items->userInfo->last_name,
                        'flag'=> "false",
                    );
                }

                $emailClient = Email_Client::where('company_id', Session::get('company_id'))->get();
                foreach ($emailClient as $item) {
                    $name = trim($item->full_name);
                    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
                    $employeDetail[] = array(
                        'id' => $item->id,
                        'email' => $item->emailUser->email,
                        'first_name' => trim(preg_replace('#' . $last_name . '#', '', $name)),
                        'last_name' => $last_name,
                        'contact_name' => $item->full_name,
                        'flag'=> "false",
                    );
                }

                $time=\Carbon\Carbon::parse( @Session::get('xero_oauth')->expires );


                return view('dashboard.company.xero.index', compact('synXeroContact', 'employeDetail','time'));

            }








    }

    public function getContacts()
    {

        try {
            $company = CompanyXero::where('company_id', Session::get('company_id'))->where('status',1)->first();
            $config = [
                'oauth' => [
                    'callback' => 'https://recordtime.dev/dashboard/company/xero/contacts',
                    'consumer_key' => $company->consumer_key,
                    'consumer_secret' => $company->consumer_secret,
                    'rsa_private_key' => 'file://' . $company->rsa_private_key,
                ],
            ];
            $xero = new PrivateApplication($config);
            $contacts = $xero->load('Accounting\\Contact')->execute();
            foreach ($contacts as $contact) {
                if (SynXeroContact::where("email", $contact->EmailAddress)->where("company_id", Session::get('company_id'))->count() == 0) {
                    $addXeroContact = new SynXeroContact();
                    $addXeroContact->contact_name = $contact->Name;
                    $addXeroContact->first_name = $contact->FirstName;
                    $addXeroContact->last_name = ($contact->LastName == "") ? " " : $contact->LastName;
                    $addXeroContact->email = $contact->EmailAddress;
                    $addXeroContact->company_xero_id = $company->id;
                    $addXeroContact->xero_contact_id = $contact->ContactId;
                    $addXeroContact->company_id = Session::get('company_id');
                    $addXeroContact->save();


                }
            }
            flash('Syn contact from xero successfully.', 'success');
            return redirect('dashboard/company/xero/contacts/view');
        }
        catch (\Exception $e) {
            flash('Please submit Xero valid Customer key.', 'success');
            return redirect()->route('Company.xero.setting');
        }
    }



    public function postEmployees()
    {

        try{
            $company = CompanyXero::where('company_id', Session::get('company_id'))->where('status',1)->first();
            $config = [
                'oauth' => [
                    'callback' => 'https://recordtime.dev/dashboard/company/xero/contacts',
                    'consumer_key' => $company->consumer_key,
                    'consumer_secret' => $company->consumer_secret,
                    'rsa_private_key' => 'file://' . $company->rsa_private_key,
                ],
            ];
            $xero = new PrivateApplication($config);
            $employee = Employee::where('company_id', Session::get('company_id'))->get();
            $employeDetail = array();

            foreach ($employee as $items) {
                $employeDetail[] = array(
                    'id' => $items->id,
                    'email' => $items->userInfo->email,
                    'first_name' => $items->userInfo->first_name,
                    'last_name' => $items->userInfo->last_name,
                    'contact_name' => $items->userInfo->last_name . " " . $items->userInfo->last_name
                );
            }
            $emailClient = Email_Client::where('company_id', Session::get('company_id'))->get();
            foreach ($emailClient as $item) {
                $name = trim($item->full_name);
                $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
                $employeDetail[] = array(
                    'id' => $item->id,
                    'email' => $item->emailUser->email,
                    'first_name' => trim(preg_replace('#' . $last_name . '#', '', $name)),
                    'last_name' => $last_name,
                    'contact_name' => $item->full_name,
                );
            }
            foreach ($employeDetail as $row) {
                $employees = new \XeroPHP\Models\Accounting\Contact($xero);
                $employees->setContactID($this->getRandNum())
                    ->setClean()
                    ->setName($row['first_name'] . ' ' . $row['last_name'])
                    ->setFirstName($row['first_name'])
                    ->setLastName($row['last_name'])
                    ->setEmailAddress($row['email']);
                if ($employees->save()) {
                    $contacts = $xero->load('Accounting\\Contact')->execute();
                    foreach ($contacts as $contact) {
                        if (SynXeroContact::where("email", $contact->EmailAddress)->where("company_id", Session::get('company_id'))->count() == 0) {
                            $addXeroContact = new SynXeroContact();
                            $addXeroContact->contact_name = $contact->Name;
                            $addXeroContact->first_name = $contact->FirstName;
                            $addXeroContact->last_name = ($contact->LastName == "") ? " " : $contact->LastName;
                            $addXeroContact->email = $contact->EmailAddress;
                            $addXeroContact->xero_contact_id = $contact->ContactId;
                            $addXeroContact->company_xero_id = $company->id;
                            $addXeroContact->company_id = Session::get('company_id');
                            $addXeroContact->save();
                        }
                    }

                }
            }
            flash('Syn contact from xero successfully.', 'success');
            return redirect('dashboard/company/xero/contacts/view');
        }

            catch (\Exception $e) {
                flash('Please submit Xero valid Customer key.', 'success');
                return redirect()->route('Company.xero.setting');
          }


    }

    public function getEmailInvoice($id){
     try{
         $company = CompanyXero::where('company_id', Session::get('company_id'))->where('status',1)->first();
         $config = [
             'oauth' => [
                 'callback'         => 'https://recordtime.dev/dashboard/company/xero/Invoices',
                 'consumer_key'     =>  $company->consumer_key,
                 'consumer_secret'  =>  $company->consumer_secret,
                 'rsa_private_key'  =>  'file://'.$company->rsa_private_key,
             ],
         ];
         $xero = new PrivateApplication($config);
         $invoiceDetail = EmailSentInvoice::where('id',$id)->first();
         if (SynXeroContact::where('email',$invoiceDetail->receiverInfo->email)->where('company_id',Session::get('company_id'))->count()!=0){
             $synXeroData=SynXeroContact::where('email',$invoiceDetail->receiverInfo->email)->where('company_id',Session::get('company_id'))->first();
             $sendXeroSetting=array();
             foreach ($invoiceDetail->sentEmailInvoiceXero->first()->sentXeroEmailInvoiceSetting as $data){
                 $sendXeroSetting[] = array(
                     'id' => $data->id,
                     'xero_field_id' => $data->xero_field_id,
                     'value' => $data->value,
                 );
             }
             $contacts = $xero->loadByGUID('Accounting\\Contact', $synXeroData->xero_contact_id);
             $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
             $invoice->setType("ACCREC")->setContact($contacts)->setReference($id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
             foreach ($invoiceDetail->invoiceDescription as $detail) {
                 $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                 $xeroLineItem->setQuantity(1);
                 $xeroLineItem->setDescription($detail->description);
                 $xeroLineItem->setUnitAmount($detail->amount);
                 $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                 $xeroLineItem->setAccountCode(explode('-',$sendXeroSetting[2]['value'])[0]);
                 $xeroLineItem->setTaxType(explode('-',$sendXeroSetting[3]['value'])[0]);
                 $invoice->addLineItem($xeroLineItem);
                 $invoice->save();
             }
             SentEmailInvoiceXero::where('sent_email_invoice_id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
         }else
             {
             $emailSentinv=EmailSentInvoice:: where('id',$id)->first();
             $emailClient = Email_Client::where('company_id', Session::get('company_id'))->where('email_user_id',$emailSentinv->receiver_user_id)->first();
             $name = trim($emailClient->full_name);
             $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
             $employeDetail[] = array(
                 'id' => $emailClient->id,
                 'email' => $emailClient->emailUser->email,
                 'first_name' => trim(preg_replace('#' . $last_name . '#', '', $name)),
                 'last_name' => $last_name,
                 'contact_name' => $emailClient->full_name,
             );
             $employees = new \XeroPHP\Models\Accounting\Contact($xero);
             $employees->setContactID($this->getRandNum())
                 ->setClean()
                 ->setName($employeDetail[0]['first_name'] . ' ' . $employeDetail[0]['last_name'])
                 ->setFirstName($employeDetail[0]['first_name'])
                 ->setLastName($employeDetail[0]['last_name'])
                 ->setEmailAddress($employeDetail[0]['email']);
             if ($employees->save()) {
                 $contacts = $xero->load('Accounting\\Contact')->execute();
                 if (SynXeroContact::where("email", $employees->EmailAddress)->where("company_id", Session::get('company_id'))->count() == 0) {
                     $addXeroContact = new SynXeroContact();
                     $addXeroContact->contact_name = $employees->Name;
                     $addXeroContact->first_name = $employees->FirstName;
                     $addXeroContact->last_name = ($employees->LastName == "") ? " " : $employees->LastName;
                     $addXeroContact->email = $employees->EmailAddress;
                     $addXeroContact->xero_contact_id = $employees->ContactId;
                     $addXeroContact->company_xero_id =$company->id;
                     $addXeroContact->company_id = Session::get('company_id');
                     $addXeroContact->save();
                 }
             }
             $sendXeroSetting=array();
             foreach ($invoiceDetail->sentEmailInvoiceXero->first()->sentXeroEmailInvoiceSetting as $data){
                 $sendXeroSetting[] = array(
                     'id' => $data->id,
                     'xero_field_id' => $data->xero_field_id,
                     'value' => $data->value,
                 );
             }
             $contacts = $xero->loadByGUID('Accounting\\Contact', $addXeroContact->xero_contact_id);
             $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
             $invoice->setType("ACCREC")->setContact($contacts)->setReference($id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
             foreach ($invoiceDetail->invoiceDescription as $detail) {
                 $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                 $xeroLineItem->setQuantity(1);
                 $xeroLineItem->setDescription($detail->description);
                 $xeroLineItem->setUnitAmount($detail->amount);
                 $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                 $xeroLineItem->setAccountCode(explode('-',$sendXeroSetting[2]['value'])[0]);
                 $xeroLineItem->setTaxType(explode('-',$sendXeroSetting[3]['value'])[0]);
                 $invoice->addLineItem($xeroLineItem);
                 $invoice->save();
             }
             SentEmailInvoiceXero::where('sent_email_invoice_id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
         }
         flash('synchrony updated successfully.','success');
         return redirect()->back();
      }
         catch (\Exception $e) {
             flash('Please submit Xero valid Customer key.', 'success');
             return redirect()->route('Company.xero.setting');
         }

    }

    public function getInvoice($id){
        try {

            if (SentInvoiceXero::where('sent_invoice_id', $id)->where('xero_invoice_id', 0)) {
                $sendInvoice = SentInvoice::where('id', $id)->first();
                $senderCompany = Company::where('id', Session::get('company_id'))->first();
                $employessender = Employee::where('company_id', Session::get('company_id'))->pluck('user_id')->toArray();
                $adminsender = Company::where('id', Session::get('company_id'))->pluck('user_id')->toArray();
                $totalCompany = array_merge($employessender, $adminsender);

                if (!in_array($sendInvoice->receiver_user_id, $totalCompany)) {
                    $reciverType = "company";
                } else {
                    if (Employee::where('user_id', $sendInvoice->receiver_user_id)->where('is_admin', 1)->count() == 1) {
                        $reciverType = 'admin';
                    } elseif (Employee::where('user_id', $sendInvoice->receiver_user_id)->where('employed', 1)->count() == 1) {
                        $reciverType = 'employee';
                    } elseif ($sendInvoice->receiver_user_id == $senderCompany->user_id) {
                        $reciverType = 'admin';
                    }
                }

                if ($sendInvoice->user_id == $senderCompany->user_id) {
                    $senderType = "admin";
                } elseif (Employee::where('user_id', $sendInvoice->user_id)->where('is_admin', 1)->count() == 1) {
                    $senderType = 'admin';
                } elseif (Employee::where('user_id', $sendInvoice->user_id)->where('employed', 1)->count() == 1) {
                    $senderType = 'employee';
                }

                if ($reciverType == "company") {
                    //from company to user
                    $company = CompanyXero::where('company_id', Session::get('company_id'))->where('status', 1)->first();
                    $config = [
                        'oauth' => [
                            'callback' => 'https://www.recordtimeapp.com.au/rtBeta/dashboard/company/xero/Invoices',
                            'consumer_key' => $company->consumer_key,
                            'consumer_secret' => $company->consumer_secret,
                            'rsa_private_key' => 'file://' . $company->rsa_private_key,
                        ],
                    ];
                    $xero = new PrivateApplication($config);
                    $invoiceDetail = SentInvoice::where('id', $id)->first();
                    if (SynXeroContact::where('email', $invoiceDetail->receiverUserInfo->email)->where('company_xero_id', $company->id)->count() != 0) {
                        $synXeroData = SynXeroContact::where('email', $invoiceDetail->receiverUserInfo->email)->where('company_xero_id', $company->id)->first();
                        $sendXeroSetting = array();
                        foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                            $sendXeroSetting[] = array(
                                'id' => $data->id,
                                'xero_field_id' => $data->xero_field_id,
                                'value' => $data->value,
                            );

                        }
                        $contacts = $xero->loadByGUID('Accounting\\Contact', $synXeroData->xero_contact_id);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                        $invoice->setType("ACCREC")->setContact($contacts)->setReference($id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                        if ($invoiceDetail->invoiceDescription->count() != 0) {
                            foreach ($invoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                        }
                        if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                            foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                $invoiceAmount = 0;
                                foreach ($invoiceAmountQuery as $amount) {
                                    $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                    $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                    $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                    $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                    $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }
                            }
                        }
                        SentInvoiceXero::where('sent_invoice_id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    } else {
                        $Sentinv = SentInvoice:: where('id', $id)->first();
                        $user = User::where('id', $Sentinv->receiver_user_id)->first();
                        $employeDetail = array();
                        $employeDetail[] = array(
                            'id' => $user->id,
                            'email' => $user->email,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'contact_name' => $user->last_name . " " . $user->last_name
                        );

                        $employees = new \XeroPHP\Models\Accounting\Contact($xero);
                        $employees->setContactID($this->getRandNum())
                            ->setClean()
                            ->setName($employeDetail[0]['first_name'] . ' ' . $employeDetail[0]['last_name'])
                            ->setFirstName($employeDetail[0]['first_name'])
                            ->setLastName($employeDetail[0]['last_name'])
                            ->setEmailAddress($employeDetail[0]['email']);
                        if ($employees->save()) {
                            if (SynXeroContact::where("email", $employees->EmailAddress)->where("company_id", Session::get('company_id'))->count() == 0) {
                                $addXeroContact = new SynXeroContact();
                                $addXeroContact->contact_name = $employees->Name;
                                $addXeroContact->first_name = $employees->FirstName;
                                $addXeroContact->last_name = ($employees->LastName == "") ? " " : $employees->LastName;
                                $addXeroContact->email = $employees->EmailAddress;
                                $addXeroContact->xero_contact_id = $employees->ContactId;
                                $addXeroContact->company_xero_id = $company->id;
                                $addXeroContact->company_id = Session::get('company_id');
                                $addXeroContact->save();
                            }
                        }
                        $sendXeroSetting = array();
                        foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                            $sendXeroSetting[] = array(
                                'id' => $data->id,
                                'xero_field_id' => $data->xero_field_id,
                                'value' => $data->value,
                            );

                        }
                        $contacts = $xero->loadByGUID('Accounting\\Contact', $addXeroContact->xero_contact_id);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                        $invoice->setType("ACCREC")->setContact($contacts)->setReference($id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                        if ($invoiceDetail->invoiceDescription->count() != 0) {
                            foreach ($invoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                        }
                        if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                            foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                $invoiceAmount = 0;
                                foreach ($invoiceAmountQuery as $amount) {
                                    $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                    $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                    $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                    $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                    $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }

                            }
                        }
                        SentInvoiceXero::where('sent_invoice_id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    }

                } elseif ($senderType == 'admin' && $reciverType == 'employee') {
                    //company to user
                    $company = CompanyXero::where('company_id', Session::get('company_id'))->where('status', 1)->first();
                    $config = [
                        'oauth' => [
                            'callback' => 'https://www.recordtimeapp.com.au/rtBeta/dashboard/company/xero/Invoices',
                            'consumer_key' => $company->consumer_key,
                            'consumer_secret' => $company->consumer_secret,
                            'rsa_private_key' => 'file://' . $company->rsa_private_key,
                        ],
                    ];

                    $xero = new PrivateApplication($config);
                    $invoiceDetail = SentInvoice::where('id', $id)->first();
                    if (SynXeroContact::where('email', $invoiceDetail->receiverUserInfo->email)->where('company_xero_id', $company->id)->count() != 0) {
                        $synXeroData = SynXeroContact::where('email', $invoiceDetail->receiverUserInfo->email)->where('company_xero_id', $company->id)->first();
                        $sendXeroSetting = array();
                        foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                            $sendXeroSetting[] = array(
                                'id' => $data->id,
                                'xero_field_id' => $data->xero_field_id,
                                'value' => $data->value,
                            );

                        }
                        $contacts = $xero->loadByGUID('Accounting\\Contact', $synXeroData->xero_contact_id);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                        $invoice->setType("ACCREC")->setContact($contacts)->setReference($id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                        if ($invoiceDetail->invoiceDescription->count() != 0) {
                            foreach ($invoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                        }
                        if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                            foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                $invoiceAmount = 0;
                                foreach ($invoiceAmountQuery as $amount) {
                                    $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                    $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                    $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                    $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                    $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }

                            }
                        }
                        SentInvoiceXero::where('sent_invoice_id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    } else {
                        $Sentinv = SentInvoice:: where('id', $id)->first();
                        $user = User::where('id', $Sentinv->receiver_user_id)->first();
                        $employeDetail = array();
                        $employeDetail[] = array(
                            'id' => $user->id,
                            'email' => $user->email,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'contact_name' => $user->last_name . " " . $user->last_name
                        );

                        $employees = new \XeroPHP\Models\Accounting\Contact($xero);
                        $employees->setContactID($this->getRandNum())
                            ->setClean()
                            ->setName($employeDetail[0]['first_name'] . ' ' . $employeDetail[0]['last_name'])
                            ->setFirstName($employeDetail[0]['first_name'])
                            ->setLastName($employeDetail[0]['last_name'])
                            ->setEmailAddress($employeDetail[0]['email']);
                        if ($employees->save()) {
                            if (SynXeroContact::where("email", $employees->EmailAddress)->where("company_id", Session::get('company_id'))->count() == 0) {
                                $addXeroContact = new SynXeroContact();
                                $addXeroContact->contact_name = $employees->Name;
                                $addXeroContact->first_name = $employees->FirstName;
                                $addXeroContact->last_name = ($employees->LastName == "") ? " " : $employees->LastName;
                                $addXeroContact->email = $employees->EmailAddress;
                                $addXeroContact->xero_contact_id = $employees->ContactId;
                                $addXeroContact->company_xero_id = $company->id;
                                $addXeroContact->company_id = Session::get('company_id');
                                $addXeroContact->save();
                            }
                        }
                        $sendXeroSetting = array();
                        foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                            $sendXeroSetting[] = array(
                                'id' => $data->id,
                                'xero_field_id' => $data->xero_field_id,
                                'value' => $data->value,
                            );

                        }
                        $contacts = $xero->loadByGUID('Accounting\\Contact', $addXeroContact->xero_contact_id);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                        $invoice->setType("ACCREC")->setContact($contacts)->setReference($id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                        if ($invoiceDetail->invoiceDescription->count() != 0) {
                            foreach ($invoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                        }
                        if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                            foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                $invoiceAmount = 0;
                                foreach ($invoiceAmountQuery as $amount) {
                                    $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                    $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                    $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                    $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                    $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }

                            }
                        }
                        SentInvoiceXero::where('sent_invoice_id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    }
                } elseif ($senderType == 'employee' && $reciverType == 'admin') {
                    //user to Employee
                    $company = CompanyXero::where('company_id', Session::get('company_id'))->where('status', 1)->first();
                    $config = [
                        'oauth' => [
                            'callback' => 'https://www.recordtimeapp.com.au/rtBeta/dashboard/company/xero/Invoices',
                            'consumer_key' => $company->consumer_key,
                            'consumer_secret' => $company->consumer_secret,
                            'rsa_private_key' => 'file://' . $company->rsa_private_key,
                        ],
                    ];

                    $xero = new PrivateApplication($config);

                    $invoiceDetail = SentInvoice::where('id', $id)->first();
                    if (SynXeroContact::where('email', $invoiceDetail->senderUserInfo->email)->where('company_xero_id', $company->id)->count() != 0) {
                        $synXeroData = SynXeroContact::where('email', $invoiceDetail->senderUserInfo->email)->where('company_xero_id', $company->id)->first();
                        $sendXeroSetting = array();
                        foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                            $sendXeroSetting[] = array(
                                'id' => $data->id,
                                'xero_field_id' => $data->xero_field_id,
                                'value' => $data->value,
                            );
                        }
                        $contacts = $xero->loadByGUID('Accounting\\Contact', $synXeroData->xero_contact_id);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                        $invoice->setType("ACCPAY")->setContact($contacts)->setReference($id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                        if ($invoiceDetail->invoiceDescription->count() != 0) {
                            foreach ($invoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                        }
                        if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                            foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                $invoiceAmount = 0;
                                foreach ($invoiceAmountQuery as $amount) {
                                    $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                    $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                    $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                    $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }

                            }
                        }
                        SentInvoiceXero::where('sent_invoice_id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    } else {
                        $Sentinv = SentInvoice:: where('id', $id)->first();
                        $user = User::where('id', $Sentinv->user_id)->first();
                        $employeDetail = array();
                        $employeDetail[] = array(
                            'id' => $user->id,
                            'email' => $user->email,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'contact_name' => $user->last_name . " " . $user->last_name
                        );

                        $employees = new \XeroPHP\Models\Accounting\Contact($xero);
                        $employees->setContactID($this->getRandNum())
                            ->setClean()
                            ->setName($employeDetail[0]['first_name'] . ' ' . $employeDetail[0]['last_name'])
                            ->setFirstName($employeDetail[0]['first_name'])
                            ->setLastName($employeDetail[0]['last_name'])
                            ->setEmailAddress($employeDetail[0]['email']);
                        if ($employees->save()) {
                            if (SynXeroContact::where("email", $employees->EmailAddress)->where("company_id", Session::get('company_id'))->count() == 0) {
                                $addXeroContact = new SynXeroContact();
                                $addXeroContact->contact_name = $employees->Name;
                                $addXeroContact->first_name = $employees->FirstName;
                                $addXeroContact->last_name = ($employees->LastName == "") ? " " : $employees->LastName;
                                $addXeroContact->email = $employees->EmailAddress;
                                $addXeroContact->xero_contact_id = $employees->ContactId;
                                $addXeroContact->company_xero_id = $company->id;
                                $addXeroContact->company_id = Session::get('company_id');
                                $addXeroContact->save();
                            }
                        }


                        // $synXeroData=SynXeroContact::where('email',$employees->EmailAddress)->where('company_id',Session::get('company_id'))->first();
                        $sendXeroSetting = array();
                        foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                            $sendXeroSetting[] = array(
                                'id' => $data->id,
                                'xero_field_id' => $data->xero_field_id,
                                'value' => $data->value,
                            );
                        }
                        $contacts = $xero->loadByGUID('Accounting\\Contact', $addXeroContact->xero_contact_id);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                        $invoice->setType("ACCPAY")->setContact($contacts)->setReference($id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                        if ($invoiceDetail->invoiceDescription->count() != 0) {
                            foreach ($invoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                        }
                        if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                            foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                $invoiceAmount = 0;
                                foreach ($invoiceAmountQuery as $amount) {
                                    $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                    $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                    $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                    $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }

                            }
                        }
                        SentInvoiceXero::where('sent_invoice_id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    }


                }
            }
            flash('synchrony updated successfully.', 'success');
            return redirect()->back();
        }

        catch (\Exception $e) {
            flash('Please submit Xero valid Customer key.', 'success');
            return redirect()->route('Company.xero.setting');
        }

    }

    public function taxInvoice(){

    }


    public function getRandNum()
    {
        $randNum = strval(rand(1000,100000));
        return $randNum;
    }

    public function getEmployee($returnObj=false)
    {
        $company = Company::where('id', Session::get('company_id'))->first();
        $config = [
            'oauth' => [
                'callback'         => 'https://recordtime.dev/dashboard/company/xero/employee',
                'consumer_key'     =>  $company->consumer_key,
                'consumer_secret'  =>  $company->consumer_secret,
                'rsa_private_key'  =>  'file://'.$company->rsa_private_key,
            ],
        ];

        $xero = new PrivateApplication($config);
        $str = '';

        //[Employees:Read]
        $employees = $xero->load('Accounting\\Employee')->execute();
        //[/Employees:Read]

        $str = $str . "Get Employee: " . count($employees) . "<br>";

        $where = $xero->load('Accounting\\Employee')->where('
			    Status=="' . \XeroPHP\Models\Accounting\Contact::CONTACT_STATUS_ACTIVE . '" 
			')->execute();
        if (count($where)) {
            $str = $str . "Get an Employee where Status is active: " . $where[0]["FirstName"] . " " . $where[0]["LastName"] . "<br>";
        } else {
            $str = $str . "No Active Employee found";
        }

        if($returnObj) {
            return $employees[0];
        } else {
            return $str;
        }
    }

    public function createEmployee($returnObj=false)
    {
        $company = Company::where('id', Session::get('company_id'))->first();
        $config = [
            'oauth' => [
                'callback'         => 'https://recordtime.dev/dashboard/company/xero/employee/create',
                'consumer_key'     =>  $company->consumer_key,
                'consumer_secret'  =>  $company->consumer_secret,
                'rsa_private_key'  =>  'file://'.$company->rsa_private_key,
            ],
        ];

        $xero = new PrivateApplication($config);
        $str = '';

        //[Employees:Create]
        $employee = new \XeroPHP\Models\Accounting\Employee($xero);
        $employee->setFirstName('Sid-' . $this->getRandNum())
            ->setLastName("Maestre - " . $this->getRandNum())
            ->setEmail("sidney".$this->getRandNum()."@maestre.com");
        $employee->save();
        //[/Employees:Create]

        $str = $str . "Create Employee: " . $employee["FirstName"] . "  " . $employee["LastName"]  .$employee["Email"]. "<br>" ;

        if($returnObj) {
            return $employee;
        } else {
            return $str;
        }
    }

    public function getLineItemForReceipt($xero)
    {
        $lineitem = new \XeroPHP\Models\Accounting\Receipt\LineItem($xero);
        $lineitem->setDescription('My Receipt 1 -' .  $this->getRandNum())
            ->setQuantity(1)
            ->setUnitAmount(20)
            ->setAccountCode("429");
        return $lineitem;
    }

    public function getLineItemForInvoice($xero)
    {
        $lineitem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
        $lineitem->setDescription('INV-' . $this->getRandNum())
            ->setQuantity(10)
            ->setUnitAmount(40)
            ->setAccountCode("429");
        return $lineitem;
    }
    public function getTrackingCategory($xero,$returnObj=false)
    {
        $str = '';

        //[TrackingCategories:Read]
        $trackingcategories = $xero->load('Accounting\\TrackingCategory')->execute();
        //[/TrackingCategories:Read]

        $str = $str . "Get TrackingCategories: " . count($trackingcategories) . "<br>";

        if($returnObj) {
            return $trackingcategories[0];
        } else {
            return $str;
        }
    }

    public function getLineItemWithTracking($xero)
    {
        $tracking = $this->getTrackingCategory($xero,true);

        $lineitem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
        $lineitem->setDescription('INV-' . $this->getRandNum())
            ->setQuantity(1)
            ->setUnitAmount(20)
            ->setAccountCode("429")
            ->addTracking($tracking);
        return $lineitem;
    }

    public function getContact($xero)
    {
        $code = $this->getRandNum();
        $contact= new \XeroPHP\Models\Accounting\Contact($xero);
        $contact->setName('Sidney-' . $code)
            ->setFirstName('Sid-' . $code)
            ->setLastName("Maestre - " . $code)
            ->setEmailAddress("sidney" . $code . "@maestre.com");
        $contact->save();
        return $contact;
    }


    public function createExpenseClaim($returnObj = false){

        $company = Company::where('id', Session::get('company_id'))->first();
        $config = [
            'oauth' => [
                'callback'         => 'https://recordtime.dev/dashboard/company/xero/create/expense/claim',
                'consumer_key'     =>  $company->consumer_key,
                'consumer_secret'  =>  $company->consumer_secret,
                'rsa_private_key'  =>  'file://'.$company->rsa_private_key,
            ],
        ];
        $xero = new PrivateApplication($config);
        $str = '';
        $all = $xero->load('Accounting\\User')->execute();
        $userGuid = $all[0]["UserID"];
        $contact = $this->getContact($xero,true);
        $lineitem = $this->getLineItemForReceipt($xero);
        if (count($all)) {
            //[ExpenseClaims:Create]
            $user = new \XeroPHP\Models\Accounting\User($xero);
            $user->setUserID($userGuid);
            $receipt = new \XeroPHP\Models\Accounting\Receipt($xero);
            $receipt->setDate(new DateTime('2017-01-02'))
                ->setContact($contact)
                ->addLineItem($lineitem)
                ->setUser($user);
            $receipt->save();
            $expenseclaim = new \XeroPHP\Models\Accounting\ExpenseClaim($xero);
            $expenseclaim->setUser($user)
                ->addReceipt($receipt);
            $expenseclaim->save();
            //[/ExpenseClaims:Create]
            $str = $str ."Created Expense Claim: " . $expenseclaim["ExpenseClaimID"] . "<br>" ;
        }
        if($returnObj) {
            return $expenseclaim;
        } else {
            return $str;
        }
    }

    public function createInvoiceAccPay($returnObj=false)
    {
        $company = Company::where('id', Session::get('company_id'))->first();
        $config = [
            'oauth' => [
                'callback'         => 'https://recordtime.dev/dashboard/company/xero/create/invoce/accpay',
                'consumer_key'     =>  $company->consumer_key,
                'consumer_secret'  =>  $company->consumer_secret,
                'rsa_private_key'  =>  'file://'.$company->rsa_private_key,
            ],
        ];
        $xero = new PrivateApplication($config);
        $str = '';

        $contact = $this->getContact($xero,true);
        $lineitem = $this->getLineItemForInvoice($xero,true);

        $new = new \XeroPHP\Models\Accounting\Invoice($xero);
        $new->setReference('Ref-' . $this->getRandNum())
            ->setDueDate(new DateTime('2018-07-03'))
            ->setType(\XeroPHP\Models\Accounting\INVOICE::INVOICE_TYPE_ACCPAY)
            ->addLineItem($lineitem)
            ->setContact($contact)
            ->setStatus(\XeroPHP\Models\Accounting\INVOICE::INVOICE_STATUS_AUTHORISED)
            ->setLineAmountType("Exclusive");
        $new->save();

        $str = $str . "Create a new Invoice: " . $new["Reference"] . " -- $" . $new["Total"] . "<br>" ;

        if($returnObj) {
            return $new;
        } else {
            return $str;
        }
    }

    public function createInvoiceWithTracking($returnObj=false)
    {
        $company = Company::where('id', Session::get('company_id'))->first();
        $config = [
            'oauth' => [
                'callback'         => 'https://recordtime.dev/dashboard/company/xero/create/invoce/tracking',
                'consumer_key'     =>  $company->consumer_key,
                'consumer_secret'  =>  $company->consumer_secret,
                'rsa_private_key'  =>  'file://'.$company->rsa_private_key,
            ],
        ];
        $xero = new PrivateApplication($config);
        $str = '';

        $contact = $this->getContact($xero,true);
        $lineitem = $this->getLineItemWithTracking($xero,true);

        $new = new \XeroPHP\Models\Accounting\Invoice($xero);
        $new->setReference('Ref-' . $this->getRandNum())
            ->setDueDate(new DateTime('2017-03-02'))
            ->setType(\XeroPHP\Models\Accounting\INVOICE::INVOICE_TYPE_ACCREC)
            ->addLineItem($lineitem)
            ->setContact($contact)
            ->setStatus(\XeroPHP\Models\Accounting\INVOICE::INVOICE_STATUS_AUTHORISED)
            ->setLineAmountType("Exclusive");
        $new->save();

        $str = $str . "Create a new Invoice: " . $new["Reference"] . " -- $" . $new["Total"] . "<br>" ;

        if($returnObj) {
            return $new;
        } else {
            return $str;
        }
    }

    public function createInvoiceAuthorised($returnObj=false)
    {
        $company = Company::where('id', Session::get('company_id'))->first();
        $config = [
            'oauth' => [
                'callback'         => 'https://recordtime.dev/dashboard/company/xero/create/invoce/authorised',
                'consumer_key'     =>  $company->consumer_key,
                'consumer_secret'  =>  $company->consumer_secret,
                'rsa_private_key'  =>  'file://'.$company->rsa_private_key,
            ],
        ];
        $xero = new PrivateApplication($config);
        $str = '';

        $contact = $this->getContact($xero,true);
        $lineitem = $this->getLineItemForInvoice($xero,true);

        $new = new \XeroPHP\Models\Accounting\Invoice($xero);
        $new->setReference('Ref-' . $this->getRandNum())
            ->setDueDate(new DateTime('2017-03-02'))
            ->setType(\XeroPHP\Models\Accounting\INVOICE::INVOICE_TYPE_ACCPAY)
            ->addLineItem($lineitem)
            ->setContact($contact)
            ->setStatus(\XeroPHP\Models\Accounting\INVOICE::INVOICE_STATUS_AUTHORISED)
            ->setLineAmountType("Exclusive");
        $new->save();

        $str = $str . "Create a new Invoice: " . $new["Reference"] . " -- $" . $new["Total"] . "<br>" ;

        if($returnObj) {
            return $new;
        } else {
            return $str;
        }
    }

    public function createInvoiceAccRec($returnObj=false)
    {
        $company = Company::where('id', Session::get('company_id'))->first();
        $config = [
            'oauth' => [
                'callback'         => 'https://recordtime.dev/dashboard/company/xero/create/invoce/authorised',
                'consumer_key'     =>  $company->consumer_key,
                'consumer_secret'  =>  $company->consumer_secret,
                'rsa_private_key'  =>  'file://'.$company->rsa_private_key,
            ],
        ];
        $xero = new PrivateApplication($config);
        $str = '';

        $contact = $this->getContact($xero,true);
        $lineitem = $this->getLineItemForInvoice($xero,true);

        $new = new \XeroPHP\Models\Accounting\Invoice($xero);
        $new->setReference('Ref-' . $this->getRandNum())
            ->setDueDate(new DateTime('2018-07-07'))
            ->setType(\XeroPHP\Models\Accounting\INVOICE::INVOICE_TYPE_ACCREC)
            ->addLineItem($lineitem)
            ->setContact($contact)
            ->setStatus(\XeroPHP\Models\Accounting\INVOICE::INVOICE_STATUS_AUTHORISED)
            ->setLineAmountType("Exclusive");
        $new->save();

        $str = $str . "Create a new Invoice: " . $new["Reference"] . " -- $" . $new["Total"] . "<br>" ;

        if($returnObj) {
            return $new;
        } else {
            return $str;
        }
    }




}
