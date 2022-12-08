@extends('layouts.layouts')
@section('title', 'Sell CG Bundle')
@section('parentPageTitle', 'CG Bundle')

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

                    <form class="form-horizontal" method="POST" action="{{ route('cgbundle.apply') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Username / Phone Number</span></div>
                                    <input type="text" name="user_name" class="form-control" placeholder="Enter Username or phone number" required>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">CG Bundle </span></div>
                                    <select name="bundle_id" data-placeholder="Choose type..." class="custom-select form-control" tabindex="2" required>
                                        @foreach($data as $dat)
                                        <option value="{{$dat->id}}">{{number_format($dat->value) . "GB NGN".number_format($dat->price). " - ".$dat->network. " ".$dat->type}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Charge User </span></div>
                                    <select name="charge" data-placeholder="Choose type..." class="custom-select form-control" tabindex="2" required>
                                        <option>no</option>
                                        <option>yes</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">Sell Bundle</button>
                                </div>

                            </div>
                        </div>
                        <!--end row-->
                    </form>


                        <h4 class="mt-0 header-title mt-5">Recent CG Bundle Purchase</h4>
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>CG Display Name</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Credited By</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="gradeX">
                                @foreach($trans as $da)
                                    <td>{{$da['id']}}</td>
                                    <td class="center">{{$da->cgbundle->display_name}}</td>
                                    <td>{{$da['user_name']}}</td>
                                    <td class="center">
                                        @if($da->status=="1")
                                            <span class="badge badge-success">Successful</span>
                                        @else
                                            <span class="badge badge-warning">Failed</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{$da['created_at']}}</option>
                                    </td>
                                    <td>
                                        {{$da['created_by']}}</option>
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
