@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 5%;">
    <div class="row justify-content-center">
        <div class="col-md-8" style="margin-bottom:20%;">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="county" class="col-md-4 col-form-label text-md-right">{{ __('County') }}</label>

                            <div class="col-md-6">
                                <select id="county" class="form-control{{ $errors->has('county') ? ' is-invalid' : '' }}" name="county" required>
                                    <option value="Adams">Adams</option>
                                    <option value="Allegheny">Allegheny</option>
                                    <option value="Armstrong">Armstrong</option>
                                    <option value="Beaver">Beaver</option>
                                    <option value="Bedford">Bedford</option>
                                    <option value="Berks">Berks</option>
                                    <option value="Blair">Blair</option>
                                    <option value="Bradford">Bradford</option>
                                    <option value="Bucks">Bucks</option>
                                    <option value="Butler">Butler</option>
                                    <option value="Cambria">Cambria</option>
                                    <option value="Cameron">Cameron</option>
                                    <option value="Carbon">Carbon</option>
                                    <option value="Centre">Centre</option>
                                    <option value="Chester">Chester</option>
                                    <option value="Clarion">Clarion</option>
                                    <option value="Clearfield">Clearfield</option>
                                    <option value="Clinton">Clinton</option>
                                    <option value="Columbia">Columbia</option>
                                    <option value="Crawford">Crawford</option>
                                    <option value="Cumberland">Cumberland</option>
                                    <option value="Dauphin">Dauphin</option>
                                    <option value="Delaware">Delaware</option>
                                    <option value="Elk">Elk</option>
                                    <option value="Erie">Erie</option>
                                    <option value="Fayette">Fayette</option>
                                    <option value="Forest">Forest</option>
                                    <option value="Franklin">Franklin</option>
                                    <option value="Fulton">Fulton</option>
                                    <option value="Greene">Greene</option>
                                    <option value="Huntingdon">Huntingdon</option>
                                    <option value="Indiana">Indiana</option>
                                    <option value="Jefferson">Jefferson</option>
                                    <option value="Juniata">Juniata</option>
                                    <option value="Lackawanna">Lackawanna</option>
                                    <option value="Lancaster">Lancaster</option>
                                    <option value="Lawrence">Lawrence</option>
                                    <option value="Lebanon">Lebanon</option>
                                    <option value="Lehigh">Lehigh</option>
                                    <option value="Luzerne">Luzerne</option>
                                    <option value="Lycoming">Lycoming</option>
                                    <option value="McKean">McKean</option>
                                    <option value="Mercer">Mercer</option>
                                    <option value="Mifflin">Mifflin</option>
                                    <option value="Monroe">Monroe</option>
                                    <option value="Montgomery">Montgomery</option>
                                    <option value="Montour">Montour</option>
                                    <option value="Northampton">Northampton</option>
                                    <option value="Northumberland">Northumberland</option>
                                    <option value="Perry">Perry</option>
                                    <option value="Philadelphia">Philadelphia</option>
                                    <option value="Pike">Pike</option>
                                    <option value="Potter">Potter</option>
                                    <option value="Schuylkill">Schuylkill</option>
                                    <option value="Snyder">Snyder</option>
                                    <option value="Somerset">Somerset</option>
                                    <option value="Sullivan">Sullivan</option>
                                    <option value="Susquehanna">Susquehanna</option>
                                    <option value="Tioga">Tioga</option>
                                    <option value="Union">Union</option>
                                    <option value="Venango">Venango</option>
                                    <option value="Warren">Warren</option>
                                    <option value="Washington">Washington</option>
                                    <option value="Wayne">Wayne</option>
                                    <option value="Westmoreland">Westmoreland</option>
                                    <option value="Wyoming">Wyoming</option>
                                    <option value="York">York</option>
                                </select>

                                @if ($errors->has('county'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('county') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
