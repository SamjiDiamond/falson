@extends('layouts.layouts')
@section('title', 'CG Bundle Transactions')
@section('parentPageTitle', 'CG Bundle')

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
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Username</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Trans Type</th>
                            <th>Wallet Charged / Receipt</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($data as $da)
                                <td>{{$da->id}}</td>
                                <td>{{$da->user_name}}</td>
                                <td class="center">{{$da->bundle_id != null ? $da->cgbundle->network : $da->type}} {{$da->bundle_id != null ? $da->cgbundle->type : ''}}</td>
                                <td class="center">{{$da->bundle_id != null ? $da->cgbundle->value : $da->value}} GB</td>
                                <td class="center">{{number_format($da->bundle_id != null ? $da->cgbundle->price : $da->price)}}</td>
                                <td class="center">
                                    @if($da->status=="1")
                                        <span class="badge badge-success">Success</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td class="center">{{$da->action}}</td>
                                <td>
                                    @if($da['charge'] == "no")
                                        <a href="{{route('show.cgtransaction',$da['id'].'.jpg')}}"> <img src="{{route('show.cgtransaction', $da['id'].'.jpg')}}" height="50px" /></a>
                                    @else
                                        {{$da['charge']}}
                                    @endif
                                </td>

                                <td>
                                    {{$da['created_at']}}</option>
                                </td>

                                <td class="center">
                                    @if($da->status == 0)
                                        <a href="{{route('cgbundle.apply_credit',$da->id )}}" class="btn btn-warning">Credit User</a>
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
    </div>
@endsection
