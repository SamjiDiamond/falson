@extends('layouts.layouts')
@section('title', 'Virtual Account Number')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4 font-13">The list of virtual accounts.</p>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Account Number</th>
                            <th>Bank Name</th>
                            <th>Customer Name</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Status</th>
                            {{--                            <th>Action</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($accounts as $account)
                            <tr>
                                <td>{{$account->account_number }}</td>
                                <td>{{$account->bank_name}}</td>
                                <td>{{$account->customer_name}}</td>
                                <td>{{$account->customer_phone}}</td>
                                <td>{{$account->customer_email}}</td>
                                <td>{{$account->created_at}}</td>
                                <td>{{$account->status}}</td>
                                {{--                            <td><a href="profile/{{ $user->user_name }}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a></td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $accounts->links() }}
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
