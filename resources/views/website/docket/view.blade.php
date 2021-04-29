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
                        <a class="btn btn-primary btn-sm" href="{{ url('docket/'.$id.'/download') }}">
                            <i class="fa fa-download" aria-hidden="true"></i> Download
                        </a>

                    @if($recipient!= null)
                        @if($sentDocket->docketApprovalType == 1 || $sentDocket->docketApprovalType == 0 )
                            @if($sentDocket->status==0)
                                @if($sentDocket->sentDocketRecipientApproval)
                                    @if(in_array(@$recipient->id,$sentDocket->sentDocketRecipientApproval->pluck('user_id')->toArray()))
                                        @if(App\SentDocketRecipientApproval::where('sent_docket_id',$sentDocket->id)->where('status',1)->where('user_id',@$recipient->id)->count()==1)
                                            <a href="javascript:void(0);" class="btn btn-success btn-sm ml-2" id="addNew"><i class="fa fa-check"></i> Approved</a>
                                        @elseif(App\SentDocketRecipientApproval::where('sent_docket_id',$sentDocket->id)->where('status',3)->where('user_id',@$recipient->id)->count()==1)
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm ml-2" id="addNew"><i class="fa fa-check"></i> Rejected</a>
                                        @else
                                            @if($sentDocket->docketApprovalType==0)
                                                <a class="btn btn-success btn-sm ml-2" href="{{ url('docket/'.$id.'/'.\Illuminate\Support\Facades\Crypt::encrypt(@$recipient->id).'/approve') }}">
                                                    <i class="fa fa-check" aria-hidden="true"></i> Approve
                                                </a>
                                                <button class="btn btn-sm btn-success btn-danger ml-2"  data-toggle="modal" data-target="#rejectDocketModal"><i class="fa fa-check"></i> Reject</button>
                                            @else
                                                <a id="first" class="btn btn-success btn-sm ml-2" data-toggle="modal" data-target="#signatureApprove" style="color:#fff;"><i class="fa fa-check"></i> Approve</a>
                                                <button class="btn btn-sm btn-success btn-danger ml-2"  data-toggle="modal" data-target="#rejectDocketModal"><i class="fa fa-check"></i> Reject</button>
                                            @endif
                                        @endif
                                    @else
                                        <a href="javascript:void(0);" class="btn btn-sm  btn-warning  ml-2" id="addNew"><i class="fa fa-check"></i> Pending</a>
                                    @endif
                                @endif

                            @elseif($sentDocket->status==3)
                                <a href="javascript:void(0);" class="btn btn-sm btn-danger ml-2"><i class="fa fa-check"></i> Rejected</a>
                            @else
                                <a href="javascript:void(0);" class="btn btn-sm  btn-success ml-2"><i class="fa fa-check"></i> Approved</a>
                            @endif
                        @endif
                    @endif    
                        
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="pull-left">
                        @if(AmazoneBucket::fileExist($sentDocket->company_logo))
                            <img src="{{ AmazoneBucket::url() }}{{ $sentDocket->company_logo }}" style="max-width: 100%;max-height:150px;" class="company-logo mb-0">
                        @else
                            <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=logo" class="company-logo">
                        @endif
                    </div>
                    <div class="pull-right">
                        <div class="text-left">
                            <strong>{{ $sentDocket->template_title }}</strong><br/>
                            <b>Date:</b> {{ $sentDocket->formattedCreatedDate() }}<br/>
                            <b>{{$sentDocket->docketInfo->docket_id_label}}:</b>  {{ $sentDocket->formattedDocketID() }}<br>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-md-7">
                    <br/>From:<br/>
                    <strong>{{ $sentDocket->sender_name }}</strong><br>
                    {{ $sentDocket->company_name }}
                    <br>
                    {{ $sentDocket->company_address }}<br>
                    <b>ABN:</b> {{ $sentDocket->abn }}
                </div>
                <div class="col-md-5">
                    <br/>To:
                    @if($sentDocket->formattedRecipientList())
                        <?php $sns = 0; ?>
                        @foreach($sentDocket->formattedRecipientList() as $key=>$value)
                            @foreach($value as $keys)
                                @php $sns++; @endphp
                                @if($sns<=count($sentDocket->recipientInfo) && $sns!=1) , @endif
                                {{ $keys }}
                            @endforeach
                        @endforeach
                        <br>
                        <?php $sn = 0; ?>
                        <b>Company Name:</b>
                        @foreach($sentDocket->formattedRecipientList() as $key=>$value)
                            <?php $sn++; ?>
                            @if($sn<=count($sentDocket->formattedRecipientList()) && $sn!=1) , @endif
                            {{$key}}
                        @endforeach
                    @endif
                </div>
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
                            @if($sentDocket->sentDocketValue()->count())
                                @foreach($sentDocket->sentDocketValue()->get() as $docketValue)
                                    @if($recipient == null)
                                            @if(!$docketValue->docketFieldInfo->is_hidden)
                                                @if($docketValue->docketFieldInfo->docket_field_category_id==5)
                                                    @include('website.docket.modularField.image')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==7)
                                                    @include('website.docket.modularField.unitRate')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==8)
                                                    @include('website.docket.modularField.checkbox')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==9)
                                                    @include('website.docket.modularField.signature')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==12)
                                                    @include('website.docket.modularField.headerTitle')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==14)
                                                    @include('website.docket.modularField.sketchPad')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==15)
                                                    @include('website.docket.modularField.document')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==18)
                                                    @include('website.docket.modularField.yesNoNaCheckbox')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==20)
                                                    @include('website.docket.modularField.manualTimer')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==22)
                                                    @include('website.docket.modularField.grid')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==24)
                                                    @include('website.docket.modularField.tallyableUnitRate')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==27)
                                                    @include('website.docket.modularField.advanceHeader')
                                                    
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id==29)
                                                    @include('website.docket.modularField.email')
            
                                                @elseif($docketValue->docketFieldInfo->docket_field_category_id!=13 && $docketValue->docketFieldInfo->docket_field_category_id!=18 && $docketValue->docketFieldInfo->docket_field_category_id!= 30)
                                                    @include('website.docket.modularField.default')
                                                @endif
                                            @endif
                                        @else
                                        @if((!$docketValue->docketFieldInfo->is_hidden && $sentDocket->sender_company_id != @$recipient->company()->id) || $sentDocket->sender_company_id==@$recipient->company()->id)
                                            @if($docketValue->docketFieldInfo->docket_field_category_id==5)
                                                @include('website.docket.modularField.image')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==7)
                                                @include('website.docket.modularField.unitRate')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==8)
                                                @include('website.docket.modularField.checkbox')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==9)
                                                @include('website.docket.modularField.signature')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==12)
                                                @include('website.docket.modularField.headerTitle')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==14)
                                                @include('website.docket.modularField.sketchPad')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==15)
                                                @include('website.docket.modularField.document')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==18)
                                                @include('website.docket.modularField.yesNoNaCheckbox')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==20)
                                                @include('website.docket.modularField.manualTimer')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==22)
                                                @include('website.docket.modularField.grid')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==24)
                                                @include('website.docket.modularField.tallyableUnitRate')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==27)
                                                @include('website.docket.modularField.advanceHeader')
                                                
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id==29)
                                                @include('website.docket.modularField.email')
        
                                            @elseif($docketValue->docketFieldInfo->docket_field_category_id!=13 && $docketValue->docketFieldInfo->docket_field_category_id!=18 && $row->docketFieldInfo->docket_field_category_id!= 30)
                                                @include('website.docket.modularField.default')
                                            @endif
                                        @endif
                                    @endif
                                @endforeach

                                @foreach($sentDocket->sentDocketValue()->get() as $docketValue)
                                    @if($recipient == null)
                                        @if(!$docketValue->docketFieldInfo->is_hidden )
                                            @if($docketValue->docketFieldInfo->docket_field_category_id==13)
                                                @include('website.docket.modularField.termsAndConditions')
                                            @endif
                                        @endif
                                    @else
                                        @if((!$docketValue->docketFieldInfo->is_hidden && $sentDocket->sender_company_id!=@$recipient->company()->id) || $sentDocket->sender_company_id==@$recipient->company()->id)
                                            @if($docketValue->docketFieldInfo->docket_field_category_id==13)
                                                @include('website.docket.modularField.termsAndConditions')
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table><!--/.docket-table-value-->
                    @if($sentDocket->attachedTimer()->count()>0)
                        @include('website.docket.modularField.attachedTimer')
                    @endif
                </div>
            </div>

 
            @if($sentDocket->docketApprovalType == 1 || $sentDocket->docketApprovalType == 0 )
                @if($sentDocket->status != 3)
                    <br/>
                    <div class="row">

                        @if($sentDocket->docketApprovalType==0)
                            <div class="col-md-6">
                                <strong>Approved By:</strong>
                                @foreach($sentDocket->sentDocketRecipientApproval as $row)
                                    @if($row->status==1)
                                        <p style="padding-top: 8px;">{{ $row->userInfo->first_name." ".$row->userInfo->last_name }}  on {{\Carbon\Carbon::parse($row->approval_time)->format('d-M-Y h:i a T')}}</p>
                                        <br>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="col-md-6">
                                <strong>Approved By:</strong>
                                @foreach($sentDocket->sentDocketRecipientApproval as $row)
                                    @if($row->status==1)
                                        <img src="{{ AmazoneBucket::url() }}{{ $row->signature }}" class="d-block" style="width:100px">
                                        <p>{{ $row->name }}  on {{\Carbon\Carbon::parse($row->approval_time)->format('d-M-Y h:i a T')}}</p>
                                    @endif
                                    <div class="clearfix"></div>
                                    <br>
                                @endforeach
                            </div>
                        @endif
                        <div class="col-md-6">
                            @if($sentDocket->status==0)
                                <strong>Pending Approval:</strong>
                                @foreach($sentDocket->sentDocketRecipientApproval as $row)
                                    @if($row->status==0)
                                        <span class="d-block">{{ $row->userInfo->first_name." ".$row->userInfo->last_name }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
            @endif
            @if(count($sentDocket->sentDocketRejectExplanation) != 0)
                <strong>Rejected By:</strong>
                @foreach($sentDocket->sentDocketRejectExplanation as $sentDocketRejection)
                    <p style="padding-top: 8px;" class="mb-0"><b>{{$sentDocketRejection->userInfo->first_name}} </b>: {{$sentDocketRejection->explanation}}  on {{\Carbon\Carbon::parse($sentDocketRejection->created_at)->format('d-M-Y h:i a T')}}</p>
                    @if($sentDocketRejection!=$sentDocket->sentDocketRejectExplanation->last())<br/>@endif
                @endforeach
            @endif
        </div>
    </div>
</div>

@if($sentDocket->docketApprovalType == 1 || $sentDocket->docketApprovalType == 0 )
    @if($sentDocket->status==0)
        @if($sentDocket->sentDocketRecipientApproval)
            @if(in_array(@$recipient->id,$sentDocket->sentDocketRecipientApproval->pluck('user_id')->toArray()))
                @if(App\SentDocketRecipientApproval::where('sent_docket_id',$sentDocket->id)->where('status',1)->where('user_id',@$recipient->id)->count()==1)
                @elseif(App\SentDocketRecipientApproval::where('sent_docket_id',$sentDocket->id)->where('status',3)->where('user_id',@$recipient->id)->count()==1)
                @else
                    @if($sentDocket->docketApprovalType!=0)
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
                                                    <input type="hidden" id="docket_approval_name"  name="sentDocketId" value="">
                                                    <label class="control-label" for="title">Name<b style="color:red;    font-size: 13px;">*</b></label>
                                                    <input type="text" id="name_approval"  name="name" class="form-control" value="" required>
                                                    <p style="display: none; font-size: 12px; color: red;" id="title" class="flashsuccess"><i>Name Required</i> </p>
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
                    @endif
                    <!--reject popup-->
                    <div class="modal fade"  id="rejectDocketModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Reject Docket</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post" action="{{ url('docket/'.$id.'/reject') }}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div style="margin-top: 4px;" class="form-group">
                                                    <input type="hidden"  name="user_id" value="{{ \Illuminate\Support\Facades\Crypt::encrypt(@$recipient->id) }}">
                                                    <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to reject this Docket?</p>

                                                    <label class="control-label" for="title">Explain<b style="color:red;    font-size: 13px;">*</b></label>
                                                    <input type="text"  name="explanation" class="form-control" value="" required>
                                                    <p style="display: none; font-size: 12px; color: red;" id="title" class="flashsuccess"><i>*Explanation Required</i> </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="save" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
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
                                    url: "{{ url('docket/'.$id.'/'.\Illuminate\Support\Facades\Crypt::encrypt(@$recipient->id).'/approve') }}",
                                    success: function (response) {
                                        window.location.href = "{{ url('docket/emailed/approved') }}";
                                    }
                                });
                            }
                        });
                    </script>
                @endif
            @endif
        @endif
    @endif
@endif
</body>
</html>