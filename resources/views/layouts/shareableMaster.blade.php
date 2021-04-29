<!DOCTYPE html>
<html lang="en">
@include('dashboard.company.partials.head')
<body>
<div class="rtNavBar">
    <nav class="navbar navbar-default">
        <div class="topNavbar">
            <div class="headerBG "></div>
            <div class="container">
                <div style="height: 60px;width: 100%;overflow: hidden;position:relative;">
                    <div class="topNavRight">
                        <ul>

                            <li class="hidden-sm hidden-xs">
                                <a href="https://recordtime.com.au/contact-invoicing-docketing-construction-paperwork-management-app" target="_blank">
                                    <i class="material-icons">headset_mic</i> Contact Us 24/7
                                </a>
                            </li>

                        </ul>
                    </div>
                    <div class="twoLinesWrapper">
                        <img src="{{ asset('assets/dashboard/img/logo.png') }}" height="40px" style="position: absolute;z-index: 3;top: 10px;">
                        <div class="twoLines visible-lg"></div>
                    </div>
                </div>
            </div>
        </div>

    </nav>
</div>
<div class="content-wrapper">
    <div class="container" style="position:relative;">
        @yield('content')
    </div><!-- /.container -->
</div><!-- /.content-wrapper -->
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

    {{--$(document).ready(function() {--}}
    {{--$('.editable').editable(--}}
    {{--type: 'text',--}}
    {{--title: 'Enter username',--}}
    {{--url: '{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}',--}}
    {{--success: function(response) {--}}
    {{--console.log(response);--}}
    {{--}--}}
    {{--);--}}
    {{--});--}}
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
</script>
</body>
@yield('customScript')
</body>
</html>