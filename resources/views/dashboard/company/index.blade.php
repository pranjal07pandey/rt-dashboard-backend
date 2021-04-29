@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Dashboard</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li class="active"><i class="fa fa-dashboard"></i>&nbsp;Dashboard</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')
    @include('dashboard.company.partials.company-summary')

    <div class="boxContent">
        <div class="boxHeader">
            <div class="pull-left">
                <strong>Recent Dockets</strong>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="boxBody">
            <table class="rtDataTable" >
                <thead>
                    <tr>
                        <th width="120px">Docket Id</th>
                        <th width="250px">Info</th>
                        <th>Docket</th>
                        <th width="120px">Date Added</th>
                        <th width="100px">Status</th>
                        <th width="190">Action</th>
                    </tr>
                </thead>
                <tbody>
                @if(@$latestDockets)
                    @php $docketCheckbox = false @endphp
                    @php $shareableFolder = false  @endphp
                    @foreach($latestDockets->sortByDesc('created_at') as $row)
                        @if($row instanceof App\SentDockets)
                            @include('dashboard.company.docketManager.partials.table-view.sent-docket-row')
                        @endif
                        @if($row instanceof App\EmailSentDocket)
                            @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-row')
                        @endif
                    @endforeach
                @endif
                @if(count(@$latestDockets)==0)
                    <tr>
                        <td colspan="6">
                            <center>Data Empty</center>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div><!--/.boxContent-->

    <div class="boxContent">
        <div class="boxHeader">
            <div class="pull-left">
                <strong>Recent Invoices</strong>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="boxBody">
            <table class="rtDataTable" >
                <thead>
                    <tr>
                        <th width="120px">Invoice Id</th>
                        <th width="250px">Info</th>
                        <th>Invoice Name</th>
                        <th width="120px">Date Added</th>
                        <th width="125px">Status</th>
                        <th width="190">Action</th>
                    </tr>
                </thead>

                <tbody>
                @if(@$latestInvoices)
                    @foreach($latestInvoices->sortByDesc('created_at') as $row)
                        @if($row instanceof App\SentInvoice)
                            @include('dashboard.company.invoiceManager.partials.table-view.sent-invoice-row')
                        @endif
                        @if($row instanceof App\EmailSentInvoice)
                            @include('dashboard.company.invoiceManager.partials.table-view.email-sent-invoice-row')
                        @endif
                    @endforeach
                @endif
                @if(count(@$latestInvoices)==0)
                    <tr>
                        <td colspan="6">
                            <center>Data Empty</center>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div><!--/.Recent Invoices-->
    <br/>

    @include('dashboard.company.docketManager.modal-popup.cancel-docket.cancel-docket')
    @include('dashboard.company.docketManager.modal-popup.docket-label.docket-label')
    @include('dashboard.company.docketManager.modal-popup.docket-label.delete-docket-label')
    @include('dashboard.company.invoiceManager.modal-popup.invoice-label.invoice-label')
    @include('dashboard.company.invoiceManager.modal-popup.invoice-label.delete-invoice-label')
    @include('dashboard.company.docketManager.modal-popup.delete-docket.delete-docket')

@endsection

@section('customScript')
    <script src="{{ asset('assets/dashboard/js/jquery.counterup.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.css" rel="stylesheet"></link>
    <script>
        jQuery(document).ready(function($) {
            $('.counter').counterUp({
                delay: 10,
                time: 1000
            });
        });
    </script>
@endsection
