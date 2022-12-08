@extends('layouts.layouts')
@section('title', 'Modify Setting')
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

                    <form class="form-horizontal" method="POST" action="{{ route('allsettingsUpdate') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Name</span></div>
                                    <input type="hidden" name="id" class="form-control" value="{{$data->id}}">
                                    <input type="text" name="bame" placeholder="Enter Name" class="form-control" value="{{$data->name}}" readonly>
                                </div>


                                @if($data->value == '1' || $data->value == '0')
                                    <div class="input-group mt-2">
                                        <select class="custom-select form-control" name="value">
                                            <option value="1" selected="{{$data->value == '1'}}">Activate</option>
                                            <option value="0" selected="{{$data->value == '0'}}">Deactivate</option>
                                        </select>
                                    </div>
                                @else
                                    <div class="input-group mt-2">
                                        <div class="input-group-prepend"><span class="input-group-text">Value </span></div>
                                        <input type="text" name="value" class="form-control" placeholder="Enter value" value="{{$data->value}}" required>
                                    </div>
                                @endif

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
