@extends('layouts.layouts')
@section('title', 'Profit And Loss')
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

                    <form class="form-horizontal" method="POST" action="{{ route('report_pnl') }}">
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

        @if($income ?? '')
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Profit & Loss Report
                            for {{\Carbon\Carbon::parse($date)->format('F, Y')}}</h4>
                        <p class="text-muted mb-4 font-13"></p>
                        <div class="table-responsive">

                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($incomed as $incomedi)
                                    <tr>
                                        <td>{{$incomedi->gl}}</td>
                                            @foreach($income as $come)
                                                @if($incomedi->gl==$come->gl)
                                                    <?php
                                                    $ti += $come->amount;
                                                    ?>
                                                @endif
                                            @endforeach
                                        <td>{{$ti}}</td>
                                        <?php $ti=0; ?>
                                    </tr>
                                @endforeach
                                Total Income: {{$income_sum}}
                                </tbody>
                            </table>

                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($expensed as $exp)
                                    <tr>
                                        <td>{{$exp->gl}}</td>
                                        @foreach($expenses as $expen)
                                            @if($exp->gl==$expen->gl)
                                                <?php
                                                $te += $expen->amount;
                                                ?>
                                            @endif
                                        @endforeach
                                        <td>{{$te}}</td>
                                        <?php $te=0; ?>
                                    </tr>
                                @endforeach

                                Total Expenses: {{$expense_sum}}
                                </tbody>
                            </table>

                            Net Income: {{$income_sum - $expense_sum}}
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
    @endif
    </div>
    <!-- end row -->
@endsection
