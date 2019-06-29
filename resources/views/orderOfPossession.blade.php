<title>New Order of Possession</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><h2>New Order of Possession</h2></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form method="post" action="{{ action('OrderOfPossessionController@formulatePDF') }}" enctype="multipart/form-data" id="eviction_form">
                                    <input type="hidden" name="_token" value="{{ Session::token() }}">
                                    <h2 style="text-align:center;" class="titles fs-subtitle">Step 2:<br> Enter the address you plan on evicting.</h2>
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
                                                <!-- Tenant Number Container -->
                                                <div class="row">
                                                    <div class="col-sm-8 tenant_num_container">
                                                        <div class="col-sm-6">
                                                            <label for="tenant_num_select" class="labels">Number of Tenants</label>
                                                            <span class="fa fa-question-circle" data-placement="right" data-toggle="tooltip" title="Select the number of tenants that are present, and put 1 name for each field that appears."></span>
                                                            <select class="form-control" id="tenant_num_select">
                                                                <option value="" selected disabled>Select # of Tenants</option>
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
                                                            </select>
                                                        </div><br>
                                                        <div class="col-sm-10" id="tenant_input_container"></div>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="tenant_num" name="tenant_num" />
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

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="offset-4 col-sm-10">
                                                    <button type="button" id="finalize_document" data-target="#modal_signature" data-toggle="modal" class="btn btn-primary">Sign and Payment</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="status_msg"></div>

                                        <input type="hidden" id="signature_source" name="signature_source"/>
                                        <input type="hidden" id="state" name="state"/>
                                        <input type="hidden" id="county" name="county"/>
                                        <input type="hidden" id="house_num" name="houseNum"/>
                                        <input type="hidden" id="street_name" name="streetName"/>
                                        <input type="hidden" id="town" name="town"/>
                                        <input type="hidden" id="court_number" name="court_number"/>
                                    </div>






                                    <!-- PAY AND SIGN MODAL-->
                                    <div class="modal fade" id="modal_signature">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal_signature modal-content">
                                                <div class="modal-header">
                                                    <h4 class="set_court_date_title">Sign Document and Payment Process </h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <h3 class="titles signature_title">Step 3:<br> Signature</h3>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div>
                                                            <span style="font-size: 32px;">X</span> <canvas id="signature_pad" width="600" height="200" style="touch-action: none;"></canvas>
                                                            <div class="signature_pad_footer">
                                                            </div>
                                                            <span id="legal_checkbox_container"><input type="checkbox" id="legal_checkbox"/><em><label style="text-align:center" for="legal_checkbox">By checking this box, I understand that my electronic signature constitutes a legal signature, <br>and that by entering my name above I acknowledge and warrant the accuracy of the information provided in this document.</label></em></span>
                                                            <button type="button" class="btn btn-warning clear_signature" data-action="clear">Clear Signature</button>
                                                            <button disabled type="button" class="btn btn-primary use_signature" data-action="clear">Use Signature</button>
                                                        </div>
                                                    </div><br><hr><br>
                                                    <div class="payment_section">
                                                        <h3 class="titles payment_title">Step 4:<br> Payment Information</h3>
                                                        <div class="form-row">
                                                            <label for="card-element">
                                                                <span class="credit_debit">Credit or debit card</span>
                                                                <img style="margin-left:70px;" alt="Credit Card Logos" title="Credit Card Logos" src="http://www.credit-card-logos.com/images/multiple_credit-card-logos-1/credit_card_logos_10.gif" width="236" height="30" border="0" />
                                                            </label>
                                                            <div id="card-element">
                                                                <!-- A Stripe Element will be inserted here. -->
                                                            </div>

                                                            <!-- Used to display form errors. -->
                                                            <div id="card-errors" role="alert"></div>
                                                        </div><br><br>
                                                    </div>
                                                </div>
                                                <div class="pay_submit_section modal-footer">
                                                    <button disabled type="submit" class="btn btn-success pay_sign_submit" data-action="save-png">Pay and Submit Document</button>
                                                </div>
                                            </div>
                                        </div>
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