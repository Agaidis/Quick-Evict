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
                        <div class="col-md-12">
                            <form method="post" action="{{ action('EvictionController@formulatePDF') }}" enctype="multipart/form-data" id="eviction_form">
                                <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <h2 style="text-align:center;" class="title">Eviction Location</h2>
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
                                    <h3 class="major_labels" style="text-align:center;">Additional Info on Property where you want to evict tenant.</h3>
                                <div class="col-md-12 offset-1">
                                        <h4 class="major_labels">Owner Information</h4>
                                    <div id="owner_container">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label class="labels">Owner Name</label>
                                                    <input class="form-control" placeholder="Owner Name" type="text" id="owner_name" name="owner_name"/>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="labels">Owner Phone #</label>
                                                    <input class="form-control" placeholder="(ext)-000-0000" type="text" id="owner_number" name="owner_phone"/>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="labels">Property Owner</div>
                                                    <input type="radio" id="rented_by_owner" value="rentedByOwner" name="rented_by">
                                                    <label for="rented_by_owner">Property is Rented by Owner</label><br>
                                                    <input type="radio" id="rented_by_other" value="rentedByOther" name="rented_by">
                                                    <label for="rented_by_other">Property is Rented by Other</label>
                                                        <div class="col-sm-12">
                                                            <input class="form-control" placeholder="Name of Landlord" type="text" id="other_name" name="other_name" value="">
                                                        </div>
                                                    <input type="hidden" id="rented_by_val" name="rented_by_val"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <h4 class="major_labels">General Information</h4>
                                            <div id="general_information_container" class="major_labels">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label for="monthly_rent">Security Deposit</label>
                                                            <input type="text" class="form-control" id="security_deposit" name="security_deposit" value="$" />
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label for="monthly_rent">Monthly Rent</label>
                                                            <input type="text" class="form-control" id="monthly_rent" name="monthly_rent" value="$" />
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="labels">Tenant(s) Name</label>
                                                            <input class="form-control" placeholder="Tenant(s) Name" type="text" id="tenant_name" name="tenant_name"/>
                                                        </div><br><br>
                                                        <div class="col-sm-4">
                                                            <label for="tenant_num" class="labels"># of Tenants on Lease</label>
                                                            <select class="form-control" id="tenant_num" name="tenant_num">
                                                                <option value="" selected disabled># of Tenants</option>
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
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label for="filing_date" class="labels">Filing Date</label>
                                                            <input class="form-control" type="date" id="filing_date" name="filing_date"/>
                                                        </div>
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
                                                            <input type="checkbox" id="breached_conditions_lease" name="breached_conditions_lease">
                                                            <label for="breached_conditions_lease">Tenant Breached Conditions of Lease</label>
                                                            <div class="details"></div><b>Tenant Breached Details:</b><input class="form-control" type="text" id="breached_details" name="breached_details" /><br>
                                                            <input type="checkbox" id="unsatisfied_lease" name="unsatisfied_lease">
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
                                                        <input type="text" class="form-control" id="due_rent" name="due_rent" value="$" />
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <label for="damage_amt">Damages for Injury to Property</label>
                                                        <input type="text" class="form-control" id="damage_amt" name="damage_amt" value="$" />
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <div class="details"></div>
                                                        <b>Property Damages Details:</b>
                                                        <input class="form-control" type="text" id="damages_details" name="damages_details" /><br>
                                                    </div>
                                                        <div class="col-sm-10">
                                                        <label for="unjust_damages">Damages for Unjust Detention of Real Property</label>
                                                        <input type="text" class="form-control" id="unjust_damages" name="unjust_damages" value="$" />
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <label for="attorney_fees">Attorney Fees</label>
                                                        <input type="text" class="form-control" id="attorney_fees" name="attorney_fees" value="$" />
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <div class="labels">Additional Rent?</div>
                                                        <input type="radio" id="addit_rent" name="addit_rent" value="yes" checked />
                                                        <label for="addit_rent">Add additional rent due at hearing date</label><br>
                                                        <input type="radio" id="no_addit_rent" name="addit_rent" value="no" />
                                                        <label for="no_addit_rent">Do Not add additional rent due at hearing date</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" id="pdf_download_btn" class="btn btn-primary">Download PDF File</button>


                                    <input type="hidden" id="state" name="state"/>
                                    <input type="hidden" id="zipcode" name="zipcode"/>
                                    <input type="hidden" id="county" name="county"/>
                                    <input type="hidden" id="house_num" name="houseNum"/>
                                    <input type="hidden" id="street_name" name="streetName"/>
                                    <input type="hidden" id="town" name="town"/>
                                    <input type="hidden" id="court_number" name="court_number"/>
                                    <input type="hidden" id="court_phone_number" name="court_phone_number"/>
                                    <input type="hidden" id="court_address1" name="court_address1"/>
                                    <input type="hidden" id="court_address2" name="court_address2"/>

                                {{--<h3 class="major_labels">Signature/Payment</h3>--}}
                                    {{--<script src="https://js.stripe.com/v3/"></script>--}}

                                    {{--<form action="/charge" method="post" id="payment-form">--}}
                                        {{--<div class="form-row">--}}
                                            {{--<div id="card-element">--}}
                                                {{--<!-- A Stripe Element will be inserted here. -->--}}
                                            {{--</div>--}}

                                            {{--<!-- Used to display form errors. -->--}}
                                            {{--<div id="card-errors" role="alert"></div>--}}
                                        {{--</div>--}}
                                        {{--<button type="button" class="btn btn-primary">Submit Payment</button>--}}
                                    {{--</form>--}}

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