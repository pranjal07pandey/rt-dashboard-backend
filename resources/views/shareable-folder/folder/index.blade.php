@extends('layouts.shareableMaster')
@section('content')
    @include('dashboard.company.include.flashMessages')

    @if($type == "Public")
        <div class="rtTab" style="margin: 0px;min-height: 400px; background: none; ">
            <div class="row">
                <div class="col-md-3" >

                    <div class="col-md-12" style="background-color: #fff;  border:0px solid #000000; padding-left: 0px;padding-right: 0px;     min-height: 529px;" >
                        <div class="boxHeader">
                            <strong class="pull-left">Folders </strong>
                            <input type="hidden" class="mainFolderId" value="{{$companyFolder[0]->id}}">

                            <div class="clearfix"></div>
                        </div>
                        <div class="boxContent">
                            <ul class="folderRtTree">
                                @if($companyFolder)
                                    @foreach($companyFolder as $rowItems)
                                        @if($rowItems->type == 0)
                                            <li>
                                                <a href="#"  id="{{$rowItems->id}}" class="active">
                                                    {{$rowItems->name}}&nbsp;&nbsp;
                                                    <span style="position: absolute;right: 4px;">@if(count($rowItems->folderItems)!=0)({{count($rowItems->folderItems)}}) @endif</span>
                                                </a>
                                                <ul style="display:none;">
                                                    <li></li>
                                                </ul>
                                                <div  class="editBtn" id="editBtnId"  data-id="{{$rowItems->id}}" data-title="{{$rowItems->name}}" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>
                                            </li>


                                        @endif
                                    @endforeach

                                    @foreach($companyFolder as $rowItems)
                                        @if($rowItems->type == 1)
                                            <li>
                                                <a href="#"  id="{{$rowItems->id}}"  class="active">
                                                    {{$rowItems->name}}&nbsp;&nbsp;
                                                    <span style="position: absolute;right: 4px;">@if(count($rowItems->folderItems)!=0)({{count($rowItems->folderItems)}}) @endif</span>
                                                </a>
                                                <ul style="display:none;">
                                                    <li></li>
                                                </ul>
                                                <div  data-id="{{$rowItems->id}}" data-title="{{$rowItems->name}}" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>
                                            </li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>

                        </div>

                    </div>
                </div>

                <div class="col-md-9 " style="padding-left: 0;">
                    <div class="viewFolder" style="padding-left: 0;     min-height: 529px;background: #ffffff;">

                        <span class="loadspin" style="font-size: 41px;position: absolute;display: none;z-index: 1;left: 50%;top: 50%;"><i class="fa fa-spinner fa-spin"></i></span>


                    @if(count(@$result)==0)
                            <div class="rtTabHeader">
                                <ul>
                                    <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;">  {{$companyFolder[0]->name}}</h4></li>
                                </ul>
                            </div>

                            <div class="rtTabContent" >
                                <div class="tableHeaderMenu" style="    min-height: 476px;">
                                    <span style="    text-align: center;display: block;font-size: 12px;padding-top: 100px;color: #ababab;">
                                        <i style="    font-size: 47px;" class="fa fa-folder-open-o" aria-hidden="true"></i>
                                        <br>
                                        Folder Empty</span>
                                </div>
                            </div>

                        @else

                        <div class="rtTabHeader">
                            <ul>
                                <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;">  {{$companyFolder[0]->name}}</h4></li>
                            </ul>
                        </div>
                        <div class="rtTabContent">
                            <table class="rtDataTable datatable" >
                                <thead>
                                <tr>
                                    <th>Docket Id</th>
                                    <th>Info</th>
                                    <th>Docket Name</th>
                                    <th>Date Added</th>
                                    <th>Status</th>
                                    <th width="200px">Action</th>
                                </tr>
                                </thead>


                                <tbody  id="folderAdvanceFilterView">

                                @if(@$result)
                                    @php $docketCheckbox = false @endphp
                                    @php $invoiceCheckbox = false @endphp
                                    @php $shareableFolder = true  @endphp


                                    @foreach($result->sortByDesc('created_at') as $row)
                                        @if($row instanceof App\SentDockets)
                                            @include('shareable-folder.folder.partials.table-view.sent-docket-row')
                                        @endif
                                        @if($row instanceof App\EmailSentDocket)
                                            @include('shareable-folder.folder.partials.table-view.email-sent-docket-row')
                                        @endif
                                        @if($row instanceof App\SentInvoice)
                                            @include('shareable-folder.folder.partials.table-view.sent-invoice-row')
                                        @endif
                                        @if($row instanceof App\EmailSentInvoice)
                                            @include('shareable-folder.folder.partials.table-view.email-sent-invoice-row')
                                        @endif
                                    @endforeach
                                    @else
                                    Folder Empty
                                @endif
                                </tbody>
                            </table>
                        </div>
                      @endif


                    </div>
                </div>
            </div>
        </div>
        <br/><br/>

    @elseif($type == "Restricted")


        <div class="rtTab" style="margin: 0px;min-height: 400px; background: none; ">
            <div class="row">
                <div class="col-md-3" >

                    <div class="col-md-12" style="background-color: #fff;  border:0px solid #000000; padding-left: 0px;padding-right: 0px;     min-height: 529px;" >
                        <div class="boxHeader">
                            <strong class="pull-left">Folders </strong>
                            <input type="hidden" class="mainFolderId" value="{{$companyFolder[0]->id}}">


                            <div class="clearfix"></div>
                        </div>
                        <div class="boxContent">
                            <ul class="folderRtTree">
                                @if($companyFolder)
                                    @foreach($companyFolder as $rowItems)
                                        @if($rowItems->type == 0)
                                            <li>
                                                <a href="#"  id="{{$rowItems->id}}"  >
                                                    {{$rowItems->name}}&nbsp;&nbsp;
                                                    <span style="position: absolute;right: 4px;">@if(count($rowItems->folderItems)!=0)({{count($rowItems->folderItems)}}) @endif</span>
                                                </a>
                                                <ul style="display:none;">
                                                    <li></li>
                                                </ul>
                                                <div  class="editBtn" id="editBtnId"  data-id="{{$rowItems->id}}" data-title="{{$rowItems->name}}" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>
                                            </li>


                                        @endif
                                    @endforeach

                                    @foreach($companyFolder as $rowItems)
                                        @if($rowItems->type == 1)
                                            <li>
                                                <a href="#"  id="{{$rowItems->id}}"  >
                                                    {{$rowItems->name}}&nbsp;&nbsp;
                                                    <span style="position: absolute;right: 4px;">@if(count($rowItems->folderItems)!=0)({{count($rowItems->folderItems)}}) @endif</span>
                                                </a>
                                                <ul style="display:none;">
                                                    <li></li>
                                                </ul>
                                                <div  data-id="{{$rowItems->id}}" data-title="{{$rowItems->name}}" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>
                                            </li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>

                        </div>

                    </div>
                </div>

                <div class="col-md-9 " style="padding-left: 0;">
                    <div class="viewFolder" style="padding-left: 0;     min-height: 529px;background: #ffffff;">
                        @if(count(@$result)==0)
                            <div class="rtTabHeader">
                                <ul>
                                    <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;">  {{$companyFolder[0]->name}}</h4></li>
                                </ul>
                            </div>

                            <div class="rtTabContent" >
                                <div class="tableHeaderMenu" style="    min-height: 476px;">
                                    <span style="    text-align: center;display: block;font-size: 12px;padding-top: 100px;color: #ababab;">
                                        <i style="    font-size: 47px;" class="fa fa-folder-open-o" aria-hidden="true"></i>
                                        <br>
                                        Folder Empty</span>
                                </div>
                            </div>

                        @else

                        <div class="rtTabHeader">
                            <ul>
                                <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;">  {{$companyFolder[0]->name}}</h4></li>
                            </ul>
                        </div>
                        <div class="rtTabContent">
                            <table class="rtDataTable datatable" >
                                <thead>
                                <tr>
                                    <th>Docket Id</th>
                                    <th>Info</th>
                                    <th>Docket Name</th>
                                    <th>Date Added</th>
                                    <th>Status</th>
                                    <th width="200px">Action</th>
                                </tr>
                                </thead>


                                <tbody  id="folderAdvanceFilterView">
                                @if(@$result)
                                    @php $docketCheckbox = false @endphp
                                    @php $invoiceCheckbox = false @endphp
                                    @php $shareableFolder = true  @endphp


                                    @foreach($result->sortByDesc('created_at') as $row)
                                        @if($row instanceof App\SentDockets)
                                            @include('shareable-folder.folder.partials.table-view.sent-docket-row')
                                        @endif
                                        @if($row instanceof App\EmailSentDocket)
                                            @include('shareable-folder.folder.partials.table-view.email-sent-docket-row')
                                        @endif
                                        @if($row instanceof App\SentInvoice)
                                            @include('shareable-folder.folder.partials.table-view.sent-invoice-row')
                                        @endif
                                        @if($row instanceof App\EmailSentInvoice)
                                            @include('shareable-folder.folder.partials.table-view.email-sent-invoice-row')
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                                <tfoot>


                                    <tr id="folderAdvanceFilterFooterView">
                                        <td colspan="3"><span>Showing  {{ $result->firstItem() }} to {{ $result->lastItem() }} of {{ $result->total() }} entries</span></td>
                                        <td colspan="5" class="text-right">
                                            @if(@$searchKey) <div id="folderPagination">  {{ $result->appends(['search'=>$searchKey,'items'=>$items])->links() }}</div>
                                            @else <div id="folderPagination"> {{ $result->appends(['items'=>$items])->links() }}</div>@endif
                                        </td>
                                    </tr>

                                </tfoot>
                            </table>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <br/><br/>


    @endif





    <style>


        .sentDocketImagePreview{
            margin: 0px;
            padding : 0px;
            list-style-type: none;
            margin-bottom: 10px;
            margin-top: 5px;
        }
        .sentDocketImagePreview li{
            display: inline-block;
            margin-right: 10px;
        }
        .badge .btn {
            display: none;
        }
        .badge:hover .btn{
            display: inline-block;
            cursor: pointer;

        }
        .badge:hover{
            cursor: pointer;

        }

        .sentDocketImagePreview{
            margin: 0px;
            padding : 0px;
            list-style-type: none;
            margin-bottom: 10px;
            margin-top: 5px;
        }
        .sentDocketImagePreview li{
            display: inline-block;
            margin-right: 10px;
        }
        .badge .btn {
            display: none;
        }
        .badge:hover .btn{
            display: inline-block;
            cursor: pointer;

        }
        .badge:hover{
            cursor: pointer;

        }
        .rtTab{
            background: #fff;
            margin-bottom: 20px;
        }
        .rtTab .rtTabHeader{

        }
        .rtTab .rtTabHeader ul{
            list-style-type: none;
            padding: 0px 0px;
            margin: 0px 0px;
            font-size: 14px;
            font-weight: 500;
        }
        .rtTabHeader ul li{
            display: inline-block;
        }
        .rtTabHeader ul li.active{
            color: #000;
            border-bottom: 2px solid #15B1B8;
        }
        .rtTabHeader ul li a{
            color: inherit;
            padding: 18px 30px;
            display: block;
            text-decoration: none;
        }
        .rtTabHeader ul li a:hover{
            color: #000;
        }
        .rtTabHeader ul li.advacedFilter{
            float: right;
        }
        .rtTabHeader ul li.advacedFilter i{
            font-size: 20px;
        }
        .rtTabHeader ul li.advacedFilter a{
            color: #15B1B8;
            padding-right:15px;
        }
        .tableHeaderMenu{
            background: #FBFBFB;
            min-height: 66px;
            width: 100%;
            border-top: 1px solid #EEEEEE;
            border-bottom: 1px solid #EEEEEE;
            color: #797979;
            font-weight: normal;
        }
        .tableHeaderMenu ul{
            list-style-type: none;
            margin: 3px 0px;
            padding: 0px;
        }
        .tableHeaderMenu ul li{
            padding: 15px 5px 0px 15px;
            display: inline-block;
        }
        .tableHeaderMenu .rtMenuBtn{
            background:none;
            border: 1px solid #15B1B8;
            height: 26px;
            font-size: 12px;
            border-radius: 13px;
            padding: 0px 15px;
            color: #797979;
        }
        .tableHeaderMenu .rtMenuBtn:hover{
            color: #000;
        }
        .rtTabContent{
        }
        .rtMenuSearch{
            border: 1px solid #E1E1E1;
            border-radius: 5px;
            margin-top: -2px;
            margin-left: 10px;
            margin-right: 10px;
        }
        .menu{
            margin-left:10px;
            padding-top:5px;
            float:left;
        }
        .menu a {
            margin-left: 5px;
        }
        .sign{
            float:right;
            margin-right: 20px;
        }
        .content {
            clear: both;
            margin-right:50px;
            padding:20px 20px 20px 20px;
            font-size: 12px;
        }


        .rtTab .menu ul{
            list-style-type: none;
            padding: 0px 0px;
            margin: 0px 0px 0 -10px;
            font-size: 14px;
            font-weight: 500;
        }
        .menu ul li{
            display: inline-block;
            width: 100%;
        }
        .menu ul li.active{
            color: #000;
            border-left: 4px solid #15B1B8;
            width: 100%;
        }
        .menu ul li a{
            color: inherit;
            padding: 12px 15px;
            display: block;
            text-decoration: none;
            border-top: 1px solid #ececec;
        }
        .menu ul li a:hover{
            color: #000;
        }
        .boxHeader{
            padding: 15px 0px 9px 16px;
            border-bottom: 1px solid #ececec;
        }

    </style>
@endsection

@section('customScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <script  src="{{asset('assets/zepto.js')}}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('V2') }}"/>
    <script src="{{  asset('assets/folder/v2/folderRtTree.js') }}"></script>
    {{--        <script src="{{  asset('assets/folder/V2/function.js') }}"></script>--}}

    <script type="text/javascript">
        $(document).ready(function() {

            $( function() {
                $( "#toDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
            } );
        } );
        $("#empolyees").chained("#company");




    </script>
@endsection