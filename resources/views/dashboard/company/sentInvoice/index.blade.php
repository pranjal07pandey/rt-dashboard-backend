@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>Sent Invoice</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Invoice Manager</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="loading overlaysplinner" style="display: none">Loading&#8230;</div>
    <form action="#" id="myForm" role="form" enctype="multipart/form-data" data-toggle="validator" method="post" accept-charset="utf-8">
        <div id="smartwizard">
            <ul>
                <li><a href="#step-1" style="padding: 10px 31px ;"><small>1. Template</small></a></li>
                <li><a href="#step-2" style="padding: 10px 31px ;"><small>2. Recipients</small></a></li>
                <li><a href="#step-3" style="padding: 10px 31px ;"><small>3. Attach Dockets</small></a></li>
                <li><a href="#step-4" id="docketOnchangeTemplate" data-content-url="" style="padding: 10px 31px ;"><small>4. Invoice</small></a></li>
            </ul>
            <div>
                <div id="step-1">
                    <div id="form-step-0" role="form" class="employeeAssign" data-toggle="validator">
                        <div class="form-group">
                            <strong>Invoice Template</strong>
                            <select id="framework" class="form-control docketTemplete"  required  name="templete" >
                                @if(@$invoiceTemplateResult)
                                    @foreach ($invoiceTemplateResult as $row)
                                        <option value="{!! $row->invoiceInfo->id !!}" id="email">{!! $row->invoiceInfo->title !!}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <div id="step-2">
                    <div  id="form-step-1" role="form" data-toggle="validator">
                        <div class="form-group ">
                            <div >
                                <strong>Recipient Type:</strong><br><br>

                                <input type="hidden" class="changeClient" value="1">
                                <div class="col-md-6">
                                    <span>Record Time Users</span>  <input type="checkbox" id="checkClient" value="1" checked disabled>
                                </div>
                                <div class="col-md-6">
                                    <span>Custom Email Client</span>   <input type="checkbox" id="checkEmailClient" value="2">
                                </div>

                                <br>
                            </div>
                            <br>
                            <strong>Recipient </strong>
                            <div class="recipientShow"></div>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <div id="step-3">
                    <div class="form-group ">
                        <div id="form-step-2" role="form" data-toggle="validator">
                            <span class="spinnerCheck" style="position:absolute;left:50%;bottom:50%;font-size: 51px; display:block;"><i class="fa fa-spinner fa-spin"></i></span>
                            <div id="invoiceableDocketView" style="margin-top: 10px;"></div>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <div id="step-4">
                    <div class="sentError" style="background: red;text-align: center;color: #ffffff;border-radius: 22px;"></div>
                    <div id="form-step-3" role="form" data-toggle="validator">
                        <div id="template" >
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <br><br>
    <div class="modal fade invoiceSuccess" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-body">
                    <div class="dashboardFlash">
                        <h4><i class="fa fa-check-circle" aria-hidden="true" style="color: green;margin-right: 10px;"></i> Invoice sent successfully.</h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style="text-transform: capitalize;" type="button" class="btn btn-secondary" onclick="location.href ='{{ url('dashboard/company/invoiceManager/allInvoice') }}'">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="signatureModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div id="second" class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Signature Pad</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <canvas id="signature-pad" class="signature-pad"  width="550px" height="200px"></canvas>
                            <a class="clearSignature btn btn-primary pull-left">Clear</a>
                            <a class="saveSignature btn btn-primary pull-right">Save</a>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customScript')
    <style>
        .filterSelect .btn-group ul{
            height: 143px !important;
        }

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
        .input-file-container {
            position: relative;
        }
        .js .input-file-trigger {
            display: block;
            padding: 8px 45px;
            background: #4ea6d6;
            color: #fff;
            font-size: 1em;
            transition: all .4s;
            cursor: pointer;
            margin-top: 9px;
        }
        .js .input-file {
            position: absolute;
            top: 0; left: 0;
            width: 225px;
            opacity: 0;
            padding: 14px 0;
            cursor: pointer;
        }
        .js .input-file:hover + .input-file-trigger,
        .js .input-file:focus + .input-file-trigger,
        .js .input-file-trigger:hover,
        .js .input-file-trigger:focus {
            background: #34495E;
            color: #39D2B4;
        }

        .file-return {
            margin: 0;
        }
        .file-return:not(:empty) {
            margin: 1em 0;
        }
        .js .file-return {
            font-style: italic;
            font-size: .9em;
            font-weight: bold;
        }
        .js .file-return:not(:empty):before {
            content: "Selected file: ";
            font-style: normal;
            font-weight: normal;
        }
        /* Absolute Center Spinner */
        .loading {
            position: fixed;
            z-index: 999;
            height: 2em;
            width: 2em;
            overflow: visible;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }

        /* Transparent Overlay */
        .loading:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
        }

        /* :not(:required) hides these rules from IE9 and below */
        .loading:not(:required) {
            /* hide "loading..." text */
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0;
        }


        .loading:not(:required):after {
            content: '';
            display: block;
            font-size: 10px;
            width: 1em;
            height: 1em;
            margin-top: -0.5em;
            -webkit-animation: spinner 1500ms infinite linear;
            -moz-animation: spinner 1500ms infinite linear;
            -ms-animation: spinner 1500ms infinite linear;
            -o-animation: spinner 1500ms infinite linear;
            animation: spinner 1500ms infinite linear;
            border-radius: 0.5em;
            -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
            box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        }

        /* Animation */

        @-webkit-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @-moz-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @-o-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }




    </style>


    <script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-with-addons.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/react/0.14.7/react-dom.js"></script>
    <link href=" {{asset('assets/dashboard/smartWizard/css/smart_wizard.css')}}" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

    <!-- Optional SmartWizard theme -->
    <link href="{{asset('assets/dashboard/smartWizard/css/smart_wizard_theme_circles.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/dashboard/smartWizard/css/smart_wizard_theme_arrows.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/dashboard/smartWizard/css/smart_wizard_theme_dots.css')}}" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="{{asset('assets/dashboard/smartWizard/js/jquery.smartWizard.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
    <script src="{{ asset('assets/dashboard/smartWizard/js/bootstrap-multiselect.js') }}"></script>
    <link href=" {{asset('assets/dashboard/smartWizard/css/custom.css')}}" rel="stylesheet" type="text/css" />








    <script type="text/javascript">
        function b64toBlob(b64Data, contentType, sliceSize) {
            contentType = contentType || '';
            sliceSize = sliceSize || 512;

            var byteCharacters = atob(b64Data);
            var byteArrays = [];

            for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                var slice = byteCharacters.slice(offset, offset + sliceSize);

                var byteNumbers = new Array(slice.length);
                for (var i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                var byteArray = new Uint8Array(byteNumbers);

                byteArrays.push(byteArray);
            }

            var blob = new Blob(byteArrays, {type: contentType});
            return blob;
        }
        $(document).ready(function () {
            var url = $(location).attr('href');
            var parts = url.split("#");
            // var last_part = parts[parts.length-1];
            if (parts[1]!=undefined){
                var childWindow = "";
                var newTabUrl="{{ url('dashboard/company/sentInvoice') }}";
                location.href=newTabUrl;
            }
        });
        $(document).ready(function(){



                // Toolbar extra buttons
            var btnFinish = $('<a></a>').text('Send Invoice')
                .addClass('btn btn-info disabled submitInvoice')
                .on('click', function(){
                    var templateId =  $('#framework option:selected').val()
                    var recipientId =  $('#frameworkemployee option:selected').val()
                    var emailrecipientId =  $('#frameworkemailemployee option:selected').val()
                    var isemail =  $('.changeClient').val();
                    var val = [];
                    $(':checkbox:checked').each(function(i){
                        val[i] = $(this).val();
                    });
                    var formData = new FormData();
                    var test  ;
                    $('.signatureValue').map(function(idx, elem) {
                        var dataAtt = $(elem).children('div.signatureList').attr('field_id');
                        $(this).find('.item').each(function(){
                            var data = 'formFieldSignature'+dataAtt;
                            var ImageURL    =    $(this).children('img').attr('src');
                            var block = ImageURL.split(";");
                            var contentType = block[0].split(":")[1];
                            var realData = block[1].split(",")[1];
                            formData.append(data+"[]", b64toBlob(realData, contentType));
                        });
                    });

                    $('.imageValue').map(function(idx, elem) {
                        var dataAtt = $(elem).children('div.input-file-container').attr('field_id')
                        var formFieldImage = []
                        if ($(elem).children('div.input-file-container').children('input').hasClass("img-"+dataAtt)){
                            formFieldImage =  $(elem).children('div.input-file-container').children('input.img-'+dataAtt).get(0).files
                            var data = 'formFieldImage'+dataAtt;
                            for (var index = 0; index < formFieldImage.length; index++) {
                                formData.append(data+"[]", formFieldImage[index]);
                            }
                        }
                    });

                    var count = []
                    $('.multipleInvoiceField').map(function(idx, elem) {
                        count.push(idx)
                        formData.append('invoiceDescriptionValue'+idx, $(elem).find('div.col-md-8').children('input').val());
                        if ($(elem).find('div.col-md-4').children('input').val() == ""){
                            formData.append('invoiceDescriptionAmount'+idx, 0);
                        }else {
                            formData.append('invoiceDescriptionAmount'+idx, $(elem).find('div.col-md-4').children('input').val());
                        }
                    });


                    formData.append('invoiceDescriptionCount', count.length)
                    formData.append('templateId', templateId );
                    formData.append('recipientId', recipientId);
                    formData.append('invoiceableDocketId', val);
                    formData.append('emailrecipientId', emailrecipientId);
                    formData.append('isemail', isemail);

                    $(".overlaysplinner").css('display','block')
                    $.ajax({
                        type:"Post",
                        data: formData,
                        url:"{{ route('sentInvoicepost') }}",
                        cache: false,
                        contentType: false,
                        processData: false,
                        success:function (response) {
                            if (response['status']== true){
                                var newTabUrl=response['data'];
                                $(".overlaysplinner").css('display','none')
                                $('.invoiceSuccess').modal();
//                                location.href=newTabUrl;
                            }else if (response['status']== false){
                                $('.sentError').html(response['message'])
                                $(".overlaysplinner").css('display','none')
                            }
                        }
                    });

                });
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
            });
            $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
                lastSelected = $('#framework option:selected').val()
                console.log(stepNumber)
                if (stepNumber==3 && stepDirection=="forward"){



                    var val = [];
                    $(':checkbox:checked').each(function(i){
                        val[i] = $(this).val();
                    });
                    var type = $('.invoiceableDocketType').val();

                    $.ajax({
                        type:"Post",
                        data: {id:lastSelected,selectedInvoiceable: val,type:type},
                        url:"{{ url('dashboard/company/sentInvoice/invoiceTemplateDetail') }}",
                        success:function (response) {
                            if (response == "Invalid attempt!") {
                                alert(response);
                            }else {
                                $('.submitInvoice').removeClass('disabled')
                                $('#template').html(response);

                                //signature pad
                                $(".signatureWindowBtn").click(function(e){
                                    e.preventDefault();
                                    $(".saveSignature").unbind();
                                    $(".clearSignature").unbind();
                                    $(".saveSignature").attr("field_id",$(this).attr("field_id"));
                                    $('#signatureModal').modal('show');
                                    var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {backgroundColor: 'rgba(255, 255, 255, 0)',penColor: 'rgb(0, 0, 0)'});
                                    $(".saveSignature").on("click", function(){
                                        var data = signaturePad.toDataURL('image/png');
                                        var signatureList   =   "#signatureList" +  $(this).attr("field_id");
                                        $(signatureList).append("<div class='item'><img src='"+data+"'><a href='#' class='remove-signature'><i class='fa fa-minus-circle' aria-hidden='true'></i></a></div>");
                                        $('#signatureModal').modal('hide')
                                    });
                                    $(".clearSignature").on("click", function() {
                                        signaturePad.clear();
                                    });
                                });

                                $(".signatureList").on('click','.remove-signature',function(e){ e.preventDefault(); $(this).closest("div.item").remove(); });

                                var max_fields      = 100; //maximum input boxes allowed
                                var wrapper   		= $(".input_fields_wrap"); //Fields wrapper
                                var add_button      = $(".add_field_button"); //Add button ID

                                var x = 1; //initlal text box count
                                $(add_button).click(function(e){ //on add input button click
                                    e.preventDefault();
                                    if(x < max_fields){ //max input box allowed
                                        x++; //text box increment
                                        $(wrapper).append('<div class="multipleInvoiceField" style="margin-bottom: 20px;"><div class="row"><div class="col-md-8"><label style="color:#000000;font-size: 14px; ">Invoice Description  &nbsp;&nbsp; </label><a href="#" style="color: #D45750; font-size: 12px;margin: 0px;" class="remove_field "> Remove</a> <br><input type="text" name="describ[]" style="height: 50px;width: 100%;"/></div><div class="col-md-4"><label style="color:#000000;font-size: 14px;">Amount</label><br><input type="number" name="amount[]" class="amountdata" style="height: 50px;width: 100%;"/></div></div></div>'); //add input box
                                    }
                                });

                                $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                                    e.preventDefault(); $(this).closest('div.row').remove(); x--;

                                    types = [];
                                    $(".amountdata").each(function() {
                                        types.push($(this).val());

                                    });
                                    invoiceamounts =[]
                                    $(".invoiceAmount").each(function() {
                                        invoiceamounts.push($(this).val());

                                    });

                                    var InvoiceTotal = 0;
                                    $.each(invoiceamounts,function() {
                                        InvoiceTotal += parseFloat(this, 10);
                                    });


                                    var total = 0;
                                    $.each(types,function() {
                                        total += parseFloat(this, 10);
                                    });


                                    if (total == "NaN"){
                                        var totalamount =   InvoiceTotal

                                    } else {
                                        var totalamount = total + InvoiceTotal

                                    }

                                    $('.subTotalValue').html("$ "+totalamount);
                                    var taxvalue = $('.taxValue').attr('value');
                                    var totalva = ((totalamount * (taxvalue/100)) + totalamount).toFixed(3)

                                    $('.TotalValue').html("$ "+totalva);
                                })

                            }
                        }
                    });
                }

                if (stepNumber==2 && stepDirection=="forward"){
                    $('.spinnerCheck').css('display','block');

                    var recipientType =  $('.changeClient').val();
                    if (recipientType == 1){
                        var recipenntId =  $('#frameworkemployee option:selected').val();
                        var url = "{{ url('dashboard/company/sentInvoice/invoiceableDocket') }}"
                        $.ajax({
                            type: "post",
                            data: {userId:recipenntId},
                            url :url,
                            success:function (response) {
                                $('#invoiceableDocketView').html(response);
                                $('#invoiceableDocketView').show( "slow" );
                                $('.selectDocketHeader').hide(1000);
                                $('.spinnerCheck').css('display','none');
                                $('#show-hidden-menu').click(function() {
                                    $('.hidden-menu').slideToggle("slow");
                                    // Alternative animation for example
                                    // slideToggle("fast");
                                });

                                $('#frameworkFilter').multiselect({
                                    enableFiltering: true,
                                    enableCaseInsensitiveFiltering: true,
                                    buttonWidth:'100%',
                                    nonSelectedText: 'Docket Template',
                                    filterPlaceholder: 'Docket Template',
                                    onChange: function(element, checked) {
                                    }
                                });

                                $( "#filterDateFrom" ).datepicker({ dateFormat: 'dd-mm-yy'});
                                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});

                                $('#applyFilter').click(function() {
                                    $.ajax({
                                        type: "post",
                                        data: {
                                            from: $('#filterDateFrom').val(),
                                            range: $('#filterPriceRange input').val(),
                                            docketTempalte_id: $('#frameworkFilter').val(),
                                            docket_id: $('#filterDocketId').val(),
                                            record_time_user:$('#record_time_user').val()
                                        },
                                        url: "{{url('dashboard/company/sentInvoice/filterInvoiceableDocket')}}",
                                        success: function (response) {
                                            $('#filterDocketAttacheable').html(response);

                                        }
                                    })
                                })



                            }

                        })
                    }else if(recipientType == 2){
                        var recipenntEmailId =  $('#frameworkemailemployee option:selected').val();
                        var url = "{{ url('dashboard/company/sentInvoice/invoiceableEmailDocket') }}"
                        $.ajax({
                            type: "post",
                            data: {userId:recipenntEmailId},
                            url :url,
                            success:function (response) {
                                $('#invoiceableDocketView').html(response);
                                $('#invoiceableDocketView').show( "slow" );
                                $('.selectDocketHeader').hide(1000);
                                $('.spinnerCheck').css('display','none');

                                $('#show-hidden-menu').click(function() {
                                    $('.hidden-menu').slideToggle("slow");
                                });

                                $('#frameworkFilter').multiselect({
                                    enableFiltering: true,
                                    enableCaseInsensitiveFiltering: true,
                                    buttonWidth:'100%',
                                    nonSelectedText: 'Docket Template',
                                    filterPlaceholder: 'Docket Template',
                                    onChange: function(element, checked) {
                                    }
                                });

                                $( "#filterDateFrom" ).datepicker({ dateFormat: 'dd-mm-yy'});
                                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});

                                $('#applyFilter').click(function() {
                                    $.ajax({
                                        type: "post",
                                        data: {
                                            from: $('#filterDateFrom').val(),
                                            range: $('#filterPriceRange input').val(),
                                            docketTempalte_id: $('#frameworkFilter').val(),
                                            docket_id: $('#filterDocketId').val(),
                                            record_time_user:$('#record_time_user').val()
                                        },
                                        url: "{{url('dashboard/company/sentInvoice/filterInvoiceableEmailDocket')}}",
                                        success: function (response) {
                                            $('#filterEmailDocketAttacheable').html(response);

                                        }
                                    })
                                })


                            }

                        })

                        // console.log(recipenntEmailId)

                    }


                }else if (stepNumber==1 && stepDirection=="forward"){
                    var url = "{{ url('dashboard/company/sentInvoice/showRecipient') }}"
                    $.ajax({
                        type: "post",
                        data: {type:1},
                        url :url,
                        success:function (response) {
                            $('.recipientShow').html(response);
                            $('#frameworkemployee').multiselect({
                                enableFiltering: true,
                                enableCaseInsensitiveFiltering: true,
                                buttonWidth:'100%',
                                nonSelectedText: 'Select Client',
                                filterPlaceholder: 'Search Client',
                                onChange: function(element, checked) {
                                    // $('#attachableInvoiceUser').html('<b>Would you to like to Attach invoiceable Docket to '+element.text()+'</b> <input type="hidden" value="'+element.val()+'" id="recipientId">')
                                }
                            });
                        }

                    })
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#framework').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                includeSelectAllOption: true,
                nonSelectedText: 'Demo',
                filterPlaceholder: 'Search Template',
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
            $('#frameworkemployee').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                nonSelectedText: 'Select Client',
                filterPlaceholder: 'Search Client',
                onChange: function(element, checked) {
                    // $('#attachableInvoiceUser').html('<b>Would you to like to Attach invoiceable Docket to '+element.text()+'</b> <input type="hidden" value="'+element.val()+'" id="recipientId">')
                }
            });
        });

        $(document).ready(function(){
            $('#frameworkemailemployee').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                nonSelectedText: 'Select Client',
                filterPlaceholder: 'Search Client',
                onChange: function(element, checked) {
                    // $('#attachableInvoiceUser').html('<b>Would you to like to Attach invoiceable Docket to '+element.text()+'</b> <input type="hidden" value="'+element.val()+'" id="recipientId">')
                }
            });
        });


        $(document).on('change','.amountdata', function (e) {
            types = [];
            $(".amountdata").each(function() {
                types.push($(this).val());
            });
            console.log('types'+types)

            invoiceamounts =[]
            $(".invoiceAmount").each(function() {
                invoiceamounts.push($(this).val());

            });
            console.log('invoiceamounts'+invoiceamounts)

            var InvoiceTotal = 0;
            $.each(invoiceamounts,function() {
                InvoiceTotal += parseFloat(this, 10);
            });
            console.log('InvoiceTotal'+InvoiceTotal)



            var total = 0;
            $.each(types,function() {
                total += parseFloat(this, 10);
            });

            //
            // if (total == "NaN"){
            //     var totalamount =   InvoiceTotal
            //     console.log(12)
            // } else {
            var totalamount = total + InvoiceTotal
            var totalamount = total + InvoiceTotal
            //     console.log(34)
            // }

            console.log('totalamount'+totalamount)




            $('.subTotalValue').html("$ "+totalamount);
            var taxvalue = $('.taxValue').attr('value');


            var totalva = ((totalamount * (taxvalue/100)) + totalamount).toFixed(3)

            console.log('taxvalue'+taxvalue)
            console.log('totalva'+totalva)


            $('.TotalValue').html("$ "+totalva);
        });


        $(document).ready(function() {

        });


        // $(document).ready(function () {
        //     $(".changeClient").click(function(){
        //         alert($(this).val())
        //
        //     })
        // })
        $(document).on('change','#checkEmailClient', function () {

            if (this.checked) {
                var checkEmailClient = document.getElementById('checkEmailClient');
                checkEmailClient.disabled = true;
                var checkClient = document.getElementById('checkClient');
                checkClient.checked = false;
                checkClient.disabled = false;

                var selectedValue = $(this).val();
                $('.changeClient').val(selectedValue)
                var url = "{{ url('dashboard/company/sentInvoice/showRecipient') }}"
                $.ajax({
                    type: "post",
                    data: {type: selectedValue},
                    url: url,
                    success: function (response) {
                        $('.recipientShow').html(response);
                        $('#frameworkemailemployee').multiselect({
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true,
                            buttonWidth: '100%',
                            nonSelectedText: 'Select Client',
                            filterPlaceholder: 'Search Client',
                            onChange: function (element, checked) {
                                // $('#attachableInvoiceUser').html('<b>Would you to like to Attach invoiceable Docket to '+element.text()+'</b> <input type="hidden" value="'+element.val()+'" id="recipientId">')
                            }
                        });
                    }

                })
            }
        })

        $(document).on('change','#checkClient', function () {
            if (this.checked) {
                var selectedValue = $(this).val();
                $('.changeClient').val(selectedValue)

                // $(".checkClient").css('disabled',true)
                var checkClient = document.getElementById('checkClient');
                checkClient.disabled = true;
                var checkEmailClient = document.getElementById('checkEmailClient');
                checkEmailClient.checked = false;
                checkEmailClient.disabled = false;
                // $(".checkEmailClient").css('disabled',false)
                var url = "{{ url('dashboard/company/sentInvoice/showRecipient') }}"
                $.ajax({
                    type: "post",
                    data: {type:selectedValue},
                    url :url,
                    success:function (response) {
                        $('.recipientShow').html(response);
                        $('#frameworkemployee').multiselect({
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true,
                            buttonWidth:'100%',
                            nonSelectedText: 'Select Client',
                            filterPlaceholder: 'Search Client',
                            onChange: function(element, checked) {
                                // $('#attachableInvoiceUser').html('<b>Would you to like to Attach invoiceable Docket to '+element.text()+'</b> <input type="hidden" value="'+element.val()+'" id="recipientId">')
                            }
                        });

                    }

                })
            }

        })

        $(document).ready(function () {
            var url = "{{ url('dashboard/company/sentInvoice/showRecipient') }}"
            $.ajax({
                type: "post",
                data: {type:1},
                url :url,
                success:function (response) {
                    $('.recipientShow').html(response);
                    $('#frameworkemployee').multiselect({
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        buttonWidth:'100%',
                        nonSelectedText: 'Select Client',
                        filterPlaceholder: 'Search Client',
                        onChange: function(element, checked) {
                            // $('#attachableInvoiceUser').html('<b>Would you to like to Attach invoiceable Docket to '+element.text()+'</b> <input type="hidden" value="'+element.val()+'" id="recipientId">')
                        }
                    });
                }

            })
        })

        // $(document).ready(function () {
        //
        //
        // })

        // $(document).ready(function() {
        //
        // });




    </script>
@endsection