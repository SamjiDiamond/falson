@extends('layouts.layouts')
@section('title', 'Admin-Role')
@section('parentPageTitle', 'Role')

@section('content')
    <div class="row">
        @can('admins-update_role')
            <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="general-label">

                        <p class="text-muted mb-4 font-13">Add User to Admin</p>

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

                        <form class="form-horizontal" method="POST" action="{{route('admin.updaterole')}}">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-12">

                                    <div class="input-group mt-2 @error('user_name') has-error @enderror">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="far fa-user"></i></span></div>
                                        <input type="text" name="id" placeholder="Enter Username/Phone number/Email" class="form-control @error('username') is-invalid @enderror">
                                    </div>

                                    <div class="input-group mt-2">
                                        <select class="custom-select form-control" name="role">
                                            @foreach($roles as $role)
                                                <option>{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="input-group mt-2" style="align-content: center">
                                        <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">Add to Admin</button>
                                    </div>

                                </div>
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4 font-13">Users Role</p>
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Username</th>
                            <th>Assigned Role</th>
                            <th>Update</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($admins as $da)
                                <form method="post" action="{{route('admin.updaterole')}}">
                                    @csrf
                                    <td>{{$i++}}</td>
                                    <input type="hidden" name="id" value="{{$da['id']}}">
                                    <td>{{$da['user_name']}}</td>
                                    <td class="center">
                                        <select class="custom-select form-control" name="role">
                                            @foreach($roles as $role)
                                                <option @if($da->getRoleNames()[0]??'' == $role->name) selected @endif>{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="center">
                                        @can('admins-update_role')
                                            <button type="submit" class="btn btn-outline-primary">Update</button>
                                        @endcan
                                        @can('admins-revoke_role')
                                            <a href="{{route('admin.rovoke', $da['id'])}}" class="btn btn-gradient-danger btn-large" type="button" style="align-self: center; align-content: center">Revoke Admin</a>
                                        @endcan
                                    </td>

                        </form>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
{{--                    {{$user->links()}}--}}

                </div>
            </div>
        </div>
    </div>
@endsection
