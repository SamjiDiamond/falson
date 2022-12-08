@extends('layouts.layouts')
@section('title', 'Users List')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-users text-gradient-success"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ number_format($t_users) ?? 'Total users' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Total Users</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body justify-content-center">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-arrow-circle-right text-gradient-danger"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ number_format($r_users) ?? 'Total Referred' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Total Referred</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-tasks text-gradient-warning"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $a_users ?? 'Total Resellers' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Total Resellers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-database text-gradient-primary"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ number_format($au_wallet+$iau_wallet) ?? 'Today Deposits' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Total Wallet</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Users Table</h4>
                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Contact No</th>
                                <th>Wallet Balance</th>
                                <th>Account Number</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        @if($user->photo)
                                            <img src="{{route('show.avatar', $user->photo)}}" alt="" class="rounded-circle thumb-sm mr-1"> {{$user->user_name}}
                                        @else
                                            <img src="img/mcd_logo.png" alt="" class="rounded-circle thumb-sm mr-1"> {{$user->user_name}}
                                        @endif
                                    </td>
                                    <td>{{$user->email }}</td>
                                    <td>{{$user->phoneno}}</td>
                                    <td>{{$user->wallet}}</td>
                                    <td>{{$user->account_number}}</td>
                                    <td><a href="profile/{{ $user->user_name }}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
