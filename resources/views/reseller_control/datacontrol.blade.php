@extends('layouts.layouts')
@section('title', 'Reseller Data Plans')
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
                    <p class="text-muted mb-4 font-13">Data Plans</p>

                        <div class="row ml-3 mb-3">
                            @can('data-plans-disable_all')
                                <a href="{{route('reseller.dataserveMultipleedit', [$data[0]->type, 'ALL', $all == 1 ? 0 : 1, $server ])}}" class="btn btn-secondary mr-3">@if($all == 1)Disable @else Enable @endif All Data</a>
                            @endcan
                            @can('data-plans-disable_cg')
                                <a href="{{route('reseller.dataserveMultipleedit', [$data[0]->type, 'CG', $cg == 1 ? 0 : 1, $server ])}}" class="btn btn-secondary mr-3">@if($cg == 1)Disable @else Enable @endif CG Data</a>
                            @endcan
                            @can('data-plans-disable_sme')
                                <a href="{{route('reseller.dataserveMultipleedit', [$data[0]->type, 'SME', $sme == 1 ? 0 : 1, $server])}}" class="btn btn-secondary mr-3">@if($sme == 1)Disable @else Enable @endif SME Data</a>
                            @endcan
                            @can('data-plans-disable_dg')
                                <a href="{{route('reseller.dataserveMultipleedit', [$data[0]->type, 'DG', $dg == 1 ? 0 : 1, $server ])}}" class="btn btn-secondary mr-3">@if($dg == 1)Disable @else Enable @endif DG Data</a>
                            @endcan
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Type</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Level1</th>
                            <th>Level2</th>
                            <th>Level3</th>
                            <th>Level4</th>
                            <th>Level5</th>
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
                                <td class="center">{{$da['type']}}</td>

                                <td>{{$da['name']}}</td>
                                <td class="center">&#8358;{{number_format($da['price'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level1'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level2'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level3'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level4'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level5'])}}</td>
                                <td>

                                    {{$da['server']}}</option>

                                </td>
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
                                    @can('reseller_data-action')
                                        <a class="btn {{$da->status =="1"? "btn-gradient-danger" : "btn-success" }}" href="{{route('reseller.datacontrolED',$da->id)}}">
                                            {{$da->status =="1"? "Disable" : "Enable" }}
                                        </a>

                                        <a href="{{route('reseller.datacontrolEdit',$da->id )}}"  class="btn btn-secondary">Modify</a>
                                    @endcan
                                </td>

                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                        </div>
                    {{$data->links()}}

                </div>
            </div>
        </div>
    </div>
@endsection
