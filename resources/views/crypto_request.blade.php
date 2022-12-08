@extends('layouts.layouts')
@section('title', 'Dormant Users')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4 font-13">The list of users greater than 3 months.</p>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Transaction ID</th>
                            <th>Crypto</th>
                            <th>Address</th>
                            <th>Fee</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($crypto as $cryptos)
                        <tr>
                            <td>{{$cryptos->user_name}} </td>
                            <td>{{$cryptos->transid}} </td>
                            <td>BTC</td>
                            <td>{{$cryptos->address}}</td>
                            <td>{{$cryptos->receive_fee}}</td>
                            <td>{{$cryptos->created_at}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $crypto->links() }}
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
