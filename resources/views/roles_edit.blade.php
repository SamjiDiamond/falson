@extends('layouts.layouts')
@section('title', 'Modify Role')
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

                    <form class="form-horizontal" method="POST" action="{{ route('roles.update', $role->id ) }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user"></i> </span></div>
                                    <input type="hidden" name="id" class="form-control" value="{{$role->id}}">
                                    <input type="text" name="name" class="form-control" value="{{$role->name}}" placeholder="Enter role name">
                                </div>

                                <div class="mb-0 mt-4">
                                    <label class="form-label fs-6 fw-bolder text-gray-700">Select/Unselect Permissions</label>

                                    @foreach($permissions as $item)
                                        <div class="col-6">
                                            <input name="permissions[]" type="checkbox" class="form-check-label" id="{{$item->id}}" value="{{$item->name}}" {{in_array($item->name, $mypermissions2->toArray() ) ? 'checked':''}}>
                                            <label for="{{$item->id}}"> {{str_replace("-", " ",$item->name)}}</label>
                                        </div>
                                    @endforeach

                                </div>


                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center"><i class="fa fa-circle-notch"></i> Update Role</button>
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
