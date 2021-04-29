@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Docket Book Manager
            <small>Add/View Docket</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Docket Book Manager</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <form action="#" id="myForm" role="form" data-toggle="validator" method="post" accept-charset="utf-8">
        <!-- SmartWizard html -->
                    <div id="smartwizard">
                        <ul>
                            <li><a href="#step-1" style="padding: 10px 31px ;">Step 1<br /><small>Assign Employee</small></a></li>
                            <li><a href="#step-2" style="padding: 10px 31px ;">Step 2<br /><small>Docket Template</small></a></li>
                            <li><a href="#step-3" style="padding: 10px 31px ;">Step 3<br /><small>Select Recipient</small></a></li>
                            <li><a href="#step-4" id="docketOnchangeTemplate" data-content-url="" style="padding: 10px 31px ;">Step 4<br /><small>New Docket</small></a></li>
                        </ul>

                        <div>

                            <div id="step-1">
                                <div class="text-center">
                                    <h4><b>Do you want to assign an employee?</b></h4>
                                    <button class="btn btn-success btn-xs btn-raised" id="yes">Yes</button>&nbsp;&nbsp;<button class="btn btn-danger btn-xs btn-raised" id="no">No</button>
                                </div>

                                <div style="display: none;" id="form-step-0" role="form" class="employeeAssign" data-toggle="validator">
                                    {{--<h3 class="text-center" style="font-weight: 800;" >Select Docket Template</h3>--}}
                                    <div class="form-group">
                                        <select id="frameworkemployee" class="form-control docketTemplete"  required  name="employe[]" >
                                            @if(@$employees)
                                                @foreach ($employees as $row)
                                                    @if($row->userInfo->isActive==1)
                                                        <option value="{!! $row['id'] !!}">{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                            </div>

                            <div id="step-2">


                                <div  id="form-step-1" role="form" data-toggle="validator">
                                    <h3 class="text-center" style="font-weight: 800;" >Select Docket Template</h3>
                                    <div class="form-group">
                                            <select id="framework" class="form-control docketTemplete"  required  name="templete" >
                                                @if(@$dockets)
                                                    @foreach ($dockets as $row)


                                                        <option value="{!! $row->id !!}" id="email">{!! $row->title !!}</option>

                                                    @endforeach
                                                @endif
                                            </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                            </div>
                            <div id="step-3">
                                <h3 class="text-center" style="font-weight: 800;">Who will you send this to?</h3>
                                <div id="form-step-2" role="form" data-toggle="validator">
                                    <div class="form-group">
                                        <select id="frameworks" class="form-control" required multiple name="recipient[]">
                                            {{--<option value="">Select Employee</option>--}}
                                            @if(@$employees)
                                                @foreach ($employees as $row)
                                                    @if($row->userInfo->isActive==1)
                                                    <option value="{!! $row['id'] !!}">{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="step-4">
                                <h3 class="text-center"  style="font-weight: 800;">Complete the docket below</h3>
                                <div id="form-step-3" role="form" data-toggle="validator">
                                    <div id="template" class="form-group">

                                        @if($docketField)
                                            @foreach($docketField as $row)
                                        <div class="row">
                                                @include('dashboard.company.docketManager.sendDocketTemplate')
                                        </div>
                                            @endforeach
                                        @endif



                                            <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
    </form>
              <br><br>
@endsection
@section('customScript')
    <style>
        .sw-btn-group-extra{
            float: right !important;
        }

         .pdf {

             list-style-type: none;
             margin: 0;
             padding: 0;
             margin-top: 2px;
         }

        .pdf li {
            display: inline-block;
            font-size: 12px;
            text-align: center;
            padding-right: 15px;

        }
        .pdf li img{
            height: 12px;
            width: 12px;
        }
        .pdf li a{
            padding-left: 5px;
        }

    </style>
    <link href="{{asset("assets/literallycanvas/css/literallycanvas.css")}}" rel="stylesheet">

    <!-- dependency: React.js -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-with-addons.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-dom.js"></script>

    <!-- Literally Canvas -->
    <script src="{{asset("assets/literallycanvas/js/literallycanvas.js")}}"></script>

    <link href=" {{asset('assets/dashboard/smartWizard/css/smart_wizard.css')}}" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- Optional SmartWizard theme -->
    <link href="{{asset('assets/dashboard/smartWizard/css/smart_wizard_theme_circles.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/dashboard/smartWizard/css/smart_wizard_theme_arrows.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/dashboard/smartWizard/css/smart_wizard_theme_dots.css')}}" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="{{asset('assets/dashboard/smartWizard/js/jquery.smartWizard.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
    <script src="{{ asset('assets/dashboard/smartWizard/js/bootstrap-multiselect.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function(){
            // Toolbar extra buttons
            var btnFinish = $('<button></button>').text('Submit')
                .addClass('btn btn-info')
                .on('click', function(){ alert('Finish Clicked'); });
            var btnCancel = $('<button></button>').text('Cancel')
                .addClass('btn btn-danger')
                .on('click', function(){ $('#smartwizard').smartWizard("reset"); });

            // Smart Wizard
            $('#smartwizard').smartWizard({
                selected: 0,
                theme: 'dots',
                transitionEffect:'slide',
                toolbarSettings: {toolbarPosition: 'bottom',
                    toolbarExtraButtons: [btnCancel,btnFinish]
                },
//                anchorSettings: {
//                    anchorClickable: true, // Enable/Disable anchor navigation
//                    enableAllAnchors: false, // Activates all anchors clickable all times
//                    markDoneStep: true, // add done css
//                    enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
//                },

            });
            $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
                var lastSelected = $('#framework option:selected').val();
                if (stepNumber==3 && stepDirection=="forward"){
                        $.ajax({
                            type:"POST",
                            url:"{{ url('dashboard/company/docketBookManager/docket/sendDocket/docketTemplete/') }}"+'/'+lastSelected,
                            success:function (response) {
                                if (response == "Invalid attempt!") {
                                    alert(response);
                                }else {
                                    $('#template').html(response);
                                }
                            }
                        });
                 }

            });
        });
//        $( "#no" ).click(function() {
//            alert("demo");
//        });
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#framework').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                includeSelectAllOption: true,
                nonSelectedText: 'Demo',
                filterPlaceholder: 'Search Docket Template',
                onChange: function(element, checked) {
                    if (element) {
                        lastSelected = element.val();
                        {{--$("a#docketOnchangeTemplate").attr("data-content-url","{{ url('dashboard/company/docketBookManager/docket/sendDocket/docketTemplete/') }}"+'/'+lastSelected)--}}
//                        lastSelected.remove();
                    }
                    else {
                        $("#framework").multiselect('select', lastSelected);
                        $("#framework").multiselect('deselect', element.val());
                    }
                }
            });

        });
        $(document).ready(function(){
            $('#frameworks').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                nonSelectedText: 'Select Client',
                filterPlaceholder: 'Search Client',


            });

        });


        $(document).ready(function(){
            $('#frameworkemployee').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                nonSelectedText: 'Select Client',
                filterPlaceholder: 'Search Client',


            });

        });

        $(document).on('click','#yes',function () {
           $('.employeeAssign').show( "slow" );

        });
//        $(document).on('click','#no',function () {
//            $('.employeeAssign').hide(1000);
//        });
        $(document).on('click', '#no' ,function () {
            var key = this.id;
            var url = "{{ url('dashboard/company/docketBookManager/docket/sendDocket/') }}";
                $.ajax({
                    type: "GET",
                    data:{key:key},
                    url: url,
                    success: function(response){
                        if(response == ""){

                        }else{
                            alert(response);
                        }
                    }
                });
        });

    </script>
@endsection