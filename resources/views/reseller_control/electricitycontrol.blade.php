@extends('layouts.layouts')
@section('title', 'Electricity Control')
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
                    <p class="text-muted mb-4 font-13">Network List</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Company</th>
                                <th>Discount</th>
                                <th>Server</th>
                                <th>Status</th>
                                <th>Date Modified</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="gradeX">
                                @foreach($data as $da)
                                    <td>{{$da['id']}}</td>
                                    <td class="center">{{$da['name']}}</td>
                                    <td>{{$da['discount']}}</td>
                                    <td class="center">{{$da['server']}}</td>
                                    <td class="center">
                                        @if($da->status=="1")
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Inactive</span>
                                        @endif
                                    </td>

                                    <td>

                                        {{$da['updated_at']}}</option>

                                    </td>


                                    <td class="center">
                                        @can('tv-plan-action')
                                            {{--                                        <a class="btn {{$da->status =="1"? "btn-gradient-danger" : "btn-success" }}" href="{{route('reseller.electricitycontrolED',$da->id)}}">--}}
                                            {{--                                            {{$da->status =="1"? "Disable" : "Enable" }}--}}
                                            {{--                                        </a>--}}
                                            <a href="{{route('reseller.electricitycontrolEdit',$da->id )}}"
                                               class="btn btn-secondary">Modify</a>
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
    </div>
@endsection
