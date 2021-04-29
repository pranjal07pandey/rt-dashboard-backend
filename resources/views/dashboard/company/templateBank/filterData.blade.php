@if ($templateBank->count()>0)
    <div class="row">
        @if ($templateBank)
            @foreach($templateBank as $row)
                <div class="mix col-md-3" style="padding-bottom: 25px;margin: 0px -15px 0px 0px; width: calc( 25% + 10px) !important;" >
                    <div style="    background: #ffffff;">
                        <div class="overlay">
                            <img src="{{asset('assets/bank.png')}}" style="width:100%">
                        </div>
                        <div class="price-detail" style="padding-left: 9px; padding-bottom: 15px; padding-right: 9px;">
                            @php $templateValue = json_decode($row->template_value); @endphp
                            @if (array_key_exists("docket",$templateValue))
                                <h4 style="font-size: 14px; font-weight: 500;">{{$templateValue->docket[0]->title}}</h4>
                            @else
                                <h4 style="font-size: 14px; font-weight: 500;">{{$templateValue->title}}</h4>

                            @endif
                            <p style="color: #777777; font-size: 12px;margin-bottom: 6px;"> <i class="fa fa-user-circle" aria-hidden="true"></i> {{$row->user->first_name." ".$row->user->last_name }}</p>
                            <p style="color: #777777; font-size: 12px;margin-bottom: 6px;"> <i class="fa fa-building" aria-hidden="true"></i> {{$row->company->name }}</p>
                            <span style="color: #777777; font-size: 12px;" class="pull-left"><i class="fa fa-calendar" aria-hidden="true"></i> {{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</span>
                            <span  style="color: #777777; font-size: 12px;"  class="pull-right"><i class="fa fa-download" aria-hidden="true"></i> {{$row->downloads}}</span>
                            <div class="clearfix"></div>
                            <hr style="margin: 10px 0px; padding: 0px;"/>
                            <button class="btn btn-info pull-left previewTemplate"  style="padding:3px 8px 3px 8px;border: 1px solid #15B1B8;color: #15B1B8;font-size: 12px;font-weight: 400;margin: 0px;"  data-url="{{ url('dashboard/company/templateBank/preview',$row->id) }}"> Preview</button>
                            <button class="btn btn-info pull-right" class="installTemplate" data-toggle="modal" data-target="#installTemplate" style="padding:3px 8px 3px 8px;border: 1px solid #15B1B8;color: #15B1B8;font-size: 12px;font-weight: 400;margin: 0px;" data-id="{{$row->id}}"> Install</button>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div>
        <span style="padding-top: 29px; color: #757575;" class="pull-left">Showing  {{ $templateBank->firstItem() }} to     {{ $templateBank->lastItem() }} of {{ $templateBank->total() }} entries</span>
        <span class="pull-right">{{ $templateBank->appends(['items'=>10]) ->links() }}</span>
        <div class="clearfix"></div>
    </div>
    @else
    <p style="text-align: center;">Result not found</p>
@endif

