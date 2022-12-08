@extends('layouts.layouts')
@section('title', 'Airtime Converter')
@section('parentPageTitle', 'Transaction')

@section('content')

    @can('airtime2cash-actions')
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

                    <form class="form-horizontal" method="POST" action="{{ route('transaction.airtime2cash.payment') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">#</span></div>
                                    <input type="text" name="ref" placeholder="Enter Reference Number" class="form-control @error('ref') is-invalid @enderror" required>
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center"><i class="fa fa-credit-card"></i> Credit Wallet</button>
                                </div>
                                @error('ref')
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @if($alist ?? '')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Reference</th>
                                <th>Amount</th>
                                <th>Network</th>
                                <th>Phone Number</th>
                                <th>Status</th>
                                <th>Username</th>
                                <th>Receiver</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->ref}}</td>
                                    <td>{{$data->amount}}</td>
                                    <td>{{$data->network}}</td>
                                    <td>{{$data->phoneno}}</td>
                                    <td>
                                        @if($data->status=="successful")
                                            <span class="badge badge-success">{{$data->status}}</span>
                                        @else
                                            <span class="badge badge-warning">{{$data->status}}</span>
                                        @endif
                                    </td>
                                    <td>{{$data->user_name}}</td>
                                    <td>{{$data->receiver}}</td>
                                    <td>{{$data->created_at}}</td>
                                    <td>
                                    <a href="{{route('transaction.airtime2cash.success', $data->id)}}" class="btn btn-success">Mark Delivered</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $datas->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    @endif
    <!-- end row -->
@endsection
