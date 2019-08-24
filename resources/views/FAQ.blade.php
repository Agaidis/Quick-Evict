<title>FAQ</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-body body_container">
                    <h2 class="titles" style="text-align:center;">FAQ</h2>
                    <div class="row" style="margin-left: 6%; margin-right: 6%;">
                        <h4>Frequently Asked Questions about CourtZip</h4>:<br><br>
                        <div>
                            <p><strong>What is CourtZip?</strong><br />CourtZip is a tech company 100% focused on making the local court filing process easier for everyone involved. CourtZip was founded by a team of judges, attorneys, investors, landlords, property managers, and tech enthusiasts who were frustrated by how complicated, time-consuming, and error prone it was to file in a Pennsylvania Magisterial District Court.</p>
                            <p><strong>What does CourtZip do?</strong><br />CourtZip's first product is to make it easier and faster to successfully file a Civil Complaint (under $12,000), Landlord-Tenant Complaint, or Order of Possession in one of Pennsylvania's Magisterial District Courts. CourtZip has a back-end database to match the address of the property to the proper Magisterial District Court. CourtZip has compiled a database for every District Court so the filing fee is automatically generated. Once the fields are filled in and the user hits the submit button -- a full civil complaint, LTC, or OOP form is completed. This form is then filed online by CourtZip.</p>
                            <p><strong>How does CourtZip know where to file?</strong><br />CourtZip uses geo-mapping to identify the proper Magisterial District Court.... pretty slick technology! CourtZip works with the Magisterial District Courts so the geo-fence changes if the Court maps change.</p>
                            <p><strong>How does CourtZip know how much the filing fee is for each Magisterial District Court? </strong>CourtZip has worked with each Court to create a master database, tied to geo data, of every filing fee. This saves users and courts time to figure out the cost of each filing, and reduces the error of filing with an incorrect fee.</p>
                            <p><strong>Where does this work? </strong>CourtZip currently works in the following counties of Pennsylvania: Lancaster, Lebanon, York, Dauphin, Cumberland, Chester. CourtZip will be rolling out new counties and states in the near future --- with a goal of eventually working in every District Court in the state.</p>
                            <p><strong>How much does it cost?</strong><br />It costs $16.99 to use CourtZip to file a complaint online.</p>
                            <p><strong>When will I find out when the hearing is?</strong><br />The Court will let you know when the hearing is typically within a week via mail AND CourtZip will let you know via email as a second form of communication.</p>
                            <p><strong>If I need help from a lawyer, can CourtZip recommend a lawyer that specializes in evictions?</strong><br />Yes, you can email CourtZip at&nbsp;<span class="m_-3593157549645946084gmail-MsoHyperlink"><a href="mailto:info@courtzip.com" target="_blank">info@courtzip.com</a></span> if you would like a recommendation for an experienced lawyer in evictions.</p>
                            <p><strong>How fast will the filing be received by the Court?</strong><br />The Court will receive the filing in its dashboard immediately. Typically a Court will create the hearing date within 1-2 business days, and generally sets a hearing date between 7 and 15 days from the date the Complaint is filed.</p>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection