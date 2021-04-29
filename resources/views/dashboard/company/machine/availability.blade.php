@extends('layouts.companyDashboard')
@section('css')
    <link href="{{ asset('assets/calendar/rescalendar.css') }}" rel="stylesheet">
@endsection
@section('content')
    <section class="content-header rt-content-header">
        <h1>Machine Availability Management</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('leave_management.index') }}">Employee Management</a></li>
            <li class="active">Machine Availability Management</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    <div class="rtTab">
        <div class="rtTabContent">
            <div id="my_calendar_simple"></div>
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

        function calendar(){
            $('#my_calendar_simple').rescalendar({
                id: 'my_calendar_simple',
                format: 'YYYY-MM-DD',
                dataKeyField: 'name',
                dataKeyValues: dataFields,
                images:dataImages,
                data: dynamicData,
                onErrorImage: '{{asset("assets/dashboard/images/logoAvatar.png")}}',
                for: 'machine',
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
            $('.hasEvent').each(function(){
                var eventLength = $(this).attr('data-attr');
                var totalBoxes = $('.employee_leave_'+eventLength).length;
                var data = $('.employee_leave_'+eventLength).first().find('a').text();
                var oneBoxLength = 8;
                var trimmedStringTitle = data.substr(0, oneBoxLength * totalBoxes);
                $('.employee_leave_'+eventLength).first().find('a').html("&nbsp;"+trimmedStringTitle);
            });
            $('#my_calendar_simple').show();
        });
    
        function crossButtonHover(employee_leave_id){
            // $('.employee_leave_'+employee_leave_id).last().find('i').show();
            // var totalBoxes = $('.employee_leave_'+employee_leave_id).length;
            // var data = $('.employee_leave_'+employee_leave_id).first().find('a').text();
            // var oneBoxLength = 8;
            // var trimmedStringTitle = data.substr(0, oneBoxLength * totalBoxes);
            // $('.employee_leave_'+employee_leave_id).first().find('a').html("&nbsp;"+trimmedStringTitle);
            $('.employee_leave_'+employee_leave_id).first().find('span').show();
        }

        function crossButtonHoverHide(employee_leave_id){
            // $('.employee_leave_'+employee_leave_id).first().find('a').html(" "+$('.employee_leave_'+employee_leave_id).first().find('span').text());
            $('.employee_leave_'+employee_leave_id).first().find('span').hide();
        }

    </script>
@endsection