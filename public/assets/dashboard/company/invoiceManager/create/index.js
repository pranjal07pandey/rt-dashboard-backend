$(document).ready(function(){
    var url = $(location).attr('href');
    var parts = url.split("#");
    if (parts[1]!=undefined){ location.href=parts[0]; }

    // Toolbar extra buttons
    var btnFinish = $('<a></a>').text('Send Invoice')
        .addClass('btn btn-info disabled submitInvoice')
        .on('click', function(){
            var templateId =  $('#framework option:selected').val()
            var recipientId =  $('#frameworkemployee option:selected').val()
            var emailrecipientId =  $('#frameworkemailemployee option:selected').val()
            var isemail =  $('.changeClient').val();
            var val = [];
            $('.invoiceablechecked:checkbox:checked').each(function(i){
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
            var url = $('.step-anchor li:nth-child(4)>a').attr('dataSendURL');
            $.ajax({
                type:"Post",
                data: formData,
                url: url,
                cache: false,
                contentType: false,
                processData: false,
                success:function (response) {
                    if (response['status']== true){
                        var newTabUrl=response['data'];
                        $(".overlaysplinner").css('display','none')
                        $('.invoiceSuccess').modal();
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
        if (stepNumber==3 && stepDirection=="forward"){
            var val = [];
            $(':checkbox:checked').each(function(i){ val[i] = $(this).val(); });
            var type = $('.invoiceableDocketType').val();
            $.ajax({
                type:"Post",
                data: {id:lastSelected,selectedInvoiceable: val,type:type},
                url: $(anchorObject).attr('dataURL'),
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
                var recipientId =  $('#frameworkemployee option:selected').val();
                createInvoiceDockets(recipientId,recipientType);
            }else if(recipientType == 2){
                var recipientId =  $('#frameworkemailemployee option:selected').val();
                createInvoiceDockets(recipientId,recipientType);
            }
        }
        else if(stepNumber==1 && stepDirection=="forward"){
            $('.changeClient').val(1)
            var checkClient = document.getElementById('checkClient');
            checkClient.disabled = true;
            checkClient.checked = true;
            var checkEmailClient = document.getElementById('checkEmailClient');
            checkEmailClient.checked = false;
            checkEmailClient.disabled = false;
            showRecipients(1);}
    });

    $(document).on('change','#checkEmailClient', function () {
        if (this.checked) {
            var checkEmailClient = document.getElementById('checkEmailClient');
            checkEmailClient.disabled = true;
            var checkClient = document.getElementById('checkClient');
            checkClient.checked = false;
            checkClient.disabled = false;

            var selectedValue = $(this).val();
            $('.changeClient').val(selectedValue)
            showRecipients(2);
        }
    })
    $(document).on('change','#checkClient', function (){
        if(this.checked){
            var selectedValue = $(this).val();
            $('.changeClient').val(selectedValue)
            var checkClient = document.getElementById('checkClient');
            checkClient.disabled = true;
            var checkEmailClient = document.getElementById('checkEmailClient');
            checkEmailClient.checked = false;
            checkEmailClient.disabled = false;
            showRecipients(1);
        }
    })

    //functions
    function showRecipients(type){
        var url = $('.step-anchor li:nth-child(2)>a').attr('dataURL');
        $.ajax({
            type: "post",
            data: {type:type},
            url :url,
            success:function (response) {
                $('.recipientShow').html(response);
                if(type==1){
                    $('#frameworkemployee').multiselect({
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        buttonWidth:'100%',
                        nonSelectedText: 'Select Client',
                        filterPlaceholder: 'Search Client',
                        onChange: function(element, checked){}
                    });
                }else{
                    $('#frameworkemailemployee').multiselect({
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        buttonWidth: '100%',
                        nonSelectedText: 'Select Client',
                        filterPlaceholder: 'Search Client',
                        onChange: function (element, checked){}
                    });
                }
            }
        })
    }
    function createInvoiceDockets(recipient, type) {
        var url = $('.step-anchor li:nth-child(3)>a').attr('dataURL');
        $.ajax({
            type: "post",
            data: {recipient:recipient, type: type},
            url :url,
            success:function (response) {
                $('.spinnerCheck').css('display','none');
                $('#invoiceableDocketView').html(response);
                $('#invoiceableDocketView').show( "slow" );
                $('.selectDocketHeader').hide(1000);

                $('#show-hidden-menu').click(function() {
                    $('.hidden-menu').slideToggle("slow");
                });

                $( "#filterDateFrom" ).datepicker({ dateFormat: 'dd-mm-yy'});
                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});

                $('#frameworkFilter').multiselect({
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth:'100%',
                    nonSelectedText: 'Docket Template',
                    filterPlaceholder: 'Docket Template',
                    onChange: function(element, checked) {
                    }
                });
                $('#dateSwitch').on('change', function(){
                    if($(this).is(':checked')){
                        $('#dateFrom').addClass('col-md-6')
                        $('#dateFrom').removeClass('col-md-12');
                        $('#dateTo').show();
                    }else{
                        $('#dateFrom').removeClass('col-md-6');
                        $('#dateFrom').addClass('col-md-12');
                        $('#dateTo').hide();
                        $('#filterDateTo').val("");
                    }
                })
                if(type == 1){
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
                            url: $("#submitURL").val(),
                            success: function (response) {
                                $('#filterDocketAttacheable').html(response);

                            }
                        })
                    })
                }
                if(type == 2){
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
                            url: $("#submitURL").val(),
                            success: function (response) {
                                $('#filterEmailDocketAttacheable').html(response);
                            }
                        })
                    })
                }
            }
        })
    }
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
});