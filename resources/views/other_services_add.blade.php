@extends('layouts.layouts')
@section('title', 'Add New Service')
@section('parentPageTitle', 'Other Services')

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

                    <form class="form-horizontal" method="POST" action="{{ route('otherservices_add') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Name </span></div>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name" required>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Action </span></div>
                                    <input type="text" name="action" placeholder="Enter URL to navigate to or leave empty" class="form-control input-lg m-b">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Image </span></div>
                                    <input type="file" name="image" placeholder="Select Image" class="form-control input-lg m-b" required>
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">Submit</button>
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
