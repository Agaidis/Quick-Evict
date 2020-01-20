<title>Create Magistrate</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h2>Create Magistrate</h2></div>
                    <div class="card-body">
                        <div class="flash-message">
                            @if(Session::has('alert-success'))
                                <p class="alert alert-success">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                            @endif
                            @if(Session::has('alert-danger'))
                                <p class="alert alert-danger">{{ Session::get('alert-danger') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                            @endif
                        </div> <!-- end .flash-message -->
                        <form method="post" action="{{ action('generalAdmin@updateDrivingFee') }}" id="driving_fee_form">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                        <div>
                            <label for="driving_distance_fee_rate">Driving Distance Fee Rate:</label>
                            <input placeholder=".55" class="form-control driving_distance_fee_rate" name="driving_distance_fee_rate" value="{{$drivingFee}}"/>
                        </div><br>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection