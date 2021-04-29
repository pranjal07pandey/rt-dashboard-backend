<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Record Time</title>

    <!-- Fonts -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/bootstrap/css/bootstrap.min.css') }}">



    <!-- Styles -->
    <style>
        html, body {
            background-color: #ededed;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        /*.content {*/
        /*text-align: center;*/
        /*}*/

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .wrapper1 {
            position: relative;
            width: 400px;
            height: 200px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .wrapper1 img {
            position: absolute;
            left: 0;
            top: 0;
            margin-top: 36px;
        }

        .signature-pad {
            position: absolute;
            left: 0;
            top: 40px;
            width:565px;
            height:200px;
        }


        .box-timer {
            text-align: center;
            background: #f5f6f5;
            padding: 16px 0px 10px 0px;
            margin-bottom: 16px;
        }
        .modal-header {
            background: #022e55;
            padding: 15px;
            color: #fff;
            border-bottom: 1px solid #e5e5e5;
        }
        .authori {width:6em}
        .authori:hover span {display:none}
        .authori:hover:before {content:"Authorise"}
    </style>
</head>
<body class="mybody">


<a class="aprovalImage" style="display: none;margin-top: 60px;" href="http://www.recordtime.com.au/" target="_blank"><img src="{{ asset('assets/beta/images/logoWhite.jpg') }}" style="margin-top: 20px;margin: 0px auto; display: block;width: 200px;"></a>
<div style="text-align: center;margin-top: 19px;" class="m-b-md">
    <p class="messagesucess"></p>
</div>
<br><br>
<div class="container aprovalContain">
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px; box-shadow: 0px 0px 5px #88888859;">
        <div class="col-md-12">
            <div id="printContainer">
                <div class="row invoice-info">
                    <div class="col-md-4 invoice-col">
                        @if(AmazoneBucket::fileExist(@$sentDocket->senderCompanyInfo->logo))
                            <img src="{{ asset(@$sentDocket->senderCompanyInfo->logo) }}" style="height:150px;">
                        @else
                            <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                        @endif
                        <br/>From:<br/>
                        <strong>{{ @$sentDocket->sender_name }}</strong><br>
                        {{ @$sentDocket->company_name }}
                        <br>
                        {{ @$sentDocket->company_address }}<br>
                        <b>ABN:</b> {{ @$sentDocket->abn }}
                        <br/><br/>

                        To:<br/>
                            @if($receiverDetail)
                                <?php $sns = 0; ?>
                                @foreach($receiverDetail as $key=>$value)
                                    @foreach($value as $keys)
                                        <?php $sns++; ?>
                                        @if($sns<=count($sentDocket->recipientInfo) && $sns!=1)
                                            ,
                                        @endif
                                        {{$keys}}
                                    @endforeach

                                @endforeach
                                <br>
                                <?php $sn = 0; ?>
                                <b>Company Name:</b>

                                @foreach($receiverDetail as $key=>$value)
                                    <?php $sn++; ?>
                                    @if($sn<=count($receiverDetail) && $sn!=1)
                                        ,
                                    @endif
                                    {{$key}}
                                @endforeach
                            @endif


                    </div>
                    <!-- /.col -->

                    <div class="pull-right" style="text-align:left;width:170px;">
                        <strong>{{ $sentDocket->template_title }}</strong><br/>
                        <b>Date:</b> {{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}<br/>
                        <b>Docket ID:</b> doc {{ @$sentDocket->id }}<br>
                    </div>
                    <!-- /.col -->
                </div>
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <br/>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="printTh"><div class="printColorDark">Description</div></th>
                                <th class="printTh"><div class="printColorDark">Value</div></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($docketFields)

                                @foreach($docketFields as $row)
                                    @if((!$row->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))
                                        @if($row->docketFieldInfo->docket_field_category_id==7)
                                            <?php $sn = 1; $total = 0; ?>
                                            @foreach($row->sentDocketUnitRateValue as $row)
                                                <tr>
                                                    <td>{{ $row->docketUnitRateInfo->label }}</td>
                                                    <td>@if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}</td>
                                                    @if($sn == 1)
                                                        <?php $total = $row->value; ?>
                                                    @else
                                                        <?php $total    =   $total*$row->value; ?>
                                                    @endif
                                                    <?php $sn++; ?>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td>
                                                    <strong>Total:</strong>
                                                </td>
                                                <td>
                                                    <strong>$ {{ $total }}</strong>
                                                </td>
                                            </tr><!--unit-rate-->

                                        @elseif($row->docketFieldInfo->docket_field_category_id==8)
                                            <tr>
                                                <td> {{ $row->label }}</td>
                                                <td> @if($row->value==1) <i class="fa fa-check-circle" style="color:green"></i> @else <i class="fa fa-close" style="color:#ff0000 !important"></i>@endif  </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==9 )
                                            <tr>
                                                <td> {{ $row->label }}</td>

                                                <td>
                                                    @if($row->sentDocketImageValue->count()>0)
                                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                            @foreach($row->sentDocketImageValue as $signature)
                                                                <li style="margin-right:10px;float: left;">
                                                                    <a href="{{ asset($signature->value) }}" target="_blank">
                                                                        <img src="{{ asset($signature->value) }}" style=" width: 100px;height: 100px;border: 1px solid #ddd;margin: 10px;padding: 10px;">
                                                                    </a>
                                                                    <p style="font-weight: 500;color: #868d90;">{{$signature->name}}</p>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <b>No Signature Attached</b>
                                                    @endif
                                                </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==5)
                                            <tr>
                                                <td> {{ $row->label }}</td>
                                                <td>
                                                    @if($row->sentDocketImageValue->count()>0)
                                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                            @foreach($row->sentDocketImageValue as $signature)
                                                                <li style="margin-right:10px;float: left;">
                                                                    <a href="{{ asset($signature->value) }}" target="_blank">
                                                                        <img src="{{ asset($signature->value) }}" style="height: 100px;">
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <b>No  Image Attached</b>
                                                    @endif
                                                </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==14)
                                            <tr>
                                                <td> {{ $row->label }}</td>
                                                <td>
                                                    @if($row->sentDocketImageValue->count()>0)
                                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                            @foreach($row->sentDocketImageValue as $sketchPad)
                                                                <li style="margin-right:10px;float: left;">
                                                                    <a href="{{ asset($sketchPad->value) }}" target="_blank">
                                                                        <img src="{{ asset($sketchPad->value) }}" style="height: 100px;">
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <b>No Sketch Pad Attached</b>
                                                    @endif
                                                </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==15)
                                            <tr>
                                                <td> {{  $row->label }}</td>
                                                <td>
                                                    @if($row->sentDocketAttachment->count()>0)
                                                        <ul class="pdf">
                                                            @foreach($row->sentDocketAttachment as $document)
                                                                <li><img src="{{ asset('assets/pdf.png') }}"><b><a href="{{asset($document->url)}}" target="_blank">{{$document->document_name}}</a></b></li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <b>No Document Attached</b>
                                                    @endif
                                                </td>
                                            <!--<td> {{ $row->value }}</td>-->
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==12)
                                            <tr>
                                                <td  colspan="2"><strong>{{ $row->label }}</strong></td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==20)
                                            <tr>
                                                <td>{{ $row->label }}</td>
                                                <td>
                                                    @foreach($row->sentDocketManualTimer as $rows)
                                                        <strong>{{ $rows->label }} :</strong>  {{ $rows->value }} &nbsp; &nbsp;
                                                    @endforeach
                                                    <br>
                                                    @foreach($row->sentDocketManualTimerBreak as $item)
                                                        <strong>{{ $item->label }} :</strong>  {{ $item->value }}<br>
                                                        <strong>Reason for break :</strong>  {{ $item->reason }}<br>
                                                    @endforeach
                                                    <strong>Total time :</strong>  {{ $row->value }}<br>

                                                </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==18 )
                                            <tr>
                                                <td colspan="2">
                                                    <!--<table style="width:100%;">-->
                                                    <!--<tr>-->
                                                    @php
                                                        $yesno = unserialize($row->label);
                                                    @endphp
                                                    <div style="width:100%;margin:0;">
                                                        <div style="width:43%;float:left;">{{ @$yesno['title']}}</div>
                                                        @if($row->value == "N/a")
                                                            <div style="width:50%; float:right;margin-right: 38px;"> N/a </div>
                                                        @else
                                                            @if(@$yesno['label_value'][$row->value]['label_type']==1)
                                                                <div style="width:50%; float:right;margin-right: 38px;"><img style="width: 23px; background-color:{{ $yesno['label_value'][$row->value]['colour']}}; border-radius:20px;" src="{{ asset(@$yesno['label_value'][$row->value]['label'])}}"></div>
                                                            @else
                                                                <div style="width:50%; float:right;margin-right: 38px;">{{ @$yesno['label_value'][$row->value]['label']}}</div>
                                                            @endif
                                                        @endif
                                                    </div>

                                                    <!-- </tr>-->
                                                    <!--</table>-->
                                                    @if(count($row->SentDocValYesNoValueInfo)==0)
                                                    @else
                                                        <table style="background: transparent; width: 100%;" class="table table-striped">
                                                            <thead style="background: transparent; ">
                                                            <tr>
                                                                <th colspan="2"><h5 style=" margin-bottom: 0px;font-size: 12px;color: #929292;margin-left: -0px;">Explanation</h5></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody >
                                                            @foreach($row->SentDocValYesNoValueInfo as $items)
                                                                @if($items->YesNoDocketsField->docket_field_category_id==5)
                                                                    @php
                                                                        $imageData=unserialize($items->value);
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $items->label }}</td>
                                                                        <td>
                                                                            <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                @if(empty($imageData))
                                                                                    <b>No Image Attached</b>
                                                                                @else
                                                                                    @foreach($imageData as $rowData)
                                                                                        <li style="margin-right:10px;float: left;">
                                                                                            <a href="{{ asset($rowData) }}" target="_blank">
                                                                                                <img src="{{ asset($rowData) }}" style="height: 100px;">
                                                                                            </a>
                                                                                        </li>
                                                                                    @endforeach
                                                                                @endif
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                @if($items->YesNoDocketsField->docket_field_category_id==1)
                                                                    <tr>
                                                                        <td> {{ $items->label }}</td>
                                                                        <td>{{$items->value }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==6 )
                                            <tr>
                                                <td> {{ $row->label }}<br>

                                                </td>
                                                @if($row->value=="")
                                                    <td> N/a</td>
                                                @else
                                                    <td>  {{ $row->value }}</td>
                                                @endif
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id!=13 && $row->docketFieldInfo->docket_field_category_id!=17 && $row->docketFieldInfo->docket_field_category_id!=6 && $row->docketFieldInfo->docket_field_category_id!=20 && $row->docketFieldInfo->docket_field_category_id!=7 )
                                            <tr>
                                                <td> {{ $row->label }}</td>
                                                @if($row->value=="")
                                                    <td> N/a</td>
                                                @else
                                                    <td> {{ $row->value }}</td>
                                                @endif
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach

                                @foreach($docketFields as $row)
                                    @if((!$row->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))
                                        @if($row->docketFieldInfo->docket_field_category_id==13)
                                            <tr>
                                                <td  colspan="2"> <strong>{{ $row->label }}</strong><br>
                                                    {{ $row->value }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach


                            @endif
                            </tbody>
                            <tfoot>
                            {{--<tr>--}}
                            {{--<th colspan="6">Total</th>--}}
                            {{--<th>Rs. </th>--}}
                            {{--</tr>--}}
                            </tfoot>
                        </table>
                        @if($docketTimer->count()>0)
                            <div class="attachedTimer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p><b> <i class="fa fa-paperclip" aria-hidden="true"></i>
                                                Timer Attachements</b></p>
                                    </div>
                                    @php
                                        $totalInterval = 0;
                                    @endphp
                                    @if($docketTimer->count())
                                        @foreach($docketTimer as $row)
                                            <div class="col-md-2">
                                                <div class="box-timer">
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                    <p><strong>{{$row->timerInfo->total_timer}}</strong></p>
                                                    <i class="fa fa-map-marker" aria-hidden="true"></i> <span>{!!  str_limit(strip_tags($row->timerInfo->location),35) !!}</span>
                                                    <p> {{ \Carbon\Carbon::parse($row->timerInfo->time_started)->format('d-M-Y') }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>

                            </div>
                        @endif
                        <a style="    margin-bottom: 17px;"  id="first" class="btn btn-raised btn-xs  btn-success pull-right" data-toggle="modal" data-id="{{$sentDockets->email_sent_docket_id}}" data-hashkey="{{$sentDockets->hashKey}}" data-target="#myModal3" >
                            <i class="fa fa-check"></i>Approve
                        </a>
                        <!-- /.col -->

                    </div>
                </div>

            </div>
        </div>
    </div>

    <br/><br/>

</div>


<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            {{--{{ Form::open(['url' => 'dashboard/company/docketBookManager/docket/view/approve' , 'files' => true]) }}--}}
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color: #fff;">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Docket Approvel</h4>
            </div>
            <div class="modal-body" style="    height: 414px;">
                <div class="row">
                    <div class="col-md-12">
                        <div style="margin-top: 4px;" class="form-group">
                            <input type="hidden" id="docket_aprovel_name"  name="sentDocketId" value="">
                            <input type="hidden" id="docket_hashKey" name="hashKey" value="">
                            <label class="control-label" for="title">Name<b style="color:red;    font-size: 13px;">*</b></label>
                            <input type="text" id="name_approval"  name="name" class="form-control" value="" required>
                        </div>
                        <div style="margin-top: 4px;" class="form-group">

                            <div class="wrapper1">
                                <label class="control-label" for="title">Signature<b style="color:red;    font-size: 13px;">*</b></label><br><br>
                                <img style="background-color: #ebebeb;" name="signature" width=565 height=200 />
                                <canvas id="signature-pad" class="signature-pad"  width=565 height=200></canvas>
                            </div>
                            <div>
                                <button style="    position: absolute;right: 18px;top: 90px" id="clear">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div  style="margin-top: -11px;" class="modal-footer">
                <button id="save" type="submit" class="btn btn-primary authori"><span>Save</span></button>
            </div>
            {{--{{ Form::close() }}--}}
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
<script>
    $('#myModal3').on('shown.bs.modal', function (e) {
        var id = $(e.relatedTarget).data('id');
        var hashkeys = $(e.relatedTarget).data('hashkey');
        $("#docket_aprovel_name").val(id);
        $('#docket_hashKey').val(hashkeys);


    });
</script>
<script>
    var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'rgb(0, 0, 0)'
    });
    var saveButton = document.getElementById('save');
    var cancelButton = document.getElementById('clear');

    saveButton.addEventListener('click', function (event) {
        var data = signaturePad.toDataURL('image/png');
        var sentDocketId_approval = $('input[name=sentDocketId]').val();
        var name_approval = $('input[name=name]').val();
        var hashKeys = $('input[name=hashKey]').val();
        $.ajax({
            type: "POST",
            data: {signature: data, sentDocketId: sentDocketId_approval, name: name_approval, hashKey: hashKeys},
            url: "{{ url('approvedDocketSignature') }}",
            success: function (response) {
                $('.dashboardFlashsuccess').css('display', 'none');
                $('.dashboardFlashdanger').css('display', 'none');
                if (response['status'] == true) {
                    var wrappermessage = ".messagesucess";
                    $(wrappermessage).html(response["message"]);
//                        $("#mobileviewHtml").html(response);
                    $('.dashboardFlashsuccess').css('display', 'block');
                    $.ajax({
                        type: "GET",
                        url: "{{url('dashboard/company/docketBookManager/docket/docketApprovalTypeView/'.$sentDockets->id) }}",
                        success: function (response) {
                            $("#mobileviewHtml").html(response);
                        }
                    });
                    $('.form-approvaltype').css('display', 'none');
                    $('.aprovalContain').css('display', 'none');
                    $('.aprovalImage').css({'display': 'block','margin-top':'0px'});
                    $('.mybody').css({'background': '#ffffff','padding-top': '56px'});


                } else {
                    window.location.href = hashKeys;
                }
                $('#myModal3').modal('hide');

            }
        });

// Send data to server instead...
    });

    cancelButton.addEventListener('click', function (event) {
        signaturePad.clear();
    });
</script>
</body>


</html>
