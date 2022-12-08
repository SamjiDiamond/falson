@extends('layouts.layouts')
@section('title', 'Change Password')
@section('parentPageTitle', 'Settings')

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

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissable">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    <form class="form-horizontal" method="POST" action="{{ route('change_password') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Current Password</span></div>
                                    <input type="text" name="current_password" placeholder="Enter Current Password" class="form-control">
                                </div>


                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">New Password</span></div>
                                    <input type="text" name="new_password" placeholder="Enter New Password" class="form-control">
                                </div>


                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Confirm Password</span></div>
                                    <input type="text" name="confirm_password" placeholder="Confirm New Password" class="form-control">
                                </div>


                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">Update</button>
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
