@extends('layouts.layouts')
@section('title', 'Transactions List')
@section('parentPageTitle', 'Transaction')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i
                                            class="fas fa-briefcase text-gradient-success"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ number_format($tt) ?? 'Total Transactions' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Total Transactions</p>
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
                                    <div class="col-2 align-self-center"><i
                                            class="fas fa-briefcase text-gradient-danger"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ number_format($ft) ?? 'Total Today' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Transactions Today</p>
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
                                    <div class="col-2 align-self-center"><i
                                            class="fas fa-briefcase text-gradient-warning"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $st ?? 'Transactions Yesterday' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Transactions Yesterday</p>
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
                                    <div class="col-2 align-self-center"><i
                                            class="fas fa-briefcase text-gradient-primary"></i></div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ number_format($rt) ?? 'Total Reversed' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Transactions 2Days Ago</p>
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
{{--                    <h4 class="mt-0 header-title">Transactions Table</h4>--}}
{{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped mb-0">
                            <thead>
                            <tr>
{{--                                <th>id</th>--}}
                                <th>Username</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>I. Wallet</th>
                                <th>F. Wallet</th>
                                <th>I.P</th>
                                <th>Device</th>
                                <th>Server</th>
                                <th>Ref</th>
                                <th>Date</th>
                                <th>Server Response</th>
{{--                                <th>Action</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $dat)
                                <tr>
                                    <td>{{$dat->id}}</td>
                                    <td><a href="profile/{{ $dat->user_name }}" class="btn btn-sm btn-success">{{$dat->user_name}}</a>
                                    </td>
                                    <td>{{$dat->amount}}</td>
                                    <td>{{$dat->description}}</td>
                                    <td class="center">

                                        @if($dat->status=="delivered" || $dat->status=="Delivered" || $dat->status=="ORDER_RECEIVED" || $dat->status=="ORDER_COMPLETED")
                                            <span class="badge badge-success">{{$dat->status}}</span>
                                        @elseif($dat->status=="not_delivered" || $dat->status=="Not Delivered" || $dat->status=="Error" || $dat->status=="ORDER_CANCELLED" || $dat->status=="Invalid Number" || $dat->status=="Unsuccessful")
                                            <span class="badge badge-warning">{{$dat->status}}</span>
                                        @else
                                            <span class="badge badge-info">{{$dat->status}}</span>
                                        @endif

                                    </td>
                                    <td>{{$dat->i_wallet}}</td>
                                    <td>{{$dat->f_wallet}}</td>
                                    <td>{{$dat->ip_address}}</td>
                                    <td>{{$dat->device_details}}</td>
                                    <td>{{$dat->server}}</td>
                                    <td>{{$dat->ref}}</td>
                                    <td>{{$dat->date}}</td>
                                    <td>{{$dat->server_response}}</td>
{{--                                    <td><a href="profile/{{ $dat->ref }}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a></td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
{{--                        {{ $data->links() }}--}}
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
