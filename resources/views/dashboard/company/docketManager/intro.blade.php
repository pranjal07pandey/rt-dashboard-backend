
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Record Time</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="{{asset('assets/dashboard/intro/style.css')}}" rel="stylesheet">
    <link href="{{asset('assets/dashboard/intro/bootstrap.min.css')}}" rel="stylesheet">

    <style type="text/css">
        body {
            padding-top: 20px;
            padding-bottom: 60px;
        }

        /* Custom container */
        /*.container {
          margin: 0 auto;
          max-width: 1000px;
        }
        .container > hr {
          margin: 60px 0;
        }*/

        /* Main marketing message and sign up button */
        .jumbotron {
            margin: 80px 0;
            text-align: center;
        }
        h2{
            /* float: left; */
            font-size: 24px;
            /* font-family: 'Avenir Next LT Pro Bold Condensed'; */
            /* font-weight: 700; */
            border-bottom: 2px solid #003f67;
            padding-bottom: 7px;
            padding-left: 10px;
            color: #000000;
            font-weight: 400;
            margin-bottom: 20px;

        }



        /* Supporting marketing content */
        .marketing {
            margin: 60px 0;
        }
        .marketing p + h4 {
            margin-top: 28px;
        }
        .btn-success {
            color: #fff;
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        /* Customize the navbar links to be fill the entire space of the .navbar */
        .navbar .navbar-inner {
            padding: 0;
        }
        .navbar .nav {
            margin: 0;
            display: table;
            width: 100%;
        }
        .navbar .nav li {
            display: table-cell;
            width: 1%;
            float: none;
        }
        .navbar .nav li a {
            font-weight: bold;
            text-align: center;
            border-left: 1px solid rgba(255,255,255,.75);
            border-right: 1px solid rgba(0,0,0,.1);
        }
        .navbar .nav li:first-child a {
            border-left: 0;
            border-radius: 3px 0 0 3px;
        }
        .navbar .nav li:last-child a {
            border-right: 0;
            border-radius: 0 3px 3px 0;
        }

        .popover-title
        {
            font-weight:bold;
        }
        .popover
        {
            width:400px;
        }
    </style>
</head>

<body>
<nav  style="border-width: 0 0 0px;" class="navbar navbar-fixed-top">
    <div class="container">
        <div class="twoLinesWrapper" style="left:0px;">
            <div class="whiteBackground"></div>
            <div class="twoLines"></div>
        </div>
        <div class="navbar-header">
            <a class="navbar-brand" href="#"><img src="http://localhost/recordtime-laravel/public/assets/beta/images/logoWhite.jpg" style="height:100%;position: absolute;top: 0;"></a>
        </div>
        <a style="position: absolute;right: 97px;padding: 10px 18px;bottom: 20px; text-decoration: none; border-radius: 5px;" href="{{ url('dashboard/company/docketBookManager/docket')}}" class="btn-success">Back to Dashboard</a>
    </div>
</nav>



    <div class="container">
        <div class="jumbotron">
            <!-- start 1 -->
            <h1 class='bootstro'
                data-bootstro-content="Record TIME is a fully mobile software application suitable for Android or IOS enabled smart phones or tablets"
                data-bootstro-width="400px"
                data-bootstro-placement='bottom' data-bootstro-step='0'> <span style="color: #0f3b5a;">Record</span> <span style="color: #40adb8">Time</span></h1>
            <!-- end 1 -->
            <p class="lead">
                How to create a Docket Template?
            </p>
            <a class="btn btn-large btn-success" href="#" id='demo'>Demo</a>
        </div>


        <div class="row">
            <!--start 6 -->
            <div  class="col-md-4 bootstro"
                  data-bootstro-content="“Docket Elements” from box 1 and checkbox from box 2"
                  data-bootstro-placement='right'
                  data-bootstro-width='400px'
                  data-bootstro-step='6'>
                <h2>Label Title</h2>
                <p><i class='icon-ok'></i><b style="color: #003f67;">●</b> Any elements “Label Title” you add to the template is <b>editable</b>. ​This way you can customise what labels you want to display on the docket templates you send to your clients.</p>
            </div>
            <!-- end 6 -->
            <!--start 2 -->
            <div style="text-align: center;" class="col-md-4 bootstro"
                 data-bootstro-content="Go to “Docket Templates” under <b>Docket Book Manager</b>"
                 data-bootstro-step='1'>
                <h2 style="text-align: left;">Docket Book Manager </h2>
                <img src="{{asset('assets/dashboard/intro/docketbookmanager.png')}}" style="width: 309px; margin-bottom: 15px;">
            </div>
            <!-- end 6 -->
            <!-- start 4 -->
            <div class="col-md-4 bootstro"
                 data-bootstro-title=""
                 data-bootstro-content="Docket Elements & Docket Info"
                 data-bootstro-width="600px"
                 data-bootstro-step='4'>
                <h2>Docket Elements & Docket Info</h2>
                <p><i class='icon-ok'></i><b style="color: #003f67;">●</b> Depending on what fields you need on your custom docket, please click the  “Docket Elements” from <b>box 1</b>.
                </p>
                <p><i class='icon-ok'></i> <b style="color: #003f67;">●</b> If you want your docket template to be “Invoiceable”, click the invoiceable checkbox from <b> box 2</b>.​ <br> <pre><span style="font-size: 12px;"><b>Note:</b> Invoiceable means being able to use dockets “<b>Unit Rate</b>” ​value to create an invoice/s for all of the works for a client</span></pre></p>
            </div>
            <!-- end 4-->
            <!-- start 5 -->
            <div class="col-md-12 bootstro"
                 data-bootstro-content=" Docket Elements  from <b>Box 1</b> & Checkbox from <b>Box 2</b>"
                 data-bootstro-width="600px"
                 data-bootstro-step='5'>
                <h2>Docket Elements & Docket Info</h2>
                <img src="{{asset('assets/dashboard/intro/docketinfo.png')}}" width="100%" height="400px">
            </div>
            <!-- end 5 -->
        </div>

        <!-- start 2 -->
        <div class="col-md-6 bootstro"
             data-bootstro-content="Click “Add New” to add a new docket template"
             data-bootstro-placement='right'
             data-bootstro-width='400px'
             data-bootstro-step='2'>
            <h2>Add New</h2>
            <img src="{{asset('assets/dashboard/intro/addnew.png')}}" width="100%" style="margin-bottom: 15px;">
        </div>
        <!-- end 2 -->
        <!-- start 3 -->
        <div class="col-md-6 bootstro"
             data-bootstro-content="Add a relevant and easy to remember “Docket Title”"
             data-bootstro-placement='left'
             data-bootstro-width='400px'
             data-bootstro-step='3'>
            <h2>Add Docket Title</h2>
            <img src="{{asset('assets/dashboard/intro/docket_title.png')}}" width="100%" style="margin-bottom: 15px;">
        </div>
        <!-- end 3 -->
        <!-- start 8 -->
        <div class="col-md-6 bootstro"
             data-bootstro-content="Add a relevant and easy to remember “Docket Title”"
             data-bootstro-placement='top'
             data-bootstro-width='400px'
             data-bootstro-step='8'>
            <h2>Pre-Start Checklist</h2>
            <p><i class='icon-ok'></i><b style="color: #003f67;">●</b> You can move the rows in any order you would like. Just  <b>“Click”, “Hold” and “Drag”</b> .</p>
        </div>
        <!-- end 8-->
        <!-- start 10 -->
        <div class="col-md-6 bootstro"
             data-bootstro-content="Do you want to asssign this Docket Templet"
             data-bootstro-placement='top'
             data-bootstro-width='400px'
             data-bootstro-step='10'>
            <h2>Assign Docket</h2>
            <p> <i class='icon-ok'></i><b style="color: #003f67;">●</b> Once you have finished creating your “Docket Template”, hit save and it will allow you to assign your employees from the list of employees you have created.</p>
        </div>
        <!-- end 10 -->
        <!-- start 11-->
        <div class="col-md-12 bootstro"
             data-bootstro-content="Do you want to asssign this Docket Templet"
             data-bootstro-placement='top'
             data-bootstro-width='400px'
             data-bootstro-step='11'>
            <h2>Assign Docket</h2>
            <img src="{{asset('assets/dashboard/intro/assign.png')}}" width="100%" style="margin-bottom: 15px;">
        </div>
        <!-- end 11 -->
        <!-- start 9 -->
        <div class="col-md-6 bootstro"
             data-bootstro-content="Add a relevant and easy to remember “Docket Title”"
             data-bootstro-placement='top'
             data-bootstro-width='400px'
             data-bootstro-step='9'>
            <h2>Pre-Start Checklist</h2>
        </div>
        <!-- end 9 -->
        <!--  start 7 -->
        <div class="col-md-6 bootstro"
             data-bootstro-content="Add a relevant and easy to remember “Docket Title”"
             data-bootstro-placement='top'
             data-bootstro-width='400px'
             data-bootstro-step='7'>
            <h2>Label Title</h2>
            <img src="{{asset('assets/dashboard/intro/label_title.png')}}" width="100%" style="margin-bottom: 15px;">
        </div>
        <!-- end 7 -->
        <!-- start 12-->
        <div class="col-md-12 bootstro"
             data-bootstro-content='Click “Add New” to add a new docket template'
             data-bootstro-step='12'>
            <h3 style="font-weight: 400;font-size: 17px;"><b>Notes:</b></h3>
            <div class='well'>
                <ol>
                    <li>You can delete a field by simply clicking the delete symbol. Please note: once a template has been used, any of its field cannot be deleted.</li>
                    <li>Click a “Docket Preview” checkbox to preview the field on “Sent” and “Received” dockets on the backend. Please note: more field you preview will slow the page load time. Therefore, it is best to preview dockets with unique docket information, this way you do not have to go inside each and every docket.</li>
                    <li>Go to Docket Book Manager >> Docket Template to view all existing docket templates. You can preview, change the fields order, change label names and so on. You can also delete docket templates from here as long as it has not been used to send a docket.</li>
                    <li>Go to Docket Book Manager >> Assign Dockets Template to assign dockets to employees and un-assign them from a docket/s.</li>
                </ol>
            </div>
        </div>
        <!-- end 12 -->

    </div> <!-- /container -->


{{--@section('customScript')--}}
    {{--<script src="{{asset('assets/dashboard/intro/jquery.js')}}"></script>--}}
    {{--<script src="{{asset('assets/dashboard/intro/bootstrap.min.js')}}"></script>--}}
    {{--<link href="{{asset('assets/dashboard/intro/bootstro.css')}}" rel="stylesheet">--}}
    {{--<script src="{{asset('assets/dashboard/intro/bootstro.js')}}"></script>--}}
    {{--<script>--}}
        {{--$(document).ready(function(){--}}
            {{--$("#zipFile").click(function(){--}}
                {{--bootstro.start(".bootstro", {--}}
                    {{--onComplete : function(params)--}}
                    {{--{--}}
                        {{--// alert("Reached end of introduction with total " + (params.idx + 1)+ " slides");--}}
                    {{--},--}}
                    {{--onExit : function(params)--}}
                    {{--{--}}
                        {{--// alert("Introduction stopped at slide #" + (params.idx + 1));--}}
                    {{--},--}}
                {{--});--}}
            {{--});--}}
            {{--$(".demo_stopOn").click(function(){--}}
                {{--alert('Clicking on the backdrop or Esc will NOT stop the show')--}}
                {{--bootstro.start('.bootstro', {stopOnBackdropClick : false, stopOnEsc:false});--}}
            {{--});--}}
            {{--$(".demo_size1").click(function(){--}}
                {{--bootstro.start('.bootstro_size1');--}}
            {{--});--}}
            {{--$(".demo_nonav").click(function(){--}}
                {{--bootstro.start('.bootstro', {--}}
                    {{--nextButton : '',--}}
                    {{--prevButton : '',--}}
                    {{--finishButton : ''--}}
                {{--});--}}
            {{--});--}}
            {{--$(".demo_ajax").click(function(){--}}
                {{--bootstro.start('', {--}}
                    {{--url : './bootstro.json',--}}
                {{--});--}}
            {{--});--}}
        {{--});--}}
    {{--</script>--}}
{{--@endsection--}}

<div class="loginFooterWrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h3>About Record Time</h3>
                <p>Record TIME is a revolutionary application that will help you manage your paperwork. The software is specifically designed for the construction industry, with the ability to create Modular Dockets. Any paperwork can be turned digital in seconds.</p>
            </div>
            <div class="col-md-5">
                <div class="footerNav">
                    <ul>
                        <li> <a href="http://recordtime.com.au/terms-of-use/" target="_blank">Terms of Use </a></li>
                        <li> <a href="http://recordtime.com.au/licence-agreement/" target="_blank">Licence Agreement</a></li>
                        <li> <a href="http://recordtime.com.au/contactus" target="_blank">Contact Us</a></li>
                        <li> <a href="http://recordtime.com.au/frequently-asked-questions/" target="_blank">FAQ</a></li>
                        <li> <a href="http://recordtime.com.au/how-to-use-record-time/" target="_blank">How To's</a></li>
                        <li> <a href="http://recordtime.com.au/contactus" target="_blank">Site Feedback</a></li>
                        <li> <a href="http://recordtime.com.au/support-record-time-paperless-docketing-system/" target="_blank">Share RT</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3">
                <div>
                    <h3>Download Our App</h3>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <a href="https://itunes.apple.com/au/app/record-time/id971035780?mt=8" target="_blank" title="Apple Store Download Link">
                                <img src="http://localhost/recordtime-laravel/public/assets/beta/images/iOSBadge.png" alt="Download Record Time for iPhone Devices" style="max-width: 130px;">
                            </a>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <a href="https://play.google.com/store/apps/details?id=com.webtecsolutions.recordtimeapp" target="_blank" title="Google Play Store Download Link">
                                <img src="http://localhost/recordtime-laravel/public/assets/beta/images/androidBadge.png" alt="Download Record Time for Android Devices" style="max-width: 130px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <p class="navbar-text" style="font-size:12px;margin-top:0px;margin-left: 0px;">© 2015 - 2017 Record TIME Pty Ltd. All rights reserved. ABN: 99 604 582 649 | Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 AUSTRALIA | Support: 02 6169 4012
        </p>

        <a href="https://www.webtecsolutions.com.au/" class="text-black pull-right" style="font-size:12px;color:#fff !important;">Developed By: WebTec Solutions</a>
    </div>
</div>
<!-- Le javascript
   ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="{{asset('assets/dashboard/intro/jquery.js')}}"></script>
<script src="{{asset('assets/dashboard/intro/bootstrap.min.js')}}"></script>
<link href="{{asset('assets/dashboard/intro/bootstro.css')}}" rel="stylesheet">
<script src="{{asset('assets/dashboard/intro/bootstro.js')}}"></script>
<script>
    $(document).ready(function(){
        $("#zipFile").click(function(){
            bootstro.start(".bootstro", {
                onComplete : function(params)
                {
                    // alert("Reached end of introduction with total " + (params.idx + 1)+ " slides");
                },
                onExit : function(params)
                {
                    // alert("Introduction stopped at slide #" + (params.idx + 1));
                },
            });
        });
        $(".demo_stopOn").click(function(){
            alert('Clicking on the backdrop or Esc will NOT stop the show')
            bootstro.start('.bootstro', {stopOnBackdropClick : false, stopOnEsc:false});
        });
        $(".demo_size1").click(function(){
            bootstro.start('.bootstro_size1');
        });
        $(".demo_nonav").click(function(){
            bootstro.start('.bootstro', {
                nextButton : '',
                prevButton : '',
                finishButton : ''
            });
        });
        $(".demo_ajax").click(function(){
            bootstro.start('', {
                url : './bootstro.json',
            });
        });
    });
</script>
</body>
</html>