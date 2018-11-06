<title>Eviction Info</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-header"><h2>Eviction Info</h2></div>
                <div class="card-body">
                    <div class="row">
                    <div class="col-md-8 offset-md-2 offset-lg-2">
                        <div class="form-group form-inline">
                    <div class="row"> 
                        <div class="col-sm-6 col-md-8 col-lg-8 col-xl-8">            
                            <label for="street_address">Rental Street Address</label>
                            <input class="form-control" type="text" id="street_address"/>
                            dgdfgd
                            as
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection