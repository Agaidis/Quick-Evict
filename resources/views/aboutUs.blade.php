<title>About Us</title>
@extends('layouts.app')
@section('content')
<meta name="csrf-token" id="token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
           <div class="card">
                <div class="card-header"><h2>About Us</h2></div>
                <div class="card-body">
                    <div class="row">
                        <div>Civil and Landlord-Tenant Complaints can be incredibly frustrating, time-consuming, and confusing. CourtZip was created and is run by a team that has a deep background in technology, engineering, and legal process. We understood how difficult the civil process can be at the Magisterial District Court level. We heard complaints of civil filings not filed properly, rejected by judges for manual errors in the process, or just was too complicated to understand. We took our engineering background and built a better solution -- to make the process easier for everyone involved.</div>

                        <div>We hope you find CourtZip helpful. If you have an idea for how to make it better, or are interested in partnering with CourtZip, please email&nbsp;<u><a href="mailto:info@courtzip.com">info@courtzip.com</a></u>.</div>

                        <div>CourtZip has a long way to go to achieve its long-term ambitions, and we hope you can be a part of the process as we set-off to use technology to make the District Court filing process simpler, straight-forward, and quicker for everyone involved.</div>

                        <div>Thanks for your interest in CourtZip!</div>
                        <div>- CourtZip Team</div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection