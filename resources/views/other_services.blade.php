@extends('layouts.layouts')
@section('title', 'Other Services')
@section('parentPageTitle', 'Services')

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
                    @can('other-service-create')
                        <a href="{{route('otherservices_add')}}" class="btn btn-primary mb-3 text-white">Add New Service</a>
                    @endcan
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($data as $da)
                                <td>{{$da['id']}}</td>
                                <td class="center">{{$da['name']}}</td>
                                <td><a target="_blank" href="{{route('show.sliders',$da['image'])}}"><img src="{{route('show.sliders',$da['image'])}}" class="img img-thumbnail" width="50px" /></a></td>
                                <td class="center">{{$da['action']}}</td>
                                <td class="center">
                                    @if($da->status=="1")
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    {{$da['created_at']}}</option>
                                </td>

                                <td class="center">
                                    @can('other-service-action')
                                        <a href="{{route('otherservicesEdit',$da->id )}}" class="btn btn-warning">Modify</a>

                                        <a href="{{route('otherservicesDelete',$da->id )}}" class="btn btn-danger">Remove</a>
                                    @endcan
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
