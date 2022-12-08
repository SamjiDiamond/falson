@extends('layouts.layouts')
@section('title', 'Reversal')
@section('parentPageTitle', 'Transaction')

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

                    <form class="form-horizontal" method="POST" action="{{ route('reversal.confirm') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">#</span></div>
                                    <input type="text" name="id" placeholder="Enter transaction id or reference" class="form-control @error('id') is-invalid @enderror" required>
                                    <button class="btn btn-gradient-primary waves-effect waves-light" type="submit" style="align-self: center; align-content: center"><i class="fa fa-search"></i>Lookup</button>
                                </div>
                                @error('id')
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

    @if($val ?? '')
        <div class="row">

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <p>Transaction</p>
                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Username</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->description}}</td>
                                        <td>{{$data->amount}}</td>
                                        <td>{{$data->user_name}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <p>Reversal</p>
                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Username</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rtran as $tran)
                                    <tr>
                                        <td>{{$tran->id}}</td>
                                        <td>Being reversal of {{$tran->description}}</td>
                                        <td>{{$tran->amount}}</td>
                                        <td>{{$tran->user_name}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
            <a href="/reverse-transaction/{{$data->id}}" class="btn btn-gradient-danger waves-effect waves-light" type="submit"><i class="mdi mdi-alert-outline mr-2"></i>Reverse Transaction</a>

        </div>
    @endif
    <!-- end row -->
@endsection
