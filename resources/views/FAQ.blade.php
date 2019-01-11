<title>FAQ</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-header"><h2>FAQ</h2></div>
                <div class="card-body">
                    <div class="row" style="margin-left: 6%; margin-right: 6%;">
                        <p><h4>Frequently Asked Questions about EvictionTech</h4>:</p><br>

                        <p><b>What is EvictionTech?</b><br>
                        EvictionTech is a tech company 100% focused on making the eviction process easier for everyone involved including owners, landlords, and magistrates.  EvictionTech was founded by a team of investors, landlords, property managers, and tech enthusiasts who were frustrated by how complicated, time-consuming, and error prone it is to file an eviction.</p>

                        <p><b>What does EvictionTech do?</b><br>
                        EvictionTech's first product is to make it easier and faster to successfully file a Landlord Tenant Complaint.  The owner / landlord / property manager can fill in some basic fields on the OnlineEviction page.  EvictionTech has a back-end database to match the address of the property to the proper magistrate.  EvictionTech has compiled a database for every magistrate so the filing fee is automatically generated.  Once the fields are filled in and the owner / property manager hits the submit button -- a full landlord - tenant complaint form is completed.  This landlord tenant complaint form is then either filed online by EvictionTech or mailed to the magistrate --- depending on the policies of the magistrate.</p>

                        <p><b>How does EvictionTech know where to file an LTC?</b><br>
                        EvictionTech uses geo-mapping to map the address to the proper magistrate.... pretty slick technology!</p>

                        <p><b>How do I know if a magistrate allows online filing?</b><br>
                        If the magistrate has online filing --- you will see a notice appear on the site after you fill in the property address.</p>

                        <p><b>Where does this work?</b><br>
                            EvictionTech currently works in the state of PA and the following counties: Lancaster, Lebanon, York, Dauphin, Cumberland, Chester.  EvictionTech will be rolling out new counties and states in the near future --- with a goal of eventually working in every magistrate in the country.</p>

                        <p><b>How much does it cost?</b><br>
                        It costs $16.99 to use EvictionTech to file a landlord tenant complaint and $3.00 for postage and handling for a total of $19.99 for each eviction.  We believe the owner will dramatically see the savings from using the technology to increase speed of filing, and have less errors on the filing.</p>

                        <p><b>When will I find out when the hearing is?</b><br>
                            The magistrate will let you know when the hearing is typically within a week.</p>

                        <p><b>If I need help from a lawyer, can EvictionTech recommend a lawyer that specializes in evictions?</b><br>
                            Yes, you can email EvictionTech at info@evictiontech.com if you would like a recommendation for an experienced lawyer in evictions.</p>
                        <p><b>How fast will the LTC be filed?</b><br>
                            The LTC will either be sent via online submission immediately OR mailed within 24 hours.</p>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection