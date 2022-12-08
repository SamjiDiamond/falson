@extends('layouts.layouts')
@section('title', 'Role List')
@section('parentPageTitle', 'Roles')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            <strong>{{ session('success') }}</strong>
                        </div>
                        <script type="text/javascript">
                            toastr.options = {
                                closeButton: true,
                                progressBar: true,
                                showMethod: 'slideDown',
                                timeOut: 4000
                            };
                            toastr.success('{{ session('success') }}', 'Success');
                        </script>
                    @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                {{ session('error') }}
                            </div>
                        @endif

                        @can('roles-create')
                            <a href="{{route('roles.create')}}" class="btn btn-primary mb-3 text-white">Add New Role</a>
                        @endcan
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($items as $da)
                                <td>{{$da['id']}}</td>
                                <td class="center">{{$da['name']}}</td>
                                <td>
                                    {{$da['created_at']}}</option>
                                </td>

                                <td class="center">
                                    @if($da['name'] != "Super Admin")
                                        @can('roles-modify')
                                            <a href="{{route('roles.edit',$da->id )}}" class="btn btn-primary">Edit</a>
                                        @endcan

                                        @can('roles-delete')
                                            <a href="{{route('roles.delete',$da->id )}}" class="btn btn-danger">Remove</a>
                                        @endcan
                                    @endif
                                </td>

                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection
