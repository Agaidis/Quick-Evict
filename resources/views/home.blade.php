@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card" id="getting_started_panel" style="margin-bottom:3%;">
                    <div class="card-body">
                        <h2 class="titles">Start a Filing:</h2>
                        <div class="button_panel">
                            <a href="{{ url('new-ltc') }}"><button type="button" class="btn btn-primary home_btns" id="ltc_btn">Landlord-Tenant Complaint</button></a>
                            <a href="{{ url('new-oop') }}"><button type="button" class="btn btn-primary home_btns" id="oop_btn">Order of Possession</button></a>
                            <a href="{{ url('new-civil-complaint') }}"><button type="button" class="btn btn-primary home_btns" id="civil_complaint_btn">Civil Complaint</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <h2 class="titles">Welcome to Court<em>Zip</em>!</h2>
                        <div>CourtZip is the leader in combining technology and court filing expertise to improve the process for filing Complaints in Pennsylvania Magisterial District Courts. CourtZip makes it easy to file Civil Complaints under $12,000, Landlord-Tenant Complaints (for evictions), and Orders of Possession (for possession of property from eviction).</div>

                        <div>Prior to CourtZip, the filing process was outdated. Filing in the District Court required extra manual work and delays for everyone involved. CourtZip results from a collaboration of innovative judges, attorneys, government officials, property managers, real estate owners, and web developers to create a better solution.</div>

                        <div>CourtZip solves the following problems:</div>

                        <ul>
                            <li><strong>Which Court to File My Complaint: </strong>Civil Complaints (under $12,000 at issue) &nbsp;and Landlord-Tenant Complaints must be filed in the correct Magisterial District Court, which is often confusing as most counties have numerous Magisterial District Courts with irregular boundaries. CourtZip has geo-coded each Pennsylvania address into the specific Court to be filed. The user types in the address and CourtZip identifies the proper court.&nbsp; You never again have to call different Courts, search cumbersome websites, or file in the wrong Court.</li>
                            <li><strong>How Much is Filing Fee:</strong> Each Court has different filing fees based on different variables, which generally require a call to the proper Court to determine. CourtZip built a database, updated routinely, of every fee for each Court. You never have to call a Court again to ascertain the fees. Just type in the basic filing info and CourtZip populates the specific filing fee for you, saving time for the Court staff and filers.</li>
                            <li><strong>Creating a Typed Civil Complaint, Landlord-Tenant Complaint, or Order of </strong><strong>Possession:</strong> Whether you are filing one complaint or you file hundreds each month, CourtZip can make it easier and faster for you to draft them.</li>
                            <li><strong>Faster Delivery of Filing: </strong>Landlord-Tenant, Order of Possession, and Civil Complaints are currently filed by mail (or in person), which causes delays and inaccuracies in filing. CourtZip enables you to file immediately online, eliminating delays and errors. Each Court has its own CourtZip dashboard where its staff and assigned Magisterial District Judge can access the filing and can set-up a hearing date.</li>
                            <li><strong>Easier Payment</strong>: CourtZip accepts debit or credit cards to make it easy for anyone to pay for the filing fee.</li>
                        </ul>
                        <p>CourtZip is constantly growing -- click here to see where CourtZip is live:&nbsp;<u><a href="https://courtzip.com/where-does-this-work" data-saferedirecturl="https://www.google.com/url?q=https://courtzip.com/where-does-this-work&amp;source=gmail&amp;ust=1552678182363000&amp;usg=AFQjCNEzs8xrrhkzKG6qWWWw7u29nIRo9g">Locations</a></u></p>
                        <p>To get more info --- contact CourtZip here:&nbsp;<u><a href="mailto:Info@courtzip.com">Info@courtzip.com</a></u></p>
                        <p style="font-size:10px; text-align:center;">DISCLAIMER:  This website is created and maintained<br> by CourtZip, a private entity, and is not owned, operated, or maintained by the Commonwealth of Pennsylvania or the Administrative Office of Pennsylvania Courts.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection