
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="0jSYAQbRlsmIYYEGPuRN2L-f9M3JsHtbTJugw_FA5bg" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CourtZip') }}</title>
    <link rel="icon" href="https://quickevict.nyc3.digitaloceanspaces.com/zipLogo.png"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>

    <script>
        let environmentPath = '{!! env('APP_URL') !!}';
    </script>

    <script type="text/javascript">
        function initMap(srcLocation, dstLocation){
            srcLocation = new google.maps.LatLng(19.075984, 72.877656);
            dstLocation = new google.maps.LatLng(12.971599, 77.594563);
            var distance = google.maps.geometry.spherical.computeDistanceBetween(srcLocation, dstLocation)
            console.log(distance/1000); // Distance in Kms.
        }
    </script>
    <script src='https://js.hcaptcha.com/1/api.js' async defer></script>



    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container-fluid">
                <a class="navbar-brand" style="padding: 8px 14px!important;" href="{{ url('/home') }}">
                    <img src="https://quickevict.nyc3.digitaloceanspaces.com/courtZipBlue.png" width="150" height="30">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->

                    <!-- Right Side Of Navbar -->

                    <ul class="nav navbar-nav navbar-right">
                    @if (Auth::check())

                            <li><a href="{{ url('dashboard') }}" id="dashboard_btn">Dashboard</a></li>
                            <li><a href="{{ url('services') }}" id="services_btn">Services</a></li>
                            <li><a href="{{ url('information') }}" id="eviction_info_btn">Information</a></li>
                            <li><a href="{{ url('FAQ') }}" id="faq_btn">FAQ</a></li>
                            <li><a href="{{ url('where-does-this-work') }}" id="where_work_btn">Where Does this Work?</a></li>
                            @if (Auth::user()->role == 'Court' || Auth::user()->role == 'Administrator')
                                <li><a href="{{ url('getFileFee') }}">Fee Calculator</a></li>
                                @endif
                    @else
                            <li><a href="{{ url('services') }}" id="services_btn">Services</a></li>
                            <li><a href="{{ url('information') }}" id="eviction_info_btn">Information</a></li>
                            <li><a href="{{ url('FAQ') }}" id="faq_btn">FAQ</a></li>
                            <li><a href="{{ url('where-does-this-work') }}" id="where_work_btn">Where Does this Work?</a></li>
                    @endif<!-- Authentication Links today -->

                    </ul>
                </div>

                @guest
                <ul class="nav navbar-nav navbar-right">
                    <li><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                    <li><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                    @else
                        <li class="nav navbar-nav nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div style="margin-left:-15px;" class="dropdown-menu" aria-labelledby="navbarDropdown">
                                @if (Auth::user()->role == 'Administrator')
                                    <a class="dropdown-item" href="{{ url('magistrateCreator') }}" id="magistrate_btn">Magistrate Creator</a>
                                    <a class="dropdown-item" href="{{ url('countyAdmin') }}" id="county_btn">County Admin</a>
                                    <a class="dropdown-item" href="{{ url('feeDuplicator') }}" id="fee_duplicator_btn">Fee Duplicator</a>
                                    <a class="dropdown-item" href="{{ url('generalAdmin') }}" id="admin_btn">General Admin</a>
                                    <a class="dropdown-item" href="{{ url('userManagement') }}" id="user_management_btn">Manage Users</a>
                                @endif
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                </ul>
                @endguest
            </div>
        </nav>
    </div>
    <main>
        @yield('content')
    </main>
     @extends('layouts.footer')


    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAfPLSbGAHZkEd-8DDB0FcGSlhrV9LQMGM&libraries=places" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
</body>
</html>
