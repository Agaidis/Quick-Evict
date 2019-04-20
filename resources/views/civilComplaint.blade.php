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
                                    <h3 style="text-align:center;" class="fs-subtitle">Enter the address you plan on complaint occurred.</h3>
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
                                                        <label class="labels" for="tenant_name">Defendant Name</label>
                                                        <input type="text" class="form-control eviction_fields" placeholder="" id="tenant_name" name="tenant_name"/>
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
                                                        <input class="form-control eviction_fields" placeholder="Plaintiff Name" type="text" id="owner_name" name="owner_name"/>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="labels">Plaintiff Phone #</label>
                                                        <input class="form-control eviction_fields" placeholder="(ext)-000-0000" type="text" id="owner_number" name="owner_phone"/>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <label class="labels">Plaintiff Address Line 1</label>
                                                        <input class="form-control eviction_fields" placeholder="1234 Main Street" type="text" id="owner_address_1" name="owner_address_1"/>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <label class="labels">Plaintiff Address Line 2 </label>
                                                        <input class="form-control eviction_fields" placeholder="Philadelphia, PA 17349" type="text" id="owner_address_2" name="owner_address_2"/>
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
                                                        <input type="text" class="form-control eviction_fields" id="total_judgment" name="total_judgment" placeholder="$" value="" />
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <label class="labels" for="total_judgment">Claim Description</label>
                                                        <textarea style="height:150px;" class="form-control eviction_fields" id="claim_description" name="claim_description" placeholder="Broken Window"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="offset-1 col-sm-10">
                                                    <button type="button" id="finalize_document" data-target="#modal_signature" data-toggle="modal" class="btn btn-primary">Finalize Document</button>
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
                                <div class="modal fade" id="modal_signature">
                                    <div class="modal-dialog" role="document">
                                        <div class="set_court_date_modal modal-content">
                                            <div class="modal-header">
                                                <h4 class="set_court_date_title">Signature: </h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-offset-3 col-sm-8">
                                                        <span><input type="checkbox" id="legal_checkbox"/><em>By checking this box, I understand that my electronic signature constitutes a legal signature, <br>and that by entering my name above I acknowledge and warrant the accuracy of the information provided in this document.</em></span>
                                                        <canvas id="signature_pad" width="600" height="200" style="touch-action: none;"></canvas>
                                                        <div class="signature_pad_footer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <div class="signature_pad_actions">
                                                    <button type="button" class="btn btn-warning clear_signature" data-action="clear">Clear</button>
                                                    <button type="button" class="btn btn-danger no_signature" data-dismiss="modal" data-action="save-png">No Signature Needed</button>
                                                    <button type="button" class="btn btn-success save_signature" data-dismiss="modal" data-action="save-png">Use Digital Signature</button>
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
    </div>
@endsection