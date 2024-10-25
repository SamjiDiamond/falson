@extends('layouts.layouts')
@section('title', 'Reseller TV Plans')
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


                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{ session('error') }}
                        </div>
                    @endif


                    <form class="form-horizontal" method="POST" action="{{ route('reseller.tvDiscountUpdate') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">

                                <div class="input-group mt-2">
                                    <select class="custom-select form-control" name="type">
                                        <option selected="selected">GOTV</option>
                                        <option>DSTV</option>
                                        <option>STARTIMES</option>
                                        <option>SHOWMAX</option>
                                    </select>
                                </div>


                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Level1 Discount </span></div>
                                    <input type="text" name="level1" class="form-control"
                                           placeholder="Enter Discount e.g 0.1">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Level2 Discount</span></div>
                                    <input type="text" name="level2" class="form-control"
                                           placeholder="Enter Discount e.g 0.1">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Level3 Discount</span></div>
                                    <input type="text" name="level3" class="form-control"
                                           placeholder="Enter Discount e.g 0.1">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Level4 Discount</span></div>
                                    <input type="text" name="level4" class="form-control"
                                           placeholder="Enter Discount e.g 0.1">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span
                                            class="input-group-text">Level5 Discount</span></div>
                                    <input type="text" name="level5" class="form-control"
                                           placeholder="Enter Discount e.g 0.5">
                                </div>

                                <div class="input-group mt-2">
                                    <select class="custom-select form-control" name="status">
                                        <option value="1">Activate</option>
                                        <option value="0">Deactivate</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2">
                                    <select class="custom-select form-control" name="server">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit"
                                            style="align-self: center; align-content: center"><i
                                            class="fa fa-money-bill-wave"></i> Mass Update
                                    </button>
                                </div>

                            </div>
                        </div>
                        <!--end row-->
                    </form>


                        <p class="text-muted mb-4 font-13">TV Plans</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Type</th>
                                <th>Name</th>
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
                                    <td class="center">{{$da['amount']}}</td>
                                    <td class="center">{{$da['level1']}}</td>
                                    <td class="center">{{$da['level2']}}</td>
                                    <td class="center">{{$da['level3']}}</td>
                                    <td class="center">{{$da['level4']}}</td>
                                    <td class="center">{{$da['level5']}}</td>
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
                                    @can('reseller_tv-action')
                                        <a class="btn {{$da->status =="1"? "btn-gradient-danger" : "btn-success" }}" href="{{route('reseller.tvcontrolED',$da->id)}}">
                                            {{$da->status =="1"? "Disable" : "Enable" }}
                                        </a>
                                        <a href="{{route('reseller.tvcontrolEdit',$da->id )}}"  class="btn btn-secondary">Modify</a>
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
