@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Invoice Book Manager
            <small>Add/View Invoice Label</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Invoice Label</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;     margin-bottom: 60px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Invoice Label</h3>
            <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px; margin: 0;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Invoice Label" data-content="Invoice labels are like tags or post-it notes. They help you mark off or highlight a docket to keep track of its status. You can colour code it, use an icon or name it as required. Example: processed, invoiced, entered in MYOB"><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

            <div class="pull-right">
                <!-- Button trigger modal -->
                <button type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info"  data-toggle="modal" data-target="#myModal"  >
                    <i class="fa fa-plus-square"></i> Add New
                </button>
            </div>
            <div class="clearfix"></div>
            <br/>
            <div id="app">
                <invoicelabel-component></invoicelabel-component>
            </div>
        </div>
    </div>


    <style>
        .colorpicker:before {
            content: none !important ;
            display: inline-block;
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 7px solid #ccc;
            border-bottom-color: rgba(0,0,0,.2);
            position: absolute;
            top: -7px;
            left: 6px;
        }
        .colorpicker:after {
            content: none !important;
            display: inline-block;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid #fff;
            position: absolute;
            top: -6px;
            left: 7px;
        }
       .vue-swatches .vue-swatches__container{
            z-index: 11111;
        }
        .popover-title{
            background: #2570ba;
            color: #ffffff;
        }
        .popover-content{
            color: #000000;
        }
        .popover.top {
            margin-top: -3px;
        }



    </style>

@endsection
@section('customScript')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">

    <script src="{{asset('js/app.js')}}"></script>


@endsection
