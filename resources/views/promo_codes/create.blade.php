@extends('layouts.layouts')
@section('title', 'Reward')
@section('parentPageTitle', 'Promo Code')

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

                    <form class="form-horizontal" method="POST" action="{{ route('promo_codes.store') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Plan Name</span>
                                    </div>
                                    <input type="hidden" name="id" class="form-control" value="{{$data->id}}">
                                    <input type="text" name="plan" placeholder="Enter plan name" class="form-control"
                                           value="{{$data->plan}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Setup Fee</span>
                                    </div>
                                    <input type="text" name="amount" placeholder="Enter Amount" class="form-control"
                                           value="{{$data->amount}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">MTN Price </span>
                                    </div>
                                    <input type="text" name="mtn" class="form-control"
                                           placeholder="Enter Price" value="{{$data->mtn}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">GLO Price </span></div>
                                    <input type="text" name="glo" class="form-control" placeholder="Enter Price"
                                           value="{{$data->glo}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Airtel Price</span></div>
                                    <input type="text" name="airtel" class="form-control" placeholder="Enter Price"
                                           value="{{$data->airtel}}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">9Mobile Price</span></div>
                                    <input type="text" name="ninemobile" class="form-control" placeholder="Enter Price"
                                           value="{{$data->ninemobile}}">
                                </div>


                                <div class="input-group mt-2">
                                    <select class="custom-select form-control" name="status">
                                        <option value="1" selected="{{$data->status == '1'}}">Activate</option>
                                        <option value="0" selected="{{$data->status == '0'}}">Deactivate</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit"
                                            style="align-self: center; align-content: center">Update
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
@endsection
