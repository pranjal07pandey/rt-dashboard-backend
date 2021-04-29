@extends('layouts.companyDashboard')
@section('css')
    <style>
        .form-group{
            margin: 0px 0 0 0;
        }
    </style>
@endsection
@section('content')
    <section class="content-header rt-content-header">
        <h1>Docket Book Manager View</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('dockets.docketDraft') }}"> Docket Book Manager</a></li>
            <li>View</li>
        </ol>
        <div class="clearfix"></div>
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="rtTab" style="margin: 0px;min-height: 400px; background: #fff;padding:30px ">
        <div class="row">
            @php
                $recipentArray = [];
                $parseDraft = $docketDraftDb->value;
                if($parseDraft['docket_data']['is_email'] == true){
                $receiver = $parseDraft['email_user_receivers'];
                foreach ($receiver as $item){
                    $recipentArray[] =  ($item['full_name'] != "") ? $item['full_name'] : $item['email'];
                }

                }else if($parseDraft['docket_data']['is_email'] == false){
                    $receiver = $parseDraft['rt_user_receivers'];
                    foreach ($receiver as $item){
                        $recipentArray[] =  $item['first_name'].' '.$item['last_name'];
                    }
                }
            @endphp
            <div class="form-group">
                <div class="col-lg-4 form-group">
                    <span><strong>Draft Title : </strong> &#9; {{ $parseDraft['docket_data']['draft_name'] }}</span>
                </div>
                <div class="col-lg-4 form-group">
                    <span><strong>Docket Name : </strong> {{ $parseDraft['template']['title'] }}</span>
                </div>
                <div class="col-lg-4 form-group">
                    <span><strong>Recipients : </strong> {{ implode(",",$recipentArray) }}</span>
                </div>
                <div class="col-lg-4 form-group">
                    <span><strong>Added Date : </strong> {{ $parseDraft['docket_data']['draft_date'] }}</span>
                </div>
                <div class="col-lg-4 form-group">
                    <span><strong>Created By : </strong> {{ $docketDraftDb->userInfo->first_name . ' ' . $docketDraftDb->userInfo->last_name }}</span>
                </div>
                <div class="col-lg-4 form-group">
                    <span><strong>Status : </strong>Synced</span>
                </div>
            </div>
            <div class="form-group">&nbsp;</div>
            
            <div class="col-xs-12 table-responsive drag">
            <hr style="border: inset">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th class="printTh" style="width:20%;font-size:20px;">Description</th>
                        <th class="printTh" style="width:80%;font-size:20px;">Value</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php
                            $docketDraft = (object) $docketDraftDb->value;
                        @endphp
                        @foreach($docketDraft->docket_data->docket_field_values as $docketValue)
                            @foreach ($docketDraft->template->docket_field as $docket_field)
                                @if($docketValue->category_id == 1)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.short_text',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 2)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.long_text',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 3)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.number',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 4)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.location',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 5)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.image',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 6)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.date',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 7)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.unit_rate',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 8)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.checkbox',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 9)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.signature',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 12)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.header',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 13)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.terms_and_condition',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 14)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.sketch_pad',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 15)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.document',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 16)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.barcode_scanner',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 18)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.yes_no_checkbox',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 20)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.manual_timer',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 21)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.total_hours',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 22)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.grid',compact('docket_field','docketValue'))
                                
                                    
                                @elseif($docketValue->category_id == 24)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.tallyable_unit_rate',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 25)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.tabllyable_value',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 26)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.time',compact('docket_field','docketValue'))
                                @elseif($docketValue->category_id == 27)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.advance_header',compact('docket_field','docketValue'))
                                {{-- @elseif($docketValue->category_id == 28)
                                    @include('dashboard.company.docketManager.dockets.draft.fields.folder',compact('docket_field','docketValue')) --}}

                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table><!--/.docket-table-value-->
            </div>
            <div class="form-group">
                <form action="{{ route('dockets.draft.save') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $docketDraftDb->id }}" name="docketDraftId">
                    <input type="submit" class="btn btn-primary float-right submitDisable" value="Submit">
                </form>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script>
        var mx = 0;
        $('.submitDisable').click(function(){
            $(this).prop('disabled',true);
            $(this).closest('form').submit();
        });

        $(".drag").on({
            mousemove: function(e) {
                var mx2 = e.pageX - this.offsetLeft;
                if(mx) this.scrollLeft = this.sx + mx - mx2;
            },
            mousedown: function(e) {
                this.sx = this.scrollLeft;
                mx = e.pageX - this.offsetLeft;
            }
        });

        $(document).on("mouseup", function(){
        mx = 0;
        });

    </script>
@endsection