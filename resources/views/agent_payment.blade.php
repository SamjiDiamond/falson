@extends('layouts.layouts')
@section('title', 'Agent Payment')
@section('parentPageTitle', 'Agent')

@section('content')

{{--    <div class="row">--}}
{{--        <div class="col-lg-12">--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-3">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="icon-contain">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-2 align-self-center"><i class="fas fa-users text-gradient-success"></i></div>--}}
{{--                                    <div class="col-10 text-right">--}}
{{--                                        <h5 class="mt-0 mb-1">{{ number_format($tt) ?? 'Total Transactions' }}</h5>--}}
{{--                                        <p class="mb-0 font-12 text-muted">Total Transactions</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-3">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body justify-content-center">--}}
{{--                            <div class="icon-contain">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-2 align-self-center"><i class="fas fa-arrow-circle-right text-gradient-danger"></i></div>--}}
{{--                                    <div class="col-10 text-right">--}}
{{--                                        <h5 class="mt-0 mb-1">{{ number_format($st) ?? 'Total Successful' }}</h5>--}}
{{--                                        <p class="mb-0 font-12 text-muted">Total Successful</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-3">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="icon-contain">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-2 align-self-center"><i class="fas fa-tasks text-gradient-warning"></i></div>--}}
{{--                                    <div class="col-10 text-right">--}}
{{--                                        <h5 class="mt-0 mb-1">{{ $ft ?? 'Total Agent' }}</h5>--}}
{{--                                        <p class="mb-0 font-12 text-muted">Total Failed</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-3">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="icon-contain">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-2 align-self-center"><i class="fas fa-database text-gradient-primary"></i></div>--}}
{{--                                    <div class="col-10 text-right">--}}
{{--                                        <h5 class="mt-0 mb-1">{{ number_format($rt) ?? 'Today Deposits' }}</h5>--}}
{{--                                        <p class="mb-0 font-12 text-muted">Total Reversed</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}


    <div class="row">
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

                        <form class="form-horizontal" method="POST" action="{{ route('agent.payment.confirmation') }}">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="far fa-user"></i></span></div>
                                        <input type="text" name="user_name" placeholder="Enter Agent Username" class="form-control @error('username') is-invalid @enderror">
                                        <button class="btn btn-gradient-primary" type="submit">Continue</button>
                                    </div>
                                    @error('user_name')
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

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        @if($alist ?? '')
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th>Wallet</th>
                                <th>Registration Date</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $data)
                                <tr>
                                    <td>{{$data->user_name}}</td>
                                    <td>{{$data->wallet}}</td>
                                    <td>{{$data->reg_date}}</td>
                                    <td>{{$data->note}}</td>
                                    <td class="center">
                                        <form method="POST" action="{{ route('agent.payment.confirmation') }}">
                                            <input type="hidden" name="user_name" value="{{$data->user_name}}">
                                            <button class="btn btn-gradient-success" type="submit">Continue</button>
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
        @if($val ?? '')
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <h4 class="mt-0 header-title">Transactions</h4>
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($trans as $data)
                                <tr>
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->description}}</td>
                                    <td>{{$data->status}}</td>
                                    <td>{{$data->amount}}</td>
                                    <td>{{$data->date}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Process Payment</h4>
                    <p class="text-muted mb-4 font-13">Confirm the right payment to be made and click on the button.</p>
                    <div class="general-label">
                        <form class="form-horizontal" method="POST" action="{{ route('agent.payment') }}">
                            @csrf
                            <input type="hidden" name="user_name" value="{{$user->user_name}}" placeholder="Enter Agent Username" class="form-control @error('username') is-invalid @enderror">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">#</span></div>
                                        <input type="number" name="count" value="{{$count}}" placeholder="Enter record count you want payment for" class="form-control @error('count') is-invalid @enderror" required>
                                        <button class="btn btn-primary" type="submit">Process Payment</button>
                                        @error('count')
                                        <div class="alert alert-danger alert-dismissable">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
    <!-- end row -->
@endsection

