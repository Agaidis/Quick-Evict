<title>County Admin</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <header class="subhead text-center">
        <div class="overlay"></div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header"><h2>County Admin</h2></div>
                        <div class="card-body">
                            <div class="flash-message">
                                @if(Session::has('alert-success'))
                                    <p class="alert alert-success">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                @endif
                                @if(Session::has('alert-danger'))
                                    <p class="alert alert-danger">{{ Session::get('alert-danger') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                @endif
                            </div> <!-- end .flash-message -->
                            <table class="table table-hover table-bordered county_table" style="" id="county_table">
                                <thead>
                                <tr>
                                    <th class="text-center">County</th>
                                    <th class="text-center" style="">Allow In Person Complaint</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($counties as $county)
                                <tr>
                                    <td>{{$county}}</td>
                                    <td><input type="checkbox" id="in_person_complaint_toggle_{{$county}}"/></td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection