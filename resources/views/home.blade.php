@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h2 style="align-content:center">Welcome to EvictionTech!</h2>
                        <table class="table table-hover table-responsive-lg table-bordered magistrate_table" id="magistrate_table">
                            <thead>
                            <tr>
                                <th>Eviction Id</th>
                                <th>Status</th>
                                <th>LTC Total Judgement</th>
                                <th>Completion Date</th>
                                <th class="text-center">Remove</th>
                            </tr>
                            </thead>
                            <tbody>
                            $eviction->status = 'Created LTC';
                            $eviction->total_judgement = $totalFees;
                            $eviction->property_address = $defendanthouseNum.' '.$defendantStreetName.'-1'.$defendantTown .',' . $defendantState.' '.$defendantZipcode;
                            $eviction->owner_name = $ownerName;
                            $eviction->tenant_name = $tenantName;
                            $eviction->court_filing_fee = $filingFee;
                            $eviction->pdf_download = '';
                            @foreach ($evictions as $eviction)
                                <tr>
                                    <td>{{$eviction->id}}</td>
                                    <td>{{$eviction->status}}</td>
                                    <td>{{$eviction->total_judgement}}</td>
                                    <td>{{$eviction->created_at}}</td>
                                    <td>{{$eviction->pdf_download}}</td>
                                    <td class="text-center"><button type="button" id="id_{{$eviction->id}}" class="text-danger eviction-remove">Delete</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
