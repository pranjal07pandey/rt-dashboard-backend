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
  <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
    <div class="col-md-12">
      <div class="row  with-border" style="border-bottom: 1px solid #ddd;margin-bottom: 15px;padding-bottom:10px;">
          <div class="col-md-6 text-left">
              <a  class="btn btn-default btn-sm" href="{{ url()->previous() }}" style="margin:0px;"><i class="fa fa-reply"></i> Back</a>
          </div>
          <div class="col-md-6 text-right">
              <button  class="btn btn-default btn-sm" onclick="location.href='{{url('dashboard/company/docketBookManager/docket/downloadViewDocket/'.$sentDocket->id)}}';" style="margin: 0px 0px 0px;"><i class="fa fa-download" aria-hidden="true"></i></i> Download</button>
              <button  class="btn btn-default btn-sm" id="printDiv" style="margin: 0px 0px 0px;"><i class="fa fa-print"></i> Print</button>
          </div>
      </div>
      <div id="printContainer">
        <div class="docket-box">
          <table border="0" cellspacing="0" cellpadding="0" class="table">
            <thead>
              <tr>
                
                <td class="barcode">
                  <ul class="list-unstyled">
                      <li><b>{{ @$sentDocket->company_name }}</b></li>
                      <li>{{ @$sentDocket->company_address }}</li>
                      <li class="abn"><b>ABN:</b> {{ @$sentDocket->abn }}</li>
                  </ul>
                </td>
                <td>
                  @if(AmazoneBucket::fileExist(@$sentDocket->senderCompanyInfo->logo))
                      <img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->senderCompanyInfo->logo }}">
                  @else
                      <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                  @endif
                </td>
              </tr>
              
            </thead>
          </table>
          
          <table border="0" cellspacing="0"cellpadding="0" class="table-x">
            <tbody>
              <tr>
                <td><b>{{ @$sentDocket->docketInfo->title }}</b></td>
                <td class="date-docket"><b>Date:</b> {{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}</td>
              </tr>
              <tr>
                <td colspan="2" class="date-docket"><b>Docket ID:</b> {{ $sentDocket->id }}</td>
              </tr>
            </tbody>
          </table>

          <table border="0" cellspacing="0" cellpadding="0" class="table-one">
              <tbody>
                <tr>
                  <td>
                    <ul class="list-unstyled">
                      <li>From:</li>
                      <li><b>{{ @$sentDocket->sender_name }}</b></li>
                      <li><b>Company Name:</b> {{ @$sentDocket->company_name }}</li>
                      <li class="email"> {{ @$sentDocket->senderUserInfo->email }}</li>
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td>
                    <ul class="list-unstyled name">
                      <li>To:</li>
                      @if($sentDocket->recipientInfo)
                      <?php $sn = 1; ?>
                        @foreach($sentDocket->recipientInfo as $recipient)
                            <li> <b>{{ @$recipient->userInfo->first_name }} {{ @$recipient->userInfo->last_name }}</b></li>
                            <li class="email">{{ @$recipient->userInfo->email }}</li>
                            @if($sn!=$sentDocket->recipientInfo->count())
                                ,
                            @endif
                            <?php $sn++; ?>
                        @endforeach
                        <?php $sns = 1; ?>
                        <li><b>Company Name:</b>
                        @foreach($company as $companys)
                            {{$companys->name}}
                            @if($sns!=$company->count())
                                ,</li>
                            @endif
                            <?php $sns++; ?>
                        @endforeach
                      @endif
                    </ul>
                  </td>
                </tr>
              </tbody>
          </table>

          <div class="table2">
              <table border="0" cellspacing="0" cellpadding="0" class="table-two">
                <tbody>
                  <tr class="head-data">
                    <td>Description</td>
                    <td>Values/Amount</td>
                  </tr>
                  @if($docketFields)
                    @foreach($docketFields as $row)
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

                        <tr >
                            <td>
                                <strong>Total:</strong>
                            </td>
                            <td>
                                <strong>$ {{ $total }}</strong>
                            </td>
                        </tr>
                      @elseif($row->docketFieldInfo->docket_field_category_id==8)
                        <tr>
                            <td> {{ $row->label }}</td>
                            <td> @if($row->value==1) <i class="fa fa-check-circle" style="color:green"></i> @else <i class="fa fa-close" style="color:#ff0000 !important"></i>@endif  </td>
                        </tr>
                      @elseif($row->docketFieldInfo->docket_field_category_id==9)
                        <tr>
                            <td> {{ $row->label }}</td>

                            <td>
                                @if($row->sentDocketImageValue->count()>0)
                                    <ul style="list-style: none;margin: 0px;padding: 0px;">
                                        @foreach($row->sentDocketImageValue as $signature)
                                            <li style="margin-right:10px;float: left;">
                                                <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                                                    <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height: 100px;border: 1px solid #ddd;margin: 10px;padding: 10px;">
                                                </a>
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
                                                <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                                                    <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height: 100px;">
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
                                                <a href="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" target="_blank">
                                                    <img src="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" style="height: 100px;">
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
                            <td> {{ $row->label }}</td>
                            <td>
                                @if($row->sentDocketAttachment->count()>0)
                                    <ul class="pdf">
                                        @foreach($row->sentDocketAttachment as $document)
                                            <li><img src="{{ asset('assets/pdf.png') }}"><b><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">{{$document->document_name}}</a></b></li>
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
                      @elseif($row->docketFieldInfo->docket_field_category_id!=13)
                        <tr>
                            <td> {{ $row->label }}</td>
                            @if($row->value=="")
                                <td> N/a</td>
                            @else
                                <td> {{ $row->value }}</td>
                            @endif
                        </tr>
                      @endif
                    @endforeach
                    @foreach($docketFields as $row)
                        @if($row->docketFieldInfo->docket_field_category_id==13)
                            <tr>
                                <td  colspan="2"> <strong>{{ $row->label }}</strong><br>
                                    {{ $row->value }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                  @endif
                </tbody>
              </table>
            </div>
          <div class="main-size" id="mobileviewHtml">
              @include('dashboard.company.docketManager.docket.approvalTypeView')
          </div>
          <footer>
            Docket was created on Recordtime
          </footer>
        </div>
      </div>
      <div class="row no-print">
          <div class="col-xs-12">
              @if($sentDocket->status==0)
                  @if($sentDocket->sender_company_id==Session::get('company_id'))
                      @if($sentDocket->sentDocketRecipientApproval)
                          @if(in_array(Auth::user()->id,$sentDocket->sentDocketRecipientApproval->pluck('user_id')->toArray()))
                              @if($sentDocket->docketApprovalType==0)
                                  <a href="" class="btn btn-raised btn-xs  btn-success pull-right"  sentDocketIdsappr="{{$sentDocket->id}}" id="ApproveDocketType"><i class="fa fa-check"></i> Approve</a>
                              @else
                                  <a  id="first" class="btn btn-raised btn-xs  btn-success pull-right" data-toggle="modal" data-id="{{$sentDocket->id}}" data-target="#myModal3" >
                                      <i class="fa fa-check"></i>Approve
                                  </a>
                              @endif
                          @else
                              <a href="#" class="btn btn-raised btn-xs  btn-warning pull-right" id="addNew"><i class="fa fa-check"></i> Pending</a>
                          @endif
                      @endif
                  @else
                      @if($sentDocket->docketApprovalType==0)
                          <a href="" class="btn btn-raised btn-xs  btn-success pull-right"  sentDocketIdsappr="{{$sentDocket->id}}" id="ApproveDocketType"><i class="fa fa-check"></i> Approve</a>
                      @else
                          <a  id="first" class="btn btn-raised btn-xs  btn-success pull-right" data-toggle="modal" data-id="{{$sentDocket->id}}" data-target="#myModal3" >
                              <i class="fa fa-check"></i>Approve
                          </a>
                      @endif
                  @endif
              @else
                  <a href="#" class="btn btn-raised btn-xs  btn-success pull-right" id="addNew"><i class="fa fa-check"></i> Approved</a>
              @endif
          </div>
      </div>
    </div>
  </div>
  <br /><br />
  <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Docket Approvel</h4>
            </div>
            <div class="modal-body" style="    height: 446px;">
                <div class="row">
                    <div class="col-md-12">
                        <div style="margin-top: 4px;" class="form-group">
                            <input type="hidden" id="docket_aprovel_name"  name="sentDocketId" value="">
                            <label class="control-label" for="title">Name</label>
                            <input type="text" id="name_approval"  name="name" class="form-control" value="{{Auth::user()->first_name}} {{Auth::user()->last_name}}">
                        </div>
                        <div style="margin-top: 4px;" class="form-group">
                            <div class="wrapper1">
                                <label class="control-label" for="title">Signature</label><br><br>
                                <img style="background-color: #ebebeb;" name="signature" width=550 height=200 />
                                <canvas id="signature-pad" class="signature-pad"  width=532 height=200></canvas>
                            </div>
                            <div>
                                <button style="    position: absolute;right: 2px;top: 9px;" id="clear">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="margin-top: -89px;" class="modal-footer">
                <button id="save" type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
  </div>
  <style type="text/css">
    @font-face {
      font-family: Arial, sans-serif; 
      src: url(SourceSansPro-Regular.ttf);
    }

    a {
      color: #0087C3;
      text-decoration: none;
    }

    body {
      position: relative;
      width: 100%;  
      min-height: 1500px;
      margin: 0 auto; 
      color: #555555;
      background: #FFFFFF; 
      font-family: Arial, sans-serif; 
      font-size: 14px; 
    }
    table{
      width: 100%;
      /* padding-right: 15px;
      padding-left: 15px; */
      padding-top: 15px;
    }
    table.table{
      color: #ffffff;
      background-color: #c9c9c594;
      padding-bottom: 15px;
    }
    table.table img{
      float: right;
      width: 130px;
    }
    table.table-one{
      padding-top: 0px;
    }

    td.barcode ul{
      float: left;
      text-align: left;
      color: #686464;
      font-size: 16px;
    }

    .date-docket{
      line-height: 1.2rem;
      text-align: right;
    }
    tr.date-docket td{
      text-align: right;
    }
    ul.list-unstyled{
    list-style: none;
    margin: 0;
    padding: 0;
    }
    ul.list-unstyled{
      line-height: 1.5rem;
    }
    li.email{
      color: #27449f;
    }
    div.table2{
      /* padding-left: 15px;
      padding-right: 15px; */
    }
    ul.name{
      padding-top: 15px;
    }
    table.table-two{
      margin-top: 30px;
      border-collapse: collapse;
    }
    table.table-two td{
      padding: 10px;
      border:1px solid #a09b9b75;
    }
    tr.head-data td{
      font-size: 16px;
      color: #ee4430;
    }
    table.table-three th{
      width: 100%;
      padding-top: 30px;
    }
    table.table-three img{
      width: 100px;
      padding-right: 15px;
    }
    th.images{
      padding-bottom: 15px;
      text-align: left;
    }

    p.footer-text{
      color: gray;
    }
    table.table-five img{
      width: 70px;
    }





    footer{
      text-align: center;
      padding-top: 30px;
    }
  </style>
@endsection

@section('customScript')
    <script type="text/javascript" src="{{ asset('assets/dashboard/js/printThis.js') }}"></script>
    <style>
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
        .dotted {
            border: 3px dashed  #919191;
            border-style: none none dashed;
            color: #fff;
            background-color: #fff;
        }

        .box-signature-shown{
            position: relative;
        }
        #blah{
            position: absolute;
            top: 16px;
            height: 200px;
            width: 100%;

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
            width:532px;
            height:200px;
        }
        .box-timer{
            text-align: center;
            background: #f5f6f5;
            padding: 16px 0px 10px 0px;
            margin-bottom: 16px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#printDiv").on("click",function(){
                $('#printContainer').printThis({
                    removeInline: false,
                    importCSS: true,
                    importStyle: true
                });
            });
        });
        $('#myModal3').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $("#docket_aprovel_name").val(id);



        });
    </script>
    <script>
      function readURL(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
        $('#blah').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
        }
        }

        $("#imgInp").change(function() {
        readURL(this);
      });
    </script>
    <script>
      $(document).on('click', '#ApproveDocketType', function(){
          var sentDocketIdsappr = $(this).attr('sentDocketIdsappr');
          $.ajax({
              type: "POST",
              data: {sentDocketId:sentDocketIdsappr},
              url: "{{ url('dashboard/company/docketBookManager/docket/view/approve') }}",
              success: function (response) {
                  $('.dashboardFlashsuccess').css('display','none');
                  if (response['status']==true) {
                      var wrappermessage = ".messagesucess";
                      $(wrappermessage).html(response["message"]);
//                        $("#mobileviewHtml").html(response);
                      $('.dashboardFlashsuccess').css('display','block');
                      $.ajax({
                          type: "GET",
                          url:"{{url('dashboard/company/docketBookManager/docket/approvalTypeView/'.$sentDocket->id) }}",
                          success:function (response) {
                              $("#mobileviewHtml").html(response);
                          }
                      });
                  }

              }


          });

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
            $.ajax({
                type: "POST",
                data: {signature: data,sentDocketId:sentDocketId_approval,name:name_approval},
                url: "{{ url('dashboard/company/docketBookManager/docket/view/approve') }}",
                success: function (response) {
                    $('.dashboardFlashsuccess').css('display','none');
                    if (response['status']==true) {
                        var wrappermessage = ".messagesucess";
                        $(wrappermessage).html(response["message"]);
//                        $("#mobileviewHtml").html(response);
                        $('.dashboardFlashsuccess').css('display','block');
                        $.ajax({
                            type: "GET",
                            url:"{{url('dashboard/company/docketBookManager/docket/approvalTypeView/'.$sentDocket->id) }}",
                            success:function (response) {
                                $("#mobileviewHtml").html(response);
                            }
                        });
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
@endsection