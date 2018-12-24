@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Dashboard</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="post" action="{{ action('DashboardController@downloadPDF') }}" enctype="multipart/form-data" id="dashboard_form">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
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
                                    <?php $propertyAddressArray = explode('-1', $eviction->property_address);
                                        $statusArray = array('Created LTC',
                                            'LTC Mailed',
                                            'LTC Submitted Online',
                                            'Court Hearing Scheduled',
                                            'Court Hearing Extended',
                                            'Judgement Issued in Favor of Owner',
                                            'Judgement Denied by Court',
                                            'Tenant Filed Appeal',
                                            'OOP Mailed',
                                            'OOP Submitted Online',
                                            'Paid Judgement',
                                            'Locked Out Tenant');?>
                                    <tr>
                                        <td>{{$eviction->id}}</td>
                                        <td>{{$propertyAddressArray[0]}} <br> {{str_replace('United States', '', $propertyAddressArray[1])}}</td>
                                        <td>{{$eviction->owner_name}}</td>
                                        <td>{{$eviction->tenant_name}}</td>
                                        <td style="width:150px;">
                                            <select title="status" class="form-control status_select" id="status_{{$eviction->id}}">
                                            @foreach ($statusArray as $status)
                                                @if ($status == $eviction->status)
                                                        <option value="{{$status}}" selected>{{$status}}</option>
                                                    @else
                                                        <option value="{{$status}}">{{$status}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>{{$eviction->total_judgement}}</td>
                                        <td>{{$eviction->court_filing_fee}}</td>
                                        <td>{{$eviction->created_at}}</td>
                                        <td class="text-center"><button type="submit" id="download_id_{{$eviction->id}}" class="pdf_download_btn_dashboard btn btn-success">Download</button></td>
                                        <td class="text-center"><button type="button" id="id_{{$eviction->id}}_{{$propertyAddressArray[0]}}" class="btn btn-danger eviction-remove">Delete</button></td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <input type="hidden" name="id" id="download_id"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
