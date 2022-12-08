@extends('layouts.layouts')
@section('title', 'Wallet List')
@section('parentPageTitle', 'Wallet')

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

                    <form class="form-horizontal" method="POST" action="{{ route('wallet') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>
                                    <input style="margin-right: 20px" type="text" name="user_name"
                                           placeholder="Search for username"
                                           class="form-control @error('user_name') is-invalid @enderror">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-briefcase"></i> </span>
                                    </div>
                                    <input style="margin-right: 20px" type="text" name="ref"
                                           placeholder="Search Funding Reference"
                                           class="form-control @error('ref') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-wallet"></i></span>
                                    </div>
                                    <input style="margin-right: 20px" type="text" name="amount"
                                           placeholder="Search for amount"
                                           class="form-control @error('amount') is-invalid @enderror">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-briefcase"></i> </span>
                                    </div>
                                    <input style="margin-right: 20px" type="text" name="status"
                                           placeholder="Search for Status"
                                           class="form-control @error('status') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-file"></i></span>
                                    </div>
                                    <input style="margin-right: 20px" type="text" name="medium"
                                           placeholder="Search for medium"
                                           class="form-control @error('medium') is-invalid @enderror">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar-check"></i> </span>
                                    </div>
                                    <input type="date" name="date"
                                           placeholder="Search for date e.g 2020-09-01"
                                           class="form-control @error('date') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit"
                                            style="align-self: center; align-content: center"><i
                                            class="fa fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
{{--                    <h4 class="mt-0 header-title">Wallet Table</h4>--}}
                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Username</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Medium</th>
                                <th>Reference</th>
                                <th>O. Wallet</th>
                                <th>N. Wallet</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $dat)
                                <tr>
                                    <td>{{$dat->id}}</td>
                                    <td>{{$dat->user_name}}</td>
                                    <td>&#8358;{{$dat->amount}}</td>
                                    <td>{{$dat->status}}</td>
                                    <td>{{$dat->medium}}</td>
                                    <td>{{$dat->ref}}</td>
                                    <td>&#8358;{{$dat->o_wallet}}</td>
                                    <td>&#8358;{{$dat->n_wallet}}</td>
                                    <td>{{$dat->date}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
