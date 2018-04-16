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
                                <section>
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
                                </section>
                                <h3>Eviction Information</h3>
                                <section>

                                    <h3 style="text-align:center;" class="fs-subtitle">Additional Info on Property where you want to evict tenant.</h3>
                                    <div class="col-md-12 offset-4">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-5 form-inline">
                                                <label>Property Address</label>
                                                <input class="form-control" placeholder="Rental Street Address" type="text" id="property_address"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label>Owner Name</label>
                                                <input class="form-control" placeholder="Owner Name" type="text" id="owner_name"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label>Owner Phone #</label>
                                                <input class="form-control" placeholder="(ext)-000-0000" type="text" id="owner_number"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="filing_date">Filing Date</label>
                                                <input class="form-control" type="date" id="filing_date"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="radio" id="residential_lease" name="lease_type" value="isResidential" checked>
                                                <label for="residential_lease">Lease is Residential</label><br>
                                                <input type="radio" id="non_residential_lease" name="lease_type" value="isNotResidential">
                                                <label for="non_residential_lease">Lease is Non-residential</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="radio" id="rented_by_owner" name="rented_by" value="rentedByOwner" checked>
                                                <label for="rented_by_owner">Property is Rented by Owner</label><br>
                                                <input type="radio" id="rented_by_other" name="rented_by" value="rentedByOwner">
                                                <label for="rented_by_other">Property is Rented by Other</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" placeholder="Name of Landlord" type="text" id="landlord" name="landlord" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="radio" id="no_notice_to_quit" name="quit_notice" value="no_quit_notice" checked>
                                                <label for="no_notice_to_quit">No Notice to Quit was Needed with Accordance to Law</label><br>
                                                <input type="radio" id="notice_to_quit" name="quit_notice" value="quit_notice_given">
                                                <label for="notice_to_quit">Notice to Quit was Given</label>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
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
                                    <div class="form-group">
                                        <div class="row">
                                            <h3>Amount Due:</h3>
                                        </div>
                                    </div>


                                </section>




                                <h3>Signature/Payment</h3>
                                <section>
                                </section>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection