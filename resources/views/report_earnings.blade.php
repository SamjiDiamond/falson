@extends('layouts.layouts')
@section('title', 'Earnings Report')
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

                        <form class="form-horizontal" method="GET" action="{{ route('report_earnings') }}">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <h4 class="mt-0 header-title">Search</h4>

                                    <div class="input-group mt-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">From</span>
                                        </div>
                                        <input name="from" type="date" value="{{ request('from') ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="form-control">
                                    </div>

                                    <div class="input-group mt-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">To</span>
                                        </div>
                                        <input name="to" type="date" value="{{ request('to') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control">
                                    </div>

                                    <div class="input-group mt-2" style="align-content: center">
                                        <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">
                                            Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="mt-3">
                            <a class="btn btn-sm btn-secondary" href="{{ route('report_earnings.pdf', ['from' => request('from'), 'to' => request('to')]) }}">Export PDF</a>
                            <a class="btn btn-sm btn-secondary" href="{{ route('report_earnings.excel', ['from' => request('from'), 'to' => request('to')]) }}">Export Excel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Earnings Summary</h4>
                    <p class="text-muted mb-4 font-13">
                        {{ \Carbon\Carbon::parse($from)->format('Y-m-d') }} to {{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="p-3 bg-light text-center">
                                <h4>₦{{ number_format($incomeSum) }}</h4>
                                <p class="mb-0">Total Income</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light text-center">
                                <h4>₦{{ number_format($expenseSum) }}</h4>
                                <p class="mb-0">Total Expenses</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light text-center">
                                <h4>₦{{ number_format($profit) }}</h4>
                                <p class="mb-0">Profit</p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <h5>Income By GL</h5>
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>GL</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($incomeByGl as $row)
                                <tr>
                                    <td>{{ $row->gl }}</td>
                                    <td>₦{{ number_format($row->total_amount) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <h5>Expenses By GL</h5>
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>GL</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($expenseByGl as $row)
                                <tr>
                                    <td>{{ $row->gl }}</td>
                                    <td>₦{{ number_format($row->total_amount) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

