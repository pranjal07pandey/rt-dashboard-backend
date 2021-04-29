@extends('layouts.companyDashboard')
@section('content')

    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> TimeSheet
            <small>Add/View TimeSheet</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">TimeSheet</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">Timesheet
                Detail</h3>
            <br>
            <h5 style="padding-left: 18px;"><strong>Employee Name: </strong>{{$user->first_name." ".$user->last_name}}
            </h5>
            <?php
            $d = array();
            foreach ($allSentDocket as $t) {
                array_push($d, $t['docketTime']);
            }
            ?>

            @foreach ($dates as $ityemsss)
                @if (in_array($ityemsss,$d))
                    @foreach ($allSentDocket as $r)
                        @if ($r['docketTime']==$ityemsss)
                            <div class="col-md-2">
                                <div class="card">
                                    <div class="box">
                                        <h5 style="text-align:center; font-weight: 700;color: gray;font-size: 13px;">{{$r['docketTitle']}}</h5>
                                        <span><strong style="color: gray;">Date: &nbsp; </strong>{{$r['docketTime']}}</span>
                                        <span><strong style="color: gray;">Total hour: &nbsp; </strong>{{$r['totalUnitRate']}}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="col-md-2">
                        <div class="card">
                            <div style="    border-top: 3px solid #ffcaca;" class="box">
                                <h5 style="text-align:center; font-weight: 700;color: gray;font-size: 13px;">Empty </h5>
                                <span><strong style="color: gray;">Date: &nbsp; </strong> {{$ityemsss}}</span>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    <br><br>
    <style>
        .card {

            background: #fff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .1);
            border-radius: 10px;
            transition: 0.5s;
            margin-top: 10px;
            margin-bottom: 15px;
            min-height: 107px;
        }

        .card:hover {
            box-shadow: 0 30px 70px rgba(0, 0, 0, .2);
        }

        .card .box {
            padding: 4px 20px 0px 19px;
            box-sizing: border-box;
            width: 100%;
        }

        .card .box h2 {
            font-size: 20px;
            color: #262626;
            margin: 20px auto;
        }

        .card .box h2 span {
            font-size: 14px;
            background: #e91e63;
            color: #fff;
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
        }

        .card .box p {
            color: #262626;
        }

        .card .box span {
            display: inline-flex;
        }

        .card .box ul {
            margin: 0;
            padding: 0;
        }

        .card .box ul li {
            list-style: none;
            float: left;
        }

        .card .box ul li a {
            display: block;
            color: #aaa;
            margin: 0 10px;
            font-size: 20px;
            transition: 0.5s;
            text-align: center;
        }

        .card .box ul li:hover a {
            color: #e91e63;
            transform: rotateY(360deg);
        }

        .box {
            box-shadow: none;
        }
    </style>



@endsection<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyXero;
use App\Docket;
use App\Email_Client;
use App\EmailSentInvoice;
use App\Employee;
use App\SentDockets;
use App\SentDocketsValue;
use App\SentDocketTimesheet;
use App\SentInvoice;
use App\TimesheetDocketAttachment;
use App\TimesheetDocketDetail;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use Faker\Provider\DateTime;
use FontLib\Table\Type\name;
use function GuzzleHttp\Promise\queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use XeroPHP\Remote\URL;
use XeroPHP;
use XeroPHP\Remote\Request as XeroRequest;
use Zend\Diactoros\Response\RedirectResponse;
use XeroPHP\Models\Accounting\Contact;


class XeroConnectionController extends Controller
{

    private $xero;

    public function __construct()
    {
        $this->xero = xero();
    }

    public function connect($scope_check, ServerRequestInterface $request, ResponseInterface $response)
    {
        if($scope_check == 1){
            $scope = 'payroll.employees,payroll.superfunds,payroll.leaveapplications,payroll.payitems,payroll.payrollcalendars,payroll.payruns,payroll.payslip,payroll.timesheets,payroll.settings';
        }else{
            $scope = '';
        }
        $url = new URL($this->xero, URL::OAUTH_REQUEST_TOKEN);
        $request = new XeroRequest($this->xero, $url);
        $request->send();
        $oauth_response = $request->getResponse()->getOAuthResponse();
        Session::put('xero_oauth', (object)[
            'token' => $oauth_response['oauth_token'],
            'token_secret' => $oauth_response['oauth_token_secret'],
            'expires' => '']);
        return new RedirectResponse($this->xero->getAuthorizeURL($oauth_response['oauth_token'])."&scope=".$scope);
    }

    public function connectionCallBack(ServerRequestInterface $request, ResponseInterface $response)
    {
        try{
            $this->xero->getOAuthClient()
                ->setToken(Session::get('xero_oauth')->token)
                ->setTokenSecret(Session::get('xero_oauth')->token_secret);
            $params = $request->getQueryParams();
            $this->xero->getOAuthClient()->setVerifier($params['oauth_verifier']);
            $url = new URL($this->xero, URL::OAUTH_ACCESS_TOKEN);
            $request = new XeroRequest($this->xero, $url);
            $request->send();
            $oauth_response = $request->getResponse()->getOAuthResponse();
            Session::put(
                'xero_oauth', (object)[
                'token' => $oauth_response['oauth_token'],
                'token_secret' => $oauth_response['oauth_token_secret'],
                'expires' => Carbon::now()->addSecond($oauth_response['oauth_expires_in'])
            ]);

            return new RedirectResponse('connectionSucess');
        }catch (\Exception $e){

            return redirect('dashboard/company/profile/xeroSetting');
        }
    }

    public function connectionSucess()
    {
//        $this->xero->getOAuthClient()
//            ->setToken(Session::get('xero_oauth')->token)
//            ->setTokenSecret(Session::get('xero_oauth')->token_secret);
//        $employee = $this->xero->load(XeroPHP\Models\PayrollAU\Employee::class)->execute();
//        dd($employee);

        $this->xero->getOAuthClient()
            ->setToken(Session::get('xero_oauth')->token)
            ->setTokenSecret(Session::get('xero_oauth')->token_secret);
        $companyXero = CompanyXero::where('company_id', Session::get('company_id'))->count();
        if ($companyXero == 0) {

            $organization = $this->xero->load(XeroPHP\Models\Accounting\Organisation::class)->execute();
            $userDetail = $this->xero->load(XeroPHP\Models\Accounting\User::class)->execute();
            $add = json_decode(json_encode($organization[0]['Addresses']), true);
            $cont = json_decode(json_encode($organization[0]['Phones']), true);
            $xeroOrganization = new CompanyXero();
            $xeroOrganization->company_id = Session::get('company_id');
            $xeroOrganization->xero_user_id = $userDetail[0]['UserID'];
            $xeroOrganization->xero_email = $userDetail[0]['EmailAddress'];
            $xeroOrganization->xero_user_first_name = $userDetail[0]['FirstName'];
            $xeroOrganization->xero_user_last_name = $userDetail[0]['LastName'];
            $xeroOrganization->xero_organization_id = $organization[0]['OrganisationID'];
            $xeroOrganization->xero_organization_name = $organization[0]['LegalName'];
            $xeroOrganization->xero_organination_address = serialize($add);
            $xeroOrganization->xero_organization_contact = serialize($cont);
            $xeroOrganization->organization_line_of_business = $organization[0]['LineOfBusiness']==null ? "N/a": $organization[0]['LineOfBusiness'];
            $xeroOrganization->save();
            flash('connection Successfully.', 'success');
            return redirect('dashboard/company/profile/xeroSetting');
        } else {

            $organization = $this->xero->load(XeroPHP\Models\Accounting\Organisation::class)->execute();
            if (CompanyXero::where('company_id', Session::get('company_id'))->first()->xero_organization_id == $organization[0]['OrganisationID']) {
                $companyXeroId = CompanyXero::where('company_id', Session::get('company_id'))->where('xero_organization_id', $organization[0]['OrganisationID'])->first();
                $organization = $this->xero->load(XeroPHP\Models\Accounting\Organisation::class)->execute();
                $userDetail = $this->xero->load(XeroPHP\Models\Accounting\User::class)->execute();
                $add = json_decode(json_encode($organization[0]['Addresses']), true);
                $cont = json_decode(json_encode($organization[0]['Phones']), true);
                $UpdatexeroOrganization = CompanyXero::findOrFail($companyXeroId->id);
                $UpdatexeroOrganization->company_id = Session::get('company_id');
                $UpdatexeroOrganization->xero_user_id = $userDetail[0]['UserID'];
                $UpdatexeroOrganization->xero_email = $userDetail[0]['EmailAddress'];
                $UpdatexeroOrganization->xero_user_first_name = $userDetail[0]['FirstName'];
                $UpdatexeroOrganization->xero_user_last_name = $userDetail[0]['LastName'];
                $UpdatexeroOrganization->xero_organization_id = $organization[0]['OrganisationID'];
                $UpdatexeroOrganization->xero_organization_name = $organization[0]['LegalName'];
                $UpdatexeroOrganization->xero_organination_address = serialize($add);
                $UpdatexeroOrganization->xero_organization_contact = serialize($cont);
                $UpdatexeroOrganization->organization_line_of_business = $organization[0]['LineOfBusiness']==null ? "N/a": $organization[0]['LineOfBusiness'];
                $UpdatexeroOrganization->save();

                flash('Xero Connection Successfully.', 'success');
                return redirect('dashboard/company/profile/xeroSetting');
            } else {
                Session::forget('xero_oauth');
//                 dd(Session::get('xero_oauth'));
                flash('You Connect Another Organization.', 'warning');
                return redirect('dashboard/company/profile/xeroSetting');
            }
        }


    }

    public function xeroTimeOut()
    {
        Session::forget('xero_oauth');
    }

    public function xeroDisconnected(){
        Session::forget('xero_oauth');
        flash('Xero Disconnect Successfully.', 'success');
        return redirect('dashboard/company/profile/xeroSetting');
    }


    public function xeroEmailInvoice($id)
    {
        if (Session::get('xero_oauth') == null) {
            flash('Please Connect to Xero.', 'warning');
            return redirect()->back();
        } else {
            $this->xero->getOAuthClient()
                ->setToken(Session::get('xero_oauth')->token)
                ->setTokenSecret(Session::get('xero_oauth')->token_secret);
            $emailSentInvoice = EmailSentInvoice::findorfail($id);
            $taxRate = $this->xero->load(XeroPHP\Models\Accounting\TaxRate::class)->where('
			    Status=="' . \XeroPHP\Models\Accounting\TaxRate::TAX_STATUS_ACTIVE . '"
			')->where('Name', $emailSentInvoice->gst.'%'.' '.'GST on Income')->execute();
//            foreach ($taxRate as $item) {

            if (count($taxRate) > 0) {
                $contact = $this->xero->load(Contact::class)->where('EmailAddress', $emailSentInvoice->receiverInfo->email)->execute();
                if (count($contact) == 0) {
                    $emailClient = Email_Client::where('company_id', Session::get('company_id'))->where('email_user_id', $emailSentInvoice->receiver_user_id)->first();
                    $name = trim($emailClient->full_name);
                    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
                    $first_name = trim(preg_replace('#' . $last_name . '#', '', $name));
                    $xeroContact = new Contact($this->xero);
                    $xeroContact->setContactID($this->getRandNum())
                        ->setClean()
                        ->setName($first_name . ' ' . $last_name)
                        ->setFirstName($first_name)
                        ->setLastName($last_name)
                        ->setEmailAddress($emailSentInvoice->receiverInfo->email);
                    if ($xeroContact->save()) {
                        $contacts = $this->xero->loadByGUID('Accounting\\Contact', $xeroContact->ContactId);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                        $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                        foreach ($emailSentInvoice->invoiceDescription as $detail) {
                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                            $xeroLineItem->setQuantity(1);
                            $xeroLineItem->setDescription($detail->description);
                            $xeroLineItem->setUnitAmount($detail->amount);
                            $xeroLineItem->setDiscountRate(0);
                            $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                            $invoice->addLineItem($xeroLineItem);
                            $invoice->save();
                        }
                        EmailSentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    }
                }
                else {
                    $contact = $this->xero->load(Contact::class)->where('EmailAddress', $emailSentInvoice->receiverInfo->email)->execute();
                    $contacts = $this->xero->loadByGUID('Accounting\\Contact', $contact[0]['ContactID']);
                    $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                    $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                    foreach ($emailSentInvoice->invoiceDescription as $detail) {
                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                        $xeroLineItem->setQuantity(1);
                        $xeroLineItem->setDescription($detail->description);
                        $xeroLineItem->setUnitAmount($detail->amount);
                        $xeroLineItem->setDiscountRate(0);
                        $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                        $invoice->addLineItem($xeroLineItem);
                        $invoice->save();
                    }
                    EmailSentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                }
            }
            else {
                $createTaxRate = new XeroPHP\Models\Accounting\TaxRate($this->xero);
                $createTaxCompnent = new XeroPHP\Models\Accounting\TaxRate\TaxComponent($this->xero);
                $createTaxRate->setClean()->setName($emailSentInvoice->gst.'%'.' '.'GST on Income')->setReportTaxType('OUTPUT');
                $createTaxCompnent->setRate($emailSentInvoice->gst)
                    ->setName($emailSentInvoice->gst.'%'.' '.'GST on Income');
                $createTaxRate->addTaxComponent($createTaxCompnent);
                if ($createTaxRate->save()){
                    $contact = $this->xero->load(Contact::class)->where('EmailAddress', $emailSentInvoice->receiverInfo->email)->execute();
                    if (count($contact) == 0) {
                        $emailClient = Email_Client::where('company_id', Session::get('company_id'))->where('email_user_id', $emailSentInvoice->receiver_user_id)->first();
                        $name = trim($emailClient->full_name);
                        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
                        $first_name = trim(preg_replace('#' . $last_name . '#', '', $name));
                        $xeroContact = new Contact($this->xero);
                        $xeroContact->setContactID($this->getRandNum())
                            ->setClean()
                            ->setName($first_name . ' ' . $last_name)
                            ->setFirstName($first_name)
                            ->setLastName($last_name)
                            ->setEmailAddress($emailSentInvoice->receiverInfo->email);
                        if ($xeroContact->save()) {
                            $contacts = $this->xero->loadByGUID('Accounting\\Contact', $xeroContact->ContactId);
                            $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                            $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                            foreach ($emailSentInvoice->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setDiscountRate(0);
                                $xeroLineItem->setTaxType($createTaxRate->TaxType);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                            EmailSentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                        }
                    }
                    else {
                        $contact = $this->xero->load(Contact::class)->where('EmailAddress', $emailSentInvoice->receiverInfo->email)->execute();
                        $contacts = $this->xero->loadByGUID('Accounting\\Contact', $contact[0]['ContactID']);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                        $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                        foreach ($emailSentInvoice->invoiceDescription as $detail) {
                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                            $xeroLineItem->setQuantity(1);
                            $xeroLineItem->setDescription($detail->description);
                            $xeroLineItem->setUnitAmount($detail->amount);
                            $xeroLineItem->setDiscountRate(0);
                            $xeroLineItem->setTaxType($createTaxRate->TaxType);
                            $invoice->addLineItem($xeroLineItem);
                            $invoice->save();
                        }
                        EmailSentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    }

                }


            }
//            }

            flash('Email Invoice Sync Successfully.', 'warning');
            return redirect()->back();
        }

    }


    public function xeroInvoice($id){

        if (Session::get('xero_oauth') == null) {
            flash('Please Connect to Xero.', 'warning');
            return redirect()->back();
        } else {
            $sentInvoiceDetail = SentInvoice::findorfail($id);
            $senderCompany = Company::where('id', Session::get('company_id'))->first();
            $employessender = Employee::where('company_id', Session::get('company_id'))->pluck('user_id')->toArray();
            $adminsender = Company::where('id', Session::get('company_id'))->pluck('user_id')->toArray();
            $totalCompany = array_merge($employessender, $adminsender);
            if (!in_array($sentInvoiceDetail->receiver_user_id, $totalCompany)) {
                $reciverType = "company";
            } else {
                if (Employee::where('user_id', $sentInvoiceDetail->receiver_user_id)->where('is_admin', 1)->count() == 1) {
                    $reciverType = 'admin';
                } elseif (Employee::where('user_id', $sentInvoiceDetail->receiver_user_id)->where('employed', 1)->count() == 1) {
                    $reciverType = 'employee';
                } elseif ($sentInvoiceDetail->receiver_user_id == $senderCompany->user_id) {
                    $reciverType = 'admin';
                }
            }
            if ($sentInvoiceDetail->user_id == $senderCompany->user_id) {
                $senderType = "admin";
            } elseif (Employee::where('user_id', $sentInvoiceDetail->user_id)->where('is_admin', 1)->count() == 1) {
                $senderType = 'admin';
            } elseif (Employee::where('user_id', $sentInvoiceDetail->user_id)->where('employed', 1)->count() == 1) {
                $senderType = 'employee';
            }
            if ($reciverType == "company") {
                $this->xero->getOAuthClient()
                    ->setToken(Session::get('xero_oauth')->token)
                    ->setTokenSecret(Session::get('xero_oauth')->token_secret);
                $taxRate = $this->xero->load(XeroPHP\Models\Accounting\TaxRate::class)->where('
			    Status=="' . \XeroPHP\Models\Accounting\TaxRate::TAX_STATUS_ACTIVE . '"
			')->where('Name', $sentInvoiceDetail->gst.'%'.' '.'GST on Income')->execute();
//            foreach ($taxRate as $item) {
                if (count($taxRate) > 0) {
                    $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->receiverUserInfo->email)->execute();
                    if (count($contact) == 0) {
                        $user = User::where('id', $sentInvoiceDetail->receiver_user_id)->first();
                        $xeroContact = new Contact($this->xero);
                        $xeroContact->setContactID($this->getRandNum())
                            ->setClean()
                            ->setName($user->first_name . ' ' . $user->last_name)
                            ->setFirstName($user->first_name)
                            ->setLastName($user->last_name)
                            ->setEmailAddress($user->email);
                        if ($xeroContact->save()) {
                            $contacts = $this->xero->loadByGUID('Accounting\\Contact', $xeroContact->ContactId);
                            $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                            $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                            foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setDiscountRate(0);
                                $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                            if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                                foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                    $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                    $invoiceAmount = 0;
                                    foreach ($invoiceAmountQuery as $amount) {
                                        $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                        $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                        $xeroLineItem->setDiscountRate(0);
                                        $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                            }
                            SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                        }
                    }
                    else {
                        $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->receiverUserInfo->email)->execute();
                        $contacts = $this->xero->loadByGUID('Accounting\\Contact', $contact[0]['ContactID']);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                        $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                        foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                            $xeroLineItem->setQuantity(1);
                            $xeroLineItem->setDescription($detail->description);
                            $xeroLineItem->setUnitAmount($detail->amount);
                            $xeroLineItem->setDiscountRate(0);
                            $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                            $invoice->addLineItem($xeroLineItem);
                            $invoice->save();
                        }
                        if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                            foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                $invoiceAmount = 0;
                                foreach ($invoiceAmountQuery as $amount) {
                                    $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                    $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                    $xeroLineItem->setDiscountRate(0);
                                    $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }
                            }
                        }
                        SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    }
                }
                else {
                    $createTaxRate = new XeroPHP\Models\Accounting\TaxRate($this->xero);
                    $createTaxCompnent = new XeroPHP\Models\Accounting\TaxRate\TaxComponent($this->xero);
                    $createTaxRate->setClean()->setName($sentInvoiceDetail->gst.'%'.' '.'GST on Income')->setReportTaxType('OUTPUT');
                    $createTaxCompnent->setRate($sentInvoiceDetail->gst)
                        ->setName($sentInvoiceDetail->gst.'%'.' '.'GST on Income');
                    $createTaxRate->addTaxComponent($createTaxCompnent);
                    if ($createTaxRate->save()){
                        $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->receiverUserInfo->email)->execute();
                        if (count($contact) == 0) {
                            $user = User::where('id', $sentInvoiceDetail->receiver_user_id)->first();
                            $xeroContact = new Contact($this->xero);
                            $xeroContact->setContactID($this->getRandNum())
                                ->setClean()
                                ->setName($user->first_name . ' ' . $user->last_name)
                                ->setFirstName($user->first_name)
                                ->setLastName($user->last_name)
                                ->setEmailAddress($user->email);
                            if ($xeroContact->save()) {
                                $contacts = $this->xero->loadByGUID('Accounting\\Contact', $xeroContact->ContactId);
                                $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                                $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                                foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($detail->description);
                                    $xeroLineItem->setUnitAmount($detail->amount);
                                    $xeroLineItem->setDiscountRate(0);
                                    $xeroLineItem->setTaxType($createTaxRate->TaxType);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }
                                if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                                    foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                        $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                        $invoiceAmount = 0;
                                        foreach ($invoiceAmountQuery as $amount) {
                                            $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                            $xeroLineItem->setQuantity(1);
                                            $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                            $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                            $xeroLineItem->setDiscountRate(0);
                                            $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                            $invoice->addLineItem($xeroLineItem);
                                            $invoice->save();
                                        }
                                    }
                                }
                                SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                            }
                        }
                        else {
                            $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->receiverUserInfo->email)->execute();
                            $contacts = $this->xero->loadByGUID('Accounting\\Contact', $contact[0]['ContactID']);
                            $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                            $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                            foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setDiscountRate(0);
                                $xeroLineItem->setTaxType($createTaxRate->TaxType);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                            if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                                foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                    $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                    $invoiceAmount = 0;
                                    foreach ($invoiceAmountQuery as $amount) {
                                        $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                        $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                        $xeroLineItem->setDiscountRate(0);
                                        $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                            }
                            SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                        }

                    }

                }

            } elseif ($senderType == 'admin' && $reciverType == 'employee') {

                $this->xero->getOAuthClient()
                    ->setToken(Session::get('xero_oauth')->token)
                    ->setTokenSecret(Session::get('xero_oauth')->token_secret);
                $taxRate = $this->xero->load(XeroPHP\Models\Accounting\TaxRate::class)->where('
			    Status=="' . \XeroPHP\Models\Accounting\TaxRate::TAX_STATUS_ACTIVE . '"
			')->where('Name', $sentInvoiceDetail->gst.'%'.' '.'GST on Income')->execute();
//            foreach ($taxRate as $item) {
                if (count($taxRate) > 0) {
                    $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->receiverUserInfo->email)->execute();
                    if (count($contact) == 0) {
                        $user = User::where('id', $sentInvoiceDetail->receiver_user_id)->first();
                        $xeroContact = new Contact($this->xero);
                        $xeroContact->setContactID($this->getRandNum())
                            ->setClean()
                            ->setName($user->first_name . ' ' . $user->last_name)
                            ->setFirstName($user->first_name)
                            ->setLastName($user->last_name)
                            ->setEmailAddress($user->email);
                        if ($xeroContact->save()) {
                            $contacts = $this->xero->loadByGUID('Accounting\\Contact', $xeroContact->ContactId);
                            $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                            $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                            if ($sentInvoiceDetail->invoiceDescription->count() != 0) {
                                foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($detail->description);
                                    $xeroLineItem->setUnitAmount($detail->amount);
                                    $xeroLineItem->setDiscountRate(0);
                                    $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }
                            }
                            if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                                foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                    $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                    $invoiceAmount = 0;
                                    foreach ($invoiceAmountQuery as $amount) {
                                        $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                        $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                        $xeroLineItem->setDiscountRate(0);
                                        $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                            }
                            SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                        }
                    }
                    else {
                        $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->receiverUserInfo->email)->execute();
                        $contacts = $this->xero->loadByGUID('Accounting\\Contact', $contact[0]['ContactID']);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                        $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                        if ($sentInvoiceDetail->invoiceDescription->count() != 0) {
                            foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setDiscountRate(0);
                                $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                        }
                        if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                            foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                $invoiceAmount = 0;
                                foreach ($invoiceAmountQuery as $amount) {
                                    $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                    $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                    $xeroLineItem->setDiscountRate(0);
                                    $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }
                            }
                        }

                        SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);

                    }
                }
                else {
                    $createTaxRate = new XeroPHP\Models\Accounting\TaxRate($this->xero);
                    $createTaxCompnent = new XeroPHP\Models\Accounting\TaxRate\TaxComponent($this->xero);
                    $createTaxRate->setClean()->setName($sentInvoiceDetail->gst.'%'.' '.'GST on Income')->setReportTaxType('OUTPUT');
                    $createTaxCompnent->setRate($sentInvoiceDetail->gst)
                        ->setName($sentInvoiceDetail->gst.'%'.' '.'GST on Income');
                    $createTaxRate->addTaxComponent($createTaxCompnent);
                    if ($createTaxRate->save()){
                        $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->receiverUserInfo->email)->execute();
                        if (count($contact) == 0) {
                            $user = User::where('id', $sentInvoiceDetail->receiver_user_id)->first();
                            $xeroContact = new Contact($this->xero);
                            $xeroContact->setContactID($this->getRandNum())
                                ->setClean()
                                ->setName($user->first_name . ' ' . $user->last_name)
                                ->setFirstName($user->first_name)
                                ->setLastName($user->last_name)
                                ->setEmailAddress($user->email);
                            if ($xeroContact->save()) {
                                $contacts = $this->xero->loadByGUID('Accounting\\Contact', $xeroContact->ContactId);
                                $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                                $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                                if ($sentInvoiceDetail->invoiceDescription->count() != 0) {
                                    foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($detail->description);
                                        $xeroLineItem->setUnitAmount($detail->amount);
                                        $xeroLineItem->setDiscountRate(0);
                                        $xeroLineItem->setTaxType($createTaxRate->TaxType);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                                if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                                    foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                        $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                        $invoiceAmount = 0;
                                        foreach ($invoiceAmountQuery as $amount) {
                                            $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                            $xeroLineItem->setQuantity(1);
                                            $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                            $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                            $xeroLineItem->setDiscountRate(0);
                                            $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                            $invoice->addLineItem($xeroLineItem);
                                            $invoice->save();
                                        }
                                    }

                                }


                                SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);

                            }
                        }
                        else {
                            $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->receiverUserInfo->email)->execute();
                            $contacts = $this->xero->loadByGUID('Accounting\\Contact', $contact[0]['ContactID']);
                            $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                            $invoice->setType("ACCREC")->setContact($contacts)->setReference($id);
                            if ($sentInvoiceDetail->invoiceDescription->count() != 0) {
                                foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($detail->description);
                                    $xeroLineItem->setUnitAmount($detail->amount);
                                    $xeroLineItem->setDiscountRate(0);
                                    $xeroLineItem->setTaxType($createTaxRate->TaxType);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }
                            }
                            if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                                foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                    $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                    $invoiceAmount = 0;
                                    foreach ($invoiceAmountQuery as $amount) {
                                        $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                        $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                        $xeroLineItem->setDiscountRate(0);
                                        $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                            }
                            SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                        }
                    }
                }

            } elseif ($senderType == 'employee' && $reciverType == 'admin') {
                $this->xero->getOAuthClient()
                    ->setToken(Session::get('xero_oauth')->token)
                    ->setTokenSecret(Session::get('xero_oauth')->token_secret);
                $taxRate = $this->xero->load(XeroPHP\Models\Accounting\TaxRate::class)->where('
			    Status=="' . \XeroPHP\Models\Accounting\TaxRate::TAX_STATUS_ACTIVE . '"
			')->where('Name', $sentInvoiceDetail->gst.'%'.' '.'GST on Expense')->execute();
                if (count($taxRate) > 0) {
                    $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->senderUserInfo->email)->execute();
                    if (count($contact) == 0) {
                        $user = User::where('id', $sentInvoiceDetail->user_id)->first();
                        $xeroContact = new Contact($this->xero);
                        $xeroContact->setContactID($this->getRandNum())
                            ->setClean()
                            ->setName($user->first_name . ' ' . $user->last_name)
                            ->setFirstName($user->first_name)
                            ->setLastName($user->last_name)
                            ->setEmailAddress($user->email);
                        if ($xeroContact->save()) {
                            $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->senderUserInfo->email)->execute();
                            $contacts = $this->xero->loadByGUID('Accounting\\Contact', $contact[0]['ContactID']);
                            $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                            $invoice->setType("ACCPAY")->setContact($contacts)->setReference($id);
                            if ($sentInvoiceDetail->invoiceDescription->count() != 0) {
                                foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($detail->description);
                                    $xeroLineItem->setUnitAmount($detail->amount);
                                    $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }
                            }
                            if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                                foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                    $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                    $invoiceAmount = 0;
                                    foreach ($invoiceAmountQuery as $amount) {
                                        $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                        $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                        $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                            }
                            SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);

                        }
                    }else{
                        $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->senderUserInfo->email)->execute();
                        $contacts = $this->xero->loadByGUID('Accounting\\Contact', $contact[0]['ContactID']);
                        $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                        $invoice->setType("ACCPAY")->setContact($contacts)->setReference($id);
                        if ($sentInvoiceDetail->invoiceDescription->count() != 0) {
                            foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                $xeroLineItem->setQuantity(1);
                                $xeroLineItem->setDescription($detail->description);
                                $xeroLineItem->setUnitAmount($detail->amount);
                                $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                $invoice->addLineItem($xeroLineItem);
                                $invoice->save();
                            }
                        }
                        if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                            foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                $invoiceAmount = 0;
                                foreach ($invoiceAmountQuery as $amount) {
                                    $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                    $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                    $xeroLineItem->setTaxType($taxRate['0']['TaxType']);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }
                            }
                        }
                        SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                    }

                }else{
                    $createTaxRate = new XeroPHP\Models\Accounting\TaxRate($this->xero);
                    $createTaxCompnent = new XeroPHP\Models\Accounting\TaxRate\TaxComponent($this->xero);
                    $createTaxRate->setClean()->setName($sentInvoiceDetail->gst.'%'.' '.'GST on Expense')->setReportTaxType('INPUT');
                    $createTaxCompnent->setRate($sentInvoiceDetail->gst)
                        ->setName($sentInvoiceDetail->gst.'%'.' '.'GST on Expense');
                    $createTaxRate->addTaxComponent($createTaxCompnent);
                    if ($createTaxRate->save()){

                        $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->senderUserInfo->email)->execute();
                        if (count($contact) == 0) {
                            $user = User::where('id', $sentInvoiceDetail->user_id)->first();
                            $xeroContact = new Contact($this->xero);
                            $xeroContact->setContactID($this->getRandNum())
                                ->setClean()
                                ->setName($user->first_name . ' ' . $user->last_name)
                                ->setFirstName($user->first_name)
                                ->setLastName($user->last_name)
                                ->setEmailAddress($user->email);
                            if ($xeroContact->save()) {
//                                $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->senderUserInfo->email)->execute();
                                $contacts = $this->xero->loadByGUID('Accounting\\Contact', $xeroContact->ContactID);
                                $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                                $invoice->setType("ACCPAY")->setContact($contacts)->setReference($id);
                                if ($sentInvoiceDetail->invoiceDescription->count() != 0) {
                                    foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($detail->description);
                                        $xeroLineItem->setUnitAmount($detail->amount);
                                        $xeroLineItem->setTaxType($createTaxRate->TaxType);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                                if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                                    foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                        $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                        $invoiceAmount = 0;
                                        foreach ($invoiceAmountQuery as $amount) {
                                            $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                            $xeroLineItem->setQuantity(1);
                                            $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                            $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                            $xeroLineItem->setTaxType($createTaxRate->TaxType);
                                            $invoice->addLineItem($xeroLineItem);
                                            $invoice->save();
                                        }
                                    }
                                }
                                SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                            }
                        }else{
                            $contact = $this->xero->load(Contact::class)->where('EmailAddress', $sentInvoiceDetail->senderUserInfo->email)->execute();
                            $contacts = $this->xero->loadByGUID('Accounting\\Contact', $contact[0]['ContactID']);
                            $invoice = new \XeroPHP\Models\Accounting\Invoice($this->xero);
                            $invoice->setType("ACCPAY")->setContact($contacts)->setReference($id);
                            if ($sentInvoiceDetail->invoiceDescription->count() != 0) {
                                foreach ($sentInvoiceDetail->invoiceDescription as $detail) {
                                    $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                    $xeroLineItem->setQuantity(1);
                                    $xeroLineItem->setDescription($detail->description);
                                    $xeroLineItem->setUnitAmount($detail->amount);
                                    $xeroLineItem->setTaxType($createTaxRate->TaxType);
                                    $invoice->addLineItem($xeroLineItem);
                                    $invoice->save();
                                }
                            }
                            if ($sentInvoiceDetail->attachedDocketsInfo->count() != 0) {
                                foreach ($sentInvoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                    $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                    $invoiceAmount = 0;
                                    foreach ($invoiceAmountQuery as $amount) {
                                        $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($this->xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                        $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                        $xeroLineItem->setTaxType($createTaxRate->TaxType);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                            }
                            SentInvoice::where('id', $id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                        }
                    }
                }

            }
            flash('Invoice Sync Successfully.', 'success');
            return redirect()->back();
        }
    }






    public function getRandNum()
    {
        $randNum = strval(rand(1000,100000));
        return $randNum;
    }


    public function xeroEmailInvoiceView($id){
        try{
            $sentEmailInvoiceDetail = EmailSentInvoice::findorfail($id);
            $this->xero->getOAuthClient()
                ->setToken(Session::get('xero_oauth')->token)
                ->setTokenSecret(Session::get('xero_oauth')->token_secret);
            $contacts = $this->xero->loadByGUID('Accounting\\Invoice', $sentEmailInvoiceDetail->xero_invoice_id);
            if ($contacts->Type == 'ACCREC'){
                $url  = 'https://go.xero.com/AccountsReceivable/Edit.aspx?InvoiceID='.$sentEmailInvoiceDetail->xero_invoice_id;
                return Redirect::to($url);
            }
        }catch (\Exception $e){

            flash('Cant view this Invoice because this invoice already sync from Another Organization. Please connect to xero.', 'danger');
            return redirect()->back();
        }


    }

    public function xeroInvoiceView($id){
        try{
            $sentInvoiceDetail = SentInvoice::findorfail($id);
            $this->xero->getOAuthClient()
                ->setToken(Session::get('xero_oauth')->token)
                ->setTokenSecret(Session::get('xero_oauth')->token_secret);
            $invoice = $this->xero->loadByGUID('Accounting\\Invoice', $sentInvoiceDetail->xero_invoice_id);
            if ($invoice->Type == 'ACCREC'){
                $url  = 'https://go.xero.com/AccountsReceivable/Edit.aspx?InvoiceID='.$sentInvoiceDetail->xero_invoice_id;
                return Redirect::to($url);
            }else{
                $url1  = 'https://go.xero.com/AccountsPayable/Edit.aspx?InvoiceID='.$sentInvoiceDetail->xero_invoice_id;
                return Redirect::to($url1);
            }
        }catch (\Exception $e){

            flash('Cant view this Invoice because this invoice already sync from Another Organization.Please connect to xero.', 'danger');
            return redirect()->back();
        }

    }


    public function xeroTimeSheet(ServerRequestInterface $request, ResponseInterface $response){
        $this->xero->getOAuthClient()
            ->setToken(Session::get('xero_oauth')->token)
            ->setTokenSecret(Session::get('xero_oauth')->token_secret);
        $createTimeSheet = new XeroPHP\Models\PayrollAU\Timesheet($this->xero);
        $createTimesheetLinesItems= new XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine($this->xero);
//        //$createNumberOfUnits= new ($this->xero);
//
        $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', '37541070-2f01-42a8-acc5-41edb4a10d5b');
        //dd($emp);
        $pay_items = $this->xero->load(XeroPHP\Models\PayrollAU\PayItem::class)->execute();
        //dd($pay_items);
        $pay_cal = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\PayrollCalendar', $emp->PayrollCalendarID);
        $createTimeSheet->setEmployeeID($emp->EmployeeID)->setStartDate($pay_cal->StartDate)->setEndDate($pay_cal->PaymentDate);
        //if($createTimeSheet->save()) {
        //$createTimeSheet->setTimesheetID($createTimeSheet->TimesheetID);
        $data = array(7.5, 7.5, 7.5, 7.5, 7.5, 0, 0,7.5, 7.5, 7.5, 7.5, 7.5, 0, 0,7.5, 7.5, 7.5, 7.5, 7.5, 0, 0,7.5, 7.5, 7.5, 7.5, 7.5, 0, 0);
        //dd($data);
        //for
        $createTimesheetLinesItems->setEarningsRateID('2b09c14a-0078-436c-bd62-107408926929');
        foreach ($data as $d)
        {
            $createTimesheetLinesItems->addNumberOfUnit($d);
        }

        $createTimeSheet->addTimesheetLine($createTimesheetLinesItems);
        $createTimeSheet->save();
        //}








        //create PayrollEmployeee

//        $this->xero->getOAuthClient()
//            ->setToken(Session::get('xero_oauth')->token)
//            ->setTokenSecret(Session::get('xero_oauth')->token_secret);
////        $employee = $this->xero->load(XeroPHP\Models\PayrollAU\Employee::class)->execute();
//        $createPayroll = new XeroPHP\Models\PayrollAU\Employee($this->xero);
//        $createomeAddress = new XeroPHP\Models\PayrollAU\Employee\HomeAddress($this->xero);
//        $createPayroll->setEmployeeID($this->getRandNum())->setClean()->setFirstName('lama')->setLastName('dai');
//        $createomeAddress->setAddressLine1('kathmandu')->setAddressLine2('nepal')->setCity('Kathmabd')->setCountry("nepal")->setPostalCode('2618')->setRegion('ACT');
//        $createPayroll->setHomeAddress($createomeAddress);
//        $createPayroll->setDateOfBirth(Carbon::now()->subMonths(120));
//        $createPayroll->setEmail('lamatshering23@gmail.com');
//        $createPayroll->save();
//        dd($createPayroll);

    }



    public function companyXeroManager(){


        if (Session::get('xero_oauth') == null) {
            flash('Please Connect to Xero.', 'warning');
            return redirect('dashboard/company/profile/xeroSetting');
        } else {
//        $docket = Docket::where('company_id',Session::get('company_id'))->where('xero_timesheet',1)->pluck('id');
//        $sentDocket = SentDockets::whereIn('docket_id', $docket);
//        $user = User::whereIn('id',$sentDocket->pluck('user_id'))->get();
            try{
                $employee = Employee::where('company_id', Session::get('company_id'));
                $xerouser = User::whereIn('id', $employee->pluck('user_id'))->pluck('email')->toArray();
                $this->xero->getOAuthClient()
                    ->setToken(Session::get('xero_oauth')->token)
                    ->setTokenSecret(Session::get('xero_oauth')->token_secret);
                $totalXeroTimesheetEmployee = $this->xero->load(XeroPHP\Models\PayrollAU\Employee::class)->execute();
                $userXeroName = array();
                foreach ($totalXeroTimesheetEmployee as $row) {
                    $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $row['EmployeeID']);
                    if (in_array($emp['Email'], $xerouser)) {
                        $userName = User::where('email', $emp['Email'])->first();
                        if ($userName->first_name == $emp['FirstName'] && $userName->last_name == $emp['LastName']) {
                            $userXeroName[] = array(
                                'id' => $userName->id,
                                'first_name' => $userName->first_name,
                                'last_name' => $userName->last_name,
                                'xero_first_name' => $emp['FirstName'],
                                'xero_last_name' => $emp['LastName'],
                                'type' => 'match',
                                'employee_id' => $emp['EmployeeID']
                            );
                        } else {
                            $userXeroName[] = array(
                                'id' => $userName->id,
                                'first_name' => $userName->first_name,
                                'last_name' => $userName->last_name,
                                'xero_first_name' => $emp['FirstName'],
                                'xero_last_name' => $emp['LastName'],
                                'type' => 'notMatch',
                                'employee_id' => $emp['EmployeeID']


                            );
                        }

                    }
                }

                $timeSheetdocketDetail = TimesheetDocketDetail::whereIn('employee_id', $employee->pluck('user_id'))->get();
                return view('dashboard.company.xero.index',compact('user','sentDocket','periodArray','userXeroName','timeSheetdocketDetail'));

            }catch (\Exception $e){
                flash('Employee not found please create new employee in xero.', 'warning');
                return redirect('dashboard/company/profile/xeroSetting');

            }

        }

    }


    public function timesheet($id){

        $user = User::findorFail($id);
        $docket = Docket::where('company_id',Session::get('company_id'))->where('xero_timesheet',1)->pluck('id');
        $sentDocket = SentDockets::whereIn('docket_id', $docket)->where('user_id',$id)->get();
        $sentDockets =  SentDockets::whereIn('docket_id', $docket)->where('user_id',$id)->pluck('created_at')->toArray();

        $start = new Carbon('2019-01-16');
        $end = new Carbon('2019-01-19');
        $end->startOfDay();
        $dates = array();
        while ($start->lte($end)) {
            $dates[] = Carbon::parse($start->copy())->format('Y-m-d');
            $start->addDay();
        }


        $allSentDocket = array();
        foreach($sentDocket as $items){
            if($items->sentDocketTimesheet){
                $docketTitle = $items->docketInfo->title;
                foreach($items->sentDocketTimesheet as $row){
                    $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
                    foreach ($a as $b){
                        if($b->docketFieldInfo->docket_field_category_id == 6){
                            $docketTime = $b->value;
                        }
                        if($b->docketFieldInfo->docket_field_category_id == 7){
                            $sn1 = 1; $total = 0;
                            foreach($b->sentDocketUnitRateValue as $items){
                                if($sn1 == 2){
                                    $total = $items->value;

                                }
                                $sn1++;
                            }
                            $totalUnitRate = $total;
                        }

                    }
                }
                $allSentDocket[] =array(
                    'docketTime' => $docketTime,
                    'docketTitle' =>$docketTitle,
                    'totalUnitRate'=>$totalUnitRate
                );
            }
        }



        $this->xero->getOAuthClient()
            ->setToken(Session::get('xero_oauth')->token)
            ->setTokenSecret(Session::get('xero_oauth')->token_secret);
        $totalXeroTimesheetEmployee = $this->xero->load(XeroPHP\Models\PayrollAU\Employee::class)->execute();
        foreach ($totalXeroTimesheetEmployee as $row){
            $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $row['EmployeeID']);
            if($emp['Email'] == $user["email"] ){
                $payrollCalendar = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\PayrollCalendar', $row->PayrollCalendarID);

                $totalTimesheet = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
                $time= array();
                foreach ($totalTimesheet as $ddd){
                    $time[] =array(
                        'startDate' => $ddd['StartDate'],
                        'endDate' =>$ddd['EndDate'],
                    );
                }
                dd($time);


            }else{
                flash('"'.$user["email"].'" not found in xero employee please create  first this employee.', 'warning');
            }

        }



        return view('dashboard.company.xero.timesheet',compact('user','sentDocket','dates','sentDockets','dates','allSentDocket'));

    }



//     public function checkedPayPeriod(Request $request){

//         try{
//             $user = User::findorFail($request->userId);
//             $this->xero->getOAuthClient()
//                 ->setToken(Session::get('xero_oauth')->token)
//                 ->setTokenSecret(Session::get('xero_oauth')->token_secret);
//             $totalXeroTimesheetEmployee = $this->xero->load(XeroPHP\Models\PayrollAU\Employee::class)->execute();
//             foreach ($totalXeroTimesheetEmployee as $row){
//                 $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $row['EmployeeID']);
//                 if($emp['Email'] == $user["email"] ){

//                     $pay_cal = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\PayrollCalendar', $row->PayrollCalendarID);
//                     $start_date = date_format($pay_cal->StartDate,"Y-m-d");
//                     $start_date = new Carbon($start_date);
//                     $end_start_date = date_format($pay_cal->StartDate,"Y-m-d");
//                     $end_start_date = new Carbon($end_start_date);
//                     $currentDate = Carbon::now();

//                     $period = CarbonPeriod::create($start_date,$currentDate)->toArray();

//                     $periodCount = CarbonPeriod::create($start_date,$currentDate)->count();
//                     $periodDiff =intval( $periodCount / 7);

//                     $timeArray = array();
//                     for ($i =  1; $i <= $periodDiff; $i++ ){

//                         if ($i == 1) {
//                             $timeArray[] = array(
//                                 'startDate'=>Carbon::parse($start_date),
//                                 'endDate'=> Carbon::parse($end_start_date)->addDays(6)
//                             );
//                         }
//                         else{
//                             $timeArray[] = array(
//                                 'startDate'=> Carbon::parse($start_date)->addDays(7*($i-1)),
//                                 'endDate'=>  Carbon::parse($end_start_date)->addDays(7*$i-1)
//                             );
//                         }

//                     }

//                     $xeroEmployee= $emp['EmployeeID'];
//  $errorHandel =0;
//                     return view('dashboard.company.xero.payPeriod',compact('timeArray','xeroEmployee','errorHandel'));

//                 }

//             }

//         }catch (\Exception $e){
//             $errorHandel =1;
//             return view('dashboard.company.xero.payPeriod', compact('timeArray', 'xeroEmployee','errorHandel'));

//         }





//     }


    public function checkedPayPeriod(Request $request){

        try {
            $user = User::findorFail($request->userId);
            $this->xero->getOAuthClient()
                ->setToken(Session::get('xero_oauth')->token)
                ->setTokenSecret(Session::get('xero_oauth')->token_secret);
            $totalXeroTimesheetEmployee = $this->xero->load(XeroPHP\Models\PayrollAU\Employee::class)->execute();
            foreach ($totalXeroTimesheetEmployee as $row) {
                $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $row['EmployeeID']);

                if ($emp['Email'] == $user["email"]) {
                    $pay_cal = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\PayrollCalendar', $row->PayrollCalendarID);
                    $start_date = date_format($pay_cal->StartDate, "Y-m-d");
                    $start_date = new Carbon($start_date);
                    $end_start_date = date_format($pay_cal->StartDate, "Y-m-d");
                    $end_start_date = new Carbon($end_start_date);
                    $currentDate = Carbon::now();
                    $period = CarbonPeriod::create($start_date, $currentDate)->toArray();
                    $periodCount = CarbonPeriod::create($start_date, $currentDate)->count();

                    if ($pay_cal['CalendarType'] == "WEEKLY") {
                        $periodDiff = intval($periodCount / 7);
                        $timeArray = array();
                        for ($i = 1; $i <= $periodDiff; $i++) {
                            if ($i == 1) {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date),
                                    'endDate' => Carbon::parse($end_start_date)->addDays(6)
                                );
                            } else {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date)->addDays(7 * ($i - 1)),
                                    'endDate' => Carbon::parse($end_start_date)->addDays(7 * $i - 1)
                                );
                            }


                        }
                    } elseif ($pay_cal['CalendarType'] == "FORTNIGHTLY") {
                        $periodDiff = intval($periodCount / 14);
                        $timeArray = array();
                        for ($i = 1; $i <= $periodDiff; $i++) {
                            if ($i == 1) {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date),
                                    'endDate' => Carbon::parse($end_start_date)->addDays(13)
                                );
                            } else {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date)->addDays(14 * ($i - 1)),
                                    'endDate' => Carbon::parse($end_start_date)->addDays(14 * $i - 1)
                                );
                            }

                        }
                    } elseif ($pay_cal['CalendarType'] == "FOURWEEKLY") {
                        $periodDiff = intval($periodCount / 28);
                        $timeArray = array();
                        for ($i = 1; $i <= $periodDiff; $i++) {
                            if ($i == 1) {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date),
                                    'endDate' => Carbon::parse($end_start_date)->addDays(27)
                                );
                            } else {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date)->addDays(28 * ($i - 1)),
                                    'endDate' => Carbon::parse($end_start_date)->addDays(28 * $i - 1)
                                );
                            }

                        }

                    } elseif ($pay_cal['CalendarType'] == "MONTHLY") {
                        $periodDiff = Carbon::parse($start_date)->diffInMonths();
                        $timeArray = array();
                        for ($i = 1; $i <= $periodDiff; $i++) {
                            if ($i == 1) {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date),
                                    'endDate' => Carbon::parse($end_start_date)->subDay(1)->endOfDay()
                                );
                            } else {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date)->addMonth($i + 1)->startOfDay(),
                                    'endDate' => Carbon::parse($end_start_date)->addMonth($i + 1)->subDay(1)->endOfDay()
                                );
                            }

                        }
                    } elseif ($pay_cal['CalendarType'] == "TWICEMONTHLY") {

                        $errorHandel = 3;
                        return view('dashboard.company.xero.payPeriod', compact('timeArray', 'xeroEmployee', 'errorHandel'));

//                        $differWeek = Carbon::parse($start_date)->diffInWeeks();
//                        $periodDiff = intval($differWeek / 2);
//                        $timeArray = array();

//                        if (strtotime(Carbon::parse($start_date)) == strtotime(Carbon::parse($start_date)->startOfMonth()) ){
//                            for ($i = 1; $i <= $periodDiff; $i++) {
//                                if ($i == 1) {
//                                    $timeArray[] = array(
//                                        'startDate' => Carbon::parse($start_date),
//                                        'endDate' => Carbon::parse($end_start_date)->addDay(14)->endOfDay()
//                                    );
//                                } else {
//
//
//                                    $timeArray[] = array(
//                                        'startDate' => Carbon::parse($end_start_date)->addDay(14)->addDay(1)->endOfDay(),
//                                        'endDate' => Carbon::parse($end_start_date)->addDays($i * 15)->endOfDay()
//                                    );
//
//                                }

//                                if ($i%2 != 0){
//                                    $timeArray[] = array(
//                                        'startDate' => Carbon::parse($start_date)->addMonths($i-1)->startOfDay(),
//                                        'endDate' => Carbon::parse($end_start_date)->addMonths($i-1)->addDay(14)->endOfDay()
//                                    );
//                                }else{
//                                    $timeArray[] = array(
//                                        'startDate' => Carbon::parse($end_start_date)->addMonths($i-1)->addDay(14),
//                                        'endDate' => Carbon::parse($end_start_date)->addMonths($i-1)->endOfMonth()
//                                    );
//                    }

//                            }

//
//                        }else{
//
//                        }

//         dd($timeArray);


//                        for ($i = 1; $i <= $periodDiff; $i++) {
//                            if ($i == 1) {
//                                $timeArray[] = array(
//                                    'startDate' => Carbon::parse($start_date),
//                                    'endDate' => Carbon::parse($end_start_date)->addWeeks(2)->endOfDay()
//                                );
//                            } else {
//                                $timeArray[] = array(
//                                    'startDate' =>Carbon::parse($end_start_date)->addWeeks($i)->endOfDay()->addDay(1) ,
//                                    'endDate' => Carbon::parse($end_start_date)->addWeeks(2)->endOfDay()->addMonths($i-1)->endOfDay()
//
//                                );
//                            }
//
//                        }
//                }





                    }elseif ($pay_cal['CalendarType']=="QUARTERLY"){
                        $quart = Carbon::parse($start_date)->diffInMonths();
                        $periodDiff = intval($quart / 3);
                        $timeArray = array();
                        for ($i = 1; $i <= $periodDiff; $i++) {
                            if ($i == 1) {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date),
                                    'endDate' => Carbon::parse($end_start_date)->addMonths(3)->subDay(1)->endOfDay()
                                );
                            } else {
                                $timeArray[] = array(
                                    'startDate' => Carbon::parse($start_date)->addMonth($i*3)->startOfDay(),
                                    'endDate' => Carbon::parse($end_start_date)->addMonth($i*3)->subDay(1)->endOfDay()
                                );
                            }

                        }


                    }



                    $xeroEmployee= $emp['EmployeeID'];
                    $errorHandel =0;
                    return view('dashboard.company.xero.payPeriod', compact('timeArray', 'xeroEmployee','errorHandel'));

                }

            }
        }catch (\Exception $e){
            $errorHandel =1;
            return view('dashboard.company.xero.payPeriod', compact('timeArray', 'xeroEmployee','errorHandel'));


        }







    }

    public function timesheetDetail(Request $request){
        if ($request->date != null){
            $xeroEmployee=$request->xeroEmployee;
            $xeroEmployeId= $request->XeroEmployeId;
            $date= $request->date;
            $xero_start_date1 = Carbon::parse(explode('|',$date)[1]);
            $xero_end_date1 = Carbon::parse(explode('|',$date)[0]);
            $user = User::findorFail($request->xeroEmployee);
            $docket = Docket::where('company_id',Session::get('company_id'))->where('xero_timesheet',1)->pluck('id');
            $sentDocket = SentDockets::whereIn('docket_id', $docket)->where('user_id',$request->xeroEmployee)->get();
            $period = CarbonPeriod::create(explode('|',$request->date)[1], explode('|',$request->date)[0]);
            $periodDates = $period->toArray();
            $allSentDocket = array();
            foreach($sentDocket as $items){
                if(count($items->sentDocketTimesheet)!= 0){
                    $docketTitle = $items->docketInfo->title;
                    foreach($items->sentDocketTimesheet as $row){
                        $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
                        foreach ($a as $b){
                            if($b->docketFieldInfo->docket_field_category_id == 6){
                                $docketTime = $b->value;
                            }
                            if($b->docketFieldInfo->docket_field_category_id == 7){
                                $sn1 = 1; $total = 0;
                                foreach($b->sentDocketUnitRateValue as $items){
                                    if($sn1 == 2){
                                        $total = $items->value;

                                    }
                                    $sn1++;
                                }
                                $totalUnitRate = $total;
                            }
                            $docketId = $b->sent_docket_id;


                        }
                    }
                    $allSentDocket[] =array(
                        'docketTime' => $docketTime,
                        'docketTitle' =>$docketTitle,
                        'totalUnitRate'=>$totalUnitRate,
                        'docketId'=>$docketId

                    );
                }
            }
            $xero_start_date = Carbon::parse(explode('|',$date)[1]);
            $xero_end_date = Carbon::parse(explode('|',$date)[0]);
            $periodTime= Carbon::parse($xero_start_date)->format('Y-m-d').'|'.Carbon::parse($xero_end_date)->format('Y-m-d');
            $timesheet_docket_detail = TimesheetDocketDetail::where('xero_employee_id',$xeroEmployeId)->where('period',$periodTime)->pluck('id');
            $timesheet_docket_detail_att =TimesheetDocketAttachment::whereIn('timesheet_docket_detail_id',$timesheet_docket_detail)->pluck('sent_docket_id')->toArray();
            if (TimesheetDocketDetail::where('xero_employee_id',$xeroEmployeId)->where('period',Carbon::parse($xero_start_date1)->format('Y-m-d').'|'.Carbon::parse($xero_end_date1)->format('Y-m-d'))->count()==0){
                $countTimesheetDocketDetail = 0;
            }else{
                $countTimesheetDocketDetail =  count(TimesheetDocketDetail::where('xero_employee_id',$xeroEmployeId)->where('period',Carbon::parse($xero_start_date1)->format('Y-m-d').'|'.Carbon::parse($xero_end_date1)->format('Y-m-d'))->first()->TimesheetDocketAttachment);

            }
            $countAllSentDocket = count($allSentDocket);
            $checkForSyncButton = $countAllSentDocket==$countTimesheetDocketDetail;
            //flash('no docket to sync','danger');

            return view('dashboard.company.xero.timesheet',compact('date','xeroEmployee','xeroEmployeId','user','allSentDocket','periodDates','timesheet_docket_detail_att','checkForSyncButton'));
        }else{
            flash('Pay Period is required.', 'warning');
            return redirect()->back();
        }

    }




    // public function syncTimeSheet(Request $request)
    // {
    //     if (Session::get('xero_oauth') == null) {
    //         flash('Please Connect to Xero.', 'warning');
    //         return redirect('dashboard/company/profile/xeroSetting');

    //     } else {
    //         $employee = $request->user_id;
    //         $xeroEmployeId = $request->xero_employe_id;
    //         $date1 = $request->date;
    //         $xero_start_date = Carbon::parse(explode('|', $date1)[1]);
    //         $xero_end_date = Carbon::parse(explode('|', $date1)[0]);
    //         $this->xero->getOAuthClient()
    //             ->setToken(Session::get('xero_oauth')->token)
    //             ->setTokenSecret(Session::get('xero_oauth')->token_secret);
    //         if (TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d'))->count() == 0) {
    //             $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $xeroEmployeId);
    //             $createTimeSheet = new XeroPHP\Models\PayrollAU\Timesheet($this->xero);
    //             $createTimesheetLinesItems = new XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine($this->xero);
    //             $pay_cal = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\PayrollCalendar', $emp->PayrollCalendarID);
    //             $createTimeSheet->setEmployeeID($emp->EmployeeID)->setStartDate(new Carbon(explode('|', $request->date)[1]))->setEndDate(new Carbon(explode('|', $request->date)[0]));
    //             if ($request->DocketId != null) {
    //                 $sentDocket = SentDockets::whereIn('id', $request->DocketId)->get();
    //                 $allSentDocket = array();
    //                 foreach ($sentDocket as $items) {
    //                     if ($items->sentDocketTimesheet) {
    //                         $docketTitle = $items->docketInfo->title;
    //                         foreach ($items->sentDocketTimesheet as $row) {
    //                             $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
    //                             foreach ($a as $b) {
    //                                 if ($b->docketFieldInfo->docket_field_category_id == 6) {
    //                                     $docketTime = $b->value;
    //                                 }
    //                                 if ($b->docketFieldInfo->docket_field_category_id == 7) {
    //                                     $sn1 = 1;
    //                                     $total = 0;
    //                                     foreach ($b->sentDocketUnitRateValue as $items) {
    //                                         if ($sn1 == 2) {
    //                                             $total = $items->value;
    //                                         }
    //                                         $sn1++;
    //                                     }
    //                                     $totalUnitRate = $total;
    //                                 }
    //                             }
    //                         }
    //                         $allSentDocket[] = array(
    //                             'docketTime' => $docketTime,
    //                             'docketTitle' => $docketTitle,
    //                             'totalUnitRate' => $totalUnitRate
    //                         );
    //                     }
    //                 }

    //                 $testData = array();
    //                 foreach ($allSentDocket as $allSentDockets) {
    //                     $testData[] = $allSentDockets['docketTime'];
    //                 }
    //               $period = CarbonPeriod::create(explode('|', $request->date)[1], explode('|', $request->date)[0]);
    //                 $periodDates = $period->toArray();
    //                 $totalDockethours = array();
    //                 foreach ($periodDates as $rowData) {
    //                     if (in_array(Carbon::parse($rowData)->format('d-M-Y'), $testData)) {
    //                         foreach ($allSentDocket as $r) {
    //                             if ($r['docketTime'] == Carbon::parse($rowData)->format('d-M-Y')) {
    //                                 array_push($totalDockethours, $r['totalUnitRate']);
    //                             }
    //                         }
    //                     } else {
    //                         $valueNull = 0;
    //                         array_push($totalDockethours, $valueNull);
    //                     }
    //                 }
    //                 $createTimesheetLinesItems->setEarningsRateID($emp->OrdinaryEarningsRateID);
    //                 foreach ($totalDockethours as $d) {
    //                     $createTimesheetLinesItems->addNumberOfUnit($d);
    //                 }
    //                 $createTimeSheet->addTimesheetLine($createTimesheetLinesItems);
    //                 if ($createTimeSheet->save()) {
    //                     $totalXeroTimesheet = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
    //                     $timeSheetId = array();

    //                     foreach ($totalXeroTimesheet as $totalXeroTimesheets) {
    //                         if ($totalXeroTimesheets['EmployeeID'] == $xeroEmployeId && date_format($totalXeroTimesheets['StartDate'], "Y-m-d") == Carbon::parse($xero_start_date)->format('Y-m-d') && date_format($totalXeroTimesheets['EndDate'], "Y-m-d") == Carbon::parse($xero_end_date)->format('Y-m-d')) {
    //                             $timeSheetId[] = array(
    //                                 'TimesheetID' => $totalXeroTimesheets['TimesheetID'],
    //                                 'TotalHours' => $totalXeroTimesheets['Hours'],
    //                             );
    //                         }
    //                     }
    //                     $timesheetDocketDetail = new TimesheetDocketDetail();
    //                     $timesheetDocketDetail->user_id = Auth::user()->id;
    //                     $timesheetDocketDetail->xero_timesheet_id = $timeSheetId[0]["TimesheetID"];
    //                     $timesheetDocketDetail->employee_id = $employee;
    //                     $timesheetDocketDetail->xero_employee_id = $xeroEmployeId;
    //                     $timesheetDocketDetail->period = Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d');
    //                     $timesheetDocketDetail->total_hours = $timeSheetId[0]["TotalHours"];

    //                     if ($timesheetDocketDetail->save()) {
    //                         foreach ($request->DocketId as $sentDocketId) {
    //                             $timesheetDocketAttachement = new TimesheetDocketAttachment();
    //                             $timesheetDocketAttachement->timesheet_docket_detail_id = $timesheetDocketDetail->id;
    //                             $timesheetDocketAttachement->sent_docket_id = $sentDocketId;
    //                             $timesheetDocketAttachement->save();

    //                         }

    //                     }
    //                 }
    //                 flash('Timesheet synced successfully!!', 'success');
    //                 return redirect()->route('timesheet.index');

    //             } else {

    //                 $xeroEmployee = $request->user_id;
    //                 $xeroEmployeId = $request->xero_employe_id;
    //                 $date = $request->date;
    //                 $xero_start_date1 = Carbon::parse(explode('|', $date)[1]);
    //                 $xero_end_date1 = Carbon::parse(explode('|', $date)[0]);
    //                 $user = User::findorFail($xeroEmployee);
    //                 $docket = Docket::where('company_id', Session::get('company_id'))->where('xero_timesheet', 1)->pluck('id');
    //                 $sentDocket = SentDockets::whereIn('docket_id', $docket)->where('user_id', $xeroEmployee)->get();
    //                 $period = CarbonPeriod::create(explode('|', $request->date)[1], explode('|', $request->date)[0]);
    //                 $periodDates = $period->toArray();
    //                 $allSentDocket = array();
    //                 foreach ($sentDocket as $items) {
    //                     if ($items->sentDocketTimesheet) {
    //                         $docketTitle = $items->docketInfo->title;

    //                         foreach ($items->sentDocketTimesheet as $row) {
    //                             $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
    //                             foreach ($a as $b) {
    //                                 if ($b->docketFieldInfo->docket_field_category_id == 6) {
    //                                     $docketTime = $b->value;
    //                                 }
    //                                 if ($b->docketFieldInfo->docket_field_category_id == 7) {
    //                                     $sn1 = 1;
    //                                     $total = 0;
    //                                     foreach ($b->sentDocketUnitRateValue as $items) {
    //                                         if ($sn1 == 2) {
    //                                             $total = $items->value;

    //                                         }
    //                                         $sn1++;
    //                                     }
    //                                     $totalUnitRate = $total;
    //                                 }
    //                                 $docketId = $b->sent_docket_id;


    //                             }
    //                         }
    //                         $allSentDocket[] = array(
    //                             'docketTime' => $docketTime,
    //                             'docketTitle' => $docketTitle,
    //                             'totalUnitRate' => $totalUnitRate,
    //                             'docketId' => $docketId

    //                         );
    //                     }
    //                 }
    //                 $xero_start_date = Carbon::parse(explode('|', $date)[1]);
    //                 $xero_end_date = Carbon::parse(explode('|', $date)[0]);
    //                 $periodTime = Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d');
    //                 $timesheet_docket_detail = TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', $periodTime)->pluck('id');
    //                 $timesheet_docket_detail_att = TimesheetDocketAttachment::whereIn('timesheet_docket_detail_id', $timesheet_docket_detail)->pluck('sent_docket_id')->toArray();
    //                 if (TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date1)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date1)->format('Y-m-d'))->count() == 0) {
    //                     $countTimesheetDocketDetail = 0;
    //                 } else {
    //                     $countTimesheetDocketDetail = count(TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date1)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date1)->format('Y-m-d'))->first()->TimesheetDocketAttachment);

    //                 }
    //                 $countAllSentDocket = count($allSentDocket);
    //                 $checkForSyncButton = $countAllSentDocket == $countTimesheetDocketDetail;
    //                 flash('no docket to sync', 'danger');
    //                 return view('dashboard.company.xero.timesheet', compact('date', 'xeroEmployee', 'xeroEmployeId', 'user', 'allSentDocket', 'periodDates', 'timesheet_docket_detail_att', 'checkForSyncButton'));


    //             }
    //         } else {
    //             $timesheetDocketDetail = TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d'))->first();
    //             $totalXeroTimesheet = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
    //             foreach ($totalXeroTimesheet as $totalXeroTimesheets) {
    //                 if ($totalXeroTimesheets['TimesheetID'] == $timesheetDocketDetail->xero_timesheet_id) {
    //                     $sentDocket = SentDockets::whereIn('id', $request->DocketId)->get();
    //                     $allSentDocket = array();
    //                     foreach ($sentDocket as $items) {
    //                         if ($items->sentDocketTimesheet) {
    //                             $docketTitle = $items->docketInfo->title;
    //                             foreach ($items->sentDocketTimesheet as $row) {
    //                                 $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
    //                                 foreach ($a as $b) {
    //                                     if ($b->docketFieldInfo->docket_field_category_id == 6) {
    //                                         $docketTime = $b->value;
    //                                     }
    //                                     if ($b->docketFieldInfo->docket_field_category_id == 7) {
    //                                         $sn1 = 1;
    //                                         $total = 0;
    //                                         foreach ($b->sentDocketUnitRateValue as $items) {
    //                                             if ($sn1 == 2) {
    //                                                 $total = $items->value;
    //                                             }
    //                                             $sn1++;
    //                                         }
    //                                         $totalUnitRate = $total;
    //                                     }
    //                                 }
    //                             }
    //                             $allSentDocket[] = array(
    //                                 'docketTime' => $docketTime,
    //                                 'docketTitle' => $docketTitle,
    //                                 'totalUnitRate' => $totalUnitRate
    //                             );
    //                         }
    //                     }
    //                     $testData = array();
    //                     foreach ($allSentDocket as $allSentDockets) {
    //                         $testData[] = $allSentDockets['docketTime'];
    //                     }
    //                     $period = CarbonPeriod::create(explode('|', $request->date)[1], explode('|', $request->date)[0]);
    //                     $periodDates = $period->toArray();
    //                     $totalDockethours = array();
    //                     foreach ($periodDates as $rowData) {
    //                         if (in_array(Carbon::parse($rowData)->format('d-M-Y'), $testData)) {
    //                             foreach ($allSentDocket as $r) {
    //                                 if ($r['docketTime'] == Carbon::parse($rowData)->format('d-M-Y')) {
    //                                     array_push($totalDockethours, $r['totalUnitRate']);
    //                                 }
    //                             }
    //                         } else {
    //                             $valueNull = '0';
    //                             array_push($totalDockethours, $valueNull);
    //                         }
    //                     }
    //                     $arr = array();
    //                     foreach ($totalXeroTimesheet as $totalXeroTimesheetss) {
    //                         if ($totalXeroTimesheetss['TimesheetID'] == $timesheetDocketDetail->xero_timesheet_id) {
    //                             $arr[] = $totalXeroTimesheetss['TimesheetLines'];
    //                         }
    //                     }
    //                     $tes = json_decode(json_encode($arr[0], true));
    //                     $timelinefromxero = json_decode(json_encode($tes), true);
    //                     $createTimeSheet = new XeroPHP\Models\PayrollAU\Timesheet($this->xero);
    //                     $createTimeSheet->setTimesheetID($timesheetDocketDetail->xero_timesheet_id)->setEmployeeID($timesheetDocketDetail->xero_employee_id)->setStartDate(new  Carbon(explode('|', $timesheetDocketDetail->period)[0]))->setEndDate(new  Carbon(explode('|', $timesheetDocketDetail->period)[1]));
    //                     $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $xeroEmployeId);
    //                     $demo = array(
    //                         'EarningsRateID' => $emp->OrdinaryEarningsRateID,
    //                         'NumberOfUnits' => $totalDockethours
    //                     );
    //                     array_push($timelinefromxero, $demo);
    //                     foreach ($timelinefromxero as $timelinefromxeros) {
    //                         $createTimesheetLinesItems = new XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine($this->xero);
    //                         $createTimesheetLinesItems->setEarningsRateID($timelinefromxeros['EarningsRateID']);
    //                         foreach ($timelinefromxeros['NumberOfUnits'] as $d) {
    //                             $createTimesheetLinesItems->addNumberOfUnit($d);
    //                         }
    //                         $createTimeSheet->addTimesheetLine($createTimesheetLinesItems);
    //                     }
    //                     if ($createTimeSheet->save()) ;
    //                     $totalXeroTimesheetss = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
    //                     $timeSheetIds = array();
    //                     foreach ($totalXeroTimesheetss as $totalXeroTimesheetsss) {
    //                         if ($totalXeroTimesheetsss['EmployeeID'] == $xeroEmployeId && date_format($totalXeroTimesheetsss['StartDate'], "Y-m-d") == Carbon::parse($xero_start_date)->format('Y-m-d') && date_format($totalXeroTimesheetsss['EndDate'], "Y-m-d") == Carbon::parse($xero_end_date)->format('Y-m-d')) {
    //                             $timeSheetIds = $totalXeroTimesheetsss['Hours'];

    //                         }
    //                     }
    //                     $timesheetDocketDetailfind = TimesheetDocketDetail::findorfail($timesheetDocketDetail->id);
    //                     $timesheetDocketDetailfind->total_hours = $timeSheetIds;
    //                     if ($timesheetDocketDetailfind->save()) {
    //                         foreach ($request->DocketId as $sentDocketId) {
    //                             $timesheetDocketAttachement = new TimesheetDocketAttachment();
    //                             $timesheetDocketAttachement->timesheet_docket_detail_id = $timesheetDocketDetail->id;
    //                             $timesheetDocketAttachement->sent_docket_id = $sentDocketId;
    //                             $timesheetDocketAttachement->save();

    //                         }
    //                     }
    //                 }
    //             }
    //             flash('Timesheet synced successfully!!', 'success');
    //             return redirect()->route('timesheet.index');
    //         }

    //     }
    // }

    public function syncTimeSheet(Request $request)
    {


        try {
            $employee = $request->user_id;
            $xeroEmployeId = $request->xero_employe_id;
            $date1 = $request->date;
            $xero_start_date = Carbon::parse(explode('|', $date1)[1]);
            $xero_end_date = Carbon::parse(explode('|', $date1)[0]);
            $this->xero->getOAuthClient()
                ->setToken(Session::get('xero_oauth')->token)
                ->setTokenSecret(Session::get('xero_oauth')->token_secret);

            $checkTimesheetId = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
            $xeroTimesheet = array();
            $b = array();
            $c = array();
            $d = array();
            foreach ($checkTimesheetId as $checkTimesheetIds){
                $xeroTimesheet[]= array(
                    'EmployeeID' =>$checkTimesheetIds['EmployeeID'],
                    'StartDate'=> date_format($checkTimesheetIds['StartDate'], "Y-m-d"),
                    'EndDate' => date_format($checkTimesheetIds['EndDate'], "Y-m-d"),
                    'TimesheetID'=>$checkTimesheetIds['TimesheetID']

                );
                array_push($b, $checkTimesheetIds['EmployeeID']);
                array_push($c, date_format($checkTimesheetIds['StartDate'], "Y-m-d"));
                array_push($d, date_format($checkTimesheetIds['EndDate'], "Y-m-d"));


            };
            if (in_array($xeroEmployeId ,$b) && in_array(Carbon::parse($xero_start_date)->format('Y-m-d'), $c) && in_array(Carbon::parse($xero_end_date)->format('Y-m-d'), $d)) {

                if ( TimesheetDocketDetail::where('xero_employee_id', $checkTimesheetIds['EmployeeID'])->where('period',date_format($checkTimesheetIds['StartDate'], "Y-m-d").'|'.date_format($checkTimesheetIds['EndDate'], "Y-m-d"))->count() == 0 ){
                    $totalXeroTimesheet = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
                    foreach ($totalXeroTimesheet as $totalXeroTimesheets) {
                        if ($totalXeroTimesheets['EmployeeID'] == $xeroEmployeId && date_format($totalXeroTimesheets['StartDate'], "Y-m-d") == Carbon::parse($xero_start_date)->format('Y-m-d') && date_format($totalXeroTimesheets['EndDate'], "Y-m-d") == Carbon::parse($xero_end_date)->format('Y-m-d')) {
                            $sentDocket = SentDockets::whereIn('id', $request->DocketId)->get();
                            $allSentDocket = array();
                            foreach ($sentDocket as $items) {
                                if ($items->sentDocketTimesheet) {
                                    $docketTitle = $items->docketInfo->title;
                                    foreach ($items->sentDocketTimesheet as $row) {
                                        $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
                                        foreach ($a as $b) {
                                            if ($b->docketFieldInfo->docket_field_category_id == 6) {
                                                $docketTime = $b->value;
                                            }
                                            if ($b->docketFieldInfo->docket_field_category_id == 7) {
                                                $sn1 = 1;
                                                $total = 0;
                                                foreach ($b->sentDocketUnitRateValue as $items) {
                                                    if ($sn1 == 2) {
                                                        $total = $items->value;
                                                    }
                                                    $sn1++;
                                                }
                                                $totalUnitRate = $total;
                                            }
                                        }
                                    }
                                    $allSentDocket[] = array(
                                        'docketTime' => $docketTime,
                                        'docketTitle' => $docketTitle,
                                        'totalUnitRate' => $totalUnitRate
                                    );
                                }
                            }
                            $testData = array();
                            foreach ($allSentDocket as $allSentDockets) {
                                $testData[] = $allSentDockets['docketTime'];
                            }
                            $period = CarbonPeriod::create(explode('|', $request->date)[1], explode('|', $request->date)[0]);
                            $periodDates = $period->toArray();
                            $totalDockethours = array();
                            foreach ($periodDates as $rowData) {
                                if (in_array(Carbon::parse($rowData)->format('d-M-Y'), $testData)) {
                                    foreach ($allSentDocket as $r) {
                                        if ($r['docketTime'] == Carbon::parse($rowData)->format('d-M-Y')) {
                                            array_push($totalDockethours, $r['totalUnitRate']);
                                        }
                                    }
                                } else {
                                    $valueNull = '0';
                                    array_push($totalDockethours, $valueNull);
                                }
                            }


                            $tes = array();
                            $timelinefromxero = json_decode(json_encode(json_decode(json_encode($totalXeroTimesheets['TimesheetLines'], true))), true);
                            $createTimeSheet = new XeroPHP\Models\PayrollAU\Timesheet($this->xero);
                            $createTimeSheet->setTimesheetID($totalXeroTimesheets['TimesheetID'])->setEmployeeID($totalXeroTimesheets['EmployeeID'])->setStartDate($xero_start_date)->setEndDate($xero_end_date);
                            $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $xeroEmployeId);
                            $demo = array(
                                'EarningsRateID' => $emp->OrdinaryEarningsRateID,
                                'NumberOfUnits' => $totalDockethours
                            );
                            array_push($timelinefromxero, $demo);
                            foreach ($timelinefromxero as $timelinefromxeros) {
                                $createTimesheetLinesItems = new XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine($this->xero);
                                $createTimesheetLinesItems->setEarningsRateID($timelinefromxeros['EarningsRateID']);
                                foreach ($timelinefromxeros['NumberOfUnits'] as $d) {
                                    $createTimesheetLinesItems->addNumberOfUnit($d);
                                }
                                $createTimeSheet->addTimesheetLine($createTimesheetLinesItems);
                            }
                            if ($createTimeSheet->save()) ;
                            $timesheetDocketDetail = new TimesheetDocketDetail();
                            $timesheetDocketDetail->user_id = Auth::user()->id;
                            $timesheetDocketDetail->xero_timesheet_id = $createTimeSheet['TimesheetID'];
                            $timesheetDocketDetail->employee_id = $employee;
                            $timesheetDocketDetail->xero_employee_id = $xeroEmployeId;
                            $timesheetDocketDetail->period = Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d');
                            $timesheetDocketDetail->total_hours = $createTimeSheet["Hours"];

                            if ($timesheetDocketDetail->save()) {
                                foreach ($request->DocketId as $sentDocketId) {
                                    $timesheetDocketAttachement = new TimesheetDocketAttachment();
                                    $timesheetDocketAttachement->timesheet_docket_detail_id = $timesheetDocketDetail->id;
                                    $timesheetDocketAttachement->sent_docket_id = $sentDocketId;
                                    $timesheetDocketAttachement->save();

                                }

                            }
                        }
                    }
                    flash('Timesheet synced successfully!!', 'success');
                    return redirect()->route('timesheet.index');
                }else{

                    $timesheetDocketDetail = TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d'))->first();
                    $totalXeroTimesheet = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
                    foreach ($totalXeroTimesheet as $totalXeroTimesheets) {
                        if ($totalXeroTimesheets['TimesheetID'] == $timesheetDocketDetail->xero_timesheet_id) {
                            $sentDocket = SentDockets::whereIn('id', $request->DocketId)->get();
                            $allSentDocket = array();
                            foreach ($sentDocket as $items) {
                                if ($items->sentDocketTimesheet) {
                                    $docketTitle = $items->docketInfo->title;
                                    foreach ($items->sentDocketTimesheet as $row) {
                                        $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
                                        foreach ($a as $b) {
                                            if ($b->docketFieldInfo->docket_field_category_id == 6) {
                                                $docketTime = $b->value;
                                            }
                                            if ($b->docketFieldInfo->docket_field_category_id == 7) {
                                                $sn1 = 1;
                                                $total = 0;
                                                foreach ($b->sentDocketUnitRateValue as $items) {
                                                    if ($sn1 == 2) {
                                                        $total = $items->value;
                                                    }
                                                    $sn1++;
                                                }
                                                $totalUnitRate = $total;
                                            }
                                        }
                                    }
                                    $allSentDocket[] = array(
                                        'docketTime' => $docketTime,
                                        'docketTitle' => $docketTitle,
                                        'totalUnitRate' => $totalUnitRate
                                    );
                                }
                            }
                            $testData = array();
                            foreach ($allSentDocket as $allSentDockets) {
                                $testData[] = $allSentDockets['docketTime'];
                            }
                            $period = CarbonPeriod::create(explode('|', $request->date)[1], explode('|', $request->date)[0]);
                            $periodDates = $period->toArray();
                            $totalDockethours = array();
                            foreach ($periodDates as $rowData) {
                                if (in_array(Carbon::parse($rowData)->format('d-M-Y'), $testData)) {
                                    foreach ($allSentDocket as $r) {
                                        if ($r['docketTime'] == Carbon::parse($rowData)->format('d-M-Y')) {
                                            array_push($totalDockethours, $r['totalUnitRate']);
                                        }
                                    }
                                } else {
                                    $valueNull = '0';
                                    array_push($totalDockethours, $valueNull);
                                }
                            }
                            $arr = array();
                            foreach ($totalXeroTimesheet as $totalXeroTimesheetss) {
                                if ($totalXeroTimesheetss['TimesheetID'] == $timesheetDocketDetail->xero_timesheet_id) {
                                    $arr[] = $totalXeroTimesheetss['TimesheetLines'];
                                }
                            }
                            $tes = json_decode(json_encode($arr[0], true));
                            $timelinefromxero = json_decode(json_encode($tes), true);
                            $createTimeSheet = new XeroPHP\Models\PayrollAU\Timesheet($this->xero);
                            $createTimeSheet->setTimesheetID($timesheetDocketDetail->xero_timesheet_id)->setEmployeeID($timesheetDocketDetail->xero_employee_id)->setStartDate(new  Carbon(explode('|', $timesheetDocketDetail->period)[0]))->setEndDate(new  Carbon(explode('|', $timesheetDocketDetail->period)[1]));
                            $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $xeroEmployeId);
                            $demo = array(
                                'EarningsRateID' => $emp->OrdinaryEarningsRateID,
                                'NumberOfUnits' => $totalDockethours
                            );
                            array_push($timelinefromxero, $demo);
                            foreach ($timelinefromxero as $timelinefromxeros) {
                                $createTimesheetLinesItems = new XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine($this->xero);
                                $createTimesheetLinesItems->setEarningsRateID($timelinefromxeros['EarningsRateID']);
                                foreach ($timelinefromxeros['NumberOfUnits'] as $d) {
                                    $createTimesheetLinesItems->addNumberOfUnit($d);
                                }
                                $createTimeSheet->addTimesheetLine($createTimesheetLinesItems);
                            }
                            if ($createTimeSheet->save()) ;
                            $totalXeroTimesheetss = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
                            $timeSheetIds = array();
                            foreach ($totalXeroTimesheetss as $totalXeroTimesheetsss) {
                                if ($totalXeroTimesheetsss['EmployeeID'] == $xeroEmployeId && date_format($totalXeroTimesheetsss['StartDate'], "Y-m-d") == Carbon::parse($xero_start_date)->format('Y-m-d') && date_format($totalXeroTimesheetsss['EndDate'], "Y-m-d") == Carbon::parse($xero_end_date)->format('Y-m-d')) {
                                    $timeSheetIds = $totalXeroTimesheetsss['Hours'];

                                }
                            }
                            $timesheetDocketDetailfind = TimesheetDocketDetail::findorfail($timesheetDocketDetail->id);
                            $timesheetDocketDetailfind->total_hours = $timeSheetIds;
                            if ($timesheetDocketDetailfind->save()) {
                                foreach ($request->DocketId as $sentDocketId) {
                                    $timesheetDocketAttachement = new TimesheetDocketAttachment();
                                    $timesheetDocketAttachement->timesheet_docket_detail_id = $timesheetDocketDetail->id;
                                    $timesheetDocketAttachement->sent_docket_id = $sentDocketId;
                                    $timesheetDocketAttachement->save();

                                }
                            }
                        }
                    }
                    flash('Timesheet synced successfully!!', 'success');
                    return redirect()->route('timesheet.index');

                }

//                    $timesheetDocketDetail = TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d'))->first();



            }
            else{

//                foreach ($checkTimesheetId as $checkTimesheetIds){
                if ( TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period',Carbon::parse($xero_start_date)->format('Y-m-d').'|'.Carbon::parse($xero_end_date)->format('Y-m-d'))->count() == 0 ){
//                    if (TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d'))->count() == 0) {
                    $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $xeroEmployeId);
                    if ($emp->OrdinaryEarningsRateID != null){
                        $createTimeSheet = new XeroPHP\Models\PayrollAU\Timesheet($this->xero);
                        $createTimesheetLinesItems = new XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine($this->xero);
                        $pay_cal = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\PayrollCalendar', $emp->PayrollCalendarID);
                        $createTimeSheet->setEmployeeID($emp->EmployeeID)->setStartDate(new Carbon(explode('|', $request->date)[1]))->setEndDate(new Carbon(explode('|', $request->date)[0]));
                        if ($request->DocketId != null) {
                            $sentDocket = SentDockets::whereIn('id', $request->DocketId)->get();
                            $allSentDocket = array();
                            foreach ($sentDocket as $items) {
                                if ($items->sentDocketTimesheet) {
                                    $docketTitle = $items->docketInfo->title;
                                    foreach ($items->sentDocketTimesheet as $row) {
                                        $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
                                        foreach ($a as $b) {
                                            if ($b->docketFieldInfo->docket_field_category_id == 6) {
                                                $docketTime = $b->value;
                                            }
                                            if ($b->docketFieldInfo->docket_field_category_id == 7) {
                                                $sn1 = 1;
                                                $total = 0;
                                                foreach ($b->sentDocketUnitRateValue as $items) {
                                                    if ($sn1 == 2) {
                                                        $total = $items->value;
                                                    }
                                                    $sn1++;
                                                }
                                                $totalUnitRate = $total;
                                            }
                                        }
                                    }
                                    $allSentDocket[] = array(
                                        'docketTime' => $docketTime,
                                        'docketTitle' => $docketTitle,
                                        'totalUnitRate' => $totalUnitRate
                                    );
                                }
                            }
                            $testData = array();
                            foreach ($allSentDocket as $allSentDockets) {
                                $testData[] = $allSentDockets['docketTime'];
                            }
                            $period = CarbonPeriod::create(explode('|', $request->date)[1], explode('|', $request->date)[0]);
                            $periodDates = $period->toArray();
                            $totalDockethours = array();
                            foreach ($periodDates as $rowData) {
                                if (in_array(Carbon::parse($rowData)->format('d-M-Y'), $testData)) {
                                    foreach ($allSentDocket as $r) {
                                        if ($r['docketTime'] == Carbon::parse($rowData)->format('d-M-Y')) {
                                            array_push($totalDockethours, $r['totalUnitRate']);
                                        }
                                    }
                                } else {
                                    $valueNull = 0;
                                    array_push($totalDockethours, $valueNull);
                                }
                            }
                            $createTimesheetLinesItems->setEarningsRateID($emp->OrdinaryEarningsRateID);
                            foreach ($totalDockethours as $d) {
                                $createTimesheetLinesItems->addNumberOfUnit($d);
                            }
                            $createTimeSheet->addTimesheetLine($createTimesheetLinesItems);
                            if ($createTimeSheet->save()) {
                                $totalXeroTimesheet = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
                                $timeSheetId = array();

                                foreach ($totalXeroTimesheet as $totalXeroTimesheets) {
                                    if ($totalXeroTimesheets['EmployeeID'] == $xeroEmployeId && date_format($totalXeroTimesheets['StartDate'], "Y-m-d") == Carbon::parse($xero_start_date)->format('Y-m-d') && date_format($totalXeroTimesheets['EndDate'], "Y-m-d") == Carbon::parse($xero_end_date)->format('Y-m-d')) {
                                        $timeSheetId[] = array(
                                            'TimesheetID' => $totalXeroTimesheets['TimesheetID'],
                                            'TotalHours' => $totalXeroTimesheets['Hours'],
                                        );
                                    }
                                }
                                $timesheetDocketDetail = new TimesheetDocketDetail();
                                $timesheetDocketDetail->user_id = Auth::user()->id;
                                $timesheetDocketDetail->xero_timesheet_id = $timeSheetId[0]["TimesheetID"];
                                $timesheetDocketDetail->employee_id = $employee;
                                $timesheetDocketDetail->xero_employee_id = $xeroEmployeId;
                                $timesheetDocketDetail->period = Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d');
                                $timesheetDocketDetail->total_hours = $timeSheetId[0]["TotalHours"];

                                if ($timesheetDocketDetail->save()) {
                                    foreach ($request->DocketId as $sentDocketId) {
                                        $timesheetDocketAttachement = new TimesheetDocketAttachment();
                                        $timesheetDocketAttachement->timesheet_docket_detail_id = $timesheetDocketDetail->id;
                                        $timesheetDocketAttachement->sent_docket_id = $sentDocketId;
                                        $timesheetDocketAttachement->save();

                                    }

                                }
                            }
                            flash('Timesheet synced successfully!!', 'success');
                            return redirect()->route('timesheet.index');

                        } else {

                            $xeroEmployee = $request->user_id;
                            $xeroEmployeId = $request->xero_employe_id;
                            $date = $request->date;
                            $xero_start_date1 = Carbon::parse(explode('|', $date)[1]);
                            $xero_end_date1 = Carbon::parse(explode('|', $date)[0]);
                            $user = User::findorFail($xeroEmployee);
                            $docket = Docket::where('company_id', Session::get('company_id'))->where('xero_timesheet', 1)->pluck('id');
                            $sentDocket = SentDockets::whereIn('docket_id', $docket)->where('user_id', $xeroEmployee)->get();
                            $period = CarbonPeriod::create(explode('|', $request->date)[1], explode('|', $request->date)[0]);
                            $periodDates = $period->toArray();
                            $allSentDocket = array();
                            foreach ($sentDocket as $items) {
                                if ($items->sentDocketTimesheet) {
                                    $docketTitle = $items->docketInfo->title;

                                    foreach ($items->sentDocketTimesheet as $row) {
                                        $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
                                        foreach ($a as $b) {
                                            if ($b->docketFieldInfo->docket_field_category_id == 6) {
                                                $docketTime = $b->value;
                                            }
                                            if ($b->docketFieldInfo->docket_field_category_id == 7) {
                                                $sn1 = 1;
                                                $total = 0;
                                                foreach ($b->sentDocketUnitRateValue as $items) {
                                                    if ($sn1 == 2) {
                                                        $total = $items->value;

                                                    }
                                                    $sn1++;
                                                }
                                                $totalUnitRate = $total;
                                            }
                                            $docketId = $b->sent_docket_id;


                                        }
                                    }
                                    $allSentDocket[] = array(
                                        'docketTime' => $docketTime,
                                        'docketTitle' => $docketTitle,
                                        'totalUnitRate' => $totalUnitRate,
                                        'docketId' => $docketId

                                    );
                                }
                            }
                            $xero_start_date = Carbon::parse(explode('|', $date)[1]);
                            $xero_end_date = Carbon::parse(explode('|', $date)[0]);
                            $periodTime = Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d');
                            $timesheet_docket_detail = TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', $periodTime)->pluck('id');
                            $timesheet_docket_detail_att = TimesheetDocketAttachment::whereIn('timesheet_docket_detail_id', $timesheet_docket_detail)->pluck('sent_docket_id')->toArray();
                            if (TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date1)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date1)->format('Y-m-d'))->count() == 0) {
                                $countTimesheetDocketDetail = 0;
                            } else {
                                $countTimesheetDocketDetail = count(TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date1)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date1)->format('Y-m-d'))->first()->TimesheetDocketAttachment);

                            }
                            $countAllSentDocket = count($allSentDocket);
                            $checkForSyncButton = $countAllSentDocket == $countTimesheetDocketDetail;
                            flash('no docket to sync', 'danger');
                            return view('dashboard.company.xero.timesheet', compact('date', 'xeroEmployee', 'xeroEmployeId', 'user', 'allSentDocket', 'periodDates', 'timesheet_docket_detail_att', 'checkForSyncButton'));


                        }

                    }else{
                        flash('Employee '. $emp->FirstName.' '.$emp->LastName .' does not have an ordinary earnings rate.', 'danger');
                        return redirect()->route('timesheet.index');


                    }


                }else {
                    $timesheetDocketDetail = TimesheetDocketDetail::where('xero_employee_id', $xeroEmployeId)->where('period', Carbon::parse($xero_start_date)->format('Y-m-d') . '|' . Carbon::parse($xero_end_date)->format('Y-m-d'))->first();
                    $totalXeroTimesheet = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
                    foreach ($totalXeroTimesheet as $totalXeroTimesheets) {
                        if ($totalXeroTimesheets['TimesheetID'] == $timesheetDocketDetail->xero_timesheet_id) {
                            $sentDocket = SentDockets::whereIn('id', $request->DocketId)->get();
                            $allSentDocket = array();
                            foreach ($sentDocket as $items) {
                                if ($items->sentDocketTimesheet) {
                                    $docketTitle = $items->docketInfo->title;
                                    foreach ($items->sentDocketTimesheet as $row) {
                                        $a = SentDocketsValue::where('docket_field_id', $row->docket_field_id)->where('sent_docket_id', $row->sent_docket_id)->get();
                                        foreach ($a as $b) {
                                            if ($b->docketFieldInfo->docket_field_category_id == 6) {
                                                $docketTime = $b->value;
                                            }
                                            if ($b->docketFieldInfo->docket_field_category_id == 7) {
                                                $sn1 = 1;
                                                $total = 0;
                                                foreach ($b->sentDocketUnitRateValue as $items) {
                                                    if ($sn1 == 2) {
                                                        $total = $items->value;
                                                    }
                                                    $sn1++;
                                                }
                                                $totalUnitRate = $total;
                                            }
                                        }
                                    }
                                    $allSentDocket[] = array(
                                        'docketTime' => $docketTime,
                                        'docketTitle' => $docketTitle,
                                        'totalUnitRate' => $totalUnitRate
                                    );
                                }
                            }
                            $testData = array();
                            foreach ($allSentDocket as $allSentDockets) {
                                $testData[] = $allSentDockets['docketTime'];
                            }
                            $period = CarbonPeriod::create(explode('|', $request->date)[1], explode('|', $request->date)[0]);
                            $periodDates = $period->toArray();
                            $totalDockethours = array();
                            foreach ($periodDates as $rowData) {
                                if (in_array(Carbon::parse($rowData)->format('d-M-Y'), $testData)) {
                                    foreach ($allSentDocket as $r) {
                                        if ($r['docketTime'] == Carbon::parse($rowData)->format('d-M-Y')) {
                                            array_push($totalDockethours, $r['totalUnitRate']);
                                        }
                                    }
                                } else {
                                    $valueNull = '0';
                                    array_push($totalDockethours, $valueNull);
                                }
                            }
                            $arr = array();
                            foreach ($totalXeroTimesheet as $totalXeroTimesheetss) {
                                if ($totalXeroTimesheetss['TimesheetID'] == $timesheetDocketDetail->xero_timesheet_id) {
                                    $arr[] = $totalXeroTimesheetss['TimesheetLines'];
                                }
                            }
                            $tes = json_decode(json_encode($arr[0], true));
                            $timelinefromxero = json_decode(json_encode($tes), true);
                            $createTimeSheet = new XeroPHP\Models\PayrollAU\Timesheet($this->xero);
                            $createTimeSheet->setTimesheetID($timesheetDocketDetail->xero_timesheet_id)->setEmployeeID($timesheetDocketDetail->xero_employee_id)->setStartDate(new  Carbon(explode('|', $timesheetDocketDetail->period)[0]))->setEndDate(new  Carbon(explode('|', $timesheetDocketDetail->period)[1]));
                            $emp = $this->xero->loadByGUID('XeroPHP\\Models\\PayrollAU\\Employee', $xeroEmployeId);
                            $demo = array(
                                'EarningsRateID' => $emp->OrdinaryEarningsRateID,
                                'NumberOfUnits' => $totalDockethours
                            );
                            array_push($timelinefromxero, $demo);
                            foreach ($timelinefromxero as $timelinefromxeros) {
                                $createTimesheetLinesItems = new XeroPHP\Models\PayrollAU\Timesheet\TimesheetLine($this->xero);
                                $createTimesheetLinesItems->setEarningsRateID($timelinefromxeros['EarningsRateID']);
                                foreach ($timelinefromxeros['NumberOfUnits'] as $d) {
                                    $createTimesheetLinesItems->addNumberOfUnit($d);
                                }
                                $createTimeSheet->addTimesheetLine($createTimesheetLinesItems);
                            }
                            if ($createTimeSheet->save()) ;
                            $totalXeroTimesheetss = $this->xero->load(XeroPHP\Models\PayrollAU\Timesheet::class)->execute();
                            $timeSheetIds = array();
                            foreach ($totalXeroTimesheetss as $totalXeroTimesheetsss) {
                                if ($totalXeroTimesheetsss['EmployeeID'] == $xeroEmployeId && date_format($totalXeroTimesheetsss['StartDate'], "Y-m-d") == Carbon::parse($xero_start_date)->format('Y-m-d') && date_format($totalXeroTimesheetsss['EndDate'], "Y-m-d") == Carbon::parse($xero_end_date)->format('Y-m-d')) {
                                    $timeSheetIds = $totalXeroTimesheetsss['Hours'];

                                }
                            }
                            $timesheetDocketDetailfind = TimesheetDocketDetail::findorfail($timesheetDocketDetail->id);
                            $timesheetDocketDetailfind->total_hours = $timeSheetIds;
                            if ($timesheetDocketDetailfind->save()) {
                                foreach ($request->DocketId as $sentDocketId) {
                                    $timesheetDocketAttachement = new TimesheetDocketAttachment();
                                    $timesheetDocketAttachement->timesheet_docket_detail_id = $timesheetDocketDetail->id;
                                    $timesheetDocketAttachement->sent_docket_id = $sentDocketId;
                                    $timesheetDocketAttachement->save();

                                }
                            }
                        }
                    }
                    flash('Timesheet synced successfully!!', 'success');
                    return redirect()->route('timesheet.index');
                }


//                }
            }

        }catch (\Exception $e){
            flash('Plese connect to xero.','success');
            return redirect('dashboard/company/profile/xeroSetting');

        }





    }



    public function xeroReset($id){
        $resetCompanyXero   =    CompanyXero::where('company_id',$id)->firstOrFail();
        $resetCompanyXero->delete();
        Session::forget('xero_oauth');
        flash('Xero Company reset Successfully.','success');
        return redirect()->back();

    }









}






