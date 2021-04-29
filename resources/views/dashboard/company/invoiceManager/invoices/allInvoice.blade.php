@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Invoice Manager</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li  class="active">Invoice Manager</li>
        </ol>
        <div class="clearfix"></div>
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="rtTab" style="margin: 0px;min-height: 400px;">
        <div class="row">
            <div class="col-md-3"  style="    padding-right: 0;">
                <div class="col-md-12" style="background-color: #fff; border:0px solid #000000;color: #505050;  padding-left: 0px;padding-right: 0px;">
                    @include('dashboard.company.invoiceManager.partials.invoices-side-nav')
                </div>
                <br>
                <div class="col-md-12" style="background-color: #fff;margin-top: 18px;  border:0px solid #000000; padding-left: 0px;padding-right: 0px;" >
                    @include('dashboard.company.folder-management.index')
                </div>
            </div>
            <div class="col-md-9">
                <div class="viewFolder" style="padding-left: 0;     min-height: 529px;background: #fbfbfb;">
                    <div class="rtTabHeader">
                        <ul>
                            <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;">All Invoice</h4></li>
                            <li class="advacedFilter"><a href = "#close" class='forum-title'  data-toggle="modal" data-target="#myModalFilter"><i class="material-icons">filter_list</i> Advanced Filter</a></li>
                        </ul>
                    </div>

                    <div class="rtTabContent">
                        @include('dashboard.company.invoiceManager.partials.table-view.table-header.table-header-menu')
                        <table class="rtDataTable datatable" >
                            <thead>
                                <tr>
                                <th><input type="checkbox" class="checkbox " value="1"  name="employed[]" ></th>
                                <th>Invoice</th>
                                <th>Info</th>
                                <th>Invoice Name</th>
                                <th>Date Added</th>
                                <th>Status</th>
                                <th width="200px">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(@$result)
                                @php $invoiceCheckbox = true @endphp
                                @foreach($result->sortByDesc('created_at')  as $row)
                                    @if($row instanceof App\SentInvoice)
                                        @include('dashboard.company.invoiceManager.partials.table-view.sent-invoice-row')
                                    @endif
                                    @if($row instanceof App\EmailSentInvoice)
                                        @include('dashboard.company.invoiceManager.partials.table-view.email-sent-invoice-row')
                                    @endif
                                @endforeach
                            @endif
                            @if(count(@$result)==0)
                                <tr>
                                    <td colspan="9">
                                        <center>Data Empty</center>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"><span>Showing  {{ $result->firstItem() }} to {{ $result->lastItem() }} of {{ $result->total() }} entries</span></td>
                                    <td colspan="5" class="text-right">
                                        @if(@$searchKey) {{ $result->appends(['search'=>$searchKey,'items'=>$items])->links() }}
                                        @else {{ $result->appends(['items'=>$items]) ->links() }} @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div><!--/.rtTabContent-->
                </div>
            </div>
        </div>
    </div><br>
    @php $filterType    =   "all"; @endphp
    @include('dashboard.company.invoiceManager.modal-popup.advanced-filter.advanced-filter')
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

margin-bottom: 20px;
}
.rtTab .rtTabHeader{
background-color: #fff;

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


.rtTabContent{
background-color: #fff;
}

.rtDataTable .pagination{
margin: 0px;
font-weight: normal;
}
.rtDataTable .pagination>li:first-child>a, .rtDataTable .pagination>li:first-child>span{
border-bottom-left-radius: 18px;
border-top-left-radius: 18px;
padding-left: 15px;
padding-right: 15px;
}
.rtDataTable .pagination>li:last-child>a, .rtDataTable .pagination>li:last-child>span{
border-bottom-right-radius: 18px;
border-top-right-radius: 18px;
padding-left: 15px;
padding-right: 15px;
}
.rtDataTable .pagination>.active>a, .rtDataTable .pagination>.active>a:focus, .rtDataTable .pagination>.active>a:hover, .rtDataTable .pagination>.active>span, .rtDataTable .pagination>.active>span:focus, .rtDataTable .pagination>.active>span:hover{
background: #15B1B8;
color: #fff;
}
.rtDataTable .pagination>li>a, .rtDataTable .pagination>li>span{
color: #787878;
}
.shell{
width:100%;


}
.shell a, shell a:link, shell a:visited, shell td a, shell td a:link,  shell td a:visited
{
text-decoration:none;
color:#666666;
}
.shell a:hover{
text-decoration:none;
color:#000000;
}
.head {
border-bottom:1px solid #eae9e9;
font-size:14px;
height: 51px;

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
</style>
@endsection
@section('customScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <script  src="{{asset('assets/zepto.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('V2') }}"/>
    <script src="{{  asset('assets/folder/v2/rtTree.js') }}"></script>
    <script type="text/javascript">
        $("#empolyees").chained("#company");
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $( function() {
                $( "#toDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
            } );
        } );
    </script>
@endsection
