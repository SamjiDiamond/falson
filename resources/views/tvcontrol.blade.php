@extends('layouts.layouts')
@section('title', 'TV Plans')
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

                    <form class="form-horizontal" method="POST" action="{{ route('tvDiscountUpdate') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text"><i
                                                class="fa fa-wallet"></i> </span></div>
                                    <input type="number" name="amount" class="form-control"
                                           placeholder="Enter Discount">
                                </div>

                                <div class="input-group mt-2">
                                    <select class="custom-select form-control" name="type">
                                        <option selected="selected">GOTV</option>
                                        <option>DSTV</option>
                                        <option>STARTIMES</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit"
                                            style="align-self: center; align-content: center"><i
                                            class="fa fa-money-bill-wave"></i> Update Discount
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
                                <td class="center">{{$da['type']}}</td>

                                <td>{{$da['name']}}</td>
                                <td class="center">&#8358;{{number_format($da['price'])}}</td>
                                <td class="center">{{$da['discount']}}</td>
                                <td>

                                    {{$da['server']}}

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
                                    @can('tv-plan-action')
                                        <a class="btn {{$da->status =="1"? "btn-gradient-danger" : "btn-success" }}" href="{{route('tvcontrolED',$da->id)}}">
                                            {{$da->status =="1"? "Disable" : "Enable" }}
                                        </a>
                                        <a href="{{route('tvcontrolEdit',$da->id )}}"  class="btn btn-secondary">Modify</a>
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
