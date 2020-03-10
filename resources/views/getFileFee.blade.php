@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <header class="subhead text-center">
        <div class="overlay"></div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h2 style="text-align:center;">Get a Filing Fee</h2></div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6 col-md-8 col-lg-8 col-xl-8">
                                    <input class="form-control" placeholder="Rental Street Address" type="text" id="pac-input"/>
                                </div>
                            </div><!-- test -->
                        </div>
                        <div class="row">
                            <div class="offset-1 col-md-5">
                                <h2 class="titles" style="margin-right:20%; text-align:center;">Step 1: Put in the Address</h2>
                                <div style="margin-left:0; height:400px; width:90%" id="map"></div>
                                <input type="hidden" id="court_number" name="court_number" />
                            </div>
                            <div class="col-md-5">
                                <h2 class="titles" style="text-align:center;">Step 2: Fill in the Fields Below</h2>
                            <div id="get_file_fields_container">
                                <div class="row col-sm-10">
                                    <label class="labels" for="file_type_select">File Type: </label><span style="color:red; margin-left:30%;" class="error_msgs" id="file_type_error_msg"></span>
                                        <select class="form-control" id="file_type_select" name="fileType">
                                            <option value="none">File type</option>
                                            <option value="ltc">Landlord Tenant-Complaint</option>
                                            <option value="oop">Request for Order of Possession</option>
{{--                                            <option value="civil">Civil Complaint</option>--}}
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
                                    <input type="text" class="form-control eviction_fields" id="total_judgment" name="total_judgment" placeholder="$" value=""  maxlength="9"/>
                                </div><br>
                                <div class="row col-md-12">
                                    <button id="calculate_file_fee" class="btn btn-primary">Calculate Filing Fee!</button><br>
                                    <div class="col-md-6">
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
    </header>
@endsection