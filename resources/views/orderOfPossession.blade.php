<title>New Order of Possession</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><h2>New Order of Possession</h2></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form method="post" action="{{ action('OrderOfPossessionController@formulatePDF') }}" enctype="multipart/form-data" id="oop_form">
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
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="labels" for="tenant_name">Tenant Name</label>
                                                        <input type="text" class="form-control eviction_fields" placeholder="" id="tenant_name" name="tenant_name"/>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label class="labels" for="docket_number">Docket Number</label>
                                                        <input type="text" class="form-control eviction_fields" placeholder="" id="docket_number" name="docket_number"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- OWNER INFORMATION -->
                                        <h4 class="major_labels">Owner Information</h4>
                                        <div id="owner_container">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="labels">Owner Name</label>
                                                        <input class="form-control eviction_fields" placeholder="Owner Name" type="text" id="owner_name" name="owner_name"/>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="labels">Owner Phone #</label>
                                                        <input class="form-control eviction_fields" placeholder="(ext)-000-0000" type="text" id="owner_number" name="owner_phone"/>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="labels">Property Owner</div>
                                                        <input checked type="radio" id="rented_by_owner" value="rentedByOwner" name="rented_by">
                                                        <label for="rented_by_owner">Property is Rented by Owner</label><br>
                                                        <div id="rented_by_owner_div">
                                                            <div class="col-sm-8">
                                                                <label class="labels">Owner Address Line 1</label>
                                                                <input class="form-control eviction_fields" placeholder="1234 Main Street" type="text" id="owner_address_1" name="owner_address_1"/>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label class="labels">Owner Address Line 2 </label>
                                                                <input class="form-control eviction_fields" placeholder="Philadelphia, PA 17349" type="text" id="owner_address_2" name="owner_address_2"/>
                                                            </div>
                                                        </div>
                                                        <input type="radio" id="rented_by_other" value="rentedByOther" name="rented_by">
                                                        <label for="rented_by_other">Property Rented by 3rd Party</label>
                                                        <div id="rented_by_other_div">
                                                            <div class="col-sm-12">
                                                                <label class="labels" for="other_name">Property Management Company Name</label>
                                                                <input class="form-control eviction_fields" placeholder="PM Company Name" type="text" id="other_name" name="other_name" value="">
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <label class="labels" for="pm_name">Property Manager Name</label>
                                                                <input class="form-control eviction_fields" placeholder="Property Manager Name" type="text" id="pm_name" name="pm_name" value="">
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label class="labels" for="pm_phone">Property Manager Phone #</label>
                                                                <input class="form-control eviction_fields" placeholder="(ext)-000-0000" type="text" id="pm_phone" name="pm_phone" value="">
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label class="labels">Property Manager Address Line 1</label>
                                                                <input class="form-control eviction_fields" placeholder="1234 Main Street" type="text" id="pm_address_1" name="pm_address_1"/>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label class="labels">Property Manager Address Line 2 </label>
                                                                <input class="form-control eviction_fields" placeholder="Philadelphia, PA 17349" type="text" id="pm_address_2" name="pm_address_2"/>
                                                            </div>
                                                            <input type="hidden" id="rented_by_val" name="rented_by_val"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Amounts Container -->
                                        <h4 class="major_labels">Amounts</h4>
                                        <div id="general_information_container" class="major_labels">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div class="col-sm-6">
                                                            <div class="col-sm-10">
                                                                <label for="unjust_damages">Judgment Amount</label>
                                                                <input type="text" class="form-control eviction_fields" id="judgment_amount" name="judgment_amount" placeholder="$" value="" />
                                                            </div>
                                                            <div class="col-sm-10">
                                                                <label for="attorney_fees">Costs in Original LT Proceeding</label>
                                                                <input type="text" class="form-control eviction_fields" id="costs_original_lt_proceeding" name="costs_original_lt_proceeding" placeholder="$" value="" />
                                                            </div>
                                                            <div class="col-sm-10">
                                                                <label for="unjust_damages">Costs in this Proceeding</label>
                                                                <input type="text" class="form-control eviction_fields" id="costs_this_proceeding" name="costs_this_proceeding" placeholder="$" value="" />
                                                            </div>
                                                            <div class="col-sm-10">
                                                                <label for="attorney_fees">Attorney Fees</label>
                                                                <input type="text" class="form-control eviction_fields" id="attorney_fees" name="attorney_fees" placeholder="$" value="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- SIGNATURE PAD -->
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="offset-1 col-sm-10">
                                                    <canvas id="signature_pad" width="600" height="200" style="touch-action: none;"></canvas>
                                                    <div class="signature_pad_footer">
                                                        <div class="signature_pad_actions">
                                                            <button type="button" class="btn btn-warning clear_signature" data-action="clear">Clear</button>
                                                            <button type="button" class="btn btn-danger no_signature" data-action="save-png">No Digital Signature</button>
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