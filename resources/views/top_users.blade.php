@extends('layouts.layouts')
@section('title', 'Top Users')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Top Users Table</h4>
                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>User name</th>
                                <th>Transaction Amount</th>
                                <th>Wallet Balance</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$user->user_name }}</td>
                                    <td>&#8358;{{number_format($user->total_amount)}}</td>
                                    <td>&#8358;{{number_format($user->wallet)}}</td>
                                    <td>{{$user->status}}</td>
                                    <td><a href="profile/{{ $user->user_name }}" class="btn btn-sm btn-success"><i
                                                class="fas fa-edit"></i></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                                                {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
