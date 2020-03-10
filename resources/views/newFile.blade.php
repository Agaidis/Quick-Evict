@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div id="new_file_container" class="col-md-6">
                <div class="card">
                    <form method="post" action="{{ action('NewFileController@proceedToFileTypeWithSelectedCounty') }}" id="new_file_form">
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                    <div class="card-body body_container">
                        <h2 class="titles" style="text-align:center;">Step 1:<br> Choose your File Type and County of Incident</h2>
                        <div class="form-group">
                            <div class="row justify-content-center">
                                <div class="col-sm-6">
                                    <label class="labels" for="file_type_select">File Type: </label>
                                    <select class="form-control" id="file_type_select" name="fileType">
                                        <option value="none">File type Select</option>
                                        <option value="ltc">Landlord Tenant-Complaint</option>
                                        <option value="oop">Request for Order of Possession</option>
{{--                                        <option value="civil">Civil Complaint</option>--}}
                                    </select>
                                    <span id="file_type_error"></span>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-sm-6">
                                    <label class="labels" for="county_select">County</label>
                                    <select class="form-control" id="county_select" name="county">
                                        <option value="none">County Select</option>
                                        @foreach ($counties as $county)
                                            <option value="{{$county->county}}">{{$county->county}}</option>
                                        @endforeach
                                    </select>
                                    <span id="county_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <button class="btn btn-primary" id="step_1_btn" type="button">Proceed to Step 2</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection