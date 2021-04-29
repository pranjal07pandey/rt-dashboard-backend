<footer class="rtCompanyFooter">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <img src="{{ asset('assets/dashboard/img/whiteLogo.png') }}" id="footerLogo">
            </div>
            <div class="col-md-push-1 col-md-6 ">
                <div class="footerNavList">
                    <ul>
                        <li> <a href="https://recordtime.com.au/terms-of-use" target="_blank">Terms of Use </a></li>
                        <li> <a href="https://recordtime.com.au/license-aggrement" target="_blank">Licence Agreement</a></li>
                        <li> <a href="https://recordtime.com.au/contact-invoicing-docketing-construction-paperwork-management-app" target="_blank">Contact Us</a></li>
                        <li> <a href="https://recordtime.com.au/frequently-asked-questions" target="_blank">FAQ</a></li>
                        <li> <a href="https://recordtime.com.au/how-tos" target="_blank">How To's</a></li>
{{--                        <li> <a href="http://recordtime.com.au/support-record-time-paperless-docketing-system/" target="_blank">Share RT</a></li>--}}
                    </ul>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div class="downloadOurApp">
                    <strong>Download Our Latest App On</strong>
                    <a href="https://play.google.com/store/apps/details?id=com.webtecsolutions.recordtimeapp" target="_blank"  class="pull-right" style="margin-left:10px;" title="Google Play Store Download Link">
                        <img src="{{ asset('assets/dashboard/img/playStore.png') }}" alt="Download Record Time for Android Devices">
                    </a>
                    <a href="https://itunes.apple.com/au/app/record-time/id971035780?mt=8" target="_blank" title="Apple Store Download Link" class="pull-right">
                        <img src="{{ asset('assets/dashboard/img/appStore.png') }}" alt="Download Record Time for iPhone Devices">
                    </a>
                </div>
            </div>
        </div>
    </div><!--/.container-->
    <div class="rtCopyRights">
        <div class="container">
            <p class="pull-left">&copy; 2015 - {{ date("Y") }} Record TIME Pty Ltd. All rights reserved. ABN: 99 604 582 649 | Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 AUSTRALIA | Support: 0421 955 630</p>
            <p class="pull-right">
                Developed By :
                <a href="https://www.webtecsolutions.com.au/" target="_blank"><strong>WebTec Solutions</strong></a>
            </p>
        </div>
    </div>
</footer>
<?php $xeroUserDetail1 = \App\CompanyXero::where('company_id', Session::get('company_id')); ?>


<!--/xero section-->
@if(\Illuminate\Support\Facades\Session::get('xero_oauth')==null)
    @if(Session::get('company_id')==1)
        @if ($xeroUserDetail1->count() == 0)
            <div  id="intercom-activator" class="deactiveactivexerobutton">
                <button class="btn btn-xs btn-raised btn-info pull-right" style="font-size: 15px;border-radius: 0px;" data-toggle="modal" data-target="#connectionxeropopup" >Connect to Xero</button>
            </div>
        @else
            @if ($xeroUserDetail1->first()->payroll_status == 1)
                <div  id="intercom-activator" class="deactiveactivexerobutton">
                    <button style="font-size: 15px;border-radius: 0px;" class="btn btn-xs btn-raised btn-info pull-right" onclick="window.location.href='{{url('dashboard/company/xero/connect/1')}}'">Connect to Xero</button>
                </div>
            @else
                <div  id="intercom-activator" class="deactiveactivexerobutton">
                    <button class="btn btn-xs btn-raised btn-info pull-right" style="font-size: 15px;border-radius: 0px;" data-toggle="modal" data-target="#connectionxeropopup" >Connect to Xero</button>
                </div>
            @endif
        @endif
    @endif
@else
    @if(\Illuminate\Support\Facades\Session::get('xero_oauth')->expires == "")

        @if ($xeroUserDetail1->count() == 0)
            <div  id="intercom-activator" class="deactiveactivexerobutton">
                <button class="btn btn-xs btn-raised btn-info pull-right" style="font-size: 15px;border-radius: 0px;" data-toggle="modal" data-target="#connectionxeropopup" >Connect to Xero</button>
            </div>
        @else
          @if ($xeroUserDetail1->first()->payroll_status == 1)
            <div  id="intercom-activator" class="deactiveactivexerobutton">
              <button style="font-size: 15px;border-radius: 0px;" class="btn btn-xs btn-raised btn-info pull-right" onclick="window.location.href='{{url('dashboard/company/xero/connect/1')}}'">Connect to Xero</button>
            </div>
          @else
              <div  id="intercom-activator" class="deactiveactivexerobutton">
                  <button class="btn btn-xs btn-raised btn-info pull-right" style="font-size: 15px;border-radius: 0px;" data-toggle="modal" data-target="#connectionxeropopup" >Connect to Xero</button>
              </div>
          @endif
        @endif
    @else
        <div  id="intercom-activator" class="activexerobutton" style="    bottom: -8px;">
            <button class="btn btn-xs btn-raised btn-success pull-right activexerobuttononclick" style="    font-size: 15px;border-radius: 0px;">Xero Connected </button>
        </div>
    @endif
    @if($xeroUserDetail1->count()==1)
        <?php
        $demo1 = $xeroUserDetail1->first()->xero_organization_contact;
        $test1=unserialize($demo1);
        $add1 = $xeroUserDetail1->first()->xero_organination_address;
        $addData1=unserialize($add1);
        ?>
        <div class="intercom-div" style="   display: none;">
            <button class="closeIntercomDiv" type="button" style="    font-weight: 800;color: #fffefe;    position: absolute;z-index: 11;background: transparent;border: none;right: 0;" aria-label="Close"><span aria-hidden="true">&times;</span></button>

            <div class="header-intercom">
            <img style="    width: 61px;" src="{{asset('assets/xero.png')}}">
                <h4 style="text-align: center;padding: 0;margin: -11px 0px 6px 0px;color: #fff;font-weight: 500;">{{$xeroUserDetail1->first()->xero_organization_name}}</h4>
                <h5 style="text-align: center;padding: 0;margin: 0;color: #fff;font-weight: 500;"> {{$xeroUserDetail1->first()->xero_user_first_name}} {{$xeroUserDetail1->first()->xero_user_last_name}}</h5>
                <div class="timer-intercom">
                    <p style="  font-weight: 500;font-size: 17px;  padding: 0;margin: 0;" id="demo"></p>

                </div>
            </div>


            <div class="userrdetail-intercom">
                <div class="col-md-12" >
                    <span style="font-size: 14px;font-weight: 500;color: #999">
                    Email : {{$xeroUserDetail1->first()->xero_email}}
                    </span>
                </div>
                <div class="col-md-12" >
                    <span style="font-size: 14px;font-weight: 500;color: #999">
                        Line Of Business : {{$xeroUserDetail1->first()->organization_line_of_business}}
                    </span>
                </div>
                <div class="col-md-12" >
                    <span style="font-size: 14px;font-weight: 500;color: #999">
                        @if(isset($test1[0]))

                            @if (array_key_exists('PhoneType', $test1[0]))

                                {{$test1[0]['PhoneType']}} :

                                @if (array_key_exists('PhoneCountryCode', $test1[0])) {{$test1[0]['PhoneCountryCode']}}@endif - @if (array_key_exists('PhoneNumber', $test1[0])) {{$test1[0]['PhoneNumber']}}@endif
                            @endif
                        @endif

                    </span>






                </div>
                <a style="margin: 16px 17px 0px 0px;" class="btn btn-xs btn-danger pull-right" href="{{url('dashboard/company/xero/disconnected')}}">Disconnect</a>

            </div>


        </div>
    @endif
@endif
<div class="modal fade" id="connectionexpirexero" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        {{--<div id="model" data-target="#myModal"></div>--}}
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Xero Connection Expired</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="docketdelete_label_id" name="id">
                        <p> <i class="fa fa-exclamation-circle"></i>  The Xero connection has expired. Please re-connect to Xero, if you need to sync any invoices or timesheets.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="connectionxeropopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        {{--<div id="model" data-target="#myModal"></div>--}}
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Xero Connection</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="docketdelete_label_id" name="id">
                        <p> <i class="fa fa-exclamation-circle"></i>  Does your Xero account have payroll access?</p>
                        <div class="pull-right">
                            <button style="margin: 16px 17px 0px 0px;" class="btn btn-xs btn-raised  btn-success pull-left" onclick="window.location.href='{{url('dashboard/company/xero/connect/1')}}'">Yes</button>
                            <button style="margin: 16px 17px 0px 0px;" class="btn btn-xs btn-raised  btn-danger pull-right" onclick="window.location.href='{{url('dashboard/company/xero/connect/0')}}'">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/xero section-->

<!-- Include all compiled plugins (below), or include individual files as needed -->
<style>
    #intercom-activator {
        /*background-image: url(https://upload.wikimedia.org/wikipedia/en/9/9f/Xero_software_logo.svg);*/
        background-size: 26px 25px;
        background-repeat: no-repeat;
        background-color: transparent;
        border: transparent;
        border-color: transparent;
        box-shadow: 0 6px 13px 0 transparent;
        border-radius: 50%;
        position: fixed;
        bottom: -8px;
        right: -1px;
        width: 48px;
        height: 48px;
        z-index: 2147483000;
        cursor: pointer;
        background-position: 50%;
    }
    .intercom-div{
        background: rgb(255, 255, 255);
        min-height: 150px;
        position: fixed;
        bottom: 47px;
        right: 22px;
        width: 340px;
        z-index: 2147483000;
        border-radius: 7px;
        box-shadow: rgb(210, 207, 207) 0px 2px 2px 0px, rgb(210, 207, 207) 0px 3px 1px -2px, rgb(210, 207, 207) 0px 1px 5px 0px;
        padding-bottom: 12px;
    }
    .header-intercom{
        background: #00b3ba;
        height: 100px;
        border-radius: 7px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
        position: relative;
    }
    .timer-intercom{
        position: absolute;
        left: 17%;
        top: 80%;
        padding:  4px 27px 4px 27px;
        background: #ffff;
        border-radius: 4px;
        box-shadow: 0 4px 15px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.1), inset 0 2px 0 0 rgba(0, 179, 186, 0.52);
    }
    .userrdetail-intercom{
        margin-top: 27px;
    }
</style>
<script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script>
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".generateTicketForm").hide();
        $(".generateTicketButton").click(function(){
            $(".generateTicketForm").slideToggle();
        });
    });

</script>
{{--<script src="{{ asset('assets/dashboard/js/drag.js') }}"></script>--}}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('assets/dashboard/js/material.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/dashboard/js/ripples.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/dashboard/plugins/datatables/jquery.dataTables.min.js') }}"></script>


<script src="{{ asset('assets/dashboard/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('assets/dashboard/plugins/fastclick/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/dashboard/dist/js/app.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script type="text/javascript" src="{{ asset('assets/BsMultiSelect.js') }}"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@toastr_js
@toastr_render
<!-- AdminLTE for demo purposes -->
{{--<script src="{{ asset('zipFile') }}"></script>--}}

<script type="text/javascript">
    $(function () {
        $('#emailClientDataTable').DataTable({
            'paging'      : true,
            'lengthChange': false,
            'searching'   : true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false
        })
    })
</script>
<script type="text/javascript">;
    $(document).ready(function(){
        $.material.init();
    });
</script>
<script type="text/javascript">
    $( function() {
        $( "#sortable" ).sortable();
        $( "#sortable" ).disableSelection();
    } );

</script>


<script>
    $('#connectionexpirexero').on('hidden.bs.modal', function () {
        location.reload();
    });

    $(document).ready(function() {
        if("{{@Session::get('xero_oauth')->expires}}" != "") {
            var expiryTime = new Date("{{@Session::get('xero_oauth')->expires}}").getTime();
            var countDownDate = new Date("{{Carbon\Carbon::now() }}").getTime();

            console.log(countDownDate);
            console.log(expiryTime);

            var totalCounter = expiryTime - countDownDate;

            var x = setInterval(function () {


                // Time calculations for days, hours, minutes and seconds
                var hours = Math.floor((totalCounter % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((totalCounter % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((totalCounter % (1000 * 60)) / 1000);

                // Output the result in an element with id="demo"
                document.getElementById("demo").innerHTML = "Session Time : "+minutes + "m " + seconds + "s ";

                // If the count down is over, write some text
                if (totalCounter < 0) {
                    clearInterval(x);
//            document.getElementById("demo").innerHTML = "EXPIRED";
                    $('.intercom-div').css('display','none');

                    $.ajax({
                        type: "Get",
                        url: '{{ url('dashboard/company/xeroTimeOut') }}',
                        success: function (response) {
                            $('.activexerobutton').css('display', 'none');
                            $('.deactiveactivexerobutton').css('display', 'block');
                            $("#connectionexpirexero").modal({
                                backdrop: 'static',
                            });

                        }
                    });

                }
                totalCounter    =   totalCounter -1000;
            }, 1000);
        }
    })


</script>

    <script>
        $('.activexerobuttononclick').on('click', function () {
            $('.intercom-div').fadeIn('3000');
        });

        $('.closeIntercomDiv').on('click', function () {
            $('.intercom-div').fadeOut('3000');
        });

        $(document).click(function (e) {
            if (!$(e.target).hasClass("btn")
                && $(e.target).parents(".intercom-div").length === 0)
            {
                $('.intercom-div').fadeOut('3000');
            }
        });

        function noImageFunction(){
            return '/assets/dashboard/images/logoAvatar.png';
        }
    </script>
</body>