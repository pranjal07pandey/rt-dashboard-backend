@extends('layouts.companyDashboard')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/dashboard/calender/tui-calendar.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/dashboard/calender/tui-date-picker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/dashboard/calender/tui-time-picker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/dashboard/spectrum/spectrum.min.css') }}" />
    <style>
        .calendar-btn {
            border-radius: 25px;
            padding: 6px 15px;
            border: 1px solid #ddd;
            color: #333;
            margin-right: 5px;
        }
        .btn-group{
            margin: 0px;
        }
        .ui-tooltip {
            display: none;
        }

        .sp-replacer{
            width: 5rem;
        }

        hr{
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .existdata{
            background-color: red !important;
        }

        .check_availability{
            display:none;
        }

        .render-range{
            cursor: unset;
            pointer-events: none;
        }

        /* start here */
        /* .tui-full-calendar-timegrid-container{
            height: 215%;
            overflow-x: hidden;
            transform: rotate(90deg);
            overflow-y: scroll;
        }

        .tui-full-calendar-timegrid-hour span{
            position: absolute;
            text-align: right;
            line-height: 3px;
            transform: rotate(270deg);
            top: 15px;
            left: auto;
        } */
    </style>
@endsection
@section('content')
    <section class="content-header rt-content-header">
        <h1>Task Management</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('dockets.allDockets') }}">Docket List</a></li>
            <li class="active">Task Management</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')
    <div id="menu">
        <span id="menu-navi">
            <select class="viewType calendar-btn">
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month" selected>Month</option>
                <option value="2week">2 Week</option>
                <option value="3week">3 Week</option>
                <option value="Narrower">Narrower Weekends</option>
                <option value="hideWeekends">Hide Weekends</option>
                <option value="taskAndSchedule">Task & Schedule</option>
                {{-- <option value="TaskOnly">Task Only</option> --}}
                <option value="Theme">Theme</option>
            </select>
            <button type="button" class="btn btn-default calendar-btn btn-sm move-today" data-action="move-today" onclick="today();">Today</button>
            <button type="button" class="btn btn-default calendar-btn btn-sm move-day" data-action="move-prev" onclick="prev();">
                <i class="fa fa-angle-left" data-action="move-prev" style="font-size: 20px"></i>
            </button>
            <button type="button" class="btn btn-default calendar-btn btn-sm move-day" data-action="move-next" onclick="next()">
                <i class="fa fa-angle-right" data-action="move-next" style="font-size: 20px"></i>
            </button>
            <button id="renderRange" class="btn btn-default calendar-btn btn-sm render-range"></button>
            <button type="button" class="btn btn-default calendar-btn btn-sm check_availability">
                Check Availability
            </button>
        </span>
    </div>
  
    <div id="calendar" style="margin-bottom: 50px;"></div>

    <div class="modal fade eventModal" id="docketTempleteStore" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close eventClose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Event</h4>
                </div>

                {{ Form::open(['files' => true]) }}
                <div class="modal-body" style="height: 400px;overflow: auto;">
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="assignName" class="control-label">Task/Job Name</label>
                            <input type="text" id="assignName" class="form-control" name="name" placeholder="Name">
                            <span class="error nameError"></span>
                        </div>
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="machineList" class="control-label">Machine</label><br>
                            <select id="machineList" name="machineList" class="form-control multiselectMachine" multiple>
                                @if($machines)
                                    @foreach($machines as $row)
                                        <option data-img="{{ $row['image'] }}" value="{!! $row['id'] !!}">{!! $row['name'] !!} <img src="{{ $row['image'] }}" /></option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error machineError"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="employeeList" class="control-label">Employee</label><br>
                            <select id="employeeList" class="form-control multiselect" multiple>
                                @if($employees)
                                    @foreach($employees as $row)
                                        <option data-img="@isset($row['image']) {{ $row['image'] }} @endisset" value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error employeeError"></span>
                        </div>
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="docketTemplate" class="control-label">Docket Template</label><br>
                            <select id="docketTemplate" class="form-control multiselectTemplate" multiple>
                                @if($templates)
                                    @foreach($templates as $row)
                                        <option value="{!! $row->id !!}">{!! $row->title !!}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error docketError"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="templateId" class="control-label">Event Color</label> &nbsp;&nbsp;
                            <input type="color" class="col-md-6 form-control color-picker" name="bgcolor" placeholder="Name" style="width: 10%" value="#022e55">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="assignStartDate" class="control-label">Start Date</label>
                            <input type="datetime-local" id="assignStartDate" class="form-control" name="start_date" placeholder="Start Date">
                            <span class="error startDateError"></span>
                        </div>
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="assignEndDate" class="control-label">End Date</label><br>
                            <input type="datetime-local" id="assignEndDate" class="form-control" name="end_date" placeholder="End Date">
                            <span class="error endDateError"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12" style="margin-top:0px;">
                            <textarea name="comment" class="form-control" placeholder="Comment"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <br/><br/>
                    <button type="button" class="btn btn-primary eventSaveModel">Save</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade eventUpdateModal" id="docketTempleteUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close eventUpdateClose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Update Event</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/docketManager/assignDocket/update', 'files' => true]) }}
                <div class="modal-body" style="height: 380px;overflow: auto;">
                    <input type="hidden" class="form-control" name="id">
                    <input type="hidden" class="form-control" name="calendarId">
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="templateId" class="control-label">Task/Job Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Name">
                            <span class="error nameError"></span>
                        </div>
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="updateMachineList" class="control-label">Machine</label><br>
                            <select id="updateMachineList" class="form-control multiselectMachine" multiple>
                                @if($machines)
                                    @foreach($machines as $row)
                                        <option data-img="{{ $row['image'] }}" value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error machineError"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="updateEmployeeList" class="control-label">Employee</label><br>
                            <select id="updateEmployeeList" class="form-control multiselect" multiple>
                                @if($employees)
                                    @foreach($employees as $row)
                                        <option data-img="@isset($row['image']) {{ $row['image'] }} @endisset" value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error employeeError"></span>
                        </div>
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="updateDocketTemplate" class="control-label">Docket Template</label><br>
                            <select id="updateDocketTemplate" class="form-control multiselectTemplate" multiple>
                                @if($templates)
                                    @foreach($templates as $row)
                                        <option value="{!! $row->id !!}">{!! $row->title !!}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error docketError"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="templateId" class="control-label">Event Color</label>
                            <input type="color" class="col-md-6 form-control color-picker" name="bgcolor" placeholder="Name" style="width: 10%" value="#022e55">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label for="templateId" class="control-label">Start Date</label>
                            <input type="datetime-local" class="form-control" name="start_date" placeholder="Start date">
                            <span class="error startDateError"></span>
                        </div>
                        <div class="form-group col-lg-6 col-md-6" style="margin-top:0px;">
                            <label class="control-label">End Date</label><br>
                            <input type="datetime-local" class="form-control" name="end_date" placeholder="End date">
                            <span class="error endDateError"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12" style="margin-top:0px;">
                            <textarea name="comment" class="form-control" placeholder="Comment"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <br/><br/>
                    <button type="button" class="btn btn-primary eventUpdateModel">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="workDayView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close workDayViewClose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Machine and Employee Availability</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/docketManager/assignDocket/update', 'files' => true]) }}
                <div class="modal-body" style="height: 570px;overflow: auto;">
                    <div class="workView row"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('customScript')
    <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('assets/dashboard/plugins/timepicker/Moment.js') }}"></script>
    <script src="{{ asset('assets/dashboard/calender/tui-code-snippet.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/calender/tui-time-picker.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/calender/tui-date-picker.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/calender/tui-calendar.js') }}"></script>
    <script src="{{ asset('assets/dashboard/spectrum/spectrum.min.js') }}"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>

        var templateData = '{{ $templates }}';
        templateData = templateData.replace(/&quot;/g, '"');
        templateData = $.parseJSON(templateData);

        var calenderData = '{{ $calender_data }}';
        calenderData = calenderData.replace(/&quot;/g, '"');
        calenderData = $.parseJSON(calenderData);
        var defaultImage = '{{ asset("/user.png") }}';

        $(document).ready(function(){
            $('.multiselect').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                nonSelectedText: 'Select Employee',
                enableHTML: true,
                optionLabel: function(element) {
                    return '<img src="'+$(element).attr('data-img')+'" alt="" style="width: 50px;height: 40px;border-radius: 50%;float: right;margin: -10px 15px 15px 0px;" > '+$(element).text();
                },
            });
            $('.multiselectTemplate').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                nonSelectedText: 'Select Template',
            });
            $('.multiselectMachine').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                nonSelectedText: 'Select Machine',
                enableHTML: true,
                optionLabel: function(element) {
                    return '<img src="'+$(element).attr('data-img')+'" alt="" style="width: 50px;height: 40px;border-radius: 50%;float: right;margin: -10px 15px 15px 0px;"> '+$(element).text();
                },
            });
            $('.color-picker').spectrum({
                type: "component"
            });
        });

        $('.check_availability').click(function () {
            $('#workDayView').addClass('in').show();
        })

        function next(){
            calendar.next();
            if(calendar.getViewName() == "day"){
                dayViewCall();
            }
            getDateValue();
        }

        function prev(){
            calendar.prev();
            if(calendar.getViewName() == "day"){
                dayViewCall();
            }
            getDateValue();
        }

        function today() {
            calendar.today();
            getDateValue();
        }

        function getDateValue(){
            var startDate = calendar.getDateRangeStart();
            var endDate = calendar.getDateRangeEnd();
            if(viewName == "day"){
                $('#renderRange').html(moment(endDate._date).format('YYYY MMM-DD'));
            }else if(viewName == "week"){
                $('#renderRange').html(moment(startDate._date).format('YYYY MMM-DD') + ' ~ ' + moment(endDate._date).format('MMM-DD'));
            }else if(viewName == "2week"){
                $('#renderRange').html(moment(startDate._date).format('YYYY MMM-DD') + ' ~ ' + moment(endDate._date).format('MMM-DD'));
            }else if(viewName == "3week"){
                $('#renderRange').html(moment(startDate._date).format('YYYY MMM-DD') + ' ~ ' + moment(endDate._date).format('MMM-DD'));
            }else if(viewName == "hideWeekends"){
               $('#renderRange').html(moment(startDate._date).format('YYYY MMM-DD') + ' ~ ' + moment(endDate._date).format('MMM-DD'));
            }else if(viewName == "taskAndSchedule"){
               $('#renderRange').html(moment(startDate._date).format('YYYY MMM-DD') + ' ~ ' + moment(endDate._date).format('MMM-DD'));
            }else{
                $('#renderRange').html(moment(endDate._date).format('YYYY MMM'));
            }
        }
        
        function dayViewCall() {            
            $('.check_availability').show();
            var date_view = calendar.getDate();
            date_view = moment(date_view._date).format('YYYY-MM-DDTHH:mm')
            var url = '{{ route("assign.docket.day.view",":ID") }}';
            url = url.replace(':ID',date_view);
            $.ajax({
                type:'get',
                url : url,
                data:{
                    date_view: date_view
                },
                dataType: 'json',
                success:function (response) {
                    $('.workView').html("");
                    if(response.status){
                        console.log(response);
                        // Load google charts
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(drawChart);

                        // Draw the chart and set the chart values
                        function drawChart() {
                            // var temp = [];
                            // temp.push(['Task' , 'Hours per Day']);
                            // temp.push(['Free' , 24 - response.totalHourperDay]);

                            // var noWork = 24 - response.totalHourperDay;
                            $('.workView').append('<div class="machineWorkView"></div>')
                            $('.workView').append('<div class="employeeWorkView"></div>')
                            response.machinesResult.forEach(element => {
                                var work = element.minutes / 60;
                                if(work >= 24){
                                    var free =  0;
                                    // noWork = 0;
                                }else{
                                    var free = response.totalHourperDay - work;
                                    if(free < 0){
                                        free = 0;
                                    }
                                }
                                var data = google.visualization.arrayToDataTable([
                                    ['Task', 'Hours per Day'],
                                    ['Work', work],
                                    ['Free', free],
                                    // ['No Work', noWork]
                                ]);
                                $('.machineWorkView').append(`<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="piechart_machine_${element.id}"></div>`);

                                // Optional; add a title and set the width and height of the charts
                                var options = {'title':element.name, 'width':250, 'height':180};

                                // Display the chart inside the <div> element with id="piechart"
                                var chart = new google.visualization.PieChart(document.getElementById('piechart_machine_'+element.id));
                                chart.draw(data, options);
                                $(`#piechart_machine_${element.id}`).append(`<img src="${element.image}" class="workViewImg" alt="" onerror="this.src='/assets/dashboard/images/logoAvatar.png'">`);
                                // temp.push([element.name , element.minutes / 60]);
                            });
                            response.userResult.forEach(element => {
                                var work = element.minutes / 60;
                                if(work >= 24){
                                    var free =  0;
                                    // noWork = 0;
                                }else{
                                    var free = response.totalHourperDay - work;
                                    if(free < 0){
                                        free = 0;
                                    }
                                }
                                var data = google.visualization.arrayToDataTable([
                                    ['Task', 'Hours per Day'],
                                    ['Work', work],
                                    ['Free', free],
                                    // ['No Work', noWork]
                                ]);

                                $('.employeeWorkView').append(`<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" id="piechart_employee_${element.id}"></div>`);

                                // Optional; add a title and set the width and height of the charts
                                var options = {'title':element.name, 'width':250, 'height':180};

                                // Display the chart inside the <div> element with id="piechart"
                                var chart = new google.visualization.PieChart(document.getElementById('piechart_employee_'+element.id));
                                chart.draw(data, options);
                                $(`#piechart_employee_${element.id}`).append(`<img src="/${element.image}" class="workViewImg" alt="" onerror="this.src='/assets/dashboard/images/logoAvatar.png'">`);
                                // temp.push([element.name , element.minutes / 60]);
                            });
                            // var data = google.visualization.arrayToDataTable( temp );

                            // $('.workView').append(`<div id="piechart_machine"></div>`);

                            // // Optional; add a title and set the width and height of the chart
                            // var options = {'title':'Machine', 'width':550, 'height':350};

                            // // Display the chart inside the <div> element with id="piechart"
                            // var chart = new google.visualization.PieChart(document.getElementById('piechart_machine'));
                            // chart.draw(data, options);
                        }
                    }else{
                        alert('error');
                    }
                },
                error:function (response) {
                    
                }
            });
        }

        function hide(event){
            $(event).closest('.tui-full-calendar-floating-layer').hide();
        }

        function defaultColor(){
            $('input[name="bgcolor"]').val('#022e55');
            $(".color-picker").spectrum({
                color: '#022e55'
            });
        }

        function modalClear(){
            $('input[name="name"]').val('');
            $('input[name="start_date"]').val('');
            $('input[name="end_date"]').val('');
            $('input[name="bgcolor"]').val('');
            $('textarea[name="comment"]').val('');

            $("#machineList option:selected").prop("selected", false);
            $("#employeeList option:selected").prop("selected", false);
            $("#docketTemplate option:selected").prop("selected", false);
            $("#updateMachineList option:selected").prop("selected", false);
            $("#updateEmployeeList option:selected").prop("selected", false);
            $("#updateDocketTemplate option:selected").prop("selected", false);

            $('#machineList').multiselect('rebuild');
            $('#employeeList').multiselect('rebuild');
            $('#docketTemplate').multiselect('rebuild');
            $('#updateMachineList').multiselect('rebuild');
            $('#updateEmployeeList').multiselect('rebuild');
            $('#updateDocketTemplate').multiselect('rebuild');
        }
        // register templates
        var templates = {
            popupIsAllDay: function() {
            return 'All Day';
            },
            popupStateFree: function() {
            return 'Free';
            },
            popupStateBusy: function() {
            return 'Busy';
            },
            titlePlaceholder: function() {
            return 'Title';
            },
            locationPlaceholder: function() {
            return 'Location';
            },
            startDatePlaceholder: function() {
            return 'Start date';
            },
            endDatePlaceholder: function() {
            return 'End date';
            },
            popupSave: function() {
            return 'Save';
            },
            popupUpdate: function() {
            return 'Update';
            },
            task: function(schedule) {
                return '#' + schedule.title;
            },
            popupDetailDate: function(isAllDay, start, end,e) {
            var isSameDate = moment(start._date).isSame(end._date);
            var endFormat = (isSameDate ? '' : 'YYYY.MM.DD ') + 'hh:mm a';

            if (isAllDay) {
                return moment(start._date).format('YYYY.MM.DD') + (isSameDate ? '' : ' - ' + moment(end._date).format('YYYY.MM.DD'));
            }
            return (moment(start._date).format('YYYY.MM.DD hh:mm a') + ' - ' + moment(end._date).format(endFormat));
            },
            popupDetailLocation: function(schedule) {
            return 'Location : ' + schedule.location;
            },
            popupDetailUser: function(schedule) {
            return 'User : ' + (schedule.attendees || []).join(', ');
            },
            popupDetailState: function(schedule) {
            if(schedule.state == 0){
                var stateValue = 'Inactive';
                var stateColor = '#ff0000';
            }else if(schedule.state == 1){
                var stateValue = 'Active';
                var stateColor = '#f5c90d';
            }else{
                var stateValue = 'Completed';
                var stateColor = '#008000';
            }
            return '<span class="tui-full-calendar-icon tui-full-calendar-calendar-dot" style="background-color: '+stateColor+'"></span>Status : ' + stateValue + '<hr>' || 'Busy';
            },
            popupDetailRepeat: function(schedule) {
            return 'Repeat : ' + schedule.recurrenceRule;
            },
            popupDetailBody: function(schedule) {
                console.log(schedule);
                var docket = '';
                schedule.raw.docketTemplate.forEach(element => {
                    templateData.forEach(template => {
                        if(element == template.id){
                            var url = createDocket.replace(":ID",element);
                            docket += `<div style="display: inline-flex;width:100%">
                                            <p style="width: 100%;word-break: break-all;">${template.title}</p>
                                            <form action="${url}" method="POST" target="_blank" style="text-align: right;display: contents;">
                                                <input type="hidden" name="_token" value="${csrfToken}">
                                                <input type="hidden" name="assign_docket_id" value="${schedule.id}">
                                                <input type="hidden" name="docketTemplate" value="${JSON.stringify(schedule.raw.docketTemplate)}">
                                                <input type="hidden" name="employeeList" value="${JSON.stringify(schedule.raw.employeeList)}">
                                                <input type="hidden" name="machineList" value="${JSON.stringify(schedule.raw.machineList)}">
                                                <button class="tui-full-calendar-popup-docket" onclick="hide(this)" style="text-align: right">
                                                    <span class="tui-full-calendar-icon tui-full-calendar-ic-state"></span>
                                                    <span class="tui-full-calendar-content">Docket</span>
                                                </button>
                                            </form>
                                        </div>`;
                        }
                    });
                });
                var machine = '';
                if(schedule.raw.machineDetail.length > 0){
                    schedule.raw.machineDetail.forEach(element => {
                        machine += `<div style="height:52px"><label>${element.name}</label><img src="${element.image}" 
                                style="width:50px;height:50px;border-radius: 50%;float: right;" onerror="this.src='/assets/dashboard/images/logoAvatar.png'"/></div>`;
                    });
                }
                var employee = '';
                if(schedule.raw.userDetail.length > 0){
                    schedule.raw.userDetail.forEach(element => {
                        employee += `<div style="height:52px"><label>${element.name}</label><img src="${element.image}" 
                                style="width:50px;height:50px;border-radius: 50%;float: right;" onerror="this.src='/assets/dashboard/images/logoAvatar.png'"/></div>`;
                    });
                }
                var comment = (schedule.raw.comment) ? "<p>" + schedule.raw.comment + "</p><hr>" : '';
                docket = (docket) ? docket + "<hr>" : '';
                machine = (machine) ? "<div>" + machine + "</div>" : '';
                employee = (employee) ? "<div>" + employee + "</div>" : '';

                return comment + docket + machine + employee;
            // return 'Body : ' + schedule.body;
            },
            popupEdit: function() {
            return 'Edit';
            },
            popupDelete: function() {
            return 'Delete';
            },
        };

        var calendar = new tui.Calendar('#calendar', {
            defaultView: 'month',
            template: templates,
            useCreationPopup: false,
            useDetailPopup: true,
        });

        var viewName = '';
        $('.viewType').change(function(){
            $('.check_availability').hide();
            viewName = $(this).val();
            if($(this).val() == 'day'){
                dayViewCall();
                calendar.changeView('day', true);
            }else if($(this).val() == 'week'){
                calendar.changeView('week', true);
            }else if($(this).val() == '2week'){
                calendar.setOptions({month: {visibleWeeksCount: 2}}, true);
                calendar.changeView('month', true);
            }else if($(this).val() == '3week'){
                calendar.setOptions({month: {visibleWeeksCount: 3}}, true);
                calendar.changeView('month', true);
            }else if($(this).val() == 'Narrower'){
                calendar.setOptions({month: {narrowWeekend: true}}, true);
                calendar.setOptions({week: {narrowWeekend: true}}, true);
                calendar.changeView(calendar.getViewName(), true);
            }else if($(this).val() == 'hideWeekends'){
                calendar.setOptions({week: {workweek: true}}, true);
                calendar.setOptions({month: {workweek: true}}, true);
                calendar.changeView(calendar.getViewName(), true);
            }else if($(this).val() == 'taskAndSchedule'){
                calendar.setOptions({taskView: true}, true);
                calendar.changeView('week', true);
            }else if($(this).val() == 'TaskOnly'){
                calendar.setOptions({taskView: ['task'],scheduleView: false}, true);
                calendar.changeView('week', true);
            }else if($(this).val() == 'Theme'){
                calendar.setTheme(COMMON_CUSTOM_THEME);
                calendar.setOptions({month: {workweek: false}}, true);
                calendar.setOptions({month: {visibleWeeksCount: null}}, true);
                calendar.changeView('month', true);
            }else{
                // calendar.setTheme(MONTHLY_CUSTOM_THEME);
                calendar.setOptions({month: {workweek: false}}, true);
                calendar.setOptions({month: {visibleWeeksCount: null}}, true);
                calendar.changeView('month', true);
            }
            getDateValue();
        });
        
        var createDocket = '{{ route("dockets.assign.draftEdit",":ID") }}';
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // event handlers
        calendar.on({
            'clickSchedule': function(e) {
                console.log('clickSchedule', e);
            },
            'beforeCreateSchedule': function(e) {
                console.log('beforeCreateSchedule', e);

                var startTime = e.start;
                var endTime = e.end;
                var isAllDay = e.isAllDay;
                var guide = e.guide;
                var triggerEventName = e.triggerEventName;
                var schedule;
                if (triggerEventName === 'click') {
                    schedule = {};
                } else if (triggerEventName === 'dblclick') {
                    $('.eventModal').find('input[name="start_date"]').val(moment(startTime._date).format('YYYY-MM-DDTHH:mm'));
                    $('.eventModal').find('input[name="end_date"]').val(moment(endTime._date).format('YYYY-MM-DDTHH:mm'));
                    $('.eventModal').addClass('in').show();
                }

                e.guide.clearGuideElement();
            },
            'beforeUpdateSchedule': function(e) {
                console.log('beforeUpdateSchedule', e);
                var triggerEventName = e.triggerEventName;
                if (triggerEventName === 'click') {
                    $(".color-picker").spectrum({
                        color: e.schedule.bgColor
                    });

                    $('.eventUpdateModal').find('input[name="id"]').val(e.schedule.id);
                    $('.eventUpdateModal').find('input[name="calendarId"]').val(e.schedule.calendarId);

                    $('.eventUpdateModal').find('input[name="bgcolor"]').val(e.schedule.bgColor);
                    $('.eventUpdateModal').find('input[name="name"]').val(e.schedule.title);
                    $('.eventUpdateModal').find('textarea[name="comment"]').val(e.schedule.raw.comment);
                    $('.eventUpdateModal').find('input[name="start_date"]').val(moment(e.schedule.start._date).format('YYYY-MM-DDTHH:mm'));
                    $('.eventUpdateModal').find('input[name="end_date"]').val(moment(e.schedule.end._date).format('YYYY-MM-DDTHH:mm'));

                    $('#updateMachineList option').each(function(){
                        e.schedule.raw.machineList.forEach(element => {
                            if($(this).val() == element){
                                $('#updateMachineList').multiselect('select', element);
                            }
                        });
                    });

                    $('#updateEmployeeList option').each(function(){
                        e.schedule.raw.employeeList.forEach(element => {
                            if($(this).val() == element){
                                $('#updateEmployeeList').multiselect('select', element);
                            }
                        });
                    });

                    $('#updateDocketTemplate option').each(function(){
                        e.schedule.raw.docketTemplate.forEach(element => {
                            if($(this).val() == element){
                                $('#updateDocketTemplate').multiselect('select', element);
                            }
                        });
                    });

                    $('.eventUpdateModal').addClass('in').show();
                }else{
                    e.schedule.start = e.start;
                    e.schedule.end = e.end;
                    calendar.updateSchedule(e.schedule.id, e.schedule.calendarId, e.schedule);

                    $.ajax({
                        type:'post',
                        url : '{{ route("assign.docket.update") }}',
                        data:{
                            id: e.schedule.id,
                            name : e.schedule.title,
                            start_date : moment(e.schedule.start._date).format('YYYY-MM-DDTHH:mm'),
                            end_date : moment(e.schedule.end._date).format('YYYY-MM-DDTHH:mm'),
                            bgcolor : e.schedule.bgColor,
                            body: 'body',
                            machineList : e.schedule.raw.machineList,
                            employeeList : e.schedule.raw.employeeList,
                            docketTemplate : e.schedule.raw.docketTemplate,
                        },
                        dataType: 'json',
                        success:function (response) {
                            if(response.status){
                               
                            }else{
                                e.schedule.start = moment(response.data.from_date).format('YYYY-MM-DD hh:mm A');
                                e.schedule.end = moment(response.data.to_date).format('YYYY-MM-DD hh:mm A');
                                calendar.updateSchedule(e.schedule.id, e.schedule.calendarId, e.schedule);
                                alert(response.error);
                            }
                        }
                    });
                }
            },
            'beforeDeleteSchedule': function(e) {
                console.log('beforeDeleteSchedule', e);
                
                var result = confirm("Are you sure you want to unassign this docket template?");
                if (result) {
                    $.ajax({
                        type:'post',
                        url : '{{ route("assign.docket.delete") }}',
                        data:{
                            id: e.schedule.id
                        },
                        dataType: 'json',
                        success:function (response) {
                            console.log(response);
                            if(response.status){
                                calendar.deleteSchedule(e.schedule.id, e.schedule.calendarId);
                            }else{
                                alert('error');
                            }
                        }
                    });
                }
            }
        });

        var counter = 1;

        $('.eventSaveModel').click(function(){
            var name = $(this).closest('form').find('input[name="name"]').val();
            var start_date = $(this).closest('form').find('input[name="start_date"]').val();
            var end_date = $(this).closest('form').find('input[name="end_date"]').val();
            var bgcolor = $(this).closest('form').find('input[name="bgcolor"]').val();
            var comment = $(this).closest('form').find('textarea[name="comment"]').val();
            var machineList = $('#machineList').val();
            var employeeList = $('#employeeList').val();
            var docketTemplate = $('#docketTemplate').val();
            var reponseStatus = validation(this,name,start_date,end_date,machineList,employeeList,docketTemplate);
            if(!reponseStatus){
                return;
            }
            var event = this;
            $.ajax({
                type:'post',
                url : '{{ route("assign.docket.store") }}',
                data:{
                    name : name,
                    start_date : start_date,
                    end_date : end_date,
                    bgcolor : bgcolor,
                    machineList : machineList,
                    employeeList : employeeList,
                    docketTemplate : docketTemplate,
                    comment: comment,
                },
                dataType: 'json',
                success:function (response) {
                    if(response.status){
                        var temp = [];
                        temp['comment'] = comment;
                        temp['machineList'] = machineList;
                        temp['employeeList'] = employeeList;
                        temp['docketTemplate'] = docketTemplate;
                        temp['machineDetail'] = response.machineDetail;
                        temp['userDetail'] = response.userDetail;
                        calendar.createSchedules(
                            [{
                                id: response.assign_docket_id,
                                calendarId: '1',
                                title: name,
                                category: 'time',
                                start: moment(start_date).format('YYYY-MM-DD hh:mm A'),
                                end: moment(end_date).format('YYYY-MM-DD hh:mm A'),
                                bgColor: bgcolor,
                                body:'body',
                                state: '0',
                                raw: temp
                            }]
                        );
                        
                        $('.eventClose').click();
                    }else{
                        if(response.type == "machine"){
                            $('#machineList').closest('div').find('input[type="checkbox"]').each(function(){
                                if($(this).val() == response.value){
                                    $(this).closest('a').addClass('existdata');
                                }
                            });
                        }else if(response.type == "employee"){
                            $('#employeeList').closest('div').find('input[type="checkbox"]').each(function(){
                                if($(this).val() == response.value){
                                    $(this).closest('a').addClass('existdata');
                                }
                            });
                        }
                        alert(response.error);
                    }
                },
                error:function (response) {
                    if(response.responseJSON.errors.name){
                        $(event).closest('form').find('.nameError').html('End date should be greater than Start date.');
                    }else if(response.responseJSON.errors.start_date){
                        $(event).closest('form').find('.startDateError').html('End date should be greater than Start date.');
                    }else if(response.responseJSON.errors.end_date){
                        $(event).closest('form').find('.endDateError').html('End date should be greater than Start date.');
                    }else{
                        alert('error');
                    }
                }
            });
        });

        $('.eventUpdateModel').click(function(){
            var scheduleId = $(this).closest('form').find('input[name="id"]').val();
            var calendarId = $(this).closest('form').find('input[name="calendarId"]').val();

            var name = $(this).closest('form').find('input[name="name"]').val();
            var start_date = $(this).closest('form').find('input[name="start_date"]').val();
            var end_date = $(this).closest('form').find('input[name="end_date"]').val();
            var bgcolor = $(this).closest('form').find('input[name="bgcolor"]').val();
            var comment = $(this).closest('form').find('textarea[name="comment"]').val();
            var machineList = $('#updateMachineList').val();
            var employeeList = $('#updateEmployeeList').val();
            var docketTemplate = $('#updateDocketTemplate').val();

            var reponseStatus = validation(this,name,start_date,end_date,machineList,employeeList,docketTemplate);
            if(!reponseStatus){
                return;
            }
            var event = this;
            $.ajax({
                type:'post',
                url : '{{ route("assign.docket.update") }}',
                data:{
                    id: scheduleId,
                    name : name,
                    start_date : start_date,
                    end_date : end_date,
                    bgcolor : bgcolor,
                    body: 'body',
                    machineList : machineList,
                    employeeList : employeeList,
                    docketTemplate : docketTemplate,
                },
                dataType: 'json',
                success:function (response) {
                    if(response.status){
                        var temp = [];
                        temp['comment'] = comment;
                        temp['machineList'] = machineList;
                        temp['employeeList'] = employeeList;
                        temp['docketTemplate'] = docketTemplate;
                        temp['machineDetail'] = response.machineDetail;
                        temp['userDetail'] = response.userDetail;
                        calendar.updateSchedule(parseInt(scheduleId), calendarId.toString(), {
                            title: name,
                            start: new Date(start_date),
                            end: new Date(end_date),
                            category: 'time',
                            bgColor: bgcolor,
                            raw: temp
                        });

                        $('.eventUpdateClose').click();
                    }else{
                        if(response.type == "machine"){
                            $('#updateMachineList').closest('div').find('input[type="checkbox"]').each(function(){
                                if($(this).val() == response.value){
                                    $(this).closest('a').addClass('existdata');
                                }
                            });
                        }else if(response.type == "employee"){
                            $('#updateEmployeeList').closest('div').find('input[type="checkbox"]').each(function(){
                                if($(this).val() == response.value){
                                    $(this).closest('a').addClass('existdata');
                                }
                            });
                        }
                        alert(response.error);
                    }
                },
                error:function (response) {
                    if(response.responseJSON.errors.name){
                        $(event).closest('form').find('.nameError').html('End date should be greater than Start date.');
                    }else if(response.responseJSON.errors.start_date){
                        $(event).closest('form').find('.startDateError').html('End date should be greater than Start date.');
                    }else if(response.responseJSON.errors.end_date){
                        $(event).closest('form').find('.endDateError').html('End date should be greater than Start date.');
                    }else{
                        alert('error');
                    }
                }
            });
        });

        $('#machineList').change(function () {
            $(this).closest('div').find('input[type="checkbox"]').each(function(){
                if($(this).closest('a').hasClass('existdata')){
                    $(this).closest('a').removeClass('existdata');
                }
            });
        });

        $('#employeeList').change(function () {
            $(this).closest('div').find('input[type="checkbox"]').each(function(){
                if($(this).closest('a').hasClass('existdata')){
                    $(this).closest('a').removeClass('existdata');
                }
            });
        });

        $('#updateMachineList').change(function () {
            $(this).closest('div').find('input[type="checkbox"]').each(function(){
                if($(this).closest('a').hasClass('existdata')){
                    $(this).closest('a').removeClass('existdata');
                }
            });
        });

        $('#updateEmployeeList').change(function () {
            $(this).closest('div').find('input[type="checkbox"]').each(function(){
                if($(this).closest('a').hasClass('existdata')){
                    $(this).closest('a').removeClass('existdata');
                }
            });
        });
        

        function validation(event,name,start_date,end_date,machineList,employeeList,docketTemplate){
            var status = true;
            // var typeStatus = true;
            $(event).closest('form').find('.error').html("");
            if(!name){
                $(event).closest('form').find('.nameError').html('This field is required.');
                status = false;
            }
            if(!start_date){
                $(event).closest('form').find('.startDateError').html('This field is required.');
                status = false;
            }
            if(!end_date){
                $(event).closest('form').find('.endDateError').html('This field is required.');
                status = false;
            }
            if(start_date && end_date){
                if ((Date.parse(end_date) <= Date.parse(start_date))) {
                    $(event).closest('form').find('.endDateError').html('End date should be greater than Start date.');
                    status = false;
                }
            }
            // if(machineList.length <= 0 && employeeList.length <= 0){
            //     typeStatus = false;
            // }
            // if(docketTemplate.length <= 0){
            //     $(event).closest('form').find('.docketError').html('This field is required.');
            //     status = false;
            // }
            // if(!typeStatus){
            //     $(event).closest('form').find('.machineError').html('This field is required.');
            //     status = false;
            // }
            return status;
        }

        $('input').focus(function(){
            $(this).closest('div').find('.error').html("");
        });
        
        $('select').click(function(){
            $(this).closest('div').find('.error').html("");
        });

        $('.eventClose').click(function(){
            defaultColor();
            modalClear();
            $(this).closest('.eventModal').removeClass('in').hide();
        }); 

        $('.eventUpdateClose').click(function(){
            defaultColor();
            modalClear();
            $(this).closest('.eventUpdateModal').removeClass('in').hide();
        }); 

        $('.workDayViewClose').click(function(){
            $(this).closest('#workDayView').removeClass('in').hide();
        }); 

        calendar.createSchedules(calenderData);

        getDateValue();

        var COMMON_CUSTOM_THEME = {
            'common.border': '1px solid #ffbb3b',
            'common.backgroundColor': '#ffbb3b0f',
            'common.holiday.color': '#f54f3d',
            'common.saturday.color': '#3162ea',
            'common.dayname.color': '#333'
        };

        var MONTHLY_CUSTOM_THEME = {
            // month header 'dayname'
            'month.dayname.height': '42px',
            'month.dayname.borderLeft': 'none',
            'month.dayname.paddingLeft': '8px',
            'month.dayname.paddingRight': '0',
            'month.dayname.fontSize': '13px',
            'month.dayname.backgroundColor': 'inherit',
            'month.dayname.fontWeight': 'normal',
            'month.dayname.textAlign': 'left',

            // month day grid cell 'day'
            'month.holidayExceptThisMonth.color': '#f3acac',
            'month.dayExceptThisMonth.color': '#bbb',
            'month.weekend.backgroundColor': '#fafafa',
            'month.day.fontSize': '16px',

            // month schedule style
            'month.schedule.borderRadius': '5px',
            'month.schedule.height': '18px',
            'month.schedule.marginTop': '2px',
            'month.schedule.marginLeft': '10px',
            'month.schedule.marginRight': '10px',

            // month more view
            'month.moreView.boxShadow': 'none',
            'month.moreView.paddingBottom': '0',
            'month.moreView.border': '1px solid #060606',
            'month.moreView.backgroundColor': '#fbf8f8',
            'month.moreViewTitle.height': '28px',
            'month.moreViewTitle.marginBottom': '0',
            'month.moreViewTitle.backgroundColor': '#f4f4f4',
            'month.moreViewTitle.borderBottom': '1px solid #ddd',
            'month.moreViewTitle.padding': '0 10px',
            'month.moreViewList.padding': '10px'
        };
    </script>
@endsection