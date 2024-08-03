@extends('layouts.layouts')
@section('title', $name.' Transactions')
@section('parentPageTitle', 'Transactions')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">{{$name}} transaction List</h4>
                    <p class="text-muted mb-4 font-13">Click on <code>Re-process</code> to reprocess in background.</p>

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
                        @csrf
                        <input type="hidden" id="all_type" name="all_type" value="all_type">
                        @can('pending-transactions-reprocess_selected')
                            <button onclick="document.getElementById('all_type').value='reprocess';document.getElementById('allForm').submit();" type="button" class="btn btn-primary mb-2">Re-process Selected</button>
                        @endcan
                        @can('pending-transactions-mark_delivered_selected')
                            <button onclick="document.getElementById('all_type').value='delivered';document.getElementById('allForm').submit();" type="submit" class="btn btn-success mb-2">Mark Delivered Selected</button>
                        @endcan
                        @can('pending-transactions-reverse_transaction_selected')
                            <button onclick="document.getElementById('all_type').value='reverse';document.getElementById('allForm').submit();" type="submit" class="btn btn-danger mb-2">Reverse Transaction Selected</button>
                        @endcan

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
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $dat)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selectIDs[]" value="{{$dat->id}}">
                                        {{$dat->id}}
                                    </td>
                                    <td>{{$dat->ref}}</td>
                                    <td>&#8358;{{number_format($dat->amount)}}</td>
                                    <td>{{$dat->description}}</td>
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
                                    <td>{{$dat->date}}</td>
                                    <td>{{$dat->ip_address}}</td>
                                    <td>{{$dat->server}}</td>
                                    <td>{{$dat->server_response}}</td>
                                    <td>
                                        @can('pending-transactions-reprocess')
                                        <form method="post" action="{{route('trans_resubmit')}}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$dat->id}}"/>
                                            <button type="submit" class="btn btn-primary">Re-process</button>
                                        </form>
                                        @endcan
                                        @can('pending-transactions-mark_delivered')
                                            <a href="{{route('trans_delivered', $dat->id)}}" class="btn btn-success mt-2">Mark Delivered</a>
                                        @endcan
                                        @can('pending-transactions-reverse_transaction')
                                            <a href="{{route('reverse2', $dat->id)}}" class="btn btn-danger mt-2">Reverse Transaction</a>
                                        @endcan
                                    </td>
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
