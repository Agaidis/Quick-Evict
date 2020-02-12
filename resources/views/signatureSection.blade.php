@extends('civilComplaint')
@section('signature')
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
@endsection