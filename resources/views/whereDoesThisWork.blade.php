<title>Location Finder</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-body body_container">
                    <h2 class="titles" style="text-align:center;">Where does this work?</h2>
                    <div class="flash-message">
                        @if(Session::has('alert-success'))
                            <p class="alert alert-success">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                        @endif
                            @if(Session::has('alert-danger'))
                                <p class="alert alert-danger">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                            @endif
                    </div> <!-- end .flash-message -->
                        <span id="where_does_this_work_content" style="text-align: center;"><div style="font-weight:bold">Thanks for your interest in CourtZip.</div>
                        <div>The goal for CourtZip is to eventually make filing evictions, order of possessions, and civil complaints easier for everyone in all communities in the United States.</div>
                            <div>Currently CourtZip will be live in Lancaster County, PA on Dec 1, 2019 and York County, PA on Jan 1, 2020</div></span><br /><br />
                        <div>More counties will be rolling out shortly.</div>
                        <div>If you would like updates as more counties are rolled out, please submit your email here:</div><br>
                    <form method="post" action="{{ action('WhereDoesThisWorkController@store') }}" id="where_does_this_work_form">
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                        <div>
                            <label for="email">Email Address:</label>
                            <input placeholder="Email" class="form-control email" name="email"/>
                        </div>
                        <div>
                            <label for="area_of_interest">Area of interest:</label>
                            <input placeholder="Pittsburgh" class="form-control area_of_interest" name="area_of_interest"/>
                        </div><br>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection