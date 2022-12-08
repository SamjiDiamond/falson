@extends('layouts.layouts')
@section('title', 'General Market History')
@section('parentPageTitle', 'Transaction')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
{{--                    <h4 class="mt-0 header-title">General Market History</h4>--}}
                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Username</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Trans ID</th>
                                <th>O. Balance</th>
                                <th>N. Balance</th>
                                <th>Version</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $dat)
                                <tr>
                                    <td>{{$dat->id}}</td>
                                    <td>{{$dat->user_name}}</td>
                                    <td>&#8358;{{$dat->amount}}</td>
                                    <td>
                                        @if($dat->type=="credit")
                                            <span class="badge badge-success">{{$dat->type}}</span>
                                        @else
                                            <span class="badge badge-warning">{{$dat->type}}</span>
                                        @endif

                                    </td>
                                    <td>{{$dat->transid}}</td>
                                    <td>&#8358;{{$dat->i_wallet}}</td>
                                    <td>&#8358;{{$dat->f_wallet}}</td>
                                    <td>{{$dat->version}}</td>
                                    <td>{{$dat->created_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
