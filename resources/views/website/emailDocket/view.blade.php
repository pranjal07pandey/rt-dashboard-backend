<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Record Time</title>
    <link rel="shortcut icon" type="image/png" href="https://www.recordtimeapp.com.au/images/favicon.png"/>


    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    {{ Html::style('assets/website/emailDocket/view.css') }}

    <script>
        window.Laravel = {!! json_encode([ 'csrfToken' => csrf_token()]) !!};
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <style type="text/css">
        .signature-pad{
            border: 1px solid #e8e8e8;
            background-color: #fff;
            border-radius: 4px;

        }
        @media only screen and (max-width: 620px) {
            .docket-details{
                text-align: left !important;
            }
            .docket-details>div{
                text-align: left!important;
                float: left !important;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="preview">
        <div class="docket-header">
            <div class="row">
                <div class="col-md-12">
                    <div style="border-bottom: 1px solid #ddd;padding-bottom: 20px;margin-bottom: 15px;text-align: right;">
                        <a class="btn btn-primary btn-sm" href="{{ url('docket/emailed/'.$id.'/download') }}">
                            <i class="fa fa-download" aria-hidden="true"></i> Download
                        </a>

                        @if($emailRecipient!= null)
                            @if($emailDocket->docketApprovalType == 1 || $emailDocket->docketApprovalType == 0 )
                                @if($emailRecipient->approval==1)
                                    @if($emailRecipient->status==1)
                                        <a class="btn btn-success btn-sm ml-2" href="#">
                                            <i class="fa fa-check" aria-hidden="true"></i> Approved
                                        </a>
                                    @else
                                        @if($emailDocket->docketApprovalType == 1)
                                            <a class="btn btn-success btn-sm ml-2" data-toggle="modal" data-target="#signatureApprove" style="color: #fff;">
                                                <i class="fa fa-check" aria-hidden="true"></i> Approve
                                            </a>
                                        @else
                                            <a class="btn btn-success btn-sm ml-2" href="{{ url('docket/emailed/'.$id.'/'.$emailRecipient->hashKey.'/approve') }}">
                                                <i class="fa fa-check" aria-hidden="true"></i> Approve
                                            </a>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
                @include('website.emailDocket.partial.docketInfo')
            </div>
        </div>
        <div class="docket-body">
            <div class="row">
                <div class="col-xs-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th class="printTh" style="width:50%"><div class="printColorDark">Description</div></th>
                            <th class="printTh" style="width:50%"><div class="printColorDark">Value</div></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($emailDocket->sentDocketValue()->count())
                            @foreach($emailDocket->sentDocketValue()->get() as $docketValue)
                                @if(!$docketValue->docketFieldInfo->is_hidden)
                                @if($docketValue->docketFieldInfo->docket_field_category_id==5)
                                    @include('website.emailDocket.modularField.image')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==7)
                                    @include('website.emailDocket.modularField.unitRate')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==8)
                                    @include('website.emailDocket.modularField.checkbox')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==9)
                                    @include('website.emailDocket.modularField.signature')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==12)
                                    @include('website.emailDocket.modularField.headerTitle')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==14)
                                    @include('website.emailDocket.modularField.sketchPad')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==15)
                                    @include('website.emailDocket.modularField.document')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==18)
                                    @include('website.emailDocket.modularField.yesNoNaCheckbox')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==20)
                                    @include('website.emailDocket.modularField.manualTimer')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==22)
                                    @include('website.emailDocket.modularField.grid')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==24)
                                    @include('website.emailDocket.modularField.tallyableUnitRate')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==27)
                                    @include('website.emailDocket.modularField.advanceHeader')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==29)
                                    @include('website.emailDocket.modularField.email')

                                @elseif($docketValue->docketFieldInfo->docket_field_category_id!=13 && $docketValue->docketFieldInfo->docket_field_category_id!=18 && $docketValue->docketFieldInfo->docket_field_category_id!= 30)
                                    @include('website.emailDocket.modularField.default')
                                @endif
                               @endif
                            @endforeach
                            @foreach($emailDocket->sentDocketValue()->get() as $docketValue)
                                @if($docketValue->docketFieldInfo->docket_field_category_id==13)
                                    @include('website.emailDocket.modularField.termsAndConditions')
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table><!--/.docket-table-value-->

                    @if($emailDocket->attachedTimer()->count()>0)
                        @include('website.emailDocket.modularField.attachedTimer')
                    @endif
                </div>
            </div>

            @if($emailDocket->docketApprovalType == 1 || $emailDocket->docketApprovalType == 0 )
                <br/><br/>
                <div class="row">
                    @if($emailDocket->docketApprovalType==0)
                        <div class="col-md-6">
                            <strong>Approved By:</strong>
                            @foreach($emailDocket->recipientInfo as $row)
                                @if($row->status==1)
                                    <p style="padding-top: 8px;">{{ $row->emailUserInfo->email }}  on {{\Carbon\Carbon::parse($row->approval_time)->format('d-M-Y h:i a T')}}</p>
                                    <div class="clearfix"></div>
                                    <br>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="col-md-6">
                            <strong>Approved By:</strong>
                            @foreach($emailDocket->recipientInfo as $row)
                                @if($row->status==1)
                                    <img src="{{ AmazoneBucket::url() }}{{ $row->signature }}" class="d-block" style="width:100px">
                                    <p style="padding-top: 8px;">{{ $row->emailUserInfo->email }}  on {{\Carbon\Carbon::parse($row->approval_time)->format('d-M-Y h:i a T')}}</p>
                                @endif
                                <div class="clearfix"></div>
                                <br>
                            @endforeach
                        </div>
                    @endif
                    <div class="col-md-6">
                        @if($emailDocket->status==0)
                            <strong>Pending Approval:</strong>
                            @foreach($emailDocket->recipientInfo as $row)
                                @if($row->approval==1 && $row->status==0)
                                    <span class="d-block">{{ $row->emailUserInfo->email }}</span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>


@if($emailDocket->docketApprovalType == 1)
    @if($emailRecipient->approval==1 && $emailRecipient->status==0)
        <div class="modal fade"  id="signatureApprove" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Docket Approval</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="margin-top: 4px;" class="form-group">
                                    <input type="hidden" id="docket_aprovel_name"  name="sentDocketId" value="">
                                    <input type="hidden" id="docket_hashKey" name="hashKey" value="">
                                    <label class="control-label" for="title">Name<b style="color:red;    font-size: 13px;">*</b></label>
                                    <input type="text" id="name_approval"  name="name" class="form-control" value="" required>
                                    <p style="display: none; font-size: 12px; color: red;" id="title" class="flashsuccess"><i>*Title Required</i> </p>
                                </div>
                                <div style="margin-top: 4px;" class="form-group">

                                    <div class="wrapper1">
                                        <label class="control-label" for="title">Signature<b style="color:red;    font-size: 13px;">*</b></label><br>
                                        <canvas id="signature-pad" class="signature-pad" width="466px" height="200px"></canvas>
                                        <p style="display: none; font-size: 12px; color: red;" class="flashsuccess" id="signature"><i>*Signature Required</i> </p>
                                    </div>
                                    <div>
                                        <button  class="btn btn-sm btn-link" style="position: absolute;right: 18px;top: 90px" id="clear">Clear</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="save" class="btn btn-primary">Authorise</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
        <script>
            if(window.innerWidth<466){
                $("#signature-pad").attr("width",(window.innerWidth -55)+"px");
            }
            console.log(window.innerWidth);
            var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });

            var saveButton = document.getElementById('save');
            var cancelButton = document.getElementById('clear');

            cancelButton.addEventListener('click', function (event) {
                signaturePad.clear();
            });


            saveButton.addEventListener('click', function (event) {


                if ($('input#name_approval').val() == ""){
                    $('#title').css('display','block');
                }
                else if(signaturePad.isEmpty()){
                    $('#signature').css('display','block');
                }else {
                    $(".flashsuccess").fadeOut();
                    var data = signaturePad.toDataURL('image/png');
                    var name_approval = $('input#name_approval').val();

                    $.ajax({
                        type: "POST",
                        data: {"signature": data,"name":name_approval,"_token": "{{ csrf_token() }}"},
                        url: "{{ url('docket/emailed/'.$id.'/'.$emailRecipient->hashKey.'/approve') }}",
                        success: function (response) {
                            window.location.href = "{{ url('docket/emailed/approved') }}";
                        }
                    });

                }
            });
        </script>
    @endif
@endif
</body>
</html>
