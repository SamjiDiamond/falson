@extends('layouts.layouts')
@section('title', 'Create Role')
@section('parentPageTitle', 'Roles')

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

                    <form class="form-horizontal" method="POST" action="{{ route('roles.create') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user"></i> </span></div>
                                    <input type="text" name="name" class="form-control" placeholder="Enter role name">
                                </div>

                                <div class="mb-0 mt-4">
                                    <label class="form-label fs-6 fw-bolder text-gray-700">Select Permissions</label>

                                    @foreach($items as $item)
                                        <div class="col-6">
                                            <input name="permissions" type="checkbox" class="form-check-label" id="{{$item->id}}" value="{{$item->name}}">
                                            <label for="{{$item->id}}"> {{$item->name}}</label>
                                        </div>
                                    @endforeach

                                </div>



                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center"><i class="fa fa-plus-square"></i> Create Role</button>
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
