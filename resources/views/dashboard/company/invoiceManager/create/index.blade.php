@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Create New Invoice</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('invoices.allInvoices') }}">Invoice Manager</a></li>
            <li class="active">Create New Invoice</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')

    <div class="loading overlaysplinner" style="display: none">Loading&#8230;</div>
    <form action="#" id="myForm" role="form" enctype="multipart/form-data" data-toggle="validator" method="post" accept-charset="utf-8">
        <div id="smartwizard" class="create-invoice-wizard">
            <ul>
                <li><a href="#step-1"><small>1. Template</small></a></li>
                <li><a href="#step-2" dataURL="{{ route('invoices.create.recipient') }}"><small>2. Recipients</small></a></li>
                <li><a href="#step-3" dataURL="{{ route('invoices.create.dockets') }}"><small>3. Attach Dockets</small></a></li>
                <li><a href="#step-4" id="docketOnchangeTemplate" dataURL="{{ route('invoices.create.invoice') }}" dataSendURL="{{ route('invoices.create.send') }}"><small>4. Invoice</small></a></li>
            </ul>
            <div>
                @include('dashboard.company.invoiceManager.create.partials.template.template')
                @include('dashboard.company.invoiceManager.create.partials.recipients.recipients')
                @include('dashboard.company.invoiceManager.create.partials.attachDockets.attachDockets')
                @include('dashboard.company.invoiceManager.create.partials.invoice.invoice')
            </div>
        </div>
    </form>
    <br><br>
    @include('dashboard.company.invoiceManager.create.modal-popup.invoice-success')
    @include('dashboard.company.invoiceManager.create.modal-popup.signature-pad')
@endsection
@section('customScript')
    <link href=" {{asset('assets/dashboard/company/invoiceManager/create/index.css')}}" rel="stylesheet" type="text/css" />

    <script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-with-addons.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-dom.js"></script>
    <link href=" {{asset('assets/dashboard/smartWizard/css/smart_wizard.css')}}" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

    <!-- Optional SmartWizard theme -->
    <link href="{{asset('assets/dashboard/smartWizard/css/smart_wizard_theme_circles.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/dashboard/smartWizard/css/smart_wizard_theme_arrows.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/dashboard/smartWizard/css/smart_wizard_theme_dots.css')}}" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="{{asset('assets/dashboard/smartWizard/js/jquery.smartWizard.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
    <script src="{{ asset('assets/dashboard/smartWizard/js/bootstrap-multiselect.js') }}"></script>
    <link href=" {{asset('assets/dashboard/smartWizard/css/custom.css')}}" rel="stylesheet" type="text/css" />

    <script src="{{asset('assets/dashboard/company/invoiceManager/create/index.js')}}"></script>

    <script type="text/javascript">
        $(document).on('change','.amountdata', function (e) {
            types = [];
            $(".amountdata").each(function() {
                types.push($(this).val());
            });
            invoiceamounts =[]
            $(".invoiceAmount").each(function() {
                invoiceamounts.push($(this).val());
            });
            var InvoiceTotal = 0;
            $.each(invoiceamounts,function() {
                InvoiceTotal += parseFloat(this, 10);
            });
            var total = 0;
            $.each(types,function() {
                total += parseFloat(this, 10);
            });

            var totalamount = total + InvoiceTotal
            var totalamount = total + InvoiceTotal

            $('.subTotalValue').html("$ "+totalamount);
            var taxvalue = $('.taxValue').attr('value');

            var totalva = ((totalamount * (taxvalue/100)) + totalamount).toFixed(3)

            $('.TotalValue').html("$ "+totalva);
        });
    </script>
@endsection