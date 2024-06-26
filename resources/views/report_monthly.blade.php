@extends('layouts.layouts')
@section('title', 'Monthly Report')
@section('parentPageTitle', 'Reports')

@section('content')

    <div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="general-label">

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

                    <form class="form-horizontal" method="POST" action="{{ route('report_monthly') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <h4 class="mt-0 header-title">Search</h4>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar-check"></i> </span>
                                    </div>
                                    <input style="margin-right: 20px" name="date" type="month"
                                           value="{{\Carbon\Carbon::now()->format('Y-m')}}"
                                           placeholder="Search for month"
                                           class="form-control @error('date') is-invalid @enderror">
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit"
                                            style="align-self: center; align-content: center"><i
                                            class="fa fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>

{{--        @if($data ?? '')--}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Monthly Report
                            for {{\Carbon\Carbon::parse($date)->format('F, Y')}}</h4>
                        <p class="text-muted mb-4 font-13"></p>
                        <div class="table-responsive">

                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount/Count</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Data</td>
                                    <td>&#8358;{{number_format($data_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>Airtime</td>
                                    <td>&#8358;{{number_format($airtime_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>TV</td>
                                    <td>&#8358;{{number_format($tv_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>Electricity</td>
                                    <td>&#8358;{{number_format($electricity_amount)}}</td>
                                </tr>
                                <tr>
                                    <td>Registered Users</td>
                                    <td>{{number_format($user_count)}}</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        <!-- end col -->
        {{--    @endif--}}
    </div>
    <!-- end row -->
@endsection
