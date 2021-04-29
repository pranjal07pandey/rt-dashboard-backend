@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">Non Active Company</a></li>
            <li class="active">View</li>
        </ol>
    </section>

    <div class="containerDiv" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">

                <strong>All Non Active Company</strong><br/><br/>
                <table class="table" id="datatable">
                    <thead>
                    <tr>
                        <th>Company Id</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Employee</th>
                        <th>Last Active</th>
                        <th width="120px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($company)
                        @foreach($company as $row)
                            <tr>
                                <td>
                                  {{$row->id}}
                                </td>
                                <td>
                                    @if ($row->name != "")
                                        {{$row->name}}
                                    @else
                                        <i style="color: #adbbd6;">no company name</i>
                                    @endif
                                </td>
                                <td>
                                    {{$row->userInfo->email }}
                                </td>
                                <td>
                                   @if (count($row->employees))
                                        @foreach($row->employees as $employee)
                                            {{$employee->userInfo->email}}
                                        @endforeach
                                   @else
                                      <i style="color: #adbbd6;">no employee</i>
                                   @endif

                                </td>
                                <td>
                                    @php
                                      $now = Carbon\Carbon::now();
                                      $oldDate = Carbon\Carbon::parse($row->userInfo->created_at);
                                      $diffday =   $oldDate->diffForHumans($now);
                                    @endphp

                                    {{$diffday}}

                                </td>
                                <td>
                                    <button data-toggle="modal" data-target="#deleteNonActiveCompany" data-id="{{$row->userInfo->id}}" class="btn btn-danger" style="font-size: 12px;">delete</button>
                                </td>

                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>

            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteNonActiveCompany" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="font-size: 14px;">
                <div class="modal-header themeSecondaryBg " style="background: #008389;color: #fff; padding-bottom: 9px;">
                    <h4 class="modal-title" id="myModalLabel" style="    font-size: 14px;font-weight: 500;margin-top: -8px;"><i class="fa fa-plus"></i>&nbsp;Delete Non Active Company</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="    margin-top: -31px;"><span aria-hidden="true" style="color: white;font-size: 19px;">&times;</span></button>

                </div>
                {{ Form::open(['url' => 'dashboard/reports/nonActiveCompany/delete', 'files' => true]) }}

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="user_id" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to remove non active company?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="deletePrefillerValue">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>


@endsection

@section('customScript')
<script>
    $('#deleteNonActiveCompany').on('show.bs.modal', function(e) {
        var id = $(e.relatedTarget).data('id');
        $('#user_id').val(id)
    });
</script>

@endsection