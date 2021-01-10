<title>New Eviction</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card-header"><h2>New Eviction</h2></div>
            <div class="row">
                <div class="col-md-12">
                    <form method="post" action="{{ action('EvictionController@showSamplePDF') }}" enctype="multipart/form-data" id="eviction_form" target="_blank">
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                        <h2 style="text-align:center;" class="titles fs-subtitle">Step 2:<br> Enter the address of the incident.</h2>
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
                            <h2 class="titles major_labels step_3_title">Step 3:<br> Fill out information on the Incident</h2>
                            <!-- Tenant Address INFORMATION -->
                            <h4 class="major_labels">Tenant Address Details</h4>
                            <div id="additional_info_container">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6 offset-4">
                                            <h4 class="incident_address_display_div"><span id="incident_address_descriptor">Incident/Tenant Address:</span> <span style="font-weight:normal;" id="incident_display_address"></span> </h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 offset-4">
                                            <h4 class="tenant_address_display_div"><span style="font-weight:normal;" id="tenant_display_address"></span></h4>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="labels" for="incident_addit_address_detail">Additional Incident Address Detail</label>
                                            <input type="text" class="form-control eviction_fields" placeholder="Example: Unit 3" id="incident_addit_address_detail" name="incident_addit_address_detail" maxlength="10"/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-10">

                                            <div class="labels">Does tenant reside at this address?</div>
                                            <input checked type="radio" id="tenant_resides" value="tenantResides" name="does_tenant_reside">
                                            <label for="tenant_resides">Yes</label><br>

                                            <input type="radio" id="tenant_does_not_reside" value="tenantDoesNotReside" name="does_tenant_reside">
                                            <label for="tenant_does_not_reside">No</label>
                                            <div id="tenant_resides_other_address_div">
                                                <div class="col-sm-8">
                                                    <div class="labels">Enter separate address where tenant resides</div>
                                                    <input class="form-control" placeholder="Tenant Street Address" type="text" id="reside_address"/>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="labels" for="tenant_addit_address_detail">Additional Tenant Address Detail</label>
                                                    <input type="text" class="form-control eviction_fields" placeholder="Example: Unit 3" id="tenant_addit_address_detail" name="tenant_addit_address_detail" maxlength="10"/>
                                                </div>
                                            </div>
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
                                            <input class="form-control eviction_fields" placeholder="Owner Name" type="text" id="owner_name" name="owner_name" maxlength="30"/>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="labels">Owner Phone #</label>
                                            <input class="form-control eviction_fields" placeholder="(ext)-000-0000" type="text" id="owner_number" name="owner_phone" maxlength="15"/>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="labels">Property Owner</div>
                                            <input checked type="radio" id="rented_by_owner" value="rentedByOwner" name="rented_by">
                                            <label for="rented_by_owner">Owner self-filing</label><br>
                                            <div id="rented_by_owner_div">
                                                <div class="col-sm-8">
                                                    <label class="labels">Owner Address Line 1</label>
                                                    <input class="form-control eviction_fields" placeholder="1234 Main Street" type="text" id="owner_address_1" name="owner_address_1" maxlength="30"/>
                                                </div>
                                                <div class="col-sm-8">
                                                    <label class="labels">Owner Address Line 2 </label>
                                                    <input class="form-control eviction_fields" placeholder="Philadelphia, PA 17349" type="text" id="owner_address_2" name="owner_address_2" maxlength="30"/>
                                                </div>
                                            </div>
                                            <input type="radio" id="rented_by_other" value="rentedByOther" name="rented_by">
                                            <label for="rented_by_other">Property Manager filing this request on behalf of owner.</label>
                                            <div id="rented_by_other_div">
                                                <div class="col-sm-12">
                                                    <label class="labels" for="other_name">Property Management Company Name</label>
                                                    <input class="form-control eviction_fields" placeholder="PM Company Name" type="text" id="other_name" name="other_name" value="" maxlength="50">
                                                </div>
                                                <div class="col-sm-12">
                                                    <label class="labels" for="pm_name">Property Manager Name</label>
                                                    <input class="form-control eviction_fields" placeholder="Property Manager Name" type="text" id="pm_name" name="pm_name" value="" maxlength="30">
                                                </div>
                                                <div class="col-sm-8">
                                                    <label class="labels" for="pm_phone">Property Manager Phone #</label>
                                                    <input class="form-control eviction_fields" placeholder="(ext)-000-0000" type="text" id="pm_phone" name="pm_phone" value="" maxlength="15">
                                                </div>
                                                <div class="col-sm-8">
                                                    <label class="labels">Property Manager Address Line 1</label>
                                                    <input class="form-control eviction_fields" placeholder="1234 Main Street" type="text" id="pm_address_1" name="pm_address_1" maxlength="30"/>
                                                </div>
                                                <div class="col-sm-8">
                                                    <label class="labels">Property Manager Address Line 2 </label>
                                                    <input class="form-control eviction_fields" placeholder="Philadelphia, PA 17349" type="text" id="pm_address_2" name="pm_address_2" maxlength="30"/>
                                                </div>
                                                <input type="hidden" id="rented_by_val" name="rented_by_val"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h4 class="major_labels">General Information</h4>
                            <div id="general_information_container">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="labels" for="monthly_rent">Security Deposit</label>
                                            <input type="text" class="form-control eviction_fields" id="security_deposit" name="security_deposit" placeholder="$" value=""  maxlength="9"/>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="labels" for="monthly_rent">Monthly Rent</label>
                                            <input type="text" class="form-control eviction_fields" id="monthly_rent" name="monthly_rent" placeholder="$" value=""  maxlength="9"/>
                                        </div>
                                    </div>
                                    <!-- Tenant Number Container -->
                                    <div class="row">
                                        <div class="col-sm-8 tenant_num_container">
                                            <div class="col-sm-6">
                                                <label for="tenant_num_select" class="labels">Number of Tenants</label>
                                                <span class="fa fa-question-circle" data-placement="right" data-toggle="tooltip" title="Select the Number of Tenants, and put 1 name in each field that appears."></span>
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
                                    <input type="hidden" id="file_type" value="{{$fileType}}"/>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div class="labels">Lease Type:</div>
                                            <input type="radio" id="residential_lease" name="lease_type" value="isResidential" checked>
                                            <label for="residential_lease">Lease is Residential</label><br>
                                            <input type="radio" id="non_residential_lease" name="lease_type" value="isNotResidential">
                                            <label for="non_residential_lease">Lease is Non-residential</label>
                                        </div><br><br>
                                        <div class="col-sm-10">
                                            <div class="labels">Notice Status:</div>
                                            <input type="radio" id="no_notice_to_quit" name="quit_notice" value="no_quit_notice" checked>
                                            <label for="no_notice_to_quit">No Notice to Quit was Needed with Accordance to Law</label><br>
                                            <input type="radio" id="notice_to_quit" name="quit_notice" value="quit_notice_given">
                                            <label for="notice_to_quit">Notice to Quit was Given</label>
                                        </div><br><br>
                                        <div class="col-sm-10">
                                            <div class="labels">Lease Status:</div>
                                            <input type="checkbox" id="term_lease_ended" name="term_lease_ended">
                                            <label for="term_lease_ended">Term of lease has ended</label><br>
                                        </div>
                                        <div class="col-sm-10">
                                            <input type="checkbox" id="unsatisfied_lease" name="unsatisfied_lease">
                                            <label for="unsatisfied_lease">Rent Reserved and due has, upon demand, remained unsatisfied</label>
                                        </div>
                                        <!-- Breached Conditions Container -->
                                        <div class="col-sm-10">
                                            <div class="labels">Breach of Contract? <span class="fa fa-question-circle" data-placement="right" data-toggle="tooltip" title="Check the box below if there was a breach of contract and give details in the input field if necessary."></span></div>
                                            <div class="breached_conditions_container">
                                                <input type="checkbox" id="breached_conditions_lease" name="breached_conditions_lease">
                                                <label for="breached_conditions_lease">Tenant Breached Conditions of Lease</label><br>
                                                <b>Tenant Breached Details:</b>
                                                <input class="form-control" placeholder="Breached Details" type="text" id="breached_details" name="breached_details" disabled  maxlength="95"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h4 class="major_labels">Amount Due:</h4>
                            <div id="amount_due_container">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="due_rent">Rent that is Due at Filing Date</label>
                                            <input type="text" class="form-control eviction_fields" id="due_rent" name="due_rent" placeholder="$" value=""  maxlength="9"/>
                                        </div>
                                        <!-- Property Damages Container -->
                                        <div class="col-sm-12">
                                            <div class="labels">Property Damages? <span class="fa fa-question-circle" data-placement="right" data-toggle="tooltip" title="Fill in the below field with the amount of damages accrued and fill in the details field if necessary."></span></div>
                                            <div class="property_damages_container">
                                                <div class="col-sm-10">
                                                    <label for="damage_amt">Damages for Injury to Property Amount $</label>
                                                    <input type="text" class="form-control eviction_fields" id="damage_amt" name="damage_amt" placeholder="$" value=""  maxlength="9"/>
                                                </div>
                                                <div class="col-sm-12">
                                                    <b>Property Damages Details:</b>
                                                    <input class="form-control eviction_fields" type="text" id="damages_details" name="damages_details" maxlength="75"/><br>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-10">
                                            <label for="unjust_damages">Damages for Unjust Detention of Real Property</label>
                                            <input type="text" class="form-control eviction_fields" id="unjust_damages" name="unjust_damages" placeholder="$" value=""  maxlength="9"/>
                                        </div>
                                        <div class="col-sm-10">
                                            <label for="attorney_fees">Attorney Fees</label>
                                            <input type="text" class="form-control eviction_fields" id="attorney_fees" name="attorney_fees" placeholder="$" value=""  maxlength="9"/>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="labels">Additional Rent? <span class="fa fa-question-circle" data-placement="right" data-toggle="tooltip" title="Was there additional rent due at the hearing date? If so, change the radio button and fill in the additional rent amount field."></span></div>
                                            <div class="additional_rent_container">
                                                <input type="radio" id="no_addit_rent" name="addit_rent" value="no" checked />
                                                <label for="no_addit_rent">Do Not add additional rent due at hearing date</label><br>
                                                <input type="radio" id="addit_rent" name="addit_rent" value="yes" />
                                                <label for="addit_rent">Add additional rent due at hearing date</label><br>
                                                <div class="additional_rent_amt_div">
                                                    <div class="col-sm-10">
                                                        <b>Amount of additional rent remaining due and unpaid on hearing date:</b>
                                                        <input class="form-control eviction_fields" type="text" id="additional_rent_amt" name="additional_rent_amt" placeholder="$" maxlength="9"/><br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="checkbox" id="is_abandoned" name="is_abandoned">
                                    <label for="is_abandoned">A determination that the manufactured home and property have been abandoned.</label><br>
                                    <input type="checkbox" id="is_determination_request" name="is_determination_request">
                                    <label for="is_determination_request">A Request for Determination of Abandonment (Form MDJS 334) must be completed and submitted with this complaint</label>
                                </div>
                            </div>
                            <div class="form-group filing_form_div">
                                <div class="row">
                                    <h3 class="major_labels">Add File Attachment</h3><br>
                                    <div class="col-md-12" id="file_container">
                                        <input type="file" name="file" id="file"><br><br>
                                        <input type="file" name="file2" class="file"><br><br>
                                        <input type="file" name="file3" class="file">
                                        <input type="hidden" name="file_addresses[]" id="file_addresses"/>
                                        <input type="hidden" name="is_extra_filing" id="is_extra_filing" value="0"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="offset-4 col-sm-10">
                                        <input type="submit" id="preview_document" class="btn btn-warning" value="Preview" />
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

                            <!-- INCIDENT ADDRESS -->
                            <input type="hidden" id="state" name="state"/>
                            <input type="hidden" id="county" name="county"/>
                            <input type="hidden" id="house_num" name="houseNum"/>
                            <input type="hidden" id="street_name" name="streetName"/>
                            <input type="hidden" id="town" name="town"/>
                            <input type="hidden" id="zipcode" name="zipcode"/>

                            <!-- RESIDED ADDRESS ELEMENTS -->
                            <input type="hidden" id="resided_state" name="residedState"/>
                            <input type="hidden" id="resided_county" name="residedCounty"/>
                            <input type="hidden" id="resided_house_num" name="residedHouseNum"/>
                            <input type="hidden" id="resided_street_name" name="residedStreetName"/>
                            <input type="hidden" id="resided_town" name="residedTown"/>
                            <input type="hidden" id="resided_zipcode" name="residedZipcode"/>

                            <input type="hidden" id="court_number" name="court_number"/>
                            <input type="hidden" id="user_email" name="user_email" value="{{$userEmail}}"/>
                            <input type="hidden" id="total_input" name="total"/>
                            <input type="hidden" name="distance_fee" id="distance_fee" />
                        </div>
                    </form>
                    <form method="post" action="{{ action('EvictionController@formulatePDF') }}" enctype="multipart/form-data" id="submit_form">
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
                                    <h3 class="titles signature_title">Step 4:<br> Signature</h3>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div>
                                                <span style="font-size: 32px;">X</span> <canvas id="signature_pad" width="600" height="100" style="touch-action: none;"></canvas>
                                                <div class="signature_pad_footer">
                                                </div>
                                                <span id="legal_checkbox_container"><input type="checkbox" id="legal_checkbox"/><em><label style="text-align:center" for="legal_checkbox">By checking this box, I understand that my electronic signature constitutes a legal signature, <br>and that by entering my name above I acknowledge and warrant the accuracy of the information provided in this document.</label></em></span>
                                                <button type="button" class="btn btn-warning clear_signature" data-action="clear">Clear Signature</button>
                                                <button type="button" class="btn btn-primary use_signature" data-action="clear">Use Signature</button><br><br>
                                                <span id="terms_of_agreement_error_msg"></span>
                                            </div>
                                        </div><br><hr><br>
                                        <div class="payment_section">
                                            <h3 class="titles payment_title">Step 5:<br> Payment Information</h3>
                                            <div class="price_ctr col-md-6">
                                                <label>Court Filing Fee: $</label><span id="filing_fee_display"></span><br>
                                                <span id="distance_fee_container"><label>Calculated Distance Fee: $</label><span id="distance_fee_display"></span><br></span>
                                                <label>CourtZip Filing Fee: </label><span> $16.99</span><br>
                                                <label>Total: $</label><span id="total"></span>
                                            </div>
                                            <div class="form-row">
                                                <label for="card-element">
                                                    <span class="credit_debit">Credit or debit card</span>
                                                    <img style="margin-left:70px;" alt="Credit Card Logos" title="Credit Card Logos" src="https://www.credit-card-logos.com/images/multiple_credit-card-logos-1/credit_card_logos_10.gif" width="236" height="30" border="0" />
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
                                        <button type="button" class="btn btn-success pay_sign_submit" id="pay_sign_submit" data-action="save-png">Pay and Submit Document</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="sending-modal"><!-- Place at bottom of page --></div>
        </div>
    </div>
</div>

@endsection