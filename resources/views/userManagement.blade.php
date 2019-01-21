<title>Manage Users</title>
@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><h2>Manage Users</h2></div>
                    <div class="card-body">
                        <div class="flash-message">
                            @if(Session::has('alert-success'))
                                <p class="alert alert-success">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                            @endif
                            @if(Session::has('alert-danger'))
                                <p class="alert alert-danger">{{ Session::get('alert-success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                            @endif
                        </div> <!-- end .flash-message -->

                        <table class="table table-hover table-responsive-md table-bordered user_table" id="user_table">
                            <thead>
                            <tr>
                                <th class="text-center">Id</th>
                                <th class="text-center">User Name</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Court Id</th>
                                <th class="text-center">Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $userRoles = array('General User',
                                    'Court',
                                    'Property Manager',
                                    'PM Company Leader',
                                    'Administrator');?>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="text-center">{{$user->id}}</td>
                                    <td class="text-center">{{$user->name}}</td>
                                    <td class="text-center">{{$user->email}}</td>
                                    <td class="text-center"><select title="status" class="form-control role_select" id="user_role_{{$user->id}}">
                                            @foreach ($userRoles as $userRole)
                                                @if ($userRole == $user->role)
                                                    <option value="{{$userRole}}" selected>{{$userRole}}</option>
                                                @else
                                                    <option value="{{$userRole}}">{{$userRole}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">{{$user->court_id}}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" id="id_{{$user->id}}_{{$user->name}}" class="fa fa-trash btn-sm btn-danger user_remove"></button>
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection