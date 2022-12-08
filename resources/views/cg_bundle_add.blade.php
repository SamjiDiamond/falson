@extends('layouts.layouts')
@section('title', 'Create CG Bundle')
@section('parentPageTitle', 'CG Bundle')

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

                    <form class="form-horizontal" method="POST" action="{{ route('cgbundle.create') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Display Name </span></div>
                                    <input type="text" name="display_name" class="form-control" placeholder="Enter Name" required>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Network </span></div>
                                    <select name="network" data-placeholder="Choose type..." class="custom-select form-control" tabindex="2" required>
                                        <option>MTN</option>
                                        <option>GLO</option>
                                        <option>9MOBILE</option>
                                        <option>AIRTEL</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Type </span></div>
                                    <select name="type" data-placeholder="Choose type..." class="custom-select form-control" tabindex="2" required>
                                        <option>CG</option>
                                        <option>SME</option>
                                        <option>DG</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Value (in GB) </span></div>
                                    <input type="text" name="value" placeholder="Enter Value" class="form-control input-lg m-b" required>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Price </span></div>
                                    <input type="text" name="price" placeholder="Enter price" class="form-control input-lg m-b" required>
                                </div>


                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">Create Bundle</button>
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
