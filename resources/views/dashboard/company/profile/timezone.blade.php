@php
    function generate_timezone_list(){
        static $allRegions = array(
            \DateTimeZone::AFRICA,
            \DateTimeZone::AMERICA,
            \DateTimeZone::ANTARCTICA,
            \DateTimeZone::ASIA,
            \DateTimeZone::ATLANTIC,
            \DateTimeZone::AUSTRALIA,
            \DateTimeZone::EUROPE,
            \DateTimeZone::INDIAN,
            \DateTimeZone::PACIFIC
        );
        // Makes it easier to create option groups next
        $list = array ('AFRICA','AMERICA','ANTARCTICA','ASIA','ATLANTIC','AUSTRALIA','EUROPE','INDIAN','PACIFIC');
        // Make array holding the regions (continents), they are arrays w/ all their cities
        $region = array();
        foreach ($allRegions as $area){
            array_push ($region,\DateTimeZone::listIdentifiers( $area ));
        }   
        $count = count ($region); $i = 0; $holder = '';
        // Go through each region one by one, sorting and formatting it's cities
        while ($i < $count){
            $chunck = $region[$i];
            // Create the region (continents) option group
            $holder .= '<optgroup label="---------- '.$list[$i].' ----------">';
            $timezone_offsets = array();
            foreach( $chunck as $timezone ){
                $tz = new \DateTimeZone($timezone);
                $timezone_offsets[$timezone] = $tz->getOffset(new \DateTime);
            }
            asort ($timezone_offsets);
            $timezone_list = array();
            foreach ($timezone_offsets as $timezone => $offset){
                $offset_prefix = $offset < 0 ? '-' : '+';
                $offset_formatted = gmdate( 'H:i', abs($offset) );
                $pretty_offset = "UTC ${offset_prefix}${offset_formatted}";
                $timezone_list[$timezone] = "(${pretty_offset}) $timezone";     
            }
            // All the formatting is done, finish and move on to next region
            foreach ($timezone_list as $key => $val){
                $company = @@App\Company::where('id',Session::get('company_id'))->first();
                if($company->time_zone == $key){
                    $holder .= '<option selected="selected" value="'.$key.'">'.$val.'</option>';
                }else{
                    $holder .= '<option value="'.$key.'">'.$val.'</option>';
                }
            }
            $holder .= '</optgroup>';
            ++$i;
        }
        return $holder;
    }
@endphp
@extends('layouts.companyDashboard')

@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-clock-o"></i> Timezone
            <small>Update Timezone</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Profile</a></li>
            <li class="active">Update Timezone</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;min-height: 400px;">
        <div class="col-md-4">
            <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header themePrimaryBg">
                    <div class="widget-user-image">
                        <img class="img-circle" src="{{ AmazoneBucket::url() }}{{ auth()->user()->image }}" alt="User Avatar" style="height: 65px;">
                    </div>
                    <!-- /.widget-user-image -->
                    <h3 class="widget-user-username">
                        @if(auth()->user()->first_name!='')
                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                        @else
                            {{ auth()->user()->email }}
                        @endif
                    </h3>
                    <h5 class="widget-user-desc">@if(Session::get('adminType')==1) Super Admin @else Admin @endif</h5>
                </div>
                <div class="box-footer no-padding">
                    <ul class="nav nav-stacked">
                        <li><a href="{{ url('dashboard/company/profile/subscription') }}"><i class="fa fa-cogs"></i> My Subscription</a></li>
                        <li><a href="#"><i class="fa fa-id-card"></i> My Profile</a></li>
                        <li><a href="{{ url('dashboard/company/profile/invoiceSetting') }}"><i class="fa fa-money"></i> Invoice Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/docketSetting') }}"><i class="fa fa-money"></i> Docket Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/changePassword') }}"><i class="fa fa-cog"></i>&nbsp;&nbsp;Change Password </a></li>
                        <li><a href="{{ route('Company.CreditCard.Update') }}"><i class="fa fa-credit-card-alt"></i> Update Credit Card </a></li>
                        <li><a href="{{ url('dashboard/company/profile/stripeInvoices') }}"><i class="fa fa-usd"></i>&nbsp;&nbsp;Billing History</a></li> 
                        <li class="active"><a href="{{ route('Company.timezone') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;Timezone</a></li> 
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block"> <i class="fa fa-clock-o"></i>&nbsp;Timezone</h3>
            {{ Form::open(['route' => 'Company.timezone.store', 'files' => true]) }}
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group label-floating">
                            <label class="control-label" for="title">Time Zone</label>
                                @php
                                    echo "<select name='timezone' class='form-control'>".generate_timezone_list()."</select>";
                                @endphp
                        </div>
                    </div>
                </div>
                <div class="col-md-12"><br/>
                    <button type="submit" class="btn btn-xs btn-raised btn-info pull-right"  id="addNew"><i class="fa fa-upload"></i> Update</button>
                </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection