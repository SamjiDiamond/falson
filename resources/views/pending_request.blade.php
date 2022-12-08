@extends('layouts.layouts')
@section('title', 'Pending Request')
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
                                        <h5 class="mt-0 mb-1">{{ number_format($tp) ?? 'Total Pending' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Total Pending</p>
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
                                    <div class="col-2 align-self-center"><i class="fas fa-user text-gradient-danger"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ number_format($ap) ?? 'Total Referred' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Pending Agent</p>
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
                                    <div class="col-2 align-self-center"><i class="fas fa-user text-gradient-warning"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $rp ?? 'Total Agent' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Pending Reseller</p>
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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Business Name</th>
                            <th>Full Name</th>
                            <th>DOB</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    @if($user->photo!=null)
                                        <img alt="image" class="rounded-circle thumb-sm mr-1" src="{{route('show.avatar', $user->photo}}">
                                    @else
                                        <img alt="image" class="rounded-circle thumb-sm mr-1" src="/img/mcd_logo.png">
                                    @endif
                                    {{$user->user_name }}</td>
                                <td>{{$user->company_name }}</td>
                                <td>{{$user->full_name }}</td>
                                <td>{{$user->dob }}</td>
                                <td>{{$user->phoneno}}</td>
                                <td>{{$user->address}}</td>
                                <td>{{$user->note}}</td>
                                <td><a href="profile/{{ $user->user_name }}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
{{--                    {{ $users->links() }}--}}
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
