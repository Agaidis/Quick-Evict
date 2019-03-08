<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="ENAlyxpEGFLMVp5yi93Wi9JO3R_uVdJYwHxxhzbZfZg" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Eviction Tech') }}</title>
    <link rel="icon" href="https://quickevict.nyc3.digitaloceanspaces.com/evictionTechlogo.png"/>

    <!-- Scripts -->
    {{--<script src="{{ asset('js/app.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/timepicker.min.js') }}" defer></script>--}}
    {{--<script src="{{ asset ('js/datepicker-ui.min.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/eviction.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/steps.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/datatables.min.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/magistrateCreator.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/home.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/userManagement.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/numeric-1.2.6.min.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/bezier.js') }}" defer></script>--}}

    {{--<script src="{{ asset('js/json2.min.js') }}" defer></script>--}}
    {{--<script src="{{ asset('js/signaturepad.js') }}" defer></script>--}}


    {{--<script src="{{ asset('js/bootstrap-timepicker.min.js') }}" defer></script>--}}



{{--<script>--}}
        {{--var stripe = Stripe('pk_test_6pRNASCoBOKtIshFeQd4XMUh');--}}
        {{--var elements = stripe.elements();--}}
        {{--var card = elements.create('card');--}}

        {{--// Add an instance of the card UI component into the `card-element` <div>--}}
        {{--card.mount('#card-element');--}}
    {{--</script>--}}
    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    {{--<link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
    {{--<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">--}}
    {{--<link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet"/>--}}
    {{--<link href="{{ asset('css/eviction.css') }}" rel="stylesheet">--}}
    {{--<link href="{{ asset('css/steps.css') }}" rel="stylesheet">--}}
    {{--<link href="{{ asset('css/signaturepad.css') }}" rel="stylesheet">--}}
    {{--<link href="{{ asset('css/timepicker.min.css') }}" rel="stylesheet>">--}}
    {{--<link href="{{ asset('css/datepicker.structure.min.css') }}" rel="stylesheet">--}}
    {{--<link href="{{ asset('css/datepicker.theme.min.css') }}" rel="stylesheet">--}}
    {{--<link href="{{ asset('css/datepicker-ui.min.css') }}" rel="stylesheet">--}}
    {{--<link href="{{ asset('css/bootstrap-timepicker.min.css') }}" rel="stylesheet">--}}
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container-fluid">
                <a class="navbar-brand" style="padding: 8px 14px!important;" href="{{ url('/home') }}">
                    <img src="https://quickevict.nyc3.digitaloceanspaces.com/EvictionTech%20logo.jpg" width="150" height="30">
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
                            <li><a href="{{ url('online-eviction') }}" id="online_eviction_btn">Online Eviction</a></li>
                            <li><a href="{{ url('information') }}" id="eviction_info_btn">Information</a></li>
                            <li><a href="{{ url('FAQ') }}" id="faq_btn">FAQ</a></li>
                            <li><a href="{{ url('where-does-this-work') }}" id="where_work_btn">Where Does this Work?</a></li>
                            <li><a href="{{ url('about-us') }}" id="about_us_btn">About Us</a></li>
                    @else
                            <li><a href="{{ url('information') }}" id="eviction_info_btn">Information</a></li>
                            <li><a href="{{ url('FAQ') }}" id="faq_btn">FAQ</a></li>
                            <li><a href="{{ url('where-does-this-work') }}" id="where_work_btn">Where Does this Work?</a></li>
                            <li><a href="{{ url('about-us') }}" id="about_us_btn">About Us</a></li>
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

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script type="text/javascript" src="{{ mix('js/courtzip.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAfPLSbGAHZkEd-8DDB0FcGSlhrV9LQMGM&libraries=places" defer></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
</body>
</html>
