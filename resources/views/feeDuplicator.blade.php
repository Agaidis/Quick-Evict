<title>General Admin</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <header class="subhead text-center">
        <div class="overlay"></div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header"><h2>Fee Duplicator</h2></div>
                        <div class="card-body">
                            <div class="flash-message">
                                @if(Session::has('alert-success'))
                                    <p class="alert alert-success">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                @endif
                                @if(Session::has('alert-danger'))
                                    <p class="alert alert-danger">{{ Session::get('alert-danger') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                @endif
                            </div> <!-- end .flash-message -->
                            <form method="post" action="{{ action('FeeDuplicatorController@duplicateFees') }}" id="fee_duplicator_form">
                                <input type="hidden" name="_token" value="{{ Session::token() }}">
                                <div class="col-md-10">
                                    <div class="offset-5 col-md-4">
                                        <label class="labels" for="fee_duplicate_court_select">Choose a County</label>
                                        <select class="form-control" id="fee_duplicate_court_select" name="court_number">
                                            <option value="none">Court Select</option>
                                            @foreach ($courts as $court)
                                                <option value="{{$court->court_number}}">{{$court->court_number}}</option>
                                            @endforeach
                                        </select><br>
                                    </div><br>
                                    <div class="offset-5 col-md-4">
                                        <label class="labels" for="fee_duplicate_magistrate_select">Select Magistrates</label>
                                        <select class="form-control" id="fee_duplicate_magistrate_select" name="magistrates">
                                            <option value="none">Select Magistrates</option>
                                        </select><br>
                                    </div><br>

                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection