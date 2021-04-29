@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Assign Docket Template
            <small>Assign/Unassign Docket Template</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li  class="active">Docket Book Manager</li>
        </ol>
        <div class="clearfix"></div>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="rtTab" style="margin: 0px;min-height: 400px; background: none; ">
        <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
            <div class="col-md-12">
                <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Assigned Dockets Template</h3>
                <div class="pull-right">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-plus-square"></i> Assign
                    </button>
                </div>
                <div class="clearfix"></div>
                <div class="searchBar">
                    <form method="GET" action="{{ route("assign.docket.template.search") }}">
                        <select class="btn btn-xs countSearch" name="count">
                            <option value="10" @isset($count) @if($count == 10) selected @endif @endisset>10</option>
                            <option value="25" @isset($count) @if($count == 25) selected @endif @endisset>25</option>
                            <option value="50" @isset($count) @if($count == 50) selected @endif @endisset>50</option>
                            <option value="100" @isset($count) @if($count == 100) selected @endif @endisset>100</option>
                        </select>
                        <input type="text" placeholder="Search docket name here ..." name="docket_name" @isset($docket_name) value="{{ $docket_name }}" @endisset class="docketSearch pull-right" >
                    </form>
                </div>
                <div class="clearfix"></div>
                <table class="table" id="datatable">
                    <thead>
                    <tr>
                        {{-- <th>Id</th> --}}
                        <th>Employee</th>
                        <th>Docket Name</th>
                        <th>Assigned By</th>
                        <th>Date Added</th>
                        <th width="120">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(@$assignedTemplate)
                        @foreach($assignedTemplate as $row)
                            @if($row->docketInfo->is_archive == 0)
                            <tr>
                                {{-- <td>{{ $row->id }}</td> --}}
                                <td>{{ @$row->userInfo->first_name." ".@$row->userInfo->last_name }}</td>
                                <td>{{ $row->docketInfo->title }}</td>
                                <td>{{ $row->assignedBy->first_name." ".$row->assignedBy->last_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                                <td>
                                    {{-- {{ Form::open(['method'=>'DELETE', 'url'=>['dashboard/company/docketManager/assignDocket', $row->id], 'style'=>'display:inline-block;']) }}
                                    {{ Form::button('<span class="glyphicon glyphicon-trash" aria-hidden="true"  />', array(
                                                    'type' => 'submit',
                                                    'class' => 'btn btn-raised btn-danger btn-xs',
                                                    'onclick'=>'return confirm("Are you sure to remove this assigned access?")'
                                                ))
                                            }}
                                    {{ Form::close() }} --}}
                                    <a  data-toggle="modal" data-target="#docketTemplete" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs"  >
                                        <span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true" style="padding: 3px 0px;" /></a>
                                    <a data-id="{{$row->id}}"  class="btn btn-raised btn-success btn-xs updateAssignedDocketTemplete" data-toggle="modal" data-target="#docketTempleteUpdate">
                                        <span class="glyphicon glyphicon-pencil templet-trash" aria-hidden="true" style="padding: 3px 0px;" /></a>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                    @if(count(@$assignedTemplate)==0)
                        <tr>
                            <td colspan="6">

                                <center>Data Empty</center>

                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="pages" style="float: right">
                    @if(isset($docket_name))
                        {{ $assignedTemplate->appends(['count' => $count ,'docket_name' => $docket_name ])->links() }}
                    @else
                        {{ $assignedTemplate->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <br/><br/>
    <!-- Modal -->
    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Assign Docket Template</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/docketManager/assignDocket/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="padding-bottom: 20px;margin: 28px 0 0 0;">
                                <label for="employeeId" class="control-label">Employee</label>
                                <br>
                                <label class="control-label"><i>Note: You Can select multiple employees at once</i></label>

                                <select  class="form-control employeeList" id="framework"  required name="employeeId[]" multiple>

                                    @if($employees)
                                        @foreach($employees as $row)
                                            <option value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group" style="margin-top:0px;">
                                <label for="templateId" class="control-label">Docket Template</label>
                                <select  class="form-control" required name="templateId">
                                    <option value="">Select Docket Template</option>
                                    @if($templates)
                                        @foreach($templates as $row)
                                            <option value="{!! $row->id !!}">{!! $row->title !!}</option>
                                        @endforeach
                                    @endif
                                </select>

                                <!--<div class="okButton col-md-12" style="display: none; ">-->

                                <!--        <button class="btn btn-xs btn-raised  btn-success pull-right okpressed">ok</button>-->

                                <!--</div>-->
                            </div>
                            {{-- @if(Session::get('company_id') == 1) --}}
                                <br>
                                <div class="form-group" style="margin-top: 0;">
                                    <div class="col-md-6">
                                        <label  style="margin-right: 10px;">Regular Assign </label>
                                        <input type="checkbox" name="assignType" class="assignType" checked value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label style="margin-right: 10px;">Date Range</label>
                                        <input type="checkbox" name="assignType" class="assignType" value="1">
                                        <input type="text" class="daterange form-control" name="daterange" value="{{\Carbon\Carbon::now()->format('m-d-Y')}} - {{\Carbon\Carbon::now()->addWeek()->format('m-d-Y')}}" style="display: none" />
                                    </div>
                                </div>
                            {{-- @endif --}}

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <br/><br/>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade updateAssignedDocketTempleteModal" id="docketTempleteUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close updateAssignedDocketTempleteClose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Assign Docket Template</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/docketManager/assignDocket/update', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin-top:0px;">
                                <label for="templateId" class="control-label">Docket Template</label>
                                <select id="templateId" class="form-control updateTemplateId" required name="templateId">
                                    <option value="">Select Docket Template</option>
                                    @if($templates)
                                        @foreach($templates as $row)
                                            <option value="{!! $row->id !!}">{!! $row->title !!}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div  style="background-color: #f9faf9;padding: 0px 20px 40px 20px;">
                                <div class="form-group" style="padding-bottom: 20px;margin: 28px 0 0 0;">
                                    <input type="hidden" class="assignDocketTemplate" name="id">
                                    <label class="control-label">Employee</label><br>
                                    <select class="form-control updateEmployeeList" required name="employeeId[]">
                                        <option value="">Select Employee</option>
                                        @if($employees)
                                            @foreach($employees as $row)
                                                <option value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: 0;">
                                    <div class="col-md-6">
                                        <label  style="margin-right: 10px;">Regular Assign </label>
                                        <input type="checkbox" name="assignType[]" class="assignTypeUpdate updateAssignType0" value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label style="margin-right: 10px;">Date Range</label>
                                        <input type="checkbox" name="assignType[]" class="assignTypeUpdate updateAssignType1" value="1">
                                        <input type="text" class="daterange updateDateRange form-control" name="daterange[]" value="{{\Carbon\Carbon::now()->format('m-d-Y')}} - {{\Carbon\Carbon::now()->addWeek()->format('m-d-Y')}}" style="display: none" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="spinnerCheckgrid" style="font-size: 30px;position: absolute;z-index: 11;left: 50%;bottom: 22%; display: none;"><i class="fa fa-spinner fa-spin"></i></span>
                </div>
                <div class="modal-footer">
                    <br/><br/>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>


    <!-- Delete Docket Modal -->
    <div class="modal fade" id="docketTemplete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/docketManager/assignDocket','method' => 'delete' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Unassign Docket Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="docket_templete" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to unassign this template?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


@endsection

@section('customScript')
    <style>
        .ui-tooltip-content{
            display: none;
        }
        .multiselect-container>.active>label:visited{
           color: #fff !important;

        }

        .okButton{
            z-index: 11111;
            position: absolute;
            bottom: -54px;
            background: #eeeeee;
            padding: 0px 9px 0px 9px;
        }
        .multiselect-container{
            padding-bottom: 45px;
        }
    </style>
    <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript">
        $(document).ready(function() {
            $.fn.dataTable.moment( 'D-MMM-YYYY' );
            $('#datatable').dataTable( {
                "order": [[ 3, "desc" ]],
                searching: false,
                paging: false,
                info: false
            } );
        } );

        $(function() {
            $('input[name="daterange[]"]').daterangepicker({
                opens: 'left',
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });

        $(function() {
            $('.daterange').daterangepicker({
                opens: 'left',
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });

        $(document).ready(function() {

            $('#docketTemplete').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#docket_templete").val(id);
            });

            $('.multiselect').on('click',function (e) {
                if($(this).parent('div').hasClass('open') == false ){
                    $('.okButton').css('display','')
                }else{
                    $('.okButton').css('display','none')
                }
            })

            $('.okpressed').on('click', function (e) {
                $('.open').removeClass('open');
                $('.okButton').css('display','none')

            })


            $(document).click(function(evt){
                if($(".multiselect .dropdown-toggle").parent('div').hasClass('open') == true ){
                    $('.okButton').css('display','')
                }else{
                    $('.okButton').css('display','none')
                }
            });


            $(document).on('click', '.assignType', function () {
                var value = $(this).val();
                var assigntype = $(this).closest('.cloneUnit').find('.assignType');
                for (var i = 0; i < assigntype.length; i++) {
                    if(assigntype[i].value != value){
                        assigntype[i].checked = false;
                    }else{
                        assigntype[i].checked = true;
                    }
                }
                if(value == 1){
                   $(this).closest('.form-group').find('.daterange').css('display','');
                }else{
                    $(this).closest('.form-group').find('.daterange').css('display','none');
                }
            })

            $(document).on('click', '.assignTypeUpdate', function () {
                var value = $(this).val();
                var assigntype = document.getElementsByClassName("assignTypeUpdate");
                for (var i = 0; i < assigntype.length; i++) {
                    if(assigntype[i].value != value){
                        assigntype[i].checked = false;
                    }else{
                        assigntype[i].checked = true;
                    }
                }
                if(value == 1){
                   $('.daterange').css('display','');
                }else{
                    $('.daterange').css('display','none');
                }
            });
        });

        function dateDisable(event,user_id){
            var url = '{{ route("leave.management.view",":ID") }}';
            url = url.replace(":ID",user_id);
            $.ajax({
                type:'GET',
                url: url,
                dataType: 'json',
                success: function(response){
                    if(response.status){
                        var day = 1000*60*60*24;
                        if(response.employee_leave.length > 0){
                            var invalidDateList = [];
                            var employeeLeave = response.employee_leave;
                            employeeLeave.forEach(leave => {
                                date1 = new Date(leave.from_date);
                                date2 = new Date(leave.to_date);
                                var diff = (date2.getTime()- date1.getTime())/day;
                                for(var i=0;i<=diff; i++)
                                {
                                    var xx = date1.getTime()+day*i;
                                    var yy = new Date(xx);

                                    invalidDateList.push(yy.getFullYear()+"-"+(yy.getMonth()+1)+"-"+yy.getDate());
                                }
                            });

                            $(event).closest('.cloneUnit').find('input[name="daterange[]"]').daterangepicker({
                                isInvalidDate: function(date,response) {
                                    if (invalidDateList.includes(date.format('YYYY-M-D'))) {
                                        return true;
                                    }
                                }
                            });
                        }
                    }
                }
            })
        }

        $('.employeeList').change(function(){
            dateDisable(this,$(this).val());
        });

        $('.cloneEmployee').click(function(){
            var clone = $('.cloneUnit:first').clone(true);
            $('.appendCloneUnit').append(clone.prepend('<a class="assignDocketClose" onclick="removeEmployee(this)"><i class="fa fa-close"></i></a>'));
            $('.daterange').daterangepicker({
                opens: 'left',
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });

        function removeEmployee(event){
            $(event).closest('.cloneUnit').remove();
        }

        $('.updateAssignedDocketTemplete').click(function(){
            $('.spinnerCheckgrid').css('display','');
            var url = '{{ route("assign.docket.template.view",":ID") }}';
            url = url.replace(":ID",$(this).attr('data-id'));
            // $('.updateAssignedDocketTempleteModal').addClass('in').show();
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function(response){
                    $('.spinnerCheckgrid').css('display','none');
                    if(response.status){
                        $('.updateTemplateId option').each(function(){
                            if($(this).val() == response.assignDocket.docket_id){
                                $(this).prop('selected','selected');
                            }
                        });

                        $('.updateEmployeeList option').each(function(){
                            if($(this).val() == response.assignDocket.user_id){
                                console.log('hit');
                                $(this).prop('selected','selected');
                            }
                        });


                        $('.assignDocketTemplate').val(response.assignDocket.id);

                        $('.updateAssignType0').prop('checked',false);
                        $('.updateAssignType1').prop('checked',false);
                        $('.updateAssignType'+response.assignDocket.assign_type).prop('checked',true);

                        $('.updateDateRange').val(response.assignDocket.date_range);
                        if(response.assignDocket.date_range){
                            var splitData = response.assignDocket.date_range.split("-");
                            $('input[name="daterange[]"]').daterangepicker({
                                startDate: moment(splitData[0]),
                                endDate: moment(splitData[1])
                            });
                        }else{
                            $('input[name="daterange[]"]').daterangepicker();
                        }

                        if(response.assignDocket.assign_type == 1){
                            $('.daterange').css('display','');
                        }else{
                            $('.daterange').css('display','none');
                        }

                        dateDisable(this,response.assignDocket.user_id);
                    }
                }
            })
        });

        $('.updateAssignedDocketTempleteClose').click(function(){
            $(this).closest('.modal').removeClass('in').hide();
        });

        $('.docketSearch').change(function(){
            $(this).closest('form').submit();
        });

        $('.countSearch').change(function(){
            $(this).closest('form').submit();
        });

    </script>
@endsection
