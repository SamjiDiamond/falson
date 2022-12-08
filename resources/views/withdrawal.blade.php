@extends('layouts.layouts')
@section('title', 'Withdrawal List')
@section('parentPageTitle', 'Wallet')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Withdrawal Requests</h4>
                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}

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

                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Username</th>
                                <th>Account Number</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Wallet</th>
                                <th>Reference</th>
                                <th>Bank Name</th>
                                <th>Version</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $dat)
                                <tr>
                                    <td>{{$dat->id}}</td>
                                    <td>{{$dat->user_name}}</td>
                                    <td>{{$dat->account_number}}</td>
                                    <td>&#8358;{{$dat->amount}}</td>
                                    <td>
                                        @if($dat->status == 1)
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($dat->status == 2)
                                            <span class="badge badge-info">Processing</span>
                                        @elseif($dat->status == 4)
                                            <span class="badge badge-info">Rejected</span>
                                        @else
                                            <span class="badge badge-danger">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{$dat->wallet}}</td>
                                    <td>{{$dat->ref}}</td>
                                    <td>{{$dat->bank}}</td>
                                    <td>{{$dat->version}}</td>
                                    <td>{{$dat->created_at}}</td>
                                    <td>
                                        @if($dat->status == 0)
                                            <form method="post" action="{{route('withdrawal_submit')}}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$dat->id}}"/>
                                                <button type="submit" class="btn btn-primary">Approve</button>
                                            </form>
                                        @endif

                                        @if($dat->status == 0)
                                            <form method="post" action="{{route('withdrawal_reject')}}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$dat->id}}"/>
                                                <button type="submit" class="btn btn-danger">Rejeect</button>
                                            </form>
                                        @endif
                                    </td>
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
