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
                            <a href="{{ url('new-ltc') }}"><button type="button" class="btn btn-primary home_btns" id="ltc_btn">LTC</button></a>
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
                        <div class="home_text">CourtZip is the leader in combining technology and court filing expertise to improve the process for filing complaints at local courts and magistrates. CourtZip makes it easy to file local complaints including civil complaints under $10,000, landlord-tenant complaints (for evictions), and order of possession (for possession of property from eviction).</div>
                        <br />
                        <div class="home_text">Prior to CourtZip, the filing process was dramatically outdated. Filing at the local court caused a lot of extra manual work and delays for everyone involved. CourtZip brought together a collaboration between cutting-edge judges, government officials, property managers, real estate owners, and web developers to create a better solution.</div>
                        <br />
                        <div>CourtZip solves the following problems:</div><br />
                        <div>
                            <ul>
                                <li><strong>Geo-Coded Local Court Zones</strong>: Local civil complaints must be filed in the correct court, which is often confusing. CourtZip has geo-coded each address into the specific court to be filed. The owner or property manager just types in the address and CourtZip matches the address to the proper court --- never have to call the court again or file in the wrong place!<br /></li>
                                <li><strong>Filing Fees</strong>: Each court has different filing fees based on a # of different variables. CourtZip built a database of every fee for each court so you never have to call a court again to know the fee. Just type in the basic filing info and CourtZip pulls the specific filing fee for you, saving time for everyone!<br /></li>
                                <li><strong>Civil Complaint</strong>: Whether you are filing one civil complaint or file hundreds each month, CourtZip can make it easier and faster for you to file.<br /></li>
                                <li><strong>Landlord-Tenant Complaint</strong>: CourtZip makes the Landlord-Tenant complaint easier to fill out correctly by walking you through the process with easy to understand fields to fill out. Once filled in, a full landlord-tenant complaint is accurately completed by CourtZip in the proper form needed by the state of Pennsylvania. This results in less errors in incorrect Civil, Landlord-Tenant, and Order of Possession complaint forms. Never have a mistake from mis-read handwriting again!<br /></li>
                                <li><strong>Online Filing</strong>: Evictions and local civil complaints are currently filed via the mail, which causes huge delays in filing. CourtZip enables you to file immediately online -- dramatically decreasing any delays. Each judge has their own CourtZip dashboard where they see the filing the same day it was submitted and set-up a court date immediately. Online filing also decreases the delays from incorrect filings via mail being sent back and forth.<br /></li>
                                <li><strong>Easy Online Payment</strong>: CourtZip accepts debit or credit cards to make it easy for anyone to pay for the filing fee online.</li>
                            </ul>

                            <div>CourtZip is constantly growing -- click here to see where CourtZip is live: <a href="https://courtzip.com/where-does-this-work" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://courtzip.com/where-does-this-work&amp;source=gmail&amp;ust=1552399282868000&amp;usg=AFQjCNF-m5XENIdqZcAXtJzQ63df9HROHg">Locations</a></div>
                            <div>To get more info --- contact CourtZip here: <a href="mailto:Info@courtzip.com" target="_blank">Info@courtzip.com</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection