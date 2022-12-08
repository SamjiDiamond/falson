@extends('layouts.layouts')
@section('title', 'Login Attempts')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4 font-13">The list of users login attempt.</p>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>User Name</th>
                            <th>IP Address</th>
                            <th>Device</th>
                            <th>Status</th>
                            <th>Provider</th>
                            <th>Date</th>
                            <th>City</th>
                            <th>Region</th>
                            <th>Country</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($login as $logins)
                        <tr>
                            <td> {{$logins->user_name}} </td>
                            <td> {{$logins->ip_address}} </td>
                            <td>{{$logins->device}}</td>
                            <td>{{$logins->status}}</td>
                            <td>{{$logins->provider}}</td>
                            <td>{{$logins->created_at}}</td>
                            <td>{{$logins->city}}</td>
                            <td>{{$logins->region}}</td>
                            <td>{{$logins->country}}</td>
                            <td><a href="profile/{{ $logins->user_name }}" class="btn btn-sm btn-success"><i class="fas fa-search"></i></a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $login->links() }}
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
