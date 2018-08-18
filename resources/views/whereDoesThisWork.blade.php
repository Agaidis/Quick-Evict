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
                        <div><br>
                            <table class="table table-responsive table-hover">
                                <tbody>
                                <tr style="height: 18px;">
                                    <td style="width: 50%; height: 18px;">Lancaster</td>
                                    <td style="width: 50%; height: 18px;">Lebanon</td>
                                </tr>
                                <tr style="height: 18px;">
                                    <td style="width: 50%; height: 18px;">Dauphin</td>
                                    <td style="width: 50%;">Cumberland</td>
                                </tr>
                                <tr>
                                    <td style="width: 50%;">York</td>
                                    <td style="width: 50%;">Chester</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>More counties will be rolling out shortly.</div>
                        <div>If you would like updates as more counties are rolled out, please submit your email here:</div><br>
                    <div>
                        <label for="email">Email Address:</label>
                        <input placeholder="Email" class="form-control email"/>
                    </div>
                        <div>
                            <label for="area_of_interest">Area of interest:</label>
                            <input placeholder="Pittsburgh" class="form-control area_of_interest"/>
                        </div><br>
                    <button type="button" class="btn btn-primary">Submit</button>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection