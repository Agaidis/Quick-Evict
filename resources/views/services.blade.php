<title>Services</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">

<header class="subhead">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-10 col-xl-10 mx-auto">
                <h2 class="titles" style="text-align:center;">Services Offered</h2>
                <div>
                    <div style="text-align:center;">The eviction process can be a confusing, time consuming, and expensive process.  CourtZip was created to simplify this process at a reasonable price.  We have built first party tech that automates much of this process and in some courts happens in real time eliminating wasted processing time in the mail.  We can simply assist in the filings or we can take it the whole way and deliver you a vacant secured property. The level of service is your choice</div>

                    <h2 style="margin-top:3%;">Step 1 - File the LandLord Tenant Complaint</h2>
                    <div style="text-align:left;">(You can start this process for any tenant with a past due balance)</div>

                    <h4 style="margin-top:5%;">$25- Landlord Tenant Complaint Filing Only</h4>

                    <div style="text-align:center;">You will fill out a form online asking for all relevant information(address, tenant name, delinquency amount, etc.).  The court fees are automatically calculated which vary depending on the number of defendants, location, and balance. The form is converted to a court approved document.  Next, you will pay the court costs and $25 service fee through our site.  Upon payment, the form will be filed electronically or by courier within 24 hrs.  Within a few business days the court will set a court date and we will notify you by US Mail.  You will attend court and represent yourself approximately 2-3 weeks after you submit this form.</div>

                    <h2 style="text-align:center;">OR</h2>

                    <h4 style="margin-top:5%;">*$225-Landlord Tenant Complaint Filing + Court Representation</h4>

                    <div style="text-align:center;">This service adds court representation to the LTC Filing Service.  The initial process is the same as above except for an added cost of $200.  Additionally, you will be sent an authorization of representation form to fill out electronically.  This gives Courtzip permission to represent you in court.  You do not need to attend court but can if you like. </div>

                    <h2 style="margin-top:3%;">Step 2 - File the Order for Possession</h2>

                    <div style="text-align:center;">(on 11th day after winning a judgment at the LT Hearing you will start this process if the tenant hasn’t paid the judgment)</div>

                    <h4 style="margin-top:5%;">$25- Order for Possession Filing Only</h4>
                    <div style="text-align:center;">You will fill out a form online asking for all relevant information(address, tenant name, delinquency amount, etc.).  The court fees are automatically calculated which vary depending on the number of defendants and location. The form is converted to a court approved document.  Next, you will pay the court costs and $25 service fee through our site.  Upon payment, the form will be filed electronically or by courier within 24 hrs.  The judge will issue an order for possession.  Next the constable will notify us of a lockout date and we will communicate that to you.  Typically lockout dates are 2-3 weeks after filing an Order for Possession.  You will meet the constable at the time and date of the lock out, change the locks and secure the property.</div>

                    <h4 style="margin-top:5%;">*$275 - Order for Possession Filing + Lock Out Attendance and Lock Change</h4>

                    <div style="text-align:center;">This service adds representation and lock changes to the OP Filing Service.  The initial process is the same as above with a CourtZip agent attending the lock out and changing the locks for an added cost of $250.  You do not need to attend the lockout but can if you like.  We can mail you a copy of the new key or leave a copy in a lockbox at the property. </div>

                    <h4>*In select counties</h4>
                </div>
            </div>
        </div>
    </div>
</header>
@endsection