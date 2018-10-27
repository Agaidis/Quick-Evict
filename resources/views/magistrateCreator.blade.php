<title>Create Magistrate</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h2>Create Magistrate</h2></div>
                    <div class="card-body">
                        <div class="flash-message">
                            @if(Session::has('alert-success'))
                                <p class="alert alert-success">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                            @endif
                        </div> <!-- end .flash-message -->
                        <table class="table table-hover table-responsive-lg table-bordered magistrate_table" id="magistrate_table">
                                    <thead>
                                    <tr>
                                        <th>Magistrate Unique Id</th>
                                        <th>Court Id</th>
                                        <th>County</th>
                                        <th>MDJ Name</th>
                                        <th>Phone #</th>
                                        <th>(1) Under 2k</th>
                                        <th>(1) Btn 2k - 4k</th>
                                        <th>(1) Over 4k</th>
                                        <th>(1) OOP</th>
                                        <th>(2) Under 2k</th>
                                        <th>(2) Btn 2k - 4k</th>
                                        <th>(2) Over 4k</th>
                                        <th>(2) OOP</th>
                                        <th class="text-center">Edit</th>
                                        <th class="text-center">Remove</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($courtDetails as $courtDetail)
                                    <tr>
                                        <td>{{$courtDetail->magistrate_id}}</td>
                                        <td>{{$courtDetail->court_number}}</td>
                                        <td>{{$courtDetail->county}}</td>
                                        <td>{{$courtDetail->mdj_name}}</td>
                                        <td>{{$courtDetail->phone_number}}</td>
                                        <td>{{$courtDetail->one_defendant_up_to_2000}}</td>
                                        <td>{{$courtDetail->one_defendant_between_2001_4000}}</td>
                                        <td>{{$courtDetail->one_defendant_greater_than_4000}}</td>
                                        <td>{{$courtDetail->one_defendant_out_of_pocket}}</td>
                                        <td>{{$courtDetail->two_defendant_up_to_2000}}</td>
                                        <td>{{$courtDetail->two_defendant_between_2001_4000}}</td>
                                        <td>{{$courtDetail->two_defendant_greater_than_4000}}</td>
                                        <td>{{$courtDetail->two_defendant_out_of_pocket}}</td>
                                        <td class="text-center"><button type="button" data-target="#modal_edit_magistrate" data-toggle="modal" id="id_{{$courtDetail->id}}_{{$courtDetail->magistrate_id}}" class=" magistrate-edit">Edit</button></td>
                                        <td class="text-center"><button type="button" id="id_{{$courtDetail->id}}_{{$courtDetail->court_number}}" class="text-danger magistrate-remove">Delete</button></td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                        <div class="flash-message">
                            @if(Session::has('alert-success'))
                                <p class="alert alert-success">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                            @endif
                        </div> <!-- end .flash-message -->
                        <form id="magistrate_form">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                <div class="col-md-10">
                                    <h4 class="major_labels">Court Information</h4>
                                    <div class="court_information_container">
                                        <div class="col-md-6">
                                            <label for="court_id">Court Id:</label>
                                            <input placeholder="02-1-01" type="text" class="form-control" id="court_id" name="court_id" value="" />
                                        </div>
                                        <div class="col-md-6">
                                            <label for="magistrate_id">Unique Magistrate Id:</label>
                                            <input placeholder="02-1-01-1" type="text" class="form-control" id="magistrate_id" name="magistrate_id" value="" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="county">County:</label>
                                            <input placeholder="Lancaster" type="text" class="form-control" id="county" name="county" value="" />
                                        </div>
                                        <div class="col-sm-8">
                                            <label for="mdj_name">MDJ Name</label>
                                            <input placeholder="Tom Pietro" type="text" class="form-control" id="mdj_name" name="mdj_name" value="" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="mdj_name">Court Phone Number</label>
                                            <input placeholder="(000)-000-0000" type="text" class="form-control" id="court_number" name="court_number" value="" />
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="major_labels">Court Address</h4>
                                        <div class="two_defendant_container">
                                            <div class="col-sm-8">
                                                <label for="court_address_line_1">Court Mailing Address 1</label>
                                                <input placeholder="123 Muholland Drive," type="text" class="form-control" id="court_address_one" name="court_address_one" value="" />
                                            </div>
                                            <div class="col-sm-8">
                                                <label for="court_address_line_2">Court Mailing Address 2</label>
                                                <input placeholder="Lancaster PA 17349" type="text" class="form-control" id="court_address_two" name="court_address_two" value="" />
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="major_labels">One Defendant Amts</h4>
                                <div class="one_defendant_container">
                                        <div class="col-sm-12">
                                            <label for="one_under_2000">Under 2,000</label>
                                            <input placeholder="$" type="text" class="form-control" id="one_under_2000" name="one_under_2000" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="one_btn_2000_4001">Between 2,001 and 4,000</label>
                                            <input placeholder="$" type="text" class="form-control" id="one_btn_2000_4001" name="one_btn_2000_4001" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="one_over_4000">Over 4,000</label>
                                            <input placeholder="$" type="text" class="form-control" id="one_over_4000" name="one_over_4000" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="one_oop">OOP</label>
                                            <input placeholder="$" type="text" class="form-control" id="one_oop" name="one_oop" value="" />
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-md-6">
                                        <h4 class="major_labels">Two Defendant Amts</h4>
                                    <div class="two_defendant_container">
                                        <div class="col-sm-12">
                                            <label for="two_under_2000">Under 2,000</label>
                                            <input placeholder="$" type="text" class="form-control" id="two_under_2000" name="two_under_2000" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="two_btn_2000_4001">Between 2,001 and 4,000</label>
                                            <input placeholder="$" type="text" class="form-control" id="two_btn_2000_4001" name="two_btn_2000_4001" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="two_over_4000">Over 4,000</label>
                                            <input placeholder="$" type="text" class="form-control" id="two_over_4000" name="two_over_4000" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="two_oop">OOP</label>
                                            <input placeholder="$" type="text" class="form-control" id="two_oop" name="two_oop" value="" /></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="major_labels">Geo Locations</h4>
                                        <div class="geo_locations">
                                            <div class="col-sm-12">
                                                <textarea placeholder="{lng: -76.104555, lat: 39.917556},{lng: -76.103088, lat: 39.9189}," type="text" class="form-control" id="geo_locations" name="geo_locations"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <button class="btn btn-primary" id="submit_magistrate" type="button">Submit Magistrate</button>
                            </div>

                        </div>
                        </form>
                        <div class="modal fade" id="modal_edit_magistrate">
                            <div class="modal-dialog" role="document">
                                <div class="edit_magistrate_modal modal-content">
                                    <div class="modal-header">
                                        <h4 class="edit_magistrate_modal_title"></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <h4 class="major_labels">Court Information</h4>
                                                        <div class="court_information_container">
                                                            <div class="col-md-6">
                                                                <label for="court_id">Court Id:</label>
                                                                <input placeholder="02-1-01" type="text" class="form-control" id="edit_court_id" name="edit_court_id" value="" />
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="magistrate_id">Unique Magistrate Id:</label>
                                                                <input placeholder="02-1-01-1" type="text" class="form-control" id="edit_magistrate_id" name="edit_magistrate_id" value="" />
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label for="county">County:</label>
                                                                <input placeholder="Lancaster" type="text" class="form-control" id="edit_county" name="edit_county" value="" />
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <label for="mdj_name">MDJ Name</label>
                                                                <input placeholder="Tom Pietro" type="text" class="form-control" id="edit_mdj_name" name="edit_mdj_name" value="" />
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label for="mdj_name">Court Phone Number</label>
                                                                <input placeholder="(000)-000-0000" type="text" class="form-control" id="edit_court_number" name="edit_court_number" value="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h4 class="major_labels">Court Address</h4>
                                                        <div class="two_defendant_container">
                                                            <div class="col-sm-12">
                                                                <label for="court_address_line_1">Court Mailing Address 1</label>
                                                                <input placeholder="123 Muholland Drive," type="text" class="form-control" id="edit_court_address_one" name="edit_court_address_one" value="" />
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <label for="court_address_line_2">Court Mailing Address 2</label>
                                                                <input placeholder="Lancaster PA 17349" type="text" class="form-control" id="edit_court_address_two" name="edit_court_address_two" value="" />
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h4 class="major_labels">One Defendant Amts</h4>
                                                        <div class="one_defendant_container">
                                                            <div class="col-sm-12">
                                                                <label for="one_under_2000">Under 2,000</label>
                                                                <input placeholder="$" type="text" class="form-control" id="edit_one_under_2000" name="edit_one_under_2000" value="" />
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <label for="one_btn_2000_4001">Between 2,001 and 4,000</label>
                                                                <input placeholder="$" type="text" class="form-control" id="edit_one_btn_2000_4001" name="edit_one_btn_2000_4001" value="" />
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <label for="one_over_4000">Over 4,000</label>
                                                                <input placeholder="$" type="text" class="form-control" id="edit_one_over_4000" name="edit_one_over_4000" value="" />
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <label for="one_oop">OOP</label>
                                                                <input placeholder="$" type="text" class="form-control" id="edit_one_oop" name="edit_one_oop" value="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h4 class="major_labels">Two Defendant Amts</h4>
                                                        <div class="two_defendant_container">
                                                            <div class="col-sm-12">
                                                                <label for="two_under_2000">Under 2,000</label>
                                                                <input placeholder="$" type="text" class="form-control" id="edit_two_under_2000" name="edit_two_under_2000" value="" />
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <label for="two_btn_2000_4001">Between 2,001 and 4,000</label>
                                                                <input placeholder="$" type="text" class="form-control" id="edit_two_btn_2000_4001" name="edit_two_btn_2000_4001" value="" />
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <label for="two_over_4000">Over 4,000</label>
                                                                <input placeholder="$" type="text" class="form-control" id="edit_two_over_4000" name="edit_two_over_4000" value="" />
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <label for="two_oop">OOP</label>
                                                                <input placeholder="$" type="text" class="form-control" id="edit_two_oop" name="edit_two_oop" value="" /></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h4 class="major_labels">Geo Locations</h4>
                                                        <div class="geo_locations">
                                                            <div class="col-sm-12">
                                                                <textarea placeholder="{lng: -76.104555, lat: 39.917556},{lng: -76.103088, lat: 39.9189}," type="text" class="form-control" id="edit_geo_locations" name="edit_geo_locations"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="db_geo_id" name="db_geo_id" />
                                                <input type="hidden" id="db_court_id" name="db_court_id" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="submit_edit" class="approve-btn btn btn-success" data-dismiss="modal">Make Changes</button>
                                        <button type="button" id="cancel_edit" class="approve-btn btn btn-primary" data-dismiss="modal" >Cancel</button>
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