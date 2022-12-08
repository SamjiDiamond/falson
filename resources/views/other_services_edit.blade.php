@extends('layouts.layouts')
@section('title', 'Update Service')
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

                    <form class="form-horizontal" method="POST" action="{{ route('otherservicesUpdate') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Name </span></div>
                                    <input type="hidden" name="id" class="form-control" placeholder="Enter id"  value="{{$data->id}}" required>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name"  value="{{$data->name}}" required>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Action </span></div>
                                    <input type="text" name="action" placeholder="Enter URL to navigate to or leave empty" value="{{$data->action}}" class="form-control input-lg m-b">
                                </div>

                                <div class="input-group mt-2">
                                    <select class="custom-select form-control" name="status">
                                        <option value="1" selected="{{$data->status == '1'}}">Activate</option>
                                        <option value="0" selected="{{$data->status == '0'}}">Deactivate</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">Update Service</button>
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
