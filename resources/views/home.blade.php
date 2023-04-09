@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
        <!-- Masthead -->
        <header class="masthead text-white text-center">
            <div class="overlay"></div>
            <div style="margin-top:-5%;" class="container">
                <div class="row" style="margin-bottom:10%;">
                    <div style="margin-bottom:1%;" class="col-xl-9 mx-auto">
                        <img src="https://quickevict.nyc3.digitaloceanspaces.com/courtzipbluenobackground.png" width="320" height="70">
                        <h2 style="color:#595959; margin-bottom:2%;">Simplify court filing at the local level</h2>

                    </div>
                    <div class="col-md-12 col-lg-8 col-xl-7 mx-auto">
                        <form method="post" class="form-horizontal" action="{{ route('DashboardController@navToSignup') }}" id="new_file_form">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <h3>File online today!</h3>
                            <div class="form-row">
                                <button type="submit" class="btn btn-success" onclick="window.location='{{ url("register") }}'" id="create_an_account">Create an Account</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Icons Grid -->
        <section class="features-icons text-center">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                            <div class="features-icons-icon d-flex">
                                <i class="fa fa-laptop m-auto text-primary"></i>
                            </div>
                            <h3>Online Submission</h3>
                            <p class="lead mb-0">File online from anywhere!<br>24/7/365</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                            <div class="features-icons-icon d-flex">
                                <i class="fas fa-phone-slash m-auto text-primary"></i>
                            </div>
                            <h3>Simple</h3>
                            <p class="lead mb-0">Eliminate phone tag with the courts</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                            <div class="features-icons-icon d-flex">
                                <i class="fas fa-bullseye m-auto text-primary"></i>
                            </div>
                            <h3>Accurate</h3>
                            <p class="lead mb-0">Eliminate inaccurate filing fees</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="features-icons-item mx-auto mb-0 mb-lg-3">
                            <div class="features-icons-icon d-flex">
                                <i class="fas fa-clock m-auto text-primary"></i>
                            </div>
                            <h3>Quick</h3>
                            <p class="lead mb-0">Create and file electronically with Court in 5 minutes! Avoid mail hassle and delay</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Image Showcases -->
    <section class="showcase bg-light">
        <div class="container-fluid p-0">
            <div class="row no-gutters">
                <div class="col-lg-6 order-lg-6 text-white showcase-img">
                    <div style="display:block; font-size:50px; color:black; margin-left:11%; margin-top:4%;" class="fas fa-file-alt"><span style="font-weight: normal; margin-left:5%; font-size:28px;">Contract Disputes</span></div>
                    <div style="display:block; font-size:50px; color:black; margin-left:10%; margin-top:4%;" class="fa fa-home"><span style="font-weight: normal; margin-left:5%; font-size:28px;">Property Disputes</span></div>
                    <div style="display:block; font-size:50px; color:black; margin-left:10%; margin-top:4%;" class="fas fa-balance-scale"><span style="font-weight: normal; margin-left:5%; font-size:28px;">Torts</span></div>
                    <div style="display:block; font-size:50px; color:black; margin-left:10%; margin-top:4%;" class="fas fa-handshake"><span style="font-weight: normal; margin-left:5%; font-size:28px;">Collections</span></div>
                </div>
                <div class="col-lg-6 order-lg-1 showcase-text">
                    <h1>Civil Complaints</h1>
                    <p class="lead mb-0"></p>
                </div>
            </div>
        </div>
    </section>

    <section class="showcase">
        <div class="container-fluid p-0">
            <div class="row no-gutters">
                <div class="col-lg-6 order-lg-6 text-white showcase-img">
                    <div style="display:block; font-size:50px; color:black; margin-left:9%; margin-top:5%;" class="fa fa-home"><span style="font-weight: normal; margin-left:5%; font-size:28px;">Residential</span></div>
                    <div style="display:block; font-size:50px; color:black; margin-left:10%; margin-top:5%;" class="fas fa-building"><span style="font-weight: normal; margin-left:5%; font-size:28px;">Commercial</span></div>
                    <div style="display:block; font-size:50px; color:black; margin-left:10%; margin-top:5%;" class="fas fa-file-alt"><span style="font-weight: normal; margin-left:5%; font-size:28px;">Order of Possession</span></div>
                </div>
                <div class="col-lg-6 order-lg-1 showcase-text">
                    <h1>Landlord/Tenant Complaints</h1>
                    <p class="lead mb-0"></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Icons Grid -->
    <section style="padding-bottom:4rem;" class="text-center">
        <div class="container">
            <div class="row">
                <div class="offset-1 col-lg-9">
                    <h1>Watch how it works.</h1>
                    <video width="620" height="440" controls="controls">
                        <source src="https://quickevict.nyc3.cdn.digitaloceanspaces.com/courtzip%20demo.mp4" type="video/mp4" >
                        Your browser does not support the video tag.
                    </video>
                </div>

            </div>
        </div>
    </section>



        <!-- Testimonials -->
        <section class="testimonials text-center bg-light">
            <div class="container">
                <h2 class="mb-5">What people are saying...</h2>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="testimonial-item mx-auto mb-5 mb-lg-0">
                            <img class="img-fluid rounded-circle mb-3" src="https://quickevict.nyc3.digitaloceanspaces.com/stars.jpg" alt="">
                            <h5>Shane O.</h5>
                            <p class="font-weight-light mb-0">"This is fantastic! Thanks so much guys!"</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="testimonial-item mx-auto mb-5 mb-lg-0">
                            <img class="img-fluid rounded-circle mb-3" src="https://quickevict.nyc3.digitaloceanspaces.com/stars.jpg" alt="">
                            <h5>Stephen K.</h5>
                            <p class="font-weight-light mb-0">"Fast and Efficient."</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="testimonial-item mx-auto mb-5 mb-lg-0">
                            <img class="img-fluid rounded-circle mb-3" src="https://quickevict.nyc3.digitaloceanspaces.com/stars.jpg" alt="">
                            <h5>Chris C.</h5>
                            <p class="font-weight-light mb-0">"Thanks so much for making this process online and stress free!"</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Testimonials -->
    <section class="testimonials text-center">
        <div class="container">
                <form method="post" class="form-horizontal" action="{{ action('NewFileController@proceedToFileTypeWithSelectedCounty') }}" id="new_file_form">
                    <input type="hidden" name="_token" value="{{ Session::token() }}">
                    <h3>File online today!</h3><br>
                    <div class="form-row">
                        <div class="form-group col-4">
                            <select class="form-control" id="county_select" name="county" style="padding-bottom: 5px;">
                                <option value="none">Select the County</option>
                                @foreach ($counties as $county)
                                    <option value="{{$county->county}}">{{$county->county}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-4">
                            <select disabled class="form-control" id="file_type_select" name="fileType">
                                <option value="none">Select a File Type</option>
                                <option value="ltc">Landlord Tenant-Complaint</option>
                                <option disabled id="ltcA" value="ltcA">Landlord-Tenant Compalint, File AND Represent Plaintiff at Hearing</option>
                                <option value="oop">Request for Order of Possession</option>
                                <option disabled id="oopA" value="oopA">Request for Order of Possession File AND attend lockout and complete lock change</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <button type="submit" class="btn btn-block btn-lg btn-primary">Go!</button>
                        </div>
                        <span id="file_type_error"></span>
                    </div>
                </form>

        </div>
    </section>
@endsection
