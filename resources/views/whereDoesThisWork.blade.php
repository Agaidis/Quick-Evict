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
                        <div>Thanks for your interest in QuickEvict.</div>
                        <div>The goal for Quick Evict is to eventually make evictions easier for owners and landlords in all communities in the United States.</div>
                        <div>Currently, QuickEvict is in beta and only works in Central Pennsylvania in the following counties:</div>
                        <div>
                            <table style="border-collapse: collapse; width: 27.6422%; height: 114px;" border="1">
                                <tbody>
                                <tr style="height: 18px;">
                                    <td style="width: 50%; height: 18px;">Lancaster</td>
                                </tr>
                                <tr style="height: 18px;">
                                    <td style="width: 50%; height: 18px;">Lebanon</td>
                                </tr>
                                <tr style="height: 18px;">
                                    <td style="width: 50%; height: 18px;">Dauphin</td>
                                </tr>
                                <tr>
                                    <td style="width: 50%;">Cumberland</td>
                                </tr>
                                <tr>
                                    <td style="width: 50%;">York</td>
                                </tr>
                                <tr>
                                    <td style="width: 50%;">Chester</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>More counties will be rolling out shortly.</div>
                        <div>If you would like updates as more counties are rolled out, please submit your email here:</div>
                        <div>Email Address:____________</div>
                        <div>Area of interest:____________</div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection