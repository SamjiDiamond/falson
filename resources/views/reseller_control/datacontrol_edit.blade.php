@extends('layouts.layouts')
@section('title', 'Modify Data Plan')
@section('parentPageTitle', 'Reseller Data')

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

                    <form class="form-horizontal" method="POST" action="{{ route('reseller.datacontrolUpdate') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Product Name</span>
                                    </div>
                                    <input type="hidden" name="id" class="form-control" value="{{$data->id}}">
                                    <input type="text" name="product_name" placeholder="Enter product name"
                                           class="form-control" value="{{$data->name}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Code</span></div>
                                    <input type="text" name="code" class="form-control" placeholder="Enter Code"
                                           value="{{$data->code}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Plan ID</span></div>
                                    <input type="text" name="plan_id" class="form-control" placeholder="Enter PlanID"
                                           value="{{$data->plan_id}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Category</span>
                                    </div>
                                    <input type="text" name="product_code" class="form-control"
                                           placeholder="Enter Category" value="{{$data->product_code}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Level1 Amount </span></div>
                                    <input type="text" name="level1" class="form-control" placeholder="Enter Amount"
                                           value="{{$data->level1}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Level2 Amount </span></div>
                                    <input type="text" name="level2" class="form-control" placeholder="Enter Amount"
                                           value="{{$data->level2}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Level3 Amount </span></div>
                                    <input type="number" name="level3" class="form-control" placeholder="Enter Amount"
                                           value="{{$data->level3}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Level4 Amount </span></div>
                                    <input type="text" name="level4" class="form-control" placeholder="Enter Amount"
                                           value="{{$data->level4}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Level5 Amount </span></div>
                                    <input type="text" name="level5" class="form-control" placeholder="Enter Amount"
                                           value="{{$data->level5}}">
                                </div>

                                <div class="input-group mt-2">
                                    <select class="custom-select form-control" name="server">
                                        <option value="0" {{$data->server == 0 ? "selected" : ''}}>0</option>
                                        <option value="1" {{$data->server == 1 ? "selected" : ''}}>1</option>
                                        <option value="2" {{$data->server == 2 ? "selected" : ''}}>2</option>
                                        <option value="3" {{$data->server == 3 ? "selected" : ''}}>3</option>
                                        <option value="4" {{$data->server == 4 ? "selected" : ''}}>4</option>
                                        <option value="5" {{$data->server == 5 ? "selected" : ''}}>5</option>
                                        <option value="6" {{$data->server == 6 ? "selected" : ''}}>6</option>
                                        <option value="7" {{$data->server == 7 ? "selected" : ''}}>7</option>
                                        <option value="8" {{$data->server == 8 ? "selected" : ''}}>8</option>
                                    </select>
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
