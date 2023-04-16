@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <input type="hidden" id="user_role" value="{{ $userRole }}"/>
    <header style="padding:2%;" class="text-center">
        <div class="overlay"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-8 col-xl-7 mx-auto">
                        <form method="post" class="form-horizontal" action="{{ action('NewFileController@proceedToFileTypeWithSelectedCounty') }}" id="new_file_form">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <h3>Start a new File</h3>
                            <div class="form-row">
                                <div class="form-group col-4">
                                    <select class="form-control" id="county_select" name="county" style="padding-bottom: 5px;">
                                        <option value="none">Select the County</option>
                                        @foreach ($counties as $county)
                                            <option value="{{$county->county}}">{{$county->county}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-4">
                                    <select disabled class="form-control" id="file_type_select" name="fileType">
                                        <option value="none">Select a File Type</option>
                                        <option value="ltc">Landlord Tenant-Complaint</option>
                                        <option disabled id="ltcA" value="ltcA">Landlord-Tenant Compalint, File AND Represent Plaintiff at Hearing</option>
                                        <option value="oop">Request for Order of Possession</option>
                                        <option disabled id="oopA" value="oopA">Request for Order of Possession File AND attend lockout and complete lock change</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <button type="submit" class="btn btn-block btn-lg btn-primary">Go!</button>
                                </div>
                                <span id="file_type_error"></span>
                            </div>
                        </form>
                    </div>
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
                                    <th style="width:18%;" class="text-center">Status</th>

                                    @if (Auth::user()->role == 'PM Company Leader' || Auth::user()->role == 'Administrator')
                                        <th class="text-center">Id <br> User</th>
                                    @else
                                        <th style="width:5%;" class="text-center">Id</th>
                                    @endif
                                    <th class="text-center" style="width:55px;">MDJ</th>
                                    <th>D/L Status</th>
                                    <th class="text-center">Property Address</th>
                                    <th class="text-center" style="width:80px;">Plaintiff</th>
                                    <th class="text-center" style="width:80px;">Defendant</th>
                                    <th class="text-center">Court Date</th>
                                    <th class="text-center">Total<br> Claim<br> Amount</th>
                                    <th class="text-center">Court Filing $</th>
                                    <th class="text-center">Submission Date</th>
                                    <th class="text-center" style="width:100px;">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if (isset($evictions))

                                    <?php
                                    $ltcStatusArray = array('Created LTC',
                                        'LTC Submitted Online',
                                        'LTC, to be Mailed',
                                        'LTC Mailed',
                                        'LTC Submitted, $$ needs del',
                                        'LTC Submitted, $$ delivered',
                                        'LTC Submitted, $$ & file needs DEL',
                                        'LTC Submitted, $$ & file delivered',
                                        'Court Hearing Scheduled',
                                        'Court Hearing Extended',
                                        'Court Hearing Rescheduled',
                                        'Judgement Issued in Favor of Owner',
                                        'Judgement Denied by Court',
                                        'Tenant Filed Appeal',
                                        'Waiting for Additional documents',
                                        'Waiting for AOR',
                                        'OOP Submitted Online',
                                        'Paid Judgement',
                                        'Case Withdrawn');

                                    $oopStatusArray = array('Created OOP',
                                        'OOP Submitted Online',
                                        'OOP, to be Mailed',
                                        'OOP Mailed',
                                        'OOP Submitted, $$ needs del',
                                        'OOP Submitted, $$ delivered',
                                        'OOP Submitted, $$ & file needs DEL',
                                        'OOP Submitted, $$ & file delivered',
                                        'Waiting for Additional documents',
                                        'Waiting for AOR',
                                        'Lockout Scheduled',
                                        'Locked Out Tenant',
                                        'Paid Judgement',
                                        'Case Withdrawn');

                                    $civilStatusArray = array('Civil Filed Online',
                                        'Civil, to be Mailed',
                                        'Civil Mailed',
                                        'Civil Submitted, $$ needs del',
                                        'Civil Submitted, $$ delivered',
                                        'Civil Submitted, $$ & file needs DEL',
                                        'Civil Submitted, $$ & file delivered',
                                        'Waiting for Additional documents',
                                        'Waiting for AOR',
                                        'Civil Hearing Scheduled',
                                        'Civil Hearing Rescheduled',
                                        'Civil Hearing Extended',
                                        'Judgment Issued');

                                    ?>
                                @foreach ($evictions as $eviction)
                                    <?php $propertyAddressArray = explode('-1', $eviction->property_address); ?>
                                    <tr>
                                        <td>
                                            @if ($eviction->status == 'LTC Submitted, $$ needs del' || $eviction->status == 'LTC Submitted, $$ & file needs DEL' || $eviction->status == 'OOP Submitted, $$ needs del' || $eviction->status == 'OOP Submitted, $$ & file needs DEL' || $eviction->status == 'Civil Submitted, $$ needs del' || $eviction->status == 'Civil Submitted, $$ & file needs DEL')
                                                <select title="status" class="form-control status_select orange" id="status_{{$eviction->id}}">
                                            @elseif ($eviction->status == 'LTC, to be Mailed' || $eviction->status == 'OOP, to be Mailed' || $eviction->status == 'Civil, to be Mailed')
                                                <select title="status" class="form-control status_select yellow" id="status_{{$eviction->id}}">
                                            @else
                                                        <select title="status" class="form-control status_select" id="status_{{$eviction->id}}">
                                            @endif

                                                @if ($eviction->file_type == 'eviction')
                                                    @foreach ($ltcStatusArray as $status)
                                                        @if ($status == $eviction->status)
                                                            <option value="{{$status}}" selected>{{$status}}</option>
                                                        @else
                                                            <option value="{{$status}}">{{$status}}</option>
                                                        @endif
                                                    @endforeach
                                                @elseif ($eviction->file_type === 'civil complaint')
                                                    @foreach ($civilStatusArray as $status)
                                                        @if ($status == $eviction->status)
                                                            <option value="{{$status}}" selected>{{$status}}</option>
                                                        @else
                                                            <option value="{{$status}}">{{$status}}</option>
                                                        @endif
                                                    @endforeach
                                                @elseif ($eviction->file_type === 'oop' || $eviction->file_type === 'oopA')
                                                    @foreach ($oopStatusArray as $status)
                                                        @if ($status == $eviction->status)
                                                            <option value="{{$status}}" selected>{{$status}}</option>
                                                        @else
                                                            <option value="{{$status}}">{{$status}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif

                                            </select>
                                        </td>
                                        @if (Auth::user()->role == 'PM Company Leader' || Auth::user()->role == 'Administrator')
                                        <td class="text-center">{{$eviction->id}}-{{$eviction->is_in_person_filing}} <br> {{$eviction->name}} </td>
                                        @else
                                        @endif

                                        @if (Auth::user()->role == 'Administrator')
                                            @if (in_array($eviction->county, $notesArray))
                                                <td class="text-center">{{$eviction->court_number}} <span style="color:red;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span></td>
                                            @else
                                                <td class="text-center">{{$eviction->court_number}}</td>
                                            @endif
                                        @else
                                            <td class="text-center">{{$eviction->court_number}}</td>
                                        @endif
                                        <td class="text-center">
                                            @if ($eviction->is_downloaded == 0)
                                                <span id="download_status_{{$eviction->id}}">No</span>
                                            @else
                                                <span id="download_status_{{$eviction->id}}">Yes</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{$propertyAddressArray[0]}} <br> {{str_replace('United States', '', $propertyAddressArray[1])}}</td>
                                        <td class="text-center">{{$eviction->owner_name}}</td>
                                        <td class="text-center">{{$eviction->tenant_name}}</td>

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
                                            @if ($eviction->is_extra_files === 1)
                                                <button type="button" id="get_filings_{{$eviction->id}}" data-target="#modal_get_filings" data-toggle="modal" class="get_filings btn-sm btn-primary fas fa-cloud-download-alt"></button>
                                            @else
                                                <button type="submit" id="download_id_{{$eviction->id}}" class="pdf_download_btn_dashboard btn-sm btn-primary fas fa-cloud-download-alt"></button>
                                            @endif
                                            @if ($eviction->is_downloaded == 2)
                                                <button type="button" id="edit_{{$eviction->id}}" data-target="#modal_edit_file" data-toggle="modal" class="fa fa-edit btn-sm btn-success eviction-edit"></button>
                                            @endif

                                                @if ($userRole != 'Court')
                                                    <button type="button" id="id_{{$eviction->id}}_{{$propertyAddressArray[0]}}" class="fa fa-trash btn-sm btn-danger eviction-remove"></button>
                                                @endif
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


                        <form method="post" action="{{ action('FileStorageController@downloadFilings') }}" enctype="multipart/form-data" id="get_filing_form">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <input type="hidden" name="filing_id" id="filing_id"/>
                            <input type="hidden" name="filing_original_name" id="filing_original_name"/>
                            <input type="hidden" name="main_filing_id" id="main_filing_id" value="" />
                            <input type="hidden" name="civil_relief_filing_id" id="civil_relief_filing_id" value="" />
                            <input type="hidden" name="civil_relief_name" id="civil_relief_name" value="" />
                            <input type="hidden" name="file_type" id="file_type" value="" />
                            <input type="hidden" name="is_main_file" id="is_main_file" value="" />
                            <div class="modal fade" id="modal_get_filings">
                                <div class="modal-dialog" role="document">
                                    <div class="get_files_modal modal-content">
                                        <div class="modal-header">
                                            <h4 class="get_files_title"></h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-offset-3 col-sm-8" style="margin-left:16%;">
                                                    <div class="get_files_container">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th class="text-center">File #</th>
                                                                <th class="text-center">File Name</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="filing_body"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" id="cancel_date" class="approve-btn btn btn-primary" data-dismiss="modal" >Exit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>





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
                                            </div>
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
    </header>
@endsection
