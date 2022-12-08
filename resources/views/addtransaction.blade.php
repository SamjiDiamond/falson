@extends('layouts.layouts')
@section('title', 'Add Airtime Transaction')
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

                    <form class="form-horizontal" method="POST" action="{{ route('addtransaction') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">

                                <div class="input-group mt-2 @error('user_name') has-error @enderror">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="far fa-user"></i></span></div>
                                    <input type="text" name="user_name" placeholder="Enter Username" class="form-control @error('user_name') is-invalid @enderror">
                                </div>
                                @error('user_name')
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    {{ $message }}
                                </div>
                                @enderror

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-wallet"></i> </span></div>
                                    <input type="number" name="amount" class="form-control" placeholder="Enter Amount">
                                    @error('amount')
                                    <div class="alert alert-danger alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="input-group mt-2">
                                    <select class="custom-select form-control" name="network">
                                        <option>MTN</option>
                                        <option>GLO</option>
                                        <option>AIRTEL</option>
                                        <option>ETISALAT</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-mobile"></i> </span></div>
                                    <input type="tel" type="text" name="phoneno" placeholder="Enter Phone no" class="form-control input-lg m-b">
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center"><i class="fa fa-plus-circle"></i> Add Transaction</button>
                                </div>

                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
