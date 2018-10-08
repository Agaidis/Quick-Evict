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
                        <div class="row">
                            <div class="col-sm-10">
                            <form>
                                <div class="col-md-6">
                                    <h4 class="major_labels">Court Information</h4>
                                    <div class="court_information_container">
                                        <div class="col-md-4">
                                            <label for="court_id">Court Id:</label>
                                            <input placeholder="Court Id" type="text" class="form-control" id="court_id" name="court_id" value="" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="county">County:</label>
                                            <input placeholder="County" type="text" class="form-control" id="county" name="county" value="" />
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <label for="mdj_name">MDJ Name</label>
                                                <input placeholder="Tom Pietro" type="text" class="form-control" id="mdj_name" name="mdj_name" value="" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label for="court_address">Court Mailing Address</label>
                                                <input placeholder="123 Muholland Drive, Lancaster PA 17349" type="text" class="form-control" id="court_address" name="court_address" value="" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label for="mdj_name">Court Phone Number</label>
                                                <input placeholder="(000)-000-0000" type="text" class="form-control" id="court_number" name="court_number" value="" />
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <h4 class="major_labels">One Defendant Amts</h4>
                                <div class="one_defendant_container">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="one_under_2000">Under 2,000</label>
                                            <input placeholder="$" type="text" class="form-control" id="one_under_2000" name="one_under_2000" value="" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="one_btn_2000_4001">Between 2,001 and 4,000</label>
                                            <input placeholder="$" type="text" class="form-control" id="one_btn_2000_4001" name="one_btn_2000_4001" value="" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="one_over_4000">Over 4,000</label>
                                            <input placeholder="$" type="text" class="form-control" id="one_over_4000" name="one_over_4000" value="" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="one_oop">OOP</label>
                                            <input placeholder="$" type="text" class="form-control" id="one_oop" name="one_oop" value="" />
                                        </div>
                                    </div>
                                </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h4 class="major_labels">Two Defendant Amts</h4>
                                    <div class="two_defendant_container">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <label for="two_under_2000">Under 2,000</label>
                                                <input placeholder="$" type="text" class="form-control" id="two_under_2000" name="two_under_2000" value="" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <label for="two_btn_2000_4001">Between 2,001 and 4,000</label>
                                                <input placeholder="$" type="text" class="form-control" id="two_btn_2000_4001" name="two_btn_2000_4001" value="" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <label for="two_over_4000">Over 4,000</label>
                                                <input placeholder="$" type="text" class="form-control" id="two_over_4000" name="two_over_4000" value="" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <label for="two_oop">OOP</label>
                                                <input placeholder="$" type="text" class="form-control" id="two_oop" name="two_oop" value="" />
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div><br><br>


                                <input class="btn btn-primary" type="submit" value="Submit Magistrate" name="submit_btn"/>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection