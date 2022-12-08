@extends('layouts.layouts')
@section('title', 'Add General News')
@section('parentPageTitle', 'General News')

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

                    <form class="form-horizontal" method="POST" action="{{ route('addgnews') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="far fa-user"></i></span></div>
                                    <input type="text" name="user_name" placeholder="Enter Username (Optional)" class="form-control @error('username') is-invalid @enderror">
                                </div>
                                @error('user_name')
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    {{ $message }}
                                </div>
                                @enderror

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Message</span></div>
                                    <textarea name="message" class="form-control" aria-label="With textarea"></textarea>
                                </div>


                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Banner </span></div>
                                    <input type="file" name="image" placeholder="Select Image" class="form-control input-lg m-b" required>
                                </div>


                                <div class="form-group row mt-2">
                                    <div class="col-12">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="push_notification" id="remember"> <label class="custom-control-label" for="remember">Send Push Notification</label></div>
                                    </div>
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-primary btn-large" type="submit" style="align-self: center; align-content: center">Broadcast</button>
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
