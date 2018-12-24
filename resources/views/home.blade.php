@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Home</div>
                    <div class="card-body">
                        <h2 style="align-content:center">Welcome to EvictionTech!!</h2>
                        <div>EvictionTech is the leader in using technology and eviction expertise to improve the eviction process for real estate owners, property managers, and magistrate district courts.</div>

                        <div>Prior to EvictionTech, the eviction process was dramatically outdated. Filing evictions caused a lot of extra manual work and delays for everyone involved. EvictionTech brought together a collaboration between cutting edge MDJs, property managers, real estate owners, and web developers to create a better solution.</div>

                        <div>EvictionTech solves the following problems:</div>
                        <div>
                            <ul>
                                <li><strong>Geo-Coded Magistrates:</strong> Evictions must be filed in the correct magistrate, which is often confusing. EvictionTech has geo-coded each rental address into the specific magistrate to be filed. The owner or property manager just types in the address and EvictionTech matches the address to the proper magistrate --- never have to call the court again or file in the wrong place!</li>
                            </ul>
                            <ul>
                                <li><strong>Filing Fees:</strong> Each magistrate has different filing fees based on a # of different variables. EvictionTech built a database of every fee for each magistrate so property managers and owners never have to call a court again to know the fee. Just type in the basic filing info and EvictionTech pulls the specific filing fee for you, saving time for everyone!</li>
                            </ul>
                            <ul>
                                <li><strong>Landlord-Tenant Complaint:</strong> EvictionTech makes the Landlord-Tenant complaint easier to fill out correctly by walking you through the process with easy to understand fields to fill out. Once filled in, a full landlord-tenant complaint is accurately completed by EvictionTech in the proper form needed by the state of Pennsylvania. This results in less errors in incorrect Landlord-Tenant complaint forms and never have a mistake from mis-reading handwriting again.</li>
                            </ul>
                            <ul>
                                <li><strong>Online Filing</strong>: Evictions are currently filed via the mail, which causes huge delays in filing. EvictionTech enables a property manager or owner to file immediately online -- dramatically decreasing the eviction delay. Each magistrate has their own EvictionTech dashboard where they see the eviction filed the same day it was submitted and set-up a court date immediately. Online filing also decreases the delays from incorrect filings via mail being sent back and forth.</li>
                            </ul>
                            <ul>
                                <li><strong>Easy Online Payment:</strong> EvictionTech accepts debit or credit cards to make it easy for owners and property managers to pay for the eviction filing online.</li>
                            </ul>

                            <div>To file an eviction, click here: <a href="https://evictiontech.com/online-eviction" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://evictiontech.com/online-eviction&amp;source=gmail&amp;ust=1545744067199000&amp;usg=AFQjCNEgtHwzpaaZGyqtrqUmbeUQmCKvAQ">https://evictiontech.com/<wbr />online-eviction</a></div>

                            <div>EvictionTech is constantly growing -- click here to see where EvictionTech is live:<a href="https://evictiontech.com/where-does-this-work" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://evictiontech.com/where-does-this-work&amp;source=gmail&amp;ust=1545744067199000&amp;usg=AFQjCNHKCBedkszbEYh7nBDiSoQZXACX5A">https://evictiontech.com/<wbr />where-does-this-work</a></div>

                            <div>To get more info --- contact EvictionTech here:</div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection