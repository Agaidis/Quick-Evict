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
                        <div>Local civil and eviction filings can be incredibly frustrating, time-consuming, and confusing. CourtZip was created and is run by a team that has a deep background in technology, engineering, and legal process. We understood how difficult the civil and eviction process can be at a local level. We heard complaints of civil filings not filed properly, rejected by judges for manual errors in the process, or just was too complicated to understand. We took our engineering background and built out a better solution -- to make the process easier to everyone involved.</div><br /> <br />

                        <div>We hope you find CourtZip to be helpful. If you have an idea for how to make it better, or are interested in partnering with CourtZip , please email <A HREF="mailto:info@courtzip.com">info@courtzip.com</A>.</div> <br /> <br />

                        <div>CourtZip has a long way to go to achieve it's long-term ambitions, and we hope you can be a part of the process as we set-off to use technology to make the local court filing process simpler, straight-forward, and quicker for everyone involved.</div> <br /><br />

                        <div>Thanks for your interest in CourtZip!</div>

                        <br /><div>- CourtZip Team</div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection