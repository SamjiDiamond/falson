@extends('layouts.layouts')
@section('title', 'FAQ List')
@section('parentPageTitle', 'FAQ')

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

                    @can('faq-create')
                        <a href="{{route('faqs.create')}}" class="btn btn-primary mb-3 text-white">Add New FAQ</a>
                    @endcan

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($data as $da)
                                <td>{{$da['id']}}</td>
                                <td class="center">{{$da['title']}}</td>
                                <td class="center">{{$da['content']}}</td>
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
                                    @can('faq-action')
                                        @if($da->status == 1)
                                            <a href="{{route('faqs.update',$da->id )}}" class="btn btn-warning">Disable</a>
                                        @else
                                            <a href="{{route('faqs.modify',$da->id )}}" class="btn btn-success">Enable</a>
                                        @endif

                                        <a href="{{route('faqs.edit',$da->id )}}" class="btn btn-primary">Edit</a>
                                        <a href="{{route('faqs.delete',$da->id )}}" class="btn btn-danger">Remove</a>
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
