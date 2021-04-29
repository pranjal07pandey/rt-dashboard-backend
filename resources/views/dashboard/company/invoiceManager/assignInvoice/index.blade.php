@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Assign Invoice
            <small>Add/Remove Assign Invoice</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Assign Invoice</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Assigned Invoice Template</h3>
            <div class="pull-right">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-plus-square"></i> Assign
                </button>
            </div>
            <div class="clearfix"></div>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Employee</th>
                    <th>Invoice Name</th>
                    <th>Assigned By</th>
                    <th>Date Added</th>
                    <th width="120">Action</th>
                </tr>
                </thead>
                <tbody>
                @if(@$assignedTemplate)
                    @foreach($assignedTemplate as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->userInfo->first_name." ".$row->userInfo->last_name }}</td>
                            <td>{{ $row->invoiceInfo->title }}</td>
                            <td>{{ $row->assignedBy->first_name." ".$row->assignedBy->last_name }}</td>
                            <td> <!--For Default time to another conversion-->
                                @php
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
                            <td>
                                {{--{{ Form::open(['method'=>'DELETE', 'url'=>['dashboard/company/invoiceManager/assignInvoice', $row->id], 'style'=>'display:inline-block;']) }}--}}
                                {{--{{ Form::button('<span class="glyphicon glyphicon-trash" aria-hidden="true"  />', array(--}}
                                                 {{--'type' => 'submit',--}}
                                                 {{--'class' => 'btn btn-raised btn-danger btn-xs',--}}
                                                 {{--'onclick'=>'return confirm("Are you sure you want to remove the access to this template?")'--}}
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
                @if(count(@$assignedTemplate)==0)
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
    <!-- Modal -->
    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Assign Invoice Template</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/invoiceManager/assignInvoice/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin-top:0px;">
                                <label for="employeeId" class="control-label">Employee</label>
                                <select id="employeeId" class="form-control" required name="employeeId[]">
                                    <option value="">Select Employee</option>
                                    @if($employees)
                                        @foreach($employees as $row)
                                            <option value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group" style="margin-top:0px;">
                                <label for="templateId" class="control-label">Invoice Template</label>
                                <select id="templateId" class="form-control" required name="templateId">
                                    <option value="">Select Docket Template</option>
                                    @if($templates)
                                        @foreach($templates as $row)
                                            <option value="{!! $row->id !!}">{!! $row->title !!}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Delete invoice Modal -->
    <div class="modal fade" id="invoiceTemplete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/invoiceManager/assignInvoice' ,'method'=>'delete', 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Unassign Invoice Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="invoice_templete" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to unassign this template?</p>
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

@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.fn.dataTable.moment( 'D-MMM-YYYY' );
            $('#datatable').dataTable( {
                "order": [[ 4, "desc" ]]
            } );
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
@endsection