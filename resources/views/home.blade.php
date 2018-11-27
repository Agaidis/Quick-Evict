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
                        <table class="table table-hover table-responsive-lg table-bordered eviction_table" id="eviction_table">
                            <thead>
                            <tr>
                                <th>Eviction Id</th>
                                <th>Property Address</th>
                                <th>Owner Name</th>
                                <th>Tenant Name</th>
                                <th>Status</th>
                                <th>LTC Total Judgement</th>
                                <th>Court Filing Fee</th>
                                <th>Completion Date</th>
                                <th>Download PDF</th>
                                <th class="text-center">Remove</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($evictions as $eviction)
                                <?php $propertyAddressArray = explode('-1', $eviction->property_address);?>
                                <tr>
                                    <td>{{$eviction->id}}</td>
                                    <td>{{$propertyAddressArray[0]}} <br> {{str_replace('United States', '', $propertyAddressArray[1])}}</td>
                                    <td>{{$eviction->owner_name}}</td>
                                    <td>{{$eviction->tenant_name}}</td>
                                    <td>{{$eviction->status}}</td>
                                    <td>{{$eviction->total_judgement}}</td>
                                    <td>{{$eviction->court_filing_fee}}</td>
                                    <td>{{$eviction->created_at}}</td>
                                    <td><button type="button" class="pdf_download_btn_dashboard">{{$eviction->pdf_download}}</button></td>
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
