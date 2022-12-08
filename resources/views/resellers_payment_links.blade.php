@extends('layouts.layouts')
@section('title', 'Payment Links')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4 font-13">The list of payment links.</p>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Reseller Reference</th>
                            <th>Amount</th>
                            <th>Customer Email</th>
                            <th>Reference</th>
                            <th>Date</th>
                            <th>Status</th>
                            {{--                            <th>Action</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($datas as $account)
                            <tr>
                                <td>{{$account->reseller_reference }}</td>
                                <td>{{$account->amount}}</td>
                                <td>{{$account->email}}</td>
                                <td>{{$account->reference}}</td>
                                <td>{{$account->created_at}}</td>
                                <td>{{$account->status}}</td>
                                {{--                            <td><a href="profile/{{ $user->user_name }}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a></td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $datas->links() }}
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
