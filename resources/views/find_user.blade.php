@extends('layouts.layouts')
@section('title', 'Search User')
@section('parentPageTitle', 'Users')

@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="general-label">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('finduser') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>
                                    <input style="margin-right: 20px" type="text" name="user_name" placeholder="Search for username" class="form-control @error('user_name') is-invalid @enderror">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-mobile"></i> </span>
                                    </div>
                                    <input type="tel" name="phoneno" placeholder="Search for phone number" class="form-control @error('phoneno') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-users"></i> </span>
                                    </div>
                                    <input style="margin-right: 20px" type="text" name="status" placeholder="Search User group e.g agent, client, reseller" class="form-control @error('status') is-invalid @enderror">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-wallet"></i></span>
                                    </div>
                                    <input type="number" name="wallet" placeholder="Search for wallet value" class="form-control @error('wallet') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i> </span>
                                    </div>
                                    <input style="margin-right: 20px" type="email" name="email" placeholder="Search for email address" class="form-control @error('email') is-invalid @enderror">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar-check"></i> </span>
                                    </div>
                                    <input type="date" name="regdate" placeholder="Search for registration date e.g 2020-09-01" class="form-control @error('regdate') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($result ?? '')
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Search Result(s)</h4>
                        <p class="text-muted mb-4 font-13">Total Result <code>{{$count}}</code></p>
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Wallet Value</th>
                                    <th>User Group</th>
                                    <th>Reg Date</th>
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
                                        <td>{{$user->status}}</td>
                                        <td>{{$user->reg_date}}</td>
                                        <td><a href="profile/{{ $user->user_name }}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
    @endif
    <!-- end row -->
@endsection
