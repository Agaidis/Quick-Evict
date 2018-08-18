<title>Location Finder</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-header"><h2>Where does this work?</h2></div>
                <div class="card-body">
                    <div class="row">
                        <div>Thanks for your interest in QuickEvict.&nbsp; The goal for Quick Evict is to eventually make evictions easier for owners and landlords in all communities in the United States.&nbsp; Currently, QuickEvict is in beta and only works in Central Pennsylvania in the following counties:&nbsp; Lancaster, Lebanon, Dauphin, Cumberland, York, Chester.&nbsp; More counties will be rolling out shortly.&nbsp;&nbsp;</div>
                        <div>&nbsp;</div>
                        <div>If you would like updates as more counties are rolled out, please submit your email here:</div>
                        <div>&nbsp;</div>
                        <div>Email Address:____________</div>
                        <div>Area of interest:____________</div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection