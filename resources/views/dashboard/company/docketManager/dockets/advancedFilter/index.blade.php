@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Docket Book Manager</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Docket Book Manager</a></li>
            <li class="active">Filter</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')
    <div class="rtTab" style="background: #fff;margin: 0px;min-height: 400px;">
        <div class="rtTabHeader">
            <ul>
                <li @if($request->type=="all") class="active" @endif><a href="{{ route('dockets.allDockets') }}" >All Dockets</a></li>
                <li @if($request->type=="sent") class="active" @endif><a href="{{ route('dockets.sentDockets') }}" >Sent Dockets</a></li>
                <li @if($request->type=="received") class="active" @endif><a href="{{ route('dockets.receivedDockets') }}" >Received Dockets</a></li>
                <li ><a href="{{ route('dockets.emailedDockets') }}" >Emailed Dockets</a></li>
            </ul>
        </div>
        <div class="rtTabContent">
            <div style="padding: 20px 15px 0px;position: relative;">
                <div class="filterDiv" style="margin-bottom: 20px;">
                    @include('dashboard.company.docketManager.dockets.advancedFilter.partials.form')
                </div>
                <button style="position: absolute;left: 209px;margin-top: 44px;    background: none;border: 1px solid #15b1b8;height: 26px;font-size: 12px;border-radius: 13px;padding: 0px 15px;color: #797979;" class="rtMenuBtn" id="exportcsv">Export .csv</button>
                <button style="position: absolute;left: 313px;margin-top: 44px;    background: none;border: 1px solid #15b1b8;height: 26px;font-size: 12px;border-radius: 13px;padding: 0px 15px;color: #797979;" class="rtMenuBtn" id="exportpdf">Export Pdf's</button>
                <strong style="border-bottom: 1px solid #ddd;display: block;margin-bottom: -1px;padding-bottom: 5px;">Results</strong>
            </div>


            <div class="tableHeaderMenu rtDataTableHeaderMenu" >
                <ul>
                    <li><button class="rtMenuBtn" id="exportcsv">Export .csv</button></li>
                    <li><button class="rtMenuBtn" id="exportpdf">Export Pdf's</button></li>
                    <li>
                        Show&nbsp;&nbsp;
                        <select class="selectPaginateAdvanceFilter" name="items" datacurrenturl="{{ route('dockets.advancedFilter',[
                                                            'company' => $request->company,
                                                            'employee' =>$request->employee,
                                                            'type' => $request->type,
                                                            'docketTemplateId' => $request->docketTemplateId,
                                                            'invoiceable' =>$request->invoiceable,
                                                            'docketId' => $request->docketId,
                                                            'date'  =>  $request->date,
                                                            'from'  =>   $request->from,
                                                            'to'    =>  $request->to]) }}">
                            <option value="10"  @if($items==10) selected @endif>10</option>
                            <option value="50" @if($items==50) selected @endif>50</option>
                            <option value="100" @if($items==100) selected @endif>100</option>
                            <option value="500" @if($items==500) selected @endif>500</option>
                        </select>&nbsp;&nbsp;entries
                    </li>
                    <li class="pull-right" style="display:none;">
                        Search
                        <input type="search" class="rtMenuSearch" id="searchInput" placeholder="">
                    </li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <table class="rtDataTable datatable" >
                <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="checkbox " value="1"  name="employed[]" >
                    </th>
                    <th>Docket Id</th>
                    <th>Info</th>
                    <th>Docket Name</th>
                    <th>Date Added</th>
                    <th>Status</th>
                    <th width="200px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $sn=1; ?>

                @if(@$sentDockets)
                    @php $docketCheckbox = true @endphp
                    @php $checktrashFolder = false @endphp
                    @foreach($sentDockets->sortByDesc('created_at') as $row)
                        @if($row instanceof App\SentDockets)
                            @include('dashboard.company.docketManager.partials.table-view.sent-docket-row')
                        @endif
                        @if($row instanceof App\EmailSentDocket)
                            @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-row')
                        @endif
                    @endforeach
                @endif

                @if(count(@$sentDockets)==0)
                    <tr>
                        <td colspan="9">
                            <center>Data Empty</center>
                        </td>
                    </tr>
                @endif
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3"><span>Showing  {{ $sentDockets->firstItem() }} to {{ $sentDockets->lastItem() }} of {{ $sentDockets->total() }} entries</span></td>
                    <td colspan="5" class="text-right">
                        @if(@$searchKey) {{ $sentDockets->appends(['search'=>$searchKey,'items'=>$items])->links() }}
                        @else {{ $sentDockets->appends(['items'=>$items,
                                                            'company' => $request->company,
                                                            'employee' =>$request->employee,
                                                            'type' => $request->type,
                                                            'docketTemplateId' => $request->docketTemplateId,
                                                            'invoiceable' =>$request->invoiceable,
                                                            'docketId' => $request->docketId,
                                                            'date'  =>  $request->date,
                                                            'from'  =>   $request->from,
                                                            'to'    =>  $request->to]) ->links() }} @endif
                    </td>
                </tr>
                </tfoot>
            </table>
            <div class="clearfix"></div>
        </div>
    </div>
    <br/><br/>
    @include('dashboard.company.docketManager.modal-popup.cancel-docket.cancel-docket')
    @include('dashboard.company.docketManager.modal-popup.docket-label.docket-label')
    @include('dashboard.company.docketManager.modal-popup.docket-label.delete-docket-label')
    @include('dashboard.company.docketManager.modal-popup.delete-docket.delete-docket')
@endsection

@section('customScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.css" rel="stylesheet"></link>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <script src="{{  asset('V2') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $( function() {
                $( "#toDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
            } );
            $.fn.dataTable.moment( 'D-MMM-YYYY' );


        });
        $(document.body).on('change',".selectPaginateAdvanceFilter",function (e) {
            var url = $(this).attr('datacurrenturl') + "&items=" + $(this).find(":checked").val()
            window.location.replace(url)
        });

    </script>
    <script type="text/javascript">
        $("#employee").chained("#company");
    </script>
@endsection
