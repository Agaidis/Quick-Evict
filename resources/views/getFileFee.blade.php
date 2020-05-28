@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
        <div class="overlay"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-lg-10 col-xl-10 mx-auto">
                    <h2 class="titles" style="margin-top:3%; margin-bottom:3%; text-align:center;">Filing Fee Calculator</h2>
                    <div class="offset-1 col-md-4">
                        <h2 class="titles">Step 1: Select a County</h2>
                        <form method="post" action="{{ action('GetFileFeeController@index') }}" enctype="multipart/form-data" id="dashboard_form">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <div id="get_file_Fee_container">
                                <div class="col-md-12">
                                    <label class="labels" for="county_select">Choose a County</label>
                                    <select class="form-control" id="county_select" name="county">
                                        <option value="none">County Select</option>
                                        @foreach ($counties as $county)
                                            @if (isset($selectedCounty) && $selectedCounty == $county->county)
                                                <option selected value="{{$county->county}}">{{$county->county}}</option>
                                            @else
                                            <option value="{{$county->county}}">{{$county->county}}</option>
                                            @endif
                                        @endforeach
                                    </select><br>
                                    <button type="submit" class="btn btn-primary" id="get_file_fee_btn">Go!</button>
                                </div>
                            </div><br>
                        </form>
                    </div>
                    @if ($isStep2)
                    <div>
                    @else
                    <div style="display:none;">
                    @endif
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8">
                                <input class="form-control" placeholder="Rental Street Address" type="text" id="pac-input"/>
                            </div>
                        </div><!-- test -->
                    </div>

                    <div class="row">
                        <div class="offset-1 col-md-5">
                            <h2 class="titles" style="margin-right:20%; text-align:center;">Step 2: Put in the Address</h2>
                            <div style="margin-left:0; height:400px; width:90%" id="map"></div>
                            <input type="hidden" id="court_number" name="court_number" />
                        </div>
                        <div class="col-md-6">
                            <h2 class="titles" style="text-align:center;">Step 3: Fill in the Fields Below</h2>
                            <div id="get_file_fields_container">
                                <div class="row col-sm-10">
                                    <label class="labels" for="file_type_select">File Type: </label><span style="color:red; margin-left:30%;" class="error_msgs" id="file_type_error_msg"></span>
                                    <select class="form-control" id="file_type_select" name="fileType">
                                        <option value="none">File type</option>
                                        <option value="ltc">Landlord Tenant-Complaint</option>
                                        <option value="oop">Request for Order of Possession</option>
{{--                                           <option value="civil">Civil Complaint</option>--}}
                                    </select>
                                </div>
                                <div style="display:none;" class="send_method_container row col-sm-10">
                                    <label class="labels" for="send_method">Delivery Method: </label>
                                    <select class="form-control" id="send_method" name="sendMethod">
                                        <option value="none">Delivery Method</option>
                                        <option value="mail">Mail</option>
                                        <option value="constable">Constable</option>
                                    </select>
                                </div>
                                <div class="row col-sm-10">
                                    <label for="num_defendants" class="labels">Number of Defendants</label><span style="color:red; margin-left:10%;" class="error_msgs" id="num_def_error_msg"></span>
                                    <select class="form-control" id="num_defendants">
                                        <option value="none" selected disabled>Select # of Defendants</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                        <option value="13">13</option>
                                        <option value="14">14</option>
                                        <option value="15">15</option>
                                        <option value="16">16</option>
                                        <option value="17">17</option>
                                        <option value="18">18</option>
                                        <option value="19">19</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                                <div class="row col-sm-10">
                                    <label class="labels" for="total_judgment">Total Judgment</label><span style="color:red; margin-left:21%" class="error_msgs" id="total_judgment_error_msg"></span>
                                    <input type="text" class="form-control eviction_fields" id="total_judgment" name="total_judgment" placeholder="$"  maxlength="9"/>
                                </div>
                                <div class="row col-sm-4">
                                    <label class="labels" for="total_judgment">Court ID</label>
                                    <input disabled type="text" class="form-control eviction_fields" id="court_number_display" name="court_number_display"/>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="labels" for="total_judgment">Mileage</label>
                                        <input disabled type="text" class="form-control eviction_fields" id="distance"/>
                                    </div>

                                    <div class="col-sm-4">
                                        <label class="labels" for="total_judgment">Calculated Distance Fee</label>
                                        <input disabled type="text" class="form-control eviction_fields" id="calculated_fee"/>
                                    </div>

                                </div><br><hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <button id="calculate_file_fee" class="btn btn-primary">Calculate Filing Fee!</button>
                                    </div>
                                        <div class="col-md-4">
                                            <input type="text" disabled class="form-control" id="filing_fee" name="filing_fee"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
@endsection