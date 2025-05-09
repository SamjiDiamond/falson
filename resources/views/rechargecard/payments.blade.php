@extends('layouts.layouts')
@section('title', 'Payments')
@section('parentPageTitle', 'Recharge Card')

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
                    <p class="text-muted mb-4 font-13">Price List</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>User</th>
                                <th>Plan Name</th>
                                <th>Amount</th>
                                <th>Date Paid</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="gradeX">
                                @foreach($data as $da)
                                    <td>{{$i++}}</td>
                                    <td class="center">{{$da->user_name}}</td>
                                    <td>{{strtoupper($da->extra)}}</td>
                                    <td>{{$da->amount}}</td>
                                    <td>
                                        {{$da['created_at']}}
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
