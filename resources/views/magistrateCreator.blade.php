<title>Create Magistrate</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><h2>Create Magistrate</h2></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <form>
                                        <div class="col-sm-12">
                                            <label for="county">County:</label>
                                            <input type="text" class="form-control" id="county" name="county" value="" />
                                            <label for="court_id">Court Id:</label>
                                            <input type="text" class="form-control" id="court_id" name="court_id" value="" />
                                        </div>
                                        <div class="col-sm-12">

                                        </div>
                                        <div class="col-sm-12">
                                            <label for="one_under_2000">One Defendant under 2,000</label>
                                            <input type="text" class="form-control" id="one_under_2000" name="one_under_2000" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="two_under_2000">Two Defendant under 2,000</label>
                                            <input type="text" class="form-control" id="two_under_2000" name="two_under_2000" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="one_btn_2000_4001">One Defendant between 2,001 and 4,000</label>
                                            <input type="text" class="form-control" id="one_btn_2000_4001" name="one_btn_2000_4001" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="two_btn_2000_4001">Two Defendants between 2,001 and 4,000</label>
                                            <input type="text" class="form-control" id="two_btn_2000_4001" name="two_btn_2000_4001" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="one_over_4000">One Defendant over 4,000</label>
                                            <input type="text" class="form-control" id="one_over_4000" name="one_over_4000" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="two_over_4000">Two Defendants over 4,000</label>
                                            <input type="text" class="form-control" id="two_over_4000" name="two_over_4000" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="one_oop">One Defendant OOP</label>
                                            <input type="text" class="form-control" id="one_oop" name="one_oop" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="two_oop">Two Defendants OOP</label>
                                            <input type="text" class="form-control" id="two_oop" name="two_oop" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="mdj_name">MDJ Name</label>
                                            <input type="text" class="form-control" id="mdj_name" name="mdj_name" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="court_address">Court Mailing Address</label>
                                            <input type="text" class="form-control" id="court_address" name="court_address" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="mdj_name">Court Phone Number</label>
                                            <input type="text" class="form-control" id="court_number" name="court_number" value="" />
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="mdj_name">Latitudes</label>
                                            <textarea style="width:150%; height:10%" placeholder="Latitudes" type="text" class="form-control" id="latitudes" name="latitudes"></textarea>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="mdj_name">Longitudes</label>
                                            <textarea style="width:150%; height:10%" placeholder="Longitudes" type="text" class="form-control" id="longitudes" name="longitudes"></textarea>
                                        </div>
                                        <input class="btn btn-primary" type="submit" value="Submit Magistrate" name="submit_btn"/>
                                    </form>
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