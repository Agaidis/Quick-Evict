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
                                <form method="post" action="{{ action('CivilComplaintController@showSamplePDF') }}" enctype="multipart/form-data" id="eviction_form" target="_blank">
                                    <input type="hidden" name="_token" value="{{ Session::token() }}">
                                    <a href="{{'new-file'}}"><button type="button" id="back_to_step_1_btn" class="btn btn-primary">Back to Step 1</button></a>
                                    <h2 style="text-align:center;" class="titles fs-subtitle">Step 2:<br> Enter the address the plaintiff alleges the complaint occurred.</h2>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 col-md-8 col-lg-8 col-xl-8">
                                                <input style="margin-left: 25%;" class="form-control" placeholder="Enter Address of Complaint" type="text" id="pac-input"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="offset-1">
                                        <div id="map"></div>
                                    </div>
                                    <div class="col-md-12 offset-1 filing_form_div">
                                        <!-- ADDITIONAL INFORMATION ON PROPERTY -->
                                        <h2 class="titles major_labels step_3_title">Step 3:<br> Fill out information on the Incident</h2>
                                        <div id="additional_info_container">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <h4 class="address_display_div"><b>Address</b>: <span id="display_address"></span></h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label class="labels" for="zipcode">Verify Zipcode: </label>
                                                        <input type="text" class="form-control eviction_fields" placeholder="07753" id="zipcode" name="zipcode" maxlength="11"/>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label class="labels" for="unit_number">Additional Address Detail</label>
                                                        <input type="text" class="form-control eviction_fields" placeholder="Example: Unit 3" id="unit_number" name="unit_number" maxlength="10"/>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="civil_delivery_type_container col-md-6">
                                                        <h4>How was the Complaint Delivered?</h4>
                                                        <label for="mail_delivery_type">Mail</label>
                                                        <input checked type="radio" id="mail_delivery_type" value="mail" name="delivery_type"><br>
                                                        <label for="constable_delivery_type">Constable</label>
                                                        <input type="radio" id="constable_delivery_type" value="constable" name="delivery_type">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-8 tenant_num_container">
                                                        <div class="col-sm-6">
                                                            <label for="tenant_num_select" class="labels">Number of Defendants</label>
                                                            <span class="fa fa-question-circle" data-placement="right" data-toggle="tooltip" title="Select the number of defendants that are present, and put 1 name for each field that appears."></span>
                                                            <select class="form-control" id="tenant_num_select">
                                                                <option value="" selected disabled>Select # of Defendants</option>
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
                                                    <input type="hidden" id="tenant_num" name="tenant_num" />
                                                    <div class="col-sm-8">
                                                        <label class="labels">Defendant Street Mailing Address</label>
                                                        <input class="form-control eviction_fields" placeholder="1234 Main Street" type="text" id="civil_defendant_address_1" name="civil_defendant_address_1" maxlength="30"/>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <label class="labels">Defendant City, State, Zip Mailing Address </label>
                                                        <input class="form-control eviction_fields" placeholder="Philadelphia, PA 17349" type="text" id="civil_defendant_address_2" name="civil_defendant_address_2" maxlength="30"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- OWNER INFORMATION -->
                                        <h4 class="major_labels">Plaintiff Information</h4>
                                        <div id="owner_container">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="labels">Plaintiff Name</label>
                                                        <input class="form-control eviction_fields" placeholder="Plaintiff Name" type="text" id="owner_name" name="owner_name" maxlength="30"/>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="labels">Plaintiff Phone #</label>
                                                        <input class="form-control eviction_fields" placeholder="(ext)-000-0000" type="text" id="owner_number" name="owner_phone" maxlength="15"/>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <label class="labels">Plaintiff Address Line 1</label>
                                                        <input class="form-control eviction_fields" placeholder="1234 Main Street" type="text" id="owner_address_1" name="owner_address_1" maxlength="30"/>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <label class="labels">Plaintiff Address Line 2 </label>
                                                        <input class="form-control eviction_fields" placeholder="Philadelphia, PA 17349" type="text" id="owner_address_2" name="owner_address_2" maxlength="30"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Amounts Container -->
                                        <h4 class="major_labels">Judgment Info</h4>
                                        <div id="amount_due_container" class="major_labels">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="labels" for="total_judgment">Total Judgment</label>
                                                        <input type="text" class="form-control eviction_fields" id="total_judgment" name="total_judgment" placeholder="$" value=""  maxlength="9"/>
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <label class="labels" for="total_judgment">Claim Description</label><div id ="textarea_feedback"></div>
                                                        <textarea style="height:150px;" class="form-control eviction_fields" id="claim_description" maxlength="500" name="claim_description" placeholder="Broken Window"></textarea>
                                                    </div>
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
                                        <input type="hidden" id="state" name="state"/>
                                        <input type="hidden" id="county" name="county"/>
                                        <input type="hidden" id="house_num" name="houseNum"/>
                                        <input type="hidden" id="street_name" name="streetName"/>
                                        <input type="hidden" id="town" name="town"/>
                                        <input type="hidden" id="court_number" name="court_number"/>
                                    </div>
                                </form>





                                <form method="post" action="{{ action('CivilComplaintController@formulatePDF') }}" enctype="multipart/form-data" id="submit_form">
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
                                                <div class="price_ctr col-md-6">
                                                    <label>Court Filing Fee: $</label><span id="filing_fee_display"></span><br>
                                                    <label>CourtZip Filing Fee: </label><span> $16.99</span><br>
                                                    <label>Total: $</label><span id="total"></span>

                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <input type="hidden" id="file_type" value="{{$fileType}}"/>
                                                        <div>
                                                            <span style="font-size: 32px;">X</span> <canvas id="signature_pad" width="600" height="100" style="touch-action: none;"></canvas>
                                                            <div class="signature_pad_footer">
                                                            </div>
                                                            <span id="legal_checkbox_container"><input type="checkbox" id="legal_checkbox"/><em><label style="text-align:center" for="legal_checkbox">By checking this box, I understand that my electronic signature constitutes a legal signature, <br>and that by entering my name above I acknowledge and warrant the accuracy of the information provided in this document.</label></em></span>
                                                            <button type="button" class="btn btn-warning clear_signature" data-action="clear">Clear Signature</button>
                                                            <button type="button" class="btn btn-primary use_signature" data-action="clear">Use Signature</button><br><br>
                                                            <span style="color:red;" id="terms_of_agreement_error_msg"></span>
                                                        </div>
                                                    </div><br><hr><br>
                                                    <div class="payment_section">
                                                        <h3 class="titles payment_title">Step 5:<br> Payment Information</h3>
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
                                                    <button disabled type="button" class="btn btn-success pay_sign_submit" id="pay_sign_submit" data-action="save-png">Pay and Submit Document</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sending-modal"><!-- Place at bottom of page --></div>
            </div>
        </div>
    </div>
@endsection