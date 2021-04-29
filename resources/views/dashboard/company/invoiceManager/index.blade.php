@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            Invoice Manager
            <small>Add/View Invoice</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Invoice Manager</a></li>
            <li class="active">View</li>
        </ol>
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}

    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Invoice Template</h3>
            {{--{{ dd($account) }}--}}
            <div class="pull-right">
                <!-- Button trigger modal -->
                <button type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info popupsecond" >
                    <i class="fa fa-plus-square"></i> Add New
                </button>
            </div>
            <div class="clearfix"></div>
            <br/>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th width="200">Assigned Folder</th>
                    <th>Created By</th>
                    <th>Date Added</th>
                    <th>Assigned</th>
                    <th width="120">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    $invoices    =    array();
                    $nonAssigned    =   array();
                    foreach ($invoice as $row){
                        if($row->assignedInvoice->count()>0){
                            $invoices[] =   $row;
                        }else{
                            $nonAssigned[]    =   $row;
                        }
                    }
                ?>
                @if(@$invoices)
                    @foreach($invoices as $row)
                        <tr @if($row->assignedInvoice->count()==0)style="background: #f6f6f6" @endif>
                            <td>{{ $row->title }}</td>
                            <td>
                                <div class="assignedFolderLink{{ $row->id }}">
                                    @if($row->docketFolderAssign)
                                        @foreach($data as $items)
                                            @if($items['id'] == $row->docketFolderAssign->folder_id)
                                                <i style="color: #EFCE4A;" class="fa fa-folder" aria-hidden="true"></i>
                                                {{@$items['value']}}
                                            @endif
                                        @endforeach

                                    @else
                                        <i style="font-size: 12px;">Not assigned yet.</i>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</td>
                            <td>   @php
                                    $format = 'Y-m-d H:i:s';
                                    $value = $row->created_at;
                                    $company =  @@App\Company::where('id',Session::get('company_id'))->first();
                                    if(!is_null($company->time_zone)){
                                        $canberra = \DateTime::createFromFormat($format, $value, $eb = new \DateTimeZone('Australia/Canberra'));
                                        $sydney = \DateTime::createFromFormat($format, $value, $as = new \DateTimeZone($company->time_zone));
                                        $offset = \DateTime::createFromFormat($format, $value, $eb)->setTimezone($as);
                                        echo $offset->format('d-M-Y H:i:s');
                                    }else{

                                         echo \Carbon\Carbon::parse($row->created_at)->format('d-M-Y H:i:s');
                                    }
                                @endphp
                            </td>
                            <td>@if($row->assignedInvoice->count()==0)No @else Yes @endif</td>
                            <td>
                                <a href="{{ url('dashboard/company/invoiceManager/designInvoice/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"  ><i class="fa fa-eye"></i></a>
                                <a  data-toggle="modal" data-target="#invoiceTemplete" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs"  >
                                    <span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  />
                                </a>

                                @if($row->docketFolderAssign)
                                    <a  data-toggle="modal" data-target="#unAssignedFolder" data-id="{{$row->id}}" data-name="{{$row->title}}" data-folder="{{$row->docketFolderAssign->folder_id}}"  data-folderid="{{$row->docketFolderAssign->id}}" style="background-color: #ff5722;" class="btn btn-raised btn-info btn-xs buttonChanger{{$row->id}}" >
                                        <i class="fa fa-folder-o" aria-hidden="true"></i>
                                        UnAssign Folder
                                    </a>
                                @else
                                    <a  data-toggle="modal" data-target="#assignedFolder" data-id="{{$row->id}}" data-name="{{$row->title}}" style="background-color: #ff9b00;" class="btn btn-raised btn-info btn-xs buttonChanger{{$row->id}}" >
                                        <i class="fa fa-folder-o" aria-hidden="true"></i>
                                        Assign Folder
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                @if(@$nonAssigned)
                    @foreach($nonAssigned as $row)
                        <tr @if($row->assignedInvoice->count()==0)style="background: #f6f6f6" @endif>
                            <td>{{ $row->title }}</td>
                            <td> <i style="font-size: 12px;">Not assigned yet.</i></td>
                            <td>{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</td>
                            <td>   @php
                                    $format = 'Y-m-d H:i:s';
                                    $value = $row->created_at;
                                    $company =  @@App\Company::where('id',Session::get('company_id'))->first();
                                    if(!is_null($company->time_zone)){
                                        $canberra = \DateTime::createFromFormat($format, $value, $eb = new \DateTimeZone('Australia/Canberra'));
                                        $sydney = \DateTime::createFromFormat($format, $value, $as = new \DateTimeZone($company->time_zone));
                                        $offset = \DateTime::createFromFormat($format, $value, $eb)->setTimezone($as);
                                        echo $offset->format('d-M-Y H:i:s');
                                    }else{

                                         echo \Carbon\Carbon::parse($row->created_at)->format('d-M-Y H:i:s');
                                    }
                                @endphp
                            </td>
                            <td>@if($row->assignedInvoice->count()==0)No @else Yes @endif</td>
                            <td>
                                <a href="{{ url('dashboard/company/invoiceManager/designInvoice/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"   ><i class="fa fa-eye"></i></a>
                                {{--{{ Form::open(['method'=>'DELETE', 'url'=>['dashboard/company/invoiceManager/designInvoice', $row->id], 'style'=>'display:inline-block;']) }}--}}
                                {{--{{ Form::button('<span class="glyphicon glyphicon-trash" aria-hidden="true"  />', array(--}}
                                                 {{--'type' => 'submit',--}}
                                                {{--'style'=>'    padding: 6px 15px;',--}}
                                                 {{--'class' => 'btn btn-raised btn-danger btn-xs',--}}
                                                 {{--'onclick'=>'return confirm("Are you sure you want to delete this invoice template?")'--}}
                                             {{--))--}}
                                         {{--}}--}}
                                {{--{{ Form::close() }}--}}
                                <a  data-toggle="modal" data-target="#invoiceTemplete" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs"  >
                                    <span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  />
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                @if(count(@$invoice)==0)
                    <tr>
                        <td colspan="6">
                            <center>Data Empty</center>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <br/><br/>



    <!-- Delete invoice Modal -->
    <div class="modal fade" id="invoiceTemplete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/invoiceManager/designInvoice' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Invoice Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="invoice_templete" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to delete this invoice template?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div id="second" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Invoice</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/invoiceManager/saveInvoice', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Invoice Name</label>
                                <input type="text" name="invoiceTitle" class="form-control" required="required" value="{!! old('invoiceTitle') !!}">
                                <input type="hidden" name="helpFlaginvoice" id="helpFlaginvoice" value="false">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Next</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignedFolder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{--{{ Form::open(['url' => 'dashboard/company/docketBookManager/docketDuplicate','method'=>'POST', 'files' => true]) }}--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Assigned Folder</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="assignTempalteErrorMessage" style="display: none;     color: white;background: red;padding: 0 0 0px 11px;font-size: 15px;">   </p>
                            <p style="    margin-left: 14px;font-size: 15px;font-weight: 600;">Template Name: <span class="assignFolderName" style="font-weight: 100;"></span> </p>
                            <input type="hidden" id="templateId">
                            <div class="col-md-12">
                                <strong>Folder</strong>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group" style="margin-top:0px;">
                                            <div style="position:relative">
                                                <select id="assignFolderId" class="form-control" name="type">
                                                    <option value="">Select Folder</option>
                                                    @if(@$data)
                                                        @foreach ($data as $datas)
                                                            <option value="{{$datas['id']}}">{{$datas['space']}}{{$datas['name'][0]}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submitAssignFolder" class="btn btn-primary">Save</button>
                </div>
            </div>
            {{--{{ Form::close() }}--}}
        </div>
    </div>



    <div class="modal fade" id="unAssignedFolder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{--{{ Form::open(['url' => 'dashboard/company/docketBookManager/archiveDocketTemplate','method'=>'POST', 'files' => true]) }}--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;UnAssign Folder</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="unassignTempalteErrorMessage" style="display: none;     color: white;background: red;padding: 0 0 0px 11px;font-size: 15px;">   </p>
                            <input type="hidden" id="unassignFolderId">
                            <input type="hidden" id="unassignTemplateId">
                            <input type="hidden" id="unassignTemplateName">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to UnAssign Folder from this template?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submitUnAssignFolder" class="btn btn-primary">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
            {{--{{ Form::close() }}--}}
        </div>
    </div>

    <style>
        .templet-trash{
            padding: 2px 0px 2px 0px;
        }
        [type="checkbox"]:not(:checked) + label,
        [type="checkbox"]:checked + label {
            position: relative;
            padding-left: 1.95em;
            cursor: pointer;
        }

        /* checkbox aspect */
        [type="checkbox"]:not(:checked) + label:before,
        [type="checkbox"]:checked + label:before {
            content: '';
            position: absolute;
            left: 0; top: 0;
            width: 1.25em; height: 1.25em;
            border: 2px solid #ccc;
            background: #fff;
            border-radius: 4px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,.1);
        }
        /* checked mark aspect */
        [type="checkbox"]:not(:checked) + label:after,
        [type="checkbox"]:checked + label:after {
            content: '\2713\0020';
            position: absolute;
            top: .15em; left: .22em;
            font-size: 1.3em;
            line-height: 0.8;
            color: #09ad7e;
            transition: all .2s;
            font-family: 'Lucida Sans Unicode', 'Arial Unicode MS', Arial;
        }
        /* checked mark aspect changes */
        [type="checkbox"]:not(:checked) + label:after {
            opacity: 0;
            transform: scale(0);
        }
        [type="checkbox"]:checked + label:after {
            opacity: 1;
            transform: scale(1);
        }
        /* disabled checkbox */
        [type="checkbox"]:disabled:not(:checked) + label:before,
        [type="checkbox"]:disabled:checked + label:before {
            box-shadow: none;
            border-color: #bbb;
            background-color: #ddd;
        }
        [type="checkbox"]:disabled:checked + label:after {
            color: #999;
        }
        [type="checkbox"]:disabled + label {
            color: #aaa;
        }
        /* accessibility */
        [type="checkbox"]:checked:focus + label:before,
        [type="checkbox"]:not(:checked):focus + label:before {
            border: none;
        }

        /* hover style just for information */
        label:hover:before {
            border: 2px solid #4778d9!important;
        }
    </style>
@endsection

@section('customScript')
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/jquery.steps.css') }}">

    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <script src="{{asset('assets/dashboard/js/jquery.steps.min.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap-tour.min.css')}}">
    {{--<script src="{{asset('assets/dashboard/tour/jquery.min.js')}}"></script>--}}
    {{--<script src="{{asset('assets/dashboard/tour/bootstrap.js')}}"></script>--}}
    <script src="{{asset('assets/dashboard/tour/bootstrap-tour.js')}}"></script>
    <script src="{{asset('assets/dashboard/tour/invoicetour.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatable').DataTable({
                "order": [[ 3, "desc" ]]
            });
        } );
    </script>
    <script>
        $(document).ready(function() {
            $('#invoiceTemplete').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#invoice_templete").val(id);
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $("#wizard").steps({
                enableFinishButton:true,
                onFinishing: function (event, currentIndex) {
                    document.getElementById("stepForm").submit();

                },
                labels: {
                    finish: "Submit",
                }




            });


            $('#myModal3').on('shown.bs.modal', function () {
                $('#myWizard').easyWizard();
            });
        });
    </script>
    <script>
        $(document).on('click','#labelCheckyes',function () {
            var checkLabel1 = document.getElementById('labelCheckno');
            checkLabel1.checked=false;
            $(".xeroWant").css("display", "block");

        });
        $(document).on('click','#labelCheckno',function () {
            var checkLabel2 = document.getElementById('labelCheckyes');
            checkLabel2.checked=false;
            $(".xeroWant").css("display", "none");
        });
    </script>

    <script>
        $(document).on('change','#line', function () {
            var labelTypeValue = $(this).val();
            if(labelTypeValue == "NoTax"){
                $("#tax option").each(function(item){
                    var element =  $(this) ;
                    if ( element.data("tag") == "NONE-0-Tax Exempt" ) {
                        $("#tax > [value = 'NONE-0-Tax Exempt']").prop('selected', 'selected');
                        $("#tax").prop('disabled', 'disabled');
                        $("#hiddenTaxrate").prop('disabled', false);
                    }
                });

            }else{
                $("#tax").prop('disabled', false);
                $("#hiddenTaxrate").prop('disabled', 'disabled');
            }

        });
        $(document).on('change','#accountTypess', function () {
            var labelTypeValue = $(this).val();
            if (labelTypeValue == "ACCPAY") {
                $("#discountRate").prop('readonly', 'readonly');
                document.getElementById("discountRate").value = 0;
            }else {
                $("#discountRate").prop('readonly', false);
            }
        });
    </script>
    <script>
        $(document).on('click','#sendingyes',function () {
            var sendingno = document.getElementById('sendingno');
            sendingno.checked=false;

        });
        $(document).on('click','#sendingno',function () {
            var sendingyes = document.getElementById('sendingyes');
            sendingyes.checked=false;
        });
    </script>

    <script>
        $(document).on('change','#labelCheckyes',function () {
            $(".spinerSubDocket").css("display", "block");
            if (this.checked) {
                $.ajax({
                    type: "POST",
                    url: '{{ url('dashboard/company/invoiceManager/xero/invoiceDesignXeroSetting/') }}',
                    success: function( response ) {
                        $("#xeroSet").html(response);
                        $(".spinerSubDocket").css("display", "none");
                        console.log($('.xeroStatus').val()==0);
                        if ($('.xeroStatus').val()==0){
                                $("#wizard").steps({
                                    enableFinishButton: false,
                                });
                        }
                    }
                });



            }
        });

        $(document).ready(function() {
            $('#assignedFolder').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var  title = $(e.relatedTarget).data('name');
                $(".assignFolderName").text(title);
                $('#templateId').val(id);



            });
        });

        $(document).on('click','#submitAssignFolder',function () {
            var templateId = $('#templateId').val();
            var type = 2;
            var folderId = $('#assignFolderId').val();
            var name = $('.assignFolderName').text();
            var assignTempalteErrorMessage = ".assignTempalteErrorMessage";
            $(assignTempalteErrorMessage).css('display','none');
            $.ajax({
                type:"Post",
                url: '{{ url('/dashboard/company/folder/assignTemplateFolder')}}',
                data:{'folderId':folderId,'type':type,'templateId':templateId,'name':name},
                success: function (response) {
                    if (response.status == true){
                        $(response.buttonConfig[1]).replaceWith(response.buttonConfig[0]);
                        $(response.buttonConfig[2]).replaceWith(response.buttonConfig[3]);
                        $('#assignedFolder').modal('hide');
                    }else if(response.status == false){
                        $(assignTempalteErrorMessage).css('display','block');
                        $(assignTempalteErrorMessage).html('<i class="fa fa-exclamation-circle"></i> '+response.message);
                    }
                }
            });


        });


        $(document).ready(function() {
            $('#unAssignedFolder').on('show.bs.modal', function(e) {
                var folderId = $(e.relatedTarget).data('folderid');
                var templateId =$(e.relatedTarget).data('id')
                var templateName =$(e.relatedTarget).data('name')
                $("#unassignFolderId").val(folderId);
                $("#unassignTemplateId").val(templateId);
                $("#unassignTemplateName").val(templateName);


            });
        });

        $(document).on('click','#submitUnAssignFolder',function () {
            var folderId =  $("#unassignFolderId").val();
            var templateId =  $("#unassignTemplateId").val();
            var templateName =  $("#unassignTemplateName").val();
            var assignTempalteErrorMessage = ".unassignTempalteErrorMessage";
            $(assignTempalteErrorMessage).css('display','none');

            $.ajax({
                type:"Post",
                url: '{{ url('/dashboard/company/folder/unassignTemplateFolder')}}',
                data:{'folderId':folderId,'templateId':templateId,'templateName':templateName},
                success: function (response) {
                    if (response.status == true){
                        $(response.buttonConfig[1]).replaceWith(response.buttonConfig[0]);
                        $(response.buttonConfig[2]).replaceWith(response.buttonConfig[3]);
                        $('#unAssignedFolder').modal('hide');
                    }else if(response.status == false){
                        $(assignTempalteErrorMessage).css('display','block');
                        $(assignTempalteErrorMessage).html('<i class="fa fa-exclamation-circle"></i> '+response.message);
                    }
                }
            });


        });
    </script>




@endsection