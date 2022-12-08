@extends('layouts.layouts')
@section('title', 'Referral Upgrade')
@section('parentPageTitle', 'User')

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

                    <form class="form-horizontal" method="POST" action="{{ route('referral.upgrade') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2 @error('user_name') has-error @enderror">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="far fa-user"></i></span></div>
                                    <input type="text" name="user_name" placeholder="Enter Username" class="form-control @error('username') is-invalid @enderror">
                                </div>
                                @error('user_name')
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    {{ $message }}
                                </div>
                                @enderror

                                <div class="input-group mt-2">
                                    <select class="custom-select form-control" name="plan">
                                        <option value="larvae" selected="selected">Larvae - #3,000</option>
                                        <option value="butterfly">Butterfly - #7,000</option>
                                        <option value="butterfly">Bronze - #15,000</option>
                                        <option value="butterfly">Silver - #25,000</option>
                                        <option value="butterfly">Gold - #35,000</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center"><i class="fa fa-user-plus"></i> Upgrade User</button>
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
