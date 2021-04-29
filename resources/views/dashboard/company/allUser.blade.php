
@extends('layouts.companyDashboard')
@section('content')
<section class="content-header">
    <h1>
        <i class="fa fa fa-file-text-o"></i> Docket Book Manager
        <small>Add/View Dockets</small>
    </h1>
    <ol class="breadcrumb hidden-sm hidden-xs">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="#">Docket Book Manager</a></li>
        <li class="active">View</li>
    </ol>
</section>
<div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
<div class="clearfix"></div>
<div class="col-md-12">
    <button  class="btn btn-default btn-sm pull-right" id="printDiv" style="margin: 0px 0px 0px;"><i class="fa fa-print"></i> Print</button>
    <div id="printContainer">
    <div class="datatable">
        <table class="table" id="datatable">
            <thead>
            <tr>
                <th>Id</th>
                <th >Full Name</th>
                <th >Email</th>
                <th >Last Active</th>
            </tr>
            </thead>
            <tbody>
            @if($activeUser->count())
                @foreach($activeUser as $row)
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->first_name." ".$row->last_name}}</td>
                        <td>{{$row->email}}</td>
                        <td>{{$row->updated_at}}</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    </div>
</div>
@endsection


@section('customScript')
        <script type="text/javascript" src="{{ url('assets/printThis.js') }}"></script>
        <script>
            $(document).ready(function(){
                $("#printDiv").on("click",function(){
                    $('#printContainer').printThis({
                        removeInline: false,
                        importCSS: true,
                        importStyle: true
                    });
                });
            })
        </script>
    <script type="text/javascript" src="{{ url('assets/dashboard/js/docket.js') }}"></script>
@endsection
