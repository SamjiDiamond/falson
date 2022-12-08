@extends('layouts.layouts')
@section('title', 'Sever8 Transactions')
@section('parentPageTitle', 'Transaction')

@section('content')


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Transactions Table</h4>
                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Description</th>
                                <th>Phone Number</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $dat)
                                <tr>
                                    <td>{{$dat['req_id']}}</td>
                                    <td>{{$dat['network']}} {{$dat['size']}}</td>
                                    <td>{{$dat['number']}}</td>
                                    <td class="center">
                                        {!! $dat['status']['statusTxt'] !!}
                                    </td>
                                    <td>{{$dat['date_time']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
