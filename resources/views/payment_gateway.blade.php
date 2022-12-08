@extends('layouts.layouts')
@section('title', 'Payment Gateway')
@section('parentPageTitle', 'Settings')

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
                    <p class="text-muted mb-4 font-13">Gateway List</p>
                    <table class="table table-striped table-bordered table-hover dataTables-example table-responsive">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Date Modified</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($data as $da)
                                <td>{{$i++}}</td>
                                <td class="center">{{$da['name']}}</td>
                                <td>{{$da['value']}}</td>
                                <td>

                                    {{$da['updated_at']}}</option>

                                </td>


                                <td class="center">
                                    @can('payment-gateway-modify')
                                        <a href="{{route('paymentgateway_edit',$da->id )}}"  class="btn btn-secondary">Modify</a>
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
