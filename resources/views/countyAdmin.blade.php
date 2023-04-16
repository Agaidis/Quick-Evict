<title>County Admin</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <header class="subhead text-center">
        <div class="overlay"></div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header"><h2>County Admin</h2></div>
                        <div class="card-body">
                            <div class="flash-message">
                                @if(Session::has('alert-success'))
                                    <p class="alert alert-success">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                @endif
                                @if(Session::has('alert-danger'))
                                    <p class="alert alert-danger">{{ Session::get('alert-danger') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                @endif
                            </div> <!-- end .flash-message -->
                            <table class="table table-hover table-bordered county_table offset-3 col-md-6" style="" id="county_table">
                                <thead>
                                <tr>
                                    <th class="text-center col-md-6">County</th>
                                    <th class="text-center col-md-4" style="">Allow In Person Complaint</th>
                                    <th class="text-center col-md-2">Notes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($counties as $county)
                                <tr>
                                    <td style="text-align:center">{{$county->county}}</td>
                                    <td style="text-align:center">
                                    @if ($county->is_allowed_in_person_complaint === 1)
                                        <input checked type="checkbox" id="in_person_complaint_toggle_{{$county->county}}" class="in_person_complaint_toggle"/>
                                    @else
                                        <input type="checkbox" id="in_person_complaint_toggle_{{$county->county}}" class="in_person_complaint_toggle"/>
                                    @endif
                                    </td>
                                    <td style="text-align:center;"><button type="button" class="btn" id="add_note_btn_{{$county->county}}" data-toggle="modal" data-target="#notesModal">
                                            <i class="fas fa-sticky-note"></i>
                                        </button></td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add/Remove Notes</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="offset-1 col-md-10">
                                    <label style="text-align: left!important" for="new_note">New Note: </label><br>
                                    <textarea id="new_note"></textarea>
                                    <input type="hidden" id="county"/>
                                </div>

                                <div class="offset-1 col-md-10">
                                    <label style="text-align: left!important" for="current_notes">Current Notes: </label><br>
                                    <div id="current_notes"></div>

                                </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="add_note">Add Note</button>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </header>
@endsection