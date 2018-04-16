<title>Admin Settings</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-header"><h2>Online Eviction</h2></div>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-md-10 offset-md-1 offset-lg-1">
                            <div id="wizard">
                                <h3>Eviction Location</h3>
                                <section>
                                    <h2 style="text-align:center;" class="title">Eviction Location</h2>
                <h3 style="text-align:center;" class="fs-subtitle">Enter the address you plan on evicting.</h3>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6 col-md-8 col-lg-8 col-xl-8">   
                                        <input class="form-control" placeholder="Rental Street Address" type="text" id="street_address"/>
                                    </div>
                                     <div id="locationField">
                                         <input id="autocomplete" placeholder="Enter your address" onFocus="geolocate()" type="text"></input>
                                
                                    </div>
                                </div>
                            </div>
                                    <div id="map"></div>
                                </section>
    
                                <h3>Eviction Information</h3>
                                <section>
                                </section>
    
                                <h3>Signature/Payment</h3>
                                <section>
                                </section>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection