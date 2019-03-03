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
                        <div>Evictions are one of the most frustrating, time-consuming, and confusing things that a real estate investor or landlord needs to do.&nbsp; EvictionTech is founded by a team that manages over 5,000 rental units and owns over 300 individual rental units, but also has a deep background in technology and engineering.&nbsp; We understood how difficult the eviction process can be for landlords and owners by living it everyday.&nbsp; &nbsp;We were frustrated when, despite our size, we continually had evictions not filed properly, rejected by judges for manual errors in the filing process, or took too long due to the manual process of filing.&nbsp; We took our engineering background and built out a better solution -- so investors can spend more time building real estate and less time dealing with evictions.&nbsp;&nbsp;</div>
                        <div>&nbsp;</div>
                        <div>We hope you find EvictionTech to be helpful.&nbsp; If you have an idea for how to make it better, or are interested in partnering with EvictionTech, please email&nbsp;<a href="mailto:info@evictiontech.com" target="_blank">info@evictiontech.com</a>.&nbsp;&nbsp;</div>
                        <div>&nbsp;</div>
                        <div>EvictionTech has a long way to go to achieve it's long-term goals, and we hope you can be a part of the process as we set-off to use technology to make the eviction process simpler, straight-forward, and quicker for everyone involved.&nbsp;</div>
                        <div>
                            <div>&nbsp;</div>
                            <div>Thanks for your interest in EvictionTech!</div>
                            <div>- EvictionTech Founding Team</div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection