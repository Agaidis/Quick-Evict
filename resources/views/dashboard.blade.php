@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-body body_container">
                        <h2 class="titles" style="text-align:center;">Current Filings</h2>
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="post" action="{{ action('DashboardController@downloadPDF') }}" enctype="multipart/form-data" id="dashboard_form">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <div style="overflow-x:auto;">
                            <table class="table table-hover table-bordered eviction_table" style="width:1475px;" id="eviction_table">
                                <thead>
                                <tr>
                                    <th class="text-center">Id</th>
                                    <th>Download Status</th>
                                    <th class="text-center">Property Address</th>
                                    <th class="text-center" style="width:80px;">Owner</th>
                                    <th class="text-center" style="width:80px;">Tenant</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Court Date</th>
                                    <th class="text-center">LTC<br> Total<br> Judgement</th>
                                    <th class="text-center">Court Filing $</th>
                                    <th class="text-center">Completion Date</th>
                                    <th class="text-center" style="width:100px;">Actions</th>
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
                                        <td class="text-center">
                                            @if ($eviction->is_downloaded == 0)
                                                No
                                            @else
                                                Yes
                                            @endif
                                        </td>
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
                                        <td class="text-center">{{$eviction->filing_fee}}</td>
                                        <td class="text-center">{{date('M j, Y', strtotime('-5 hour', strtotime($eviction->created_at)))}}<br>
                                            {{date('h:i A', strtotime('-5 hour', strtotime($eviction->created_at)))}}</td>
                                        <td class="text-center">
                                            <button type="submit" id="download_id_{{$eviction->id}}" class="pdf_download_btn_dashboard btn-sm btn-primary fas fa-cloud-download-alt"></button>
                                            @if ($eviction->is_downloaded == 2)
                                                <button type="button" id="edit_{{$eviction->id}}" data-target="#modal_edit_file" data-toggle="modal" class="fa fa-edit btn-sm btn-success eviction-edit"></button>
                                            @endif
                                            <button type="button" id="id_{{$eviction->id}}_{{$propertyAddressArray[0]}}" class="fa fa-trash btn-sm btn-danger eviction-remove"></button>
                                        </td>

                                    </tr>
                                @endforeach
                                    @endif
                                </tbody>
                            </table>
                            </div>
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


                        <div class="modal fade" id="modal_edit_file">
                            <div class="modal-dialog" role="document">
                                <div class="edit_file_modal modal-content">
                                    <div class="modal-header">
                                        <h4 class="edit_file_title">File # </h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-sm-8">
                                                <div class ="oop_edit_container">
                                                    <div class="col-sm-10">
                                                        <label for="unjust_damages">Judgment Amount</label>
                                                        <input type="text" class="form-control eviction_fields" id="judgment_amount" name="judgment_amount" placeholder="$" value="" maxlength="9" />
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <label for="attorney_fees">Costs in Original LT Proceeding</label>
                                                        <input type="text" class="form-control eviction_fields" id="costs_original_lt_proceeding" name="costs_original_lt_proceeding" placeholder="$" value="" maxlength="9"/>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8 tenant_num_container">
                                                    <div class="col-sm-6">
                                                        <label for="tenant_num_select" class="labels">Number of Tenants</label>
                                                        <span class="fa fa-question-circle" data-placement="right" data-toggle="tooltip" title="Select the number of tenants that are present, and put 1 name for each field that appears."></span>
                                                        <select class="form-control" id="tenant_num_select">
                                                            <option value="" selected disabled>Select # of Tenants</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                            <option value="8">8</option>
                                                            <option value="9">9</option>
                                                            <option value="10">10</option>
                                                        </select>
                                                    </div><br>
                                                    <div class="col-sm-10" id="tenant_input_container"></div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="tenant_num" name="tenant_num" />
                                                            $costThisProceeding
                                                            $docketNumber
                                                            $tenantName
                                                            $plantiffName
                                                            $plantiffPhone
                                                            $plantiffAddress1
                                                            $plantiffAddress2
                                                            $attorneyFees
                                                            $totalFees

                                            </div>
                                        <input type="hidden" id="file_id" name="id"/>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" id="submit_date" class="approve-btn btn btn-success" data-dismiss="modal">Update Filing</button>
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
