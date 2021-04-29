@extends('layouts.companyDashboard')
@section('content')

    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Docket Book Manager
            <small>Add/View Project Manager</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Docket Book Manager</a></li>
            <li class="active">View</li>
        </ol>
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
    </section>
    @include('dashboard.company.include.flashMessages')
{{--   {{\App\Http\Controllers\CompanyDashboard::}}--}}

    <br>
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="clearfix"></div>
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 29px;font-weight: 500;display:inline-block">All Projects</h3>
            <div class="pull-right">
                <!-- Button trigger modal -->
                <button style="margin-top: -1px;" type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#projectAdd">
                    <i class="fa fa-plus-square"></i> Add New
                    <div class="ripple-container"></div></button>
            </div>
        </div>
        <div class="col-md-12">
            <div class="datatable">
                <table class="table" id="emailClientDataTable">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Project Name</th>
                        <th>Budget</th>
                        <th>Number of Sent Docket</th>
                        <th>Running Total</th>
                        <th width="200px">Date Added</th>
                        <th width="180">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $sn = 1 ?>
                    @if(@$project)
                      @foreach($project as $proo)

                          <?php

                          $emailDocket = array();
                          $docket = array();

                          foreach ($proo->sentDocketProjectInfo as $sentDocketProjects){
                              if ($sentDocketProjects->is_email == 0){
                                  $docket[] = $sentDocketProjects->sent_docket_id;

                              }else if ($sentDocketProjects->is_email == 1){
                                  $emailDocket[] = $sentDocketProjects->sent_docket_id;
                              }
                          }


                          $emailDockets = \App\EmailSentDocket::wherein('id',$emailDocket)->get();
                          $dockets = \App\SentDockets::whereIn('id',$docket)->get();


                          $docketTallyValue = array();
                          $emailDocketTallyValue = array();
                          foreach ($dockets as $items){
                              foreach ($items->docketInfo->docketField as $docketField){
                                  if ($docketField->docket_field_category_id == 24 ){
                                      $sn = 1; $total = 0;
                                      foreach($docketField->docketFieldValueBySentDocketId($items->id)->first()->sentDocketTallyableUnitRateValue as $row){
                                          if($sn == 1){
                                              $total = $row->value;
                                          }else{
                                              $total    =   $total*$row->value;
                                          }
                                          $sn++;
                                      }


                                      $docketTallyValue[] =  $total;


                                  }elseif($docketField->docket_field_category_id == 25 ){

                                      $docketTallyValue[] =   intval($docketField->docketFieldValueBySentDocketId($items->id)->first()->value);

                                  }
                              }

                          }



                          foreach ($emailDockets as $items){
                              foreach ($items->docketInfo->docketField as $docketField){
                                  if ($docketField->docket_field_category_id == 24 ){
                                      $sn = 1; $total = 0;
                                      foreach($docketField->docketFieldValueByEmailSentDocketId($items->id)->first()->sentDocketTallyableUnitRateValue as $row){
                                          if($sn == 1){
                                              $total = $row->value;
                                          }else{
                                              $total    =   $total*$row->value;
                                          }
                                          $sn++;
                                      }


                                      $emailDocketTallyValue[] =  $total;


                                  }elseif($docketField->docket_field_category_id == 25 ){
                                      $emailDocketTallyValue[] =   intval($docketField->docketFieldValueByEmailSentDocketId($items->id)->first()->value);

                                  }
                              }

                          }


                          $totalValue = array_sum(array_merge($emailDocketTallyValue,$docketTallyValue));
                          ?>

                            <tr>
                                <td>{{$sn}}</td>
                                <td>{{$proo->name}}</td>
                                <td>${{$proo->budget}}</td>
                                <td>{{$proo->sentDocketProjectInfo->count()}}</td>
                                <td>${{$totalValue}}</td>
                                <td>{{ \Carbon\Carbon::parse($proo->created_at)->format('d-M-Y') }}</td>

                                <td>
                                    <a  href="{{ url('dashboard/company/docketManager/project/'.$proo->id) }}"  class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                                    <a  data-toggle="modal" data-target="#projectUpdate" data-name="{{$proo->name}}"  data-budget="{{$proo->budget}}"  data-id="{{$proo->id}}"  class="btn btn-info btn-xs btn-raised"  ><i class="fa fa-pencil"></i></a>
                                    <a  data-toggle="modal" data-target="#projectDelete"  data-project="{{$proo->id}}"  class="btn btn-danger btn-xs btn-raised"  ><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php $sn++ ?>
                      @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br/><br/>
    <div class="modal fade" id="projectAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/docketManager/project', 'files' => true]) }}

            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Project</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Project Name</label>
                                <input type="text" class="form-control" id="projectName" name="project_name" >
                            </div>

                            <div class="form-group">
                               <label>Budget</label>
                               <input type="number" class="form-control" name="budget" step="0.01" >
                            </div>

                            <div class="form-group">
                                <label>Select Docket Template</label>
                                <select class="form-control" name="docket_id[]" multiple>
                                    @foreach($docketDetail as $rows)
                                            <option value="{{$rows->id}}">{{$rows->title}}</option>
                                    @endforeach
                                </select>
                                <small>Select Docket Template (Template must have the tallyable field).</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>


    <div class="modal fade" id="projectDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div id="model" data-target="#myModal"></div>
{{--            {{ Form::open(['url' => 'dashboard/company/docketManager/prefillerManager/deletePrefillerLabel' ,'method'=>'delete', 'files' => true]) }}--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Text Manager Label</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="project_id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this Project?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="deleteProjectItems">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
{{--            {{ Form::close() }}--}}
        </div>
    </div>


    <div class="modal fade" id="projectUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/docketManager/project/updates', 'files' => true]) }}

            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Project</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="proj_id" name="project_id">
                            <div class="form-group">
                                <label>Update Project Name</label>
                                <input type="text" class="form-control" id="projectsname" name="project_name" >
                            </div>

                            <div class="form-group">
                                <label>Budget</label>
                                <input type="number" class="form-control" name="budget" id="budgets"  step="0.01">
                            </div>

                            <div class="form-group">
                                <label>Select Docket Template <span class="spinnerCheck" style="font-size: 17px;display: none;position: absolute;top: 0px;color: black;left: 21%;"><i class="fa fa-spinner fa-spin"></i></span></label>


                                <div id="selectDocket"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit"  class="btn btn-primary">Add</button>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>


@endsection



@section('customScript')

    <!-- Stylesheet -->

    <!-- jQuery & Bootstrap -->

    <style>
        #emailClientDataTable_filter{
            position: absolute;
            right: 110px;
            top: -54px;
        }
        .dashboardcode-bsmultiselect  .dropdown-menu {
            height: 125px;
            overflow: auto;
        }
        .dashboardcode-bsmultiselect  .dropdown-menu .px-2 {
            margin: 0 0 0 11px;
        }
        .form-group.is-focused label{
            color: lightslategray !important;

        }
        .is-focused .dashboardcode-bsmultiselect ul .form-control{
            box-shadow: none !important;
            border-color: transparent !important;
        }

        .badge{
            padding: 1px 7px 1px 0 !important;
            margin-left: 8px !important;
            background-color: #15b1b8 !important;
        }
        .badge span{
            margin-left: 14px;
        }
        .badge button{
            margin-left: -6px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#projectDelete').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('project');
                $("#project_id").val(id);

            });

            $("#deleteProjectItems").on('click', function () {
                var id = $("#project_id").val();
                $.ajax({
                    type: "Delete",
                    url: "{{url('dashboard/company/docketManager/project/')}}"+'/'+id,
                    success: function (response) {
                        $('#projectDelete').modal('hide')
                        window.location.reload();

                    }
                });


            });


            $('#projectUpdate').on('show.bs.modal', function(e) {
                $(".spinnerCheck").css('display','block');
                var name = $(e.relatedTarget).data('name');
                var budget = $(e.relatedTarget).data('budget');
                var id = $(e.relatedTarget).data('id');
                $("#proj_id").val(id);
                $("#budgets").val(budget)
                $("#projectsname").val(name)
                $.ajax({
                    type: "get",
                    url: "{{url('dashboard/company/docketManager/project/create')}}",
                    data: {id: id},
                    success: function (response) {
                        $('#selectDocket').html(response['data'])
                        $("select").bsMultiSelect({})
                        $(".spinnerCheck").css('display','none');
                    }
                });





            });


        });


        $("select").bsMultiSelect({
            selectedPanelDefMinHeight: 'calc(2.25rem + 2px)',
            selectedPanelLgMinHeight: 'calc(2.875rem + 2px)',
            selectedPanelSmMinHeight: 'calc(1.8125rem + 2px)',
            selectedPanelDisabledBackgroundColor: '#e9ecef',
            selectedPanelFocusBorderColor: '#80bdff',
            selectedPanelFocusBoxShadow: '0 0 0 0.2rem rgba(0, 123, 255, 0.25)',
            selectedPanelFocusValidBoxShadow: '0 0 0 0.2rem rgba(40, 167, 69, 0.25)',
            selectedPanelFocusInvalidBoxShadow: '0 0 0 0.2rem rgba(220, 53, 69, 0.25)',
            filterInputColor: '#495057',
            selectedItemContentDisabledOpacity: '.65',
            dropdDownLabelDisabledColor: '#6c757d',
            containerClass: 'dashboardcode-bsmultiselect',
            dropDownItemClass: 'px-2',
            dropDownItemHoverClass: 'text-primary bg-light',
            selectedPanelClass: 'form-control',
            selectedItemClass: 'badge',
            removeSelectedItemButtonClass: 'close',
            filterInputItemClass: '',
            filterInputClass: ''
        });




    </script>
@endsection