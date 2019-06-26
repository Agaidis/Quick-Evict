<title>Eviction Info</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-body body_container">
                    <h2 class="titles" style="text-align:center;">Additional Information</h2>
                    <div class="row" style="margin-left: 6%; margin-right: 6%;">
                        <p><strong>Information on Filing Evictions in Pennsylvania</strong>: Evictions are a tricky process for both new and experienced real estate investors, property managers, and landlords. Here are the major steps to work through an eviction:</p>
                        <p><strong>Documents</strong>: Make sure that the owner has the property licensed as a rental property and have a copy of the signed lease for the current tenant. The judge may deny the eviction if the owner does not have a valid lease or have the property licensed as a rental.</p>
                        <p><strong>Notice to Quit</strong>: Landlord must post a notice to quit on the door of the property 10 days prior to filing the Landlord Tenant Complaint. The only exception to this Notice requirement is if the tenant waived the notice to quit in the signed lease.</p>
                        <p><strong>Landlord-Tenant Complaint</strong>: The next step is to file a Landlord-Tenant Complaint in the appropriate Magisterial District Court. It is essential that you file with the correct Court, based on the address of the property. Each Court has a unique fee for the filing based on different variables. The Complaint also must be completed properly or the Court will return it. The completed and signed Complaint used to require mailing to the correct Court with a check or money order for the amount due. CourtZip makes this process significantly easier by providing a simple form to complete online. Once the form is complete, CourtZip will file it electronically with the Court.</p>
                        <p><strong>Court Hearing for Eviction</strong>: Once the &nbsp;LTC is filed, the Court will set a date for the eviction hearing. The landlord/owner needs to attend the hearing. A lawyer is not required at the hearing. The landlord/owner should bring a copy of the signed lease, any communications (e-mails, text messages, letters, invoices, checks) that are relevant to the case, and any documents (invoices, checks, pictures) as evidence.</p>
                        <p><strong>Order of Possession</strong>: After the eviction hearing, the Court will issue a Judgment within three days. The Judgment may award financial damages and possession of the property to the owner. To obtain possession, an owner must wait 10 days and then file an Order of Possession with the Court. This Order of Possession can be created and filed using CourtZip. </p>
                        <p><strong>Lock-out</strong>: After the Court receives the Order of Possession, it will schedule a lock-out date with the local constable, who will provide a copy of the Order of Possession to the tenant. The lock-out date cannot be less than 10 day from the date the Order is issued.&nbsp; If the tenant does not vacate the property, the owner should attend the lock-out with the constable to take possession of the property.</p>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection