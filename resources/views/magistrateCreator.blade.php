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
                        <table class="table table-hover table-responsive-lg table-bordered magistrate_table">
                                    <thead>
                                    <tr>
                                        <th>Magistrate #</th>
                                        <th>County</th>
                                        <th>MDJ Name</th>
                                        <th>Address</th>
                                        <th>Phone #</th>
                                        <th>(1) Under 2k</th>
                                        <th>(1) Btn 2k - 4k</th>
                                        <th>(1) Over 4k</th>
                                        <th>(1) OOP</th>
                                        <th>(2) Under 2k</th>
                                        <th>(2) Btn 2k - 4k</th>
                                        <th>(2) Over 4k</th>
                                        <th>(2) OOP</th>
                                        <th class="text-center">Remove</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($courtDetails as $courtDetail)
                                    <tr>
                                        <td>{{$courtDetail->court_number}}</td>
                                        <td>{{$courtDetail->county}}</td>
                                        <td>{{$courtDetail->mdj_name}}</td>
                                        <td>{{$courtDetail->mailing_address}}</td>
                                        <td>{{$courtDetail->phone_number}}</td>
                                        <td>{{$courtDetail->one_defendant_up_to_2000}}</td>
                                        <td>{{$courtDetail->one_defendant_between_2001_4000}}</td>
                                        <td>{{$courtDetail->one_defendant_greater_than_4000}}</td>
                                        <td>{{$courtDetail->one_defendant_out_of_pocket}}</td>
                                        <td>{{$courtDetail->two_defendant_up_to_2000}}</td>
                                        <td>{{$courtDetail->two_defendant_between_2001_4000}}</td>
                                        <td>{{$courtDetail->two_defendant_greater_than_4000}}</td>
                                        <td>{{$courtDetail->two_defendant_out_of_pocket}}</td>
                                        <td class="text-center"><a href="javascript:void(0)" id="id_{{$courtDetail->id}}_{{$courtDetail->court_number}}" class="text-danger magistrate-remove"><span style="border: 1px solid grey; border-radius:10em;" class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span><b>X</b></a></td>
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
                                            <input placeholder="Court Id" type="text" class="form-control" id="court_id" name="court_id" value="" />
                                        </div>
                                        <div class="col-md-6">
                                            <label for="magistrate_id">Unique Magistrate Id:</label>
                                            <input placeholder="Magistrate Id" type="text" class="form-control" id="magistrate_id" name="magistrate_id" value="" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="county">County:</label>
                                            <input placeholder="County" type="text" class="form-control" id="county" name="county" value="" />
                                        </div>
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
                                </div><br>
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
                                                <label for="geo_locations">Geo Locations</label>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection