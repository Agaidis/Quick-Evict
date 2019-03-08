<title>Eviction Info</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-header"><h2>Additional Information</h2></div>
                <div class="card-body">
                    <div class="row" style="margin-left: 6%; margin-right: 6%;">
                        <p><b>Information on Filing Evictions in Pennsylvania</b>:
                        Evictions are a tricky process for both new and experienced real estate investors, property managers, and landlords.  Here are the major steps to work through an eviction:</p>

                        <p><b>Documents</b>: Make sure that the owner has the property licensed as a rental property and have a copy of the signed lease for the current tenant.  The judge may deny the eviction if the owner does not have a valid lease or a or have the property licensed as a rental.</p>

                        <p><b>Notice to Quit</b>:  Landlord must post a notice to quit on the door of the property 10 days prior to filing the Landlord Tenant Complaint.  The only exception to this is if the landlord has a signed lease that waives the notice to quit.</p>

                        <p><b>Landlord Tenant Complaint</b>:  The next step in the process is to file a Landlord Tenant Complain with the local magistrate.  It's essential that you file with the correct local magistrate --- based on the address of the property.  Each magistrate has a unique fee for the filing based on a couple different variables.  The complaint also must be filled out properly or the court will return it.  The complaint should be mailed to the correct magistrate with a fully completed and signed LTC and a check or money order for the amount due to the magistrate.  EvictionTech makes this process significantly easier by providing a simple form to fill out online.  Once submitting the form, EvictionTech will either submit it online to the magistrate (if the magistrate allows online submissions) or mail it to the magistrate for you (if magistrate does not allow online submissions).</p>

                        <p><b>Court Hearing for Eviction</b>: Once the owner or landlord successfully files the LTC, a court date will be set for the eviction hearing.  The landlord / owner needs to attend the hearing.  A lawyer is not required at the hearing.  The owner / landlord should bring a copy of the signed lease, any communication that is key to the case, and any pictures / videos as evidence.</p>
                        <p><b>Order of Possession</b>: After the eviction hearing, the court will let the owner / landlord know if they won the eviction hearing.  If the owner / landlord wins, you can file a an order of possession with the court.  This must be mailed to the local magistrate with a check or money order.  The amount of order of posession is different for every court based on a couple different variables.</p>

                        <p><b>Lock-out</b>:  After the magistrate gets the order of possession, they will schedule a lock-out date with the local constable.  The owner / landlord should attend the lock-out with the magistrate to take possession of the property.</p>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection