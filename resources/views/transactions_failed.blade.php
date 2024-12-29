@extends('layouts.layouts')
@section('title', $name.' Transactions')
@section('parentPageTitle', 'Transactions')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{$name}} transaction List</h4>
                    <p class="text-muted mb-4 font-13">List of failed transactions</p>

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

                    <form id="allForm" method="post" action="{{route('trans_resubmitAll')}}">

                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Ref</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>I.P</th>
                                    <th>Server</th>
                                    <th>Server Response</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $dat)
                                    <tr>
                                        <td>
                                            {{$dat->id}}
                                        </td>
                                        <td>{{$dat->ref}}</td>
                                        <td>&#8358;{{number_format($dat->amount)}}</td>
                                        <td>{{$dat->description}}</td>
                                        <td>
                                            @if($dat->status == "delivered" || $dat->status == "successful")
                                                <span class="badge badge-success"></span>
                                            @elseif($dat->status == "inprogress")
                                                <span class="badge badge-info">Processing</span>
                                            @elseif($dat->status == "reversed")
                                                <span class="badge badge-info">{{$dat->status}}</span>
                                            @else
                                                <span class="badge badge-danger">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{$dat->date}}</td>
                                        <td>{{$dat->ip_address}}</td>
                                        <td>{{$dat->server}}</td>
                                        <td>{{$dat->server_response}}</td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $data->links() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
