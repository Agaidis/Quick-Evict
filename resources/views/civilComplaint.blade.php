<title>New Civil Complaint</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><h2>New Civil Complaint</h2></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form method="post" action="{{ action('CivilComplaintController@formulatePDF') }}" enctype="multipart/form-data" id="eviction_form">
                                    <input type="hidden" name="_token" value="{{ Session::token() }}">
                                    <h2 style="text-align:center;" class="title">Defendant Location</h2>
                                    <h3 style="text-align:center;" class="fs-subtitle">Enter the address you plan on evicting.</h3>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 col-md-8 col-lg-8 col-xl-8">
                                                <input style="margin-left: 25%;" class="form-control" placeholder="Rental Street Address" type="text" id="pac-input"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="offset-1">
                                        <div id="map"></div>
                                    </div>

                                    <div class="col-md-12 offset-1 filing_form_div">

                                        <!-- ADDITIONAL INFORMATION ON PROPERTY -->
                                        <h4 class="major_labels">Additional Info on Property.</h4>
                                        <div id="additional_info_container">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <h4 class="address_display_div"><b>Address</b>: <span id="display_address"></span></h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="labels" for="zipcode">Verify Zipcode: </label>
                                                        <input type="text" class="form-control eviction_fields" placeholder="07753" id="zipcode" name="zipcode"/>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="labels" for="unit_number">Additional Address Detail</label>
                                                        <input type="text" class="form-control eviction_fields" placeholder="Example: Unit 3" id="unit_number" name="unit_number"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="offset-1 col-sm-10">
                                                    <canvas id="signature_pad" width="600" height="200" style="touch-action: none;"></canvas>
                                                    <div class="signature_pad_footer">
                                                        <div class="description">Sign above</div>
                                                        <div class="signature_pad_actions">
                                                            <button type="button" class="btn btn-warning clear_signature" data-action="clear">Clear</button>
                                                            <button type="button" class="btn btn-danger no_signature" data-action="save-png">Do not use Digital Signature</button>
                                                            <button type="button" class="btn btn-success save_signature" data-action="save-png">Use Digital Signature</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="status_msg"></div>
                                        <button type="submit" id="pdf_download_btn" disabled class="btn btn-primary">Download PDF File</button>
                                        <input type="hidden" id="signature_source" name="signature_source"/>
                                        <input type="hidden" id="state" name="state"/>
                                        <input type="hidden" id="county" name="county"/>
                                        <input type="hidden" id="house_num" name="houseNum"/>
                                        <input type="hidden" id="street_name" name="streetName"/>
                                        <input type="hidden" id="town" name="town"/>
                                        <input type="hidden" id="court_number" name="court_number"/>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection