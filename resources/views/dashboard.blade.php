@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row">
            <div id="new_filing_container" class="col-md-4">
                <div class="card">
                    <div class="card-body body_container">
                        <h3 class="titles" style="text-align:center;">Start a New Filing</h3>
                        <div class="button_panel">
                            <a href="{{ url('new-ltc') }}"><button type="button" class="btn home_btns" id="ltc_btn">Landlord-Tenant Complaint</button></a><br>
                            <a href="{{ url('new-oop') }}"><button type="button" class="btn home_btns" id="oop_btn">Order of Possession</button></a><br>
                            <a href="{{ url('new-civil-complaint') }}"><button type="button" class="btn home_btns" id="civil_complaint_btn">Civil Complaint</button></a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="notification_container" class="col-md-8">
                <div class="card">
                    <div class="card-body body_container"><h3 class="titles" style="text-align:center;">Latest Notifications</h3>
                        <table class="table table-hover table-responsive-md table-bordered eviction_table" id="notification_table">
                            <thead>
                            <tr>
                                <th style="width: 1%" class="text-center">Id</th>
                                <th style="width: 1%" class="text-center">Urgency</th>
                                <th style="width: 9%" class="text-center">Property Address</th>
                                <th style="width: 10%" class="text-center">Completion Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td class="text-center"><span class="urgency_dot" id="red_urgency"></span></td>
                                <td class="text-center">1234 Main Street</td>
                                <td class="text-center">6/25/19</td>
                            </tr>
                            <tr>
                                <td class="text-center">1</td>
                                <td class="text-center"><span class="urgency_dot" id="red_urgency"></span></td>
                                <td class="text-center">5678 Bully Street</td>
                                <td class="text-center">6/23/19</td>
                            </tr>
                            <tr>
                                <td class="text-center">1</td>
                                <td class="text-center"><span class="urgency_dot" id="yellow_urgency"></span></td>
                                <td class="text-center">1112 Fulton Ave</td>
                                <td class="text-center">6/26/19</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body body_container"><h3 class="titles" style="text-align:center;">Current Filings</h3>
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="post" action="{{ action('DashboardController@downloadPDF') }}" enctype="multipart/form-data" id="dashboard_form">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <table class="table table-hover table-responsive-md table-bordered eviction_table" id="eviction_table">
                                <thead>
                                <tr>
                                    <th style="width: 1%" class="text-center">Id</th>
                                    <th style="width: 14%" class="text-center">Property Address</th>
                                    <th style="width: 9%" class="text-center">Owner</th>
                                    <th style="width: 9%" class="text-center">Tenant</th>
                                    <th style="width: 15%" class="text-center">Status</th>
                                    <th style="width: 16%" class="text-center">Court Date</th>
                                    <th style="width: 8%" class="text-center">LTC<br> Total<br> Judgement</th>
                                    <th style="width: 10%" class="text-center">Court Filing $</th>
                                    <th style="width: 10%" class="text-center">Completion Date</th>
                                    <th style="width: 10%" class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if (isset($evictions))
                                @foreach ($evictions as $eviction)
                                    <?php $propertyAddressArray = explode('-1', $eviction->property_address);
                                        $statusArray = array('Created LTC', 'Created OOP', 'Created Civil Complaint',
                                            'LTC Mailed',
                                            'LTC Submitted Online',
                                            'Waiting on AOR',
                                            'Court Hearing Scheduled',
                                            'Court Hearing Extended',
                                            'Court Hearing Rescheduled',
                                            'Judgement Issued in Favor of Owner',
                                            'Judgement Denied by Court',
                                            'Tenant Filed Appeal',
                                            'OOP Scheduled',
                                            'OOP Mailed',
                                            'OOP Submitted Online',
                                            'Paid Judgement',
                                            'Case Withdrawn',
                                            'Locked Out Tenant');?>
                                    <tr>
                                        <td class="text-center">{{$eviction->id}}</td>
                                        <td class="text-center">{{$propertyAddressArray[0]}} <br> {{str_replace('United States', '', $propertyAddressArray[1])}}</td>
                                        <td class="text-center">{{$eviction->owner_name}}</td>
                                        <td class="text-center">{{$eviction->tenant_name}}</td>
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
                                        <td class="text-center">
                                            <span data-toggle="tooltip" data-placement="right" title="Set Court Date" class="calendar_tooltip">
                                                <span id="court_date_{{$eviction->id}}_btn" data-target="#modal_set_court_date" data-toggle="modal" class="court_calendar">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </span>
                                            </span><br>
                                            <span class="text-center" id="court_date_{{$eviction->id}}">
                                                @if ($eviction->court_date != '')
                                                    {{date('M j, Y', strtotime($eviction->court_date))}}<br>
                                                    {{date('h:i A', strtotime($eviction->court_date))}}</span>
                                                @endif
                                        </td>
                                        <td class="text-center">{{$eviction->total_judgement}}</td>
                                        <td class="text-center">{{$eviction->court_filing_fee}}</td>
                                        <td class="text-center">{{date('M j, Y', strtotime('-5 hour', strtotime($eviction->created_at)))}}<br>
                                            {{date('h:i A', strtotime('-5 hour', strtotime($eviction->created_at)))}}</td>
                                        <td class="text-center">
                                            <button type="submit" id="download_id_{{$eviction->id}}" class="pdf_download_btn_dashboard btn-sm btn-primary fas fa-cloud-download-alt"></button>
                                            <button type="button" id="id_{{$eviction->id}}_{{$propertyAddressArray[0]}}" class="fa fa-trash btn-sm btn-danger eviction-remove"></button>
                                        </td>

                                    </tr>
                                @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <input type="hidden" name="id" id="download_id"/>
                        </form>
                            <div class="modal fade" id="modal_set_court_date">
                                <div class="modal-dialog" role="document">
                                    <div class="set_court_date_modal modal-content">
                                        <div class="modal-header">
                                            <h4 class="set_court_date_title">Court Date: </h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-offset-3 col-sm-8">
                                                    <label for="court_date">Date: <input class="form-control" name="court_date" id="court_date"></label>
                                                    <label for="court_time">Time: <input class="form-control" name="court_time" id="court_time" /></label>
                                                </div>
                                            </div>
                                            <input type="hidden" id="id_court_date" name="id"/>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" id="submit_date" class="approve-btn btn btn-success" data-dismiss="modal">Set Date</button>
                                            <button type="button" id="cancel_date" class="approve-btn btn btn-primary" data-dismiss="modal" >Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
