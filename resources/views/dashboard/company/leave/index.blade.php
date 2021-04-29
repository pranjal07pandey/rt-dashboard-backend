@extends('layouts.companyDashboard')
@section('css')
    <link href="{{ asset('assets/calendar/rescalendar.css') }}" rel="stylesheet">
@endsection
@section('content')
    <section class="content-header rt-content-header">
        <h1>Leaves</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('leave_management.index') }}">Leave Management</a></li>
            <li class="active">Leaves</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    <div class="rtTab">
        <div class="rtTabContent">
            <div id="my_calendar_simple"></div>
        </div>
    </div>

    <div class="modal fade @if($errors->any() && session('route_name') == "leave.management.store" )in @endif" id="employeeLeave" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" @if($errors->any()) style="display: block;" @else style="display: none;" @endif >
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close modalClose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Leave</h4>
                </div>
                <form action="{{ route('leave.management.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group @if($errors->has('user_id')) has-error @endif">
                                            <label class="control-label" for="employees">Employees</label>
                                            <select id="employees" name="user_id" class="form-control">
                                                <option value="">Select Employee</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->user_id }}">{{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('user_id'))
                                                <span class="error" role="alert">{{ $errors->first('user_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group @if($errors->has('user_id')) has-error @endif">
                                            <label class="control-label" for="machine">Machines</label>
                                            <select id="machine" name="machine_id" class="form-control">
                                                <option value="">Select Machine</option>
                                                @foreach ($machines as $machine)
                                                    <option value="{{ $machine->id }}">{{ $machine->name }} ({{ $machine->registration }})</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('machine_id'))
                                                <span class="error" role="alert">{{ $errors->first('machine_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group @if($errors->has('description')) has-error @endif">
                                            <label class="control-label" for="description">Description</label>
                                            <input type="text" id="description" name="description" class="form-control" value="{{ old('description') }}" required>
                                            @if($errors->has('description'))
                                                <span class="error" role="alert">{{ $errors->first('description') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group @if($errors->has('from_date')) has-error @endif">
                                            <label class="control-label" for="from_date">From Date</label>
                                            <input type="date" id="from_date" name="from_date" class="form-control" value="{{ old('from_date') }}" required>
                                            @if($errors->has('from_date'))
                                                <span class="error" role="alert">{{ $errors->first('from_date') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group @if($errors->has('to_date')) has-error @endif">
                                            <label class="control-label" for="to_date">To Date</label>
                                            <input type="date" id="to_date" name="to_date" class="form-control" value="{{ old('to_date') }}" required>
                                            @if($errors->has('to_date'))
                                                <span class="error" role="alert">{{ $errors->first('to_date') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div class="modal fade @if($errors->any() && session('route_name') == "leave.management.update" ) in @endif" id="employeeLeaveEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" @if($errors->any()) style="display: block;" @else style="display: none;" @endif >
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close modalClose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Edit Leave</h4>
                </div>
                <form action="{{ route('leave.management.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="employee_leave_id" class="edit_employee_leave_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group @if($errors->has('user_id')) has-error @endif">
                                            <label class="control-label" for="editEmployees">Employees</label>
                                            <select id="editEmployees" name="user_id" class="form-control">
                                                <option value="">Select Employee</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->user_id }}">{{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('user_id'))
                                                <span class="error" role="alert">{{ $errors->first('user_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group @if($errors->has('user_id')) has-error @endif">
                                            <label class="control-label" for="editMachine">Machines</label>
                                            <select id="editMachine" name="machine_id" class="form-control">
                                                <option value="">Select Machine</option>
                                                @foreach ($machines as $machine)
                                                    <option value="{{ $machine->id }}">{{ $machine->name }} ({{ $machine->registration }})</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('machine_id'))
                                                <span class="error" role="alert">{{ $errors->first('machine_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group @if($errors->has('description')) has-error @endif">
                                            <label class="control-label" for="edit_description">Description</label>
                                            <input type="text" id="edit_description" name="description" value="{{ old('description') }}" class="form-control"  required>
                                            @if($errors->has('description'))
                                                <span class="error" role="alert">{{ $errors->first('description') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group @if($errors->has('from_date')) has-error @endif">
                                            <label class="control-label" for="edit_from_date">From Date</label>
                                            <input type="date" id="edit_from_date" name="from_date" value="{{ old('from_date') }}" class="form-control" required>
                                            @if($errors->has('from_date'))
                                                <span class="error" role="alert">{{ $errors->first('from_date') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group @if($errors->has('to_date')) has-error @endif">
                                            <label class="control-label" for="edit_to_date">To Date</label>
                                            <input type="date" id="edit_to_date" name="to_date" value="{{ old('to_date') }}" class="form-control" required>
                                            @if($errors->has('to_date'))
                                                <span class="error" role="alert">{{ $errors->first('to_date') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="spinnerCheckgrid" style="font-size: 30px;position: absolute;z-index: 11;left: 50%;bottom: 22%; display: none;"><i class="fa fa-spinner fa-spin"></i></span>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('customScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script src="{{asset('assets/calendar/rescalendar.js')}}"></script>
    <script>
        var dbData = '{{ $data }}';
        dbData = dbData.replace(/&quot;/g, '"');
        dbData = $.parseJSON(dbData);
        
        var dynamicData = dbData;
        var dataFields = [];
        var dataImages = [];

        function search(event){
            var searchValue = $(event).val();
            var temp = dynamicData
            var search = new RegExp(searchValue , 'i');
            
            dynamicData = dbData.filter(element => search.test(element.name));
            if(dynamicData.length <= 0){
                toastr.error('No employee found');
                return;
            }
            dataFields = [];
            dataImages = [];
            getDataKeyValues(dynamicData);
            calendar();
            loadEvent();
            $('.employeeSearchCalender').val(searchValue).focus();
            return
        };

        function deleteLeave(event){
            var result = confirm("Are you sure you want to delete?");
            if (!result) {
                return;
            }
            $.ajax({
                type:'post',
                url : '{{ route("leave.management.delete") }}',
                data:{
                    id : $(event).attr('data_id')
                },
                dataType: 'json',
                success:function (response) {
                    if(response.status){
                        dbData = dbData.filter(element => {
                            if(element.id != $(event).attr('data_id')){
                                return element;
                            }
                        });

                        dynamicData = dbData;
                        toastr.success('Employee leave removed.');
                        dataFields = [];
                        dataImages = [];
                        getDataKeyValues(dynamicData);
                        calendar();
                        loadEvent();
                    }else{
                        toastr.error(response.errorMessage);
                    }
                }
            });
            
        }

        function editLeave(event) {
            $('.spinnerCheckgrid').css('display','');
            var url = '{{ route("leave.management.edit",":ID") }}';
            url = url.replace(':ID',$(event).attr('data_id'));
            $('#editEmployees option[value=""]').attr("selected",true);
            $('#editMachine option[value=""]').attr("selected",true);
            $('#editEmployees option').each(function() {
                $(this).attr('selected',false);
            });
            $('#editMachine option').each(function() {
                $(this).attr('selected',false);
            });
            $.ajax({
                type:'GET',
                url : url,
                dataType: 'json',
                success:function (response) {
                    console.log(response);  
                    $('.spinnerCheckgrid').css('display','none');
                    if(response.status){
                        $('#editEmployees option').each(function() {
                            if($(this).val() == response.employee_leave.user_id){
                                $(this).attr('selected','selected');
                            }
                        });
                        $('#editMachine option').each(function() {
                            if($(this).val() == response.employee_leave.machine_id){
                                $(this).attr('selected','selected');
                            }
                        });
                        $('.edit_employee_leave_id').val(response.employee_leave.id);
                        $('#edit_description').val(response.employee_leave.description);
                        $('#edit_from_date').val(response.employee_leave.from_date);
                        $('#edit_to_date').val(response.employee_leave.to_date);
                    }else{
                        toastr.error(response.errorMessage);
                    }
                }
            });
        }

        function calendar(){
            $('#my_calendar_simple').rescalendar({
                id: 'my_calendar_simple',
                format: 'YYYY-MM-DD',
                dataKeyField: 'name',
                dataKeyValues: dataFields,
                images:dataImages,
                data: dynamicData,
                onErrorImage: '{{asset("assets/dashboard/images/logoAvatar.png")}}',
                for: 'leave',
            });
        }

        function getDataKeyValues(dynamicData){
            for(i=0; i<dynamicData.length; i++) {
                if(!(dataFields.indexOf(dynamicData[i].name) !== -1)){
                    dataFields.push(dynamicData[i].name);
                    dataImages.push(dynamicData[i].image);
                }
            }
        }

        getDataKeyValues(dynamicData);
        calendar(); //inital load calendar

        $(window).on('load', function() {
            loadEvent();
        });

        function loadEvent() {
            $('.hasEvent').each(function(){
                var eventLength = $(this).attr('data-attr');
                var totalBoxes = $('.employee_leave_'+eventLength).length;
                var data = $('.employee_leave_'+eventLength).first().find('a').text();
                var oneBoxLength = 8;
                var trimmedStringTitle = data.substr(0, oneBoxLength * totalBoxes);
                $('.employee_leave_'+eventLength).first().find('a').html("&nbsp;"+trimmedStringTitle);
            });
            $('#my_calendar_simple').show();
        }
        
        function crossButtonHover(employee_leave_id){
            $('.employee_leave_'+employee_leave_id).last().find('i').show();
            $('.employee_leave_'+employee_leave_id).first().find('span').show();
        }

        function crossButtonHoverHide(employee_leave_id){
            $('.employee_leave_'+employee_leave_id).last().find('i').hide();
            $('.employee_leave_'+employee_leave_id).first().find('span').hide();
        }

        $('.modalClose').click(function(){
            $(this).closest('.modal').removeClass('in').hide();
        });

    </script>
@endsection