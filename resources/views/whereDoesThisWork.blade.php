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
                        Pennsylvania 
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection