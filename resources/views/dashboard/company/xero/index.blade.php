
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
            <div>
                <div class="pull-left" style="padding: 9px 0px 0px 19px;">
                    Show&nbsp;&nbsp;
                    <select aria-controls="datatable" class="selectPaginate"  name="items">
                        <option value="10"  @if($items==10) selected @endif>10</option>
                        <option value="50" @if($items==50) selected @endif>50</option>
                        <option value="100" @if($items==100) selected @endif>100</option>
                    </select>&nbsp;&nbsp;entries
                </div>

                <div class="pull-right" style="padding: 9px 0px 0px 19px;">
                    Search
                    <input type="search" class="rtMenuSearch" id="searchInput" placeholder="" @if(@$searchKey) value="{{ @$searchKey }}" @endif>
                </div>
            </div>
            <br>
            <div class="clearfix"></div>
            <div class="pull-right">
                <!-- Button trigger modal -->
                @if(count($userXeroName)==0)
                    <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#myModalNoEmployee">
                        <i class="fa fa-plus-square"></i> Create Timesheet
                    </button>
                @else
                    <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-plus-square"></i> Create Timesheet
                    </button>

                    <input type="hidden" id="employeIdSyncBulk" value=" {{$userXeroName[0]['employee_id']}}">
                    <button  type="button" class="btn btn-xs btn-raised btn-block btn-info" id="syncBulkPayperiod"> <i class="fa fa-plus-square"></i>  Sync All Timesheets</button>

                @endif

            </div>
            <div class="clearfix"></div>
            <table class="table datatable" id="">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Full  Name</th>
                    <th >Period</th>
                    <th>Total Hour(s)</th>
                    <th>Xero Timesheet Id</th>
                    <th >Action</th>
                </tr>
                </thead>
                <tbody>

                @if(count($timeSheetdocketDetail)==0)
                    <tr>
                        <td  colspan="5"  style="text-align: center;">Empty Data</td>
                    </tr>
                @else

                    @foreach($timeSheetdocketDetail as $rowData)
                        <tr>
                            <td>{{$rowData->id}}</td>
                            <td>{{$rowData->UserId->first_name}} {{$rowData->UserId->last_name}}</td>
                            <td> {{\Carbon\Carbon::parse(explode('|',$rowData->period)[0])->format('Y-m-d')}}  - {{\Carbon\Carbon::parse(explode('|',$rowData->period)[1])->format('Y-m-d')}}</td>
                            <td>{{ round($rowData->total_hours, 2)}}</td>
                            <td>{{$rowData->xero_timesheet_id}}</td>

                            <td>
                                <a  href="{{url('dashboard/company/xero/view/'.$rowData->id)}}"  class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach

                @endif
                </tbody>
                @if(count($timeSheetdocketDetail) != 0)
                    <tfoot>
                    <tr id="folderAdvanceFilterFooterView">
                        <td colspan="3" style="padding: 33px 0px 0px 9px;"><span>Showing  {{ $timeSheetdocketDetail->firstItem() }} to {{ $timeSheetdocketDetail->lastItem() }} of {{ $timeSheetdocketDetail->total() }} entries</span></td>
                        <td colspan="5" class="text-right">
                            @if(@$searchKey)
                                <div id="folderPagination">  {{ $timeSheetdocketDetail->appends(['search'=>$searchKey,'items'=>$items])->links() }}</div>
                            @else
                                <div id="folderPagination"> {{ $timeSheetdocketDetail->appends(['items'=>$items])->links() }}</div>
                            @endif
                        </td>
                    </tr>
                    </tfoot>
                @endif

            </table>
        </div>
    </div>
    <br><br>




    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Sync or Create Timesheet</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/xero/timesheetDetail', 'id'=>'syncTimeSheet', 'files' => true]) }}

                <div class="modal-body">
                    <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerSubDocket">
                        <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-top:0px;">
                                <label for="xeroEmployee" class="control-label">Employee</label>
                                <select id="xeroEmployee" class="form-control" required name="xeroEmployee">
                                    <option value="null">Select Employee</option>
                                    @if($userXeroName)
                                        @foreach($userXeroName as $row)
                                            @if($row['type']=='match')
                                                <option value="{!! $row['id'] !!}"> {{$row['first_name']}} {{$row['last_name']}}</option>
                                            @else
                                                <option value="{!! $row['id'] !!}"> {{$row['first_name']}} {{$row['last_name']}} ({{$row['xero_first_name']}} {{$row['xero_last_name']}})</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-top:0px;" id="disapleDateRange">
                                <label for="templateId" class="control-label">Pay Period</label>
                                <select id="templateId" class="form-control" required name="date" disabled="">
                                    <option>Select Date</option>
                                </select>
                            </div>
                            <div id="payperiod">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submitSyncButton" style="" class="btn btn-primary" disabled>Submit</button>
                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>

    <div class="modal fade " id="bulkSyncPayPeriod" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Sync All Timesheets</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/xero/syncAllData', 'id'=>'syncTimeSheet', 'files' => true]) }}

                <div class="modal-body">
                    <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerSubDocket">
                        <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="row">
                        @if($userXeroName)
                            @foreach($userXeroName as $row)
                                @if($row['type']=='match')
                                    <input type="hidden" class="multipleEmployeId" name="multipleEmployeId[]" value="{{$row['id']}}_{{$row['employee_id']}}" >
                                @else
                                    <input type="hidden" class="multipleEmployeId" name="multipleEmployeId[]" value="{{$row['id']}}_{{$row['employee_id']}}">
                                @endif
                            @endforeach
                        @endif
                        <div class="col-md-12">
                            <div id="bulkSyncPayPeriodData">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submitSyncButtons" style="" class="btn btn-primary" disabled>Sync</button>
                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>


    <div class="modal fade " id="myModalNoEmployee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Sync or Create Timesheet</h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin-top:0px;">
                                Please make sure the employee names and e-mail addresses in Record Time match the ones in Xero.

                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal"   class="btn btn-primary " >close</button>
                </div>

            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $( "#xeroEmployee" ).change(function() {
                var selectedUserId = $( "#xeroEmployee" ).val();
                if(selectedUserId =="null") {
                    $('#submitSyncButton').prop("disabled", true);
                    $('#disapleDateRange').css('display','block');
                    $('#payperiod').css('display','none');
                }else{
                    $(".spinerSubDocket").css("display", "block");

                    $.ajax({
                        type: "POST",
                        url: '{{ url('dashboard/company/xero/checkedPayPeriod') }}',
                        data: { "userId": selectedUserId},
                        success: function (response) {
                            $('#disapleDateRange').css('display','none');
                            $(".spinerSubDocket").css("display", "none");
                            $('#payperiod').css('display','block');
                            $("#payperiod").html(response);
                            $('#submitSyncButton').prop("disabled", false)

                        }
                    });
                }



            });


            $( "#submitSyncButton" ).change(function() {
                document.getElementById('syncTimeSheet').submit();
            });


        });

        $(document).on('click', '#syncBulkPayperiod' ,function () {
            var data = $('#employeIdSyncBulk').val()
            $(".spinerSubDocket").css("display", "block");
            $('#bulkSyncPayPeriod').modal('show');
            $.ajax({
                type:"POST",
                data:{"employeId":data},
                url:"{{url('dashboard/company/xero/bulkSyncPayPeriod')}}",
                success:function (response) {
                    $("#bulkSyncPayPeriodData").html(response);
                    $(".spinerSubDocket").css("display", "none");
                    $('#submitSyncButtons').prop("disabled", false)
                }
            })

        })

        $(document).ready(function() {

            var timer = null;
            $('#searchInput').keydown(function(){
                clearTimeout(timer);
                timer = setTimeout(doStuff, 1000)
            });

            function doStuff() {
                $(".datatable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');
                if($('#searchInput').val().length>0){
                    $.ajax({
                        type: "GET",
                        url: "{{ url('dashboard/company/xero/searchTimeSheet?search=') }}" + $('#searchInput').val(),
                        success: function(response){
                            if(response == ""){

                            }else{

                                $(".datatable").html(response).show();
                            }
                        }
                    });
                }else{
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: "{{ url('dashboard/company/xero/searchTimeSheet?search=') }}",
                        success: function(response){
                            if(response == ""){

                            }else{
                                $(".datatable").html(response).show();
                            }
                        }
                    });
                }
            }

        } );

    </script>



@endsection

@section('customScript')



@endsection