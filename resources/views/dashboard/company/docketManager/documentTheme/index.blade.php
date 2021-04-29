@extends('layouts.companyDashboard')
@section('content')
 	<section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Document Theme Manager
            <small>View/Purchase</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Document Theme Manager</a></li>
            <li class="active">View/Purchase</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="dashboardFlashsuccess" style="display: none;">
        <div class="alert alert-success" style="padding: 5px 10px;font-size: 13px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
            <p class="messagesucess"></p>
        </div>
    </div>
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="clearfix"></div>
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 0px;font-weight: 500;display:inline-block">All Document Themes</h3>
            <hr>
        </div>
        <div class="col-md-12">
            <div class="controls pull-left">
                <button class="filter btn btn-info mixitup-control-active" data-mixitup-control data-filter="all">ALL</button>
                <button class="filter btn btn-info" data-mixitup-control data-filter=".free">FREE</button>
                <button class="filter btn btn-info" data-mixitup-control data-filter=".paid">PAID</button>
                <button class="filter btn btn-info" data-mixitup-control data-filter=".purchased">PURCHASED</button>
            </div>
            <div class="controls pull-right">
                {{--<fieldset class="filter-group search">--}}
                    <input type="text" class="big-dog input" placeholder="Search" data-ref="input-search"/>
                {{--</fieldset>--}}
            </div>
            <div class="clearfix"></div>
            <div class="conta" data-ref="container">


                <div class="row">
                    @foreach ($themes as $row )

                        @php
                            $theme_purchase = @@App\ThemePurchase::where('company_id', $company->id)->where('theme_id', $row->id)->first();
                        @endphp
                        {{--@foreach( unserialize($row->screenshot) as  $rowData)--}}
                        {{--<img src="{{asset($rowData)}}" style="width:20px;height: 20px;">--}}
                       {{--@endforeach--}}

                        <div class="mix  @if($row->paid_free == 0) free @elseif($row->id == @$theme_purchase->theme_id) purchased @else  paid  @endif  col-md-4" >
                           <div class="overlay">
                                <img src="{{ AmazoneBucket::url() }}{{ $row->preview }}" style="width:100%">

                           </div>
                           <div class="middle">
                                <button class="btn btn-primary pull-right" style="color: #ffffff;" data-toggle="modal" data-target="#themeDetail" data-description="{{ $row->description }}" data-title="{{$row->name}}" data-id="{{ $row->id }}" data-image="{{$row->screenshot}}">DETAILS</button>

                           </div>
                            <h3>{{ $row->name }}</h3>
                        <div class="price-detail">
                                @if($row->paid_free == 0)
                                <span style="color: #15b1b8;text-transform: uppercase; font-size: 12px;font-weight: 600;" class="pull-left">Free</span>
                                @else
                                    <span style="color: #15b1b8;text-transform: uppercase; font-size: 12px;font-weight: 600;" class="pull-left"> A$&nbsp;{{ $row->price }}</span>
                                @endif
                                    @if($row->paid_free == 1)
                                        @if($row->id == @$theme_purchase->theme_id)
                                            <button style="color: #fff;    text-transform: uppercase !important; background: rgb(180, 180, 180);" class="btn btn-primary  pull-right">Purchased</button>
                                        @else
                                            <button type="button" class="btn btn-primary  pull-right" data-id="{{ $row->id }}" data-name="{{$row->name}}" style="margin:0px;" data-toggle="modal" data-target="#purchaseModal"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
                                        @endif
                                    @else
                                        {{--<button class="btn btn-primary  pull-right" ><i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>--}}
                                @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="gap"></div>
                <div class="gap"></div>
            </div>
        </div>
    </div>
    <br />
    <!-- Preview Modal -->
    <div class="modal fade " id="previewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <h4 class="modal-title" id="myModalLabel">Theme Preview</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="search-box">
                        <div class="row">
                        	<div class="col-md-12">
                        		<div class="form-group">
                        			<input type="text" id="name" class="form-control">
                        		</div>
                        	</div>
                        	<div class="col-md-12">
                        		<div class="form-group">
                        			<textarea class="form-control" id="description"></textarea>
                        		</div>
                        	</div>
                        	<div class="col-md-12" style="max-height: 500px; overflow-x: scroll;">
                        		 <img src="" id="preview" width="100%">
                        	</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="themeDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="    min-height: 611px;">
                <div class="modal-header themeSecondaryBg">
                    <h4 class="modal-title popname" id="myModalLabel ">Theme Preview</h4>
                    <button style="    margin-top: -25px;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="popDescription" style="    font-size: 14px;font-weight: 500;"></p>
                    <div>
                        <h5 style="font-weight: 600;margin-top: 20px;">Previews</h5>
                        <div id="wait" style="display: block; width: 59px;height: 59px;border: none;position: absolute;left: 49%;margin-top: 131px;"><img src='http://web-graphique.com.tn/smart/themes/default/assets/img/demo_wait.gif' width="64" height="64" /><br></div>

                        <div id="mobileviewHtml">

                            {{--@include('dashboard.company.docketManager.documentTheme.screenshot')--}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Purchase Modal -->
    <div class="modal fade " id="purchaseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
			<form accept-charset="UTF-8" action="{{ route('purchase.theme') }}" method="post">
			{{ csrf_field() }}
        	<script src='https://checkout.stripe.com/checkout.j' class="stripe-button" data-key="pk_test_uFaaxpDmcLnwnjddP8bFPyIt" data-amount="999" data-name="Record Time" data-description="Widget" data-image="https://stripe.com/img/documentation/checkout/marketplace.png" data-locale="auto" data-currency="aud"></script>
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <h4 class="modal-title" id="myModalLabel">Purchase Theme</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="search-box">
                        <div class="row">
                        	<div class="col-md-12">
                        		<div class="form-group">
                        			<input type="hidden" name="id" id="theme_id">
                        			<label class="control-label" for="message">Name</label><br/>
                        			<input type="text" id="theme_name" class="form-control">
                        		</div>
                        	</div>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer">
                    <button type="submit" class="btn btn-primary purchase">Purchase</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    <style>
        .mixitup-control-active{
            color: #fff !important;
            background-color: #15b1b8;
            border-color: #15b1b8;
           outline:0;
        }
      .btn:not(.btn-raised):not(.btn-link):hover{
           color: #fff;
           background-color: #15b1b8;
           border-color: #15b1b8;
          outline:0;
       }
       .btn:not(.btn-raised).btn-info, .input-group-btn .btn:not(.btn-raised).btn-info {
           font-size: 12px;
           color: #000000;
           font-weight: 600;
           padding: 3px 23px 3px 23px;
           border-radius: 30px;
           outline:0;
       }
       .btn:not(.btn-raised):not(.btn-link):focus, .btn:not(.btn-raised):not(.btn-link):hover, .input-group-btn .btn:not(.btn-raised):not(.btn-link):focus, .input-group-btn .btn:not(.btn-raised):not(.btn-link):hover {
           color: #fff !important;
           background-color: #15b1b8;
           border-color: #15b1b8;
           outline:0;
       }
       input[type="text"].big-dog::-webkit-input-placeholder {
           color: #d4d4d4;
           /*background-color: #eeeeeecf;*/
           font-style: italic;
           /*padding-left: 10px;*/
           /*border-radius: 20px;*/

           font-size: 12px;


       }
       input{
           border: none;
           color: #d4d4d4;
           background-color: #eeeeeecf;
           /*font-style: italic;*/
           border-radius: 20px;
           padding-top: 4px;
           padding-bottom: 4px;
           padding-left: 10px;
       }
        /*.filter-group{*/
            /*background-color: #d4d4d4;*/
           /**/
        /*}*/

        .mix{
            margin-bottom: 30px;
        }
        .conta{
            margin-top: 10px;
        }
        .mix h3{
            font-size: 13px;
            font-weight: 600;
        }
        .price-detail{
            margin-top: -7px;
        }
        .mix img{
            border-radius: 5px;
            opacity: 1;
            display: block;
            width: 100%;
            height: auto;
            transition: .5s ease;
            backface-visibility: hidden;
        }
        .price-detail button{
            background: #15b1b8;
            color: #fff;
            padding: 0px 20px 0px 20px;
            border-radius: 20px;
            font-size: 11px;
            margin: 0;
            outline:0;
        }
       .price-detail button i{
           color: #fff;

       }
       /*.image {*/
         /**/
       /*}*/

       .middle {
           transition: .5s ease;
           opacity: 0;
           position: absolute;
           top: 41%;
           left: 49%;
           transform: translate(-50%, -50%);
           -ms-transform: translate(-50%, -50%);
           text-align: center;

       }

       .mix:hover img {
         opacity: 0.3;
       }

       .mix:hover .middle {
           opacity: 1;
       }

       .middle  button{
           background: #15b1b8;
           color: #fff;
           padding: 2px 14px 2px 14px;
           border-radius: 20px;
           font-size: 13px;
           margin: 0;
       }
       .overlay {
           top: 0;
           bottom: 0;
           left: 0;
           right: 0;
           height: 100%;
           width: 100%;
           transition: .5s ease;
           background-color: #a6a5a5;
           border-radius: 5px;
       }
       button:focus {outline:0;}


    </style>
@endsection
@section('customScript')
    <script src="{{ asset('assets/dashboard/mixitup.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/prefixfree.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#previewModal').on('show.bs.modal', function(e) {
                var  name= $(e.relatedTarget).data('name');
                var description = $(e.relatedTarget).data('description');
                var preview = $(e.relatedTarget).data('preview');
                $("#name").val(name);
                $("#description").val(description);
                
                if(preview != "{{ asset('') }}"){
                    $("#preview").attr("src",preview);
                }
            });

            $('#purchaseModal').on('show.bs.modal', function(e) {
            	var  id= $(e.relatedTarget).data('id');
                var  name= $(e.relatedTarget).data('name');
                 $("#theme_id").val(id);
                $("#theme_name").val(name);
            });
        });

    </script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('#themeDetail').on('show.bs.modal', function(e) {
                var  name= $(e.relatedTarget).data('title');
                var  id= $(e.relatedTarget).data('id');
                var  description= $(e.relatedTarget).data('description');
                var  image= $(e.relatedTarget).data('image');
                $('.popname').text(name);
                $('#popDescription').text(description);
                $("#wait").css("display", "block");
                $.ajax({
                    type: "get",
                    data: {themeId:id},
                    url: "{{ url('/dashboard/company/docketManager/documentTheme/screenshot') }}",
                    success: function(response){
                        $("#wait").css("display", "none");
                        $("#mobileviewHtml").html(response);

                    }

                });



            });
        });
    </script>
    <script>
        var mixer = mixitup('.conta', {
            controls: {
                toggleDefault: 'none'
            },
            selectors: {
                control: '[data-mixitup-control]'
            }
        });


    </script>
    {{--<script>--}}
        {{--var container = document.querySelector('[data-ref="container"]');--}}
        {{--var inputSearch = document.querySelector('[data-ref="input-search"]');--}}

        {{--var keyupTimeout;--}}
        {{--var mixer = mixitup(container, {--}}
            {{--animation: {--}}
                {{--duration: 350--}}
            {{--},--}}
            {{--callbacks: {--}}
                {{--onMixClick: function() {--}}
                    {{--// Reset the search if a filter is clicked--}}
                    {{--if (this.matches('[data-filter]')) {--}}
                        {{--inputSearch.value = '';--}}
                    {{--}--}}
                {{--}--}}
            {{--}--}}
        {{--});--}}
        {{--// Set up a handler to listen for "keyup" events from the search input--}}
        {{--inputSearch.addEventListener('keyup', function() {--}}
            {{--var searchValue;--}}
            {{--if (inputSearch.value.length < 3) {--}}
                {{--// If the input value is less than 3 characters, don't send--}}
                {{--searchValue = '';--}}
            {{--} else {--}}
                {{--searchValue = inputSearch.value.toLowerCase().trim();--}}
            {{--}--}}
            {{--// Very basic throttling to prevent mixer thrashing. Only search--}}
            {{--// once 350ms has passed since the last keyup event--}}
            {{--clearTimeout(keyupTimeout);--}}
            {{--keyupTimeout = setTimeout(function() {--}}
                {{--filterByString(searchValue);--}}
            {{--}, 350);--}}
        {{--});--}}
        {{--/**--}}
         {{--* Filters the mixer using a provided search string, which is matched against--}}
         {{--* the contents of each target's "class" attribute. Any custom data-attribute(s)--}}
         {{--* could also be used.--}}
         {{--*--}}
         {{--* @param  {string} searchValue--}}
         {{--* @return {void}--}}
         {{--*/--}}
        {{--function filterByString(searchValue) {--}}
            {{--if (searchValue) {--}}
                {{--// Use an attribute wildcard selector to check for matches--}}
                {{--mixer.filter('[class*="' + searchValue + '"]');--}}
            {{--} else {--}}
                {{--// If no searchValue, treat as filter('all')--}}
                {{--mixer.filter('all');--}}
            {{--}--}}
        {{--}--}}
    {{--</script>--}}
@endsection