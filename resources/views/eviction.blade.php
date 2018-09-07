<title>New Eviction</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-header"><h2>New Eviction</h2></div>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-md-10 offset-md-1 offset-lg-1">
                            <div id="wizard">
                                <h3>Eviction Location</h3>
                                    <h2 style="text-align:center;" class="title">Eviction Location</h2>
                <h3 style="text-align:center;" class="fs-subtitle">Enter the address you plan on evicting.</h3>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6 col-md-8 col-lg-8 col-xl-8">   
                                        <input class="form-control" placeholder="Rental Street Address" type="text" id="pac-input"/>
                                    </div>

                                </div>
                            </div>
                                    <div id="map"></div>
                                    <h3 class="major_labels" style="text-align:center;">Additional Info on Property where you want to evict tenant.</h3>
                                    <div class="col-md-12 offset-1">
                                    <h4 class="major_labels">Property Address</h4>
                                        <div id="address_container">
                                        <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <label class="labels">Address Line 1</label>
                                                <input class="form-control" placeholder="Rental Street Address" type="text" id="property_address_line_1"/>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <label class="labels">Address Line 2</label>
                                                    <input class="form-control" placeholder="City State, Zipcode" type="text" id="property_address_line_2"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                        <h4 class="major_labels">Owner Information</h4>
                                        <div id="owner_container">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="labels">Owner Name</label>
                                                <input class="form-control" placeholder="Owner Name" type="text" id="owner_name"/>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="labels">Owner Phone #</label>
                                                <input class="form-control" placeholder="(ext)-000-0000" type="text" id="owner_number"/>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="labels">Property Owner</div>
                                                <input type="radio" id="rented_by_owner" name="rented_by" value="rentedByOwner" checked>
                                                <label for="rented_by_owner">Property is Rented by Owner</label><br>
                                                <input type="radio" id="rented_by_other" name="rented_by" value="rentedByOwner">
                                                <label for="rented_by_other">Property is Rented by Other</label>
                                                <div class="col-sm-12">
                                                    <input class="form-control" placeholder="Name of Landlord" type="text" id="landlord" name="landlord" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        </div>
                                        <h4 class="major_labels">General Information</h4>
                                            <div id="general_information_container" class="major_labels">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <label for="filing_date" class="labels">Filing Date</label>
                                                            <input class="form-control" type="date" id="filing_date"/>
                                                        </div>
                                                        <div class="col-sm-6">
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
                                                            <input type="radio" id="term_lease_ended" name="term_lease" value="lease_ended" checked>
                                                            <label for="term_lease_ended">Term of lease has ended</label><br>
                                                            <input type="radio" id="breached_conditions_lease" name="term_lease" value="breached_conditions_lease">
                                                            <label for="breached_conditions_lease">Tenant Breached Conditions of Lease</label><br>
                                                            <input type="radio" id="unsatisfied_lease" name="term_lease" value="unsatisfied_lease">
                                                            <label for="unsatisfied_lease">Rent Reserved and due has, upon demand, remained unsatisfied</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <h4 class="major_labels">Amount Due:</h4>
                                        <div id="amount_due_container" class="major_labels">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <label for="due_rent">Rent that is Due at Filing Date</label>
                                                        <input type="text" class="form-control" id="due_rent" name="term_lease" value="$" />
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <label for="damage_amt">Damages for Injury to Property</label>
                                                        <input type="text" class="form-control" id="damage_amt" name="damage_amt" value="$" />
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <label for="unjust_damages">Damages for Unjust Detention of Real Property</label>
                                                        <input type="text" class="form-control" id="unjust_damages" name="unjust_damages" value="$" />
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <label for="attorney_fees">Attorney Fees</label>
                                                        <input type="text" class="form-control" id="attorney_fees" name="attorney_fees" value="$" />
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <input type="radio" id="addit_rent" name="addit_rent" value="" />
                                                        <label for="addit_rent">Add additional rent due at hearing date</label><br>
                                                        <input type="radio" id="addit_rent" name="addit_rent" value="" />
                                                        <label for="addit_rent">Do Not add additional rent due at hearing date</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" id="pdf_download_btn" class="btn btn-primary">Download PDF File</button>







                                <h3 class="major_labels">Signature/Payment</h3>
                                    <script src="https://js.stripe.com/v3/"></script>

                                    <form action="/charge" method="post" id="payment-form">
                                        <div class="form-row">
                                            <div id="card-element">
                                                <!-- A Stripe Element will be inserted here. -->
                                            </div>

                                            <!-- Used to display form errors. -->
                                            <div id="card-errors" role="alert"></div>
                                        </div>
                                        <button type="button" class="btn btn-primary">Submit Payment</button>
                                    </form>

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