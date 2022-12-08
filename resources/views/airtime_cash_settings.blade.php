@extends('layouts.layouts')
@section('title', 'Airtime Converter')
@section('parentPageTitle', 'Settings')

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

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Network</th>
                                                    <th>Phone Number</th>
                                                    <th>Discount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($datas as $data)
                                                    <tr>
                                                        <td>{{$i++}}</td>
                                                        <td>{{$data->network}}</td>
                                                        <td>{{$data->number}}</td>
                                                        <td>{{$data->discount}}</td>
                                                        <td>
                                                            @if($data->status=="1")
                                                                <span class="badge badge-success">Active</span>
                                                            @else
                                                                <span class="badge badge-warning">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>{{$data->created_at}}</td>
                                                        <td class="center">
                                                            @can('airtime2cash-settings-modify')
                                                                <a class="btn {{$data->status =="1"? "btn-gradient-danger" : "btn-success" }}" href="{{route('transaction.airtime2cashSettings.ed',$data->id)}}">
                                                                    {{$data->status =="1"? "Disable" : "Enable" }}
                                                                </a>
                                                                <a href="{{route('transaction.airtime2cashSettings.edit', $data->id)}}" class="btn btn-secondary">Modify</a>
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
                            <!-- end col -->
                        </div>


                </div>
            </div>
        </div>
    </div>


@endsection
