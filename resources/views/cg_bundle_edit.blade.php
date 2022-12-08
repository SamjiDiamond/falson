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

                    <form class="form-horizontal" method="POST" action="{{ route('cgbundle.update') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Display Name </span></div>
                                    <input type="hidden" name="id" class="form-control" placeholder="id" value="{{$data->id}}" required>
                                    <input type="text" name="display_name" class="form-control" placeholder="Enter Name" value="{{$data->display_name}}" required>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Network </span></div>
                                    <select name="network" data-placeholder="Choose type..." class="custom-select form-control" tabindex="2" required>
                                        <option {{$data->network == "MTN" ?'Selected':''}}>MTN</option>
                                        <option {{$data->network == "GLO" ?'Selected':''}}>GLO</option>
                                        <option {{$data->network == "9MOBILE" ?'Selected':''}}>9MOBILE</option>
                                        <option {{$data->network == "9MOBILE" ?'Selected':''}}>AIRTEL</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Type </span></div>
                                    <select name="type" data-placeholder="Choose type..." class="custom-select form-control" tabindex="2" required>
                                        <option {{$data->type == "CG" ?'Selected':''}}>CG</option>
                                        <option {{$data->type == "SME" ?'Selected':''}}>SME</option>
                                        <option {{$data->type == "DG" ?'Selected':''}}>DG</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Value (in GB) </span></div>
                                    <input type="text" name="value" placeholder="Enter Value" class="form-control input-lg m-b" value="{{$data->value}}" required>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Price </span></div>
                                    <input type="text" name="price" placeholder="Enter price" class="form-control input-lg m-b" value="{{$data->price}}" required>
                                </div>


                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">Update Bundle</button>
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
