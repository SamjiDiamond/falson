@extends('layouts.layouts')
@section('title', 'Home')
@section('parentPageTitle', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            {{--            <div class="row">--}}
            {{--                <div class="col-lg-3">--}}
            {{--                    <div class="card">--}}
            {{--                        <div class="card-body">--}}
            {{--                            <div class="icon-contain">--}}
            {{--                                <div class="row">--}}
            {{--                                    <div class="col-2 align-self-center"><i--}}
            {{--                                            class="fas fa-tasks text-gradient-success"></i></div>--}}
            {{--                                    <div class="col-10 text-right">--}}
            {{--                                        <h5 class="mt-0 mb-1">{{ $today_transaction ?? 'Today Transactions Calculated' }}</h5>--}}
            {{--                                        <p class="mb-0 font-12 text-muted">Transactions</p>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--                <div class="col-lg-3">--}}
            {{--                    <div class="card">--}}
            {{--                        <div class="card-body justify-content-center">--}}
            {{--                            <div class="icon-contain">--}}
            {{--                                <div class="row">--}}
            {{--                                    <div class="col-2 align-self-center"><i class="far fa-gem text-gradient-danger"></i></div>--}}
            {{--                                    <div class="col-10 text-right">--}}
            {{--                                        <h5 class="mt-0 mb-1">{{ $p_nd_l ?? 'p and l' }}</h5>--}}
            {{--                                        <p class="mb-0 font-12 text-muted">Today's Charges</p>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--                <div class="col-lg-3">--}}
            {{--                    <div class="card">--}}
            {{--                        <div class="card-body">--}}
            {{--                            <div class="icon-contain">--}}
            {{--                                <div class="row">--}}
            {{--                                    <div class="col-2 align-self-center"><i class="fas fa-users text-gradient-warning"></i></div>--}}
            {{--                                    <div class="col-10 text-right">--}}
            {{--                                        <h5 class="mt-0 mb-1">{{ $today_user ?? 'Active User Calculated' }}</h5>--}}
            {{--                                        <p class="mb-0 font-12 text-muted">Today's Users</p>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--                <div class="col-lg-3">--}}
            {{--                    <div class="card">--}}
            {{--                        <div class="card-body">--}}
            {{--                            <div class="icon-contain">--}}
            {{--                                <div class="row">--}}
            {{--                                    <div class="col-2 align-self-center"><i--}}
            {{--                                            class="fas fa-database text-gradient-primary"></i></div>--}}
            {{--                                    <div class="col-10 text-right">--}}
            {{--                                        <h5 class="mt-0 mb-1">{{ $today_deposits ?? 'Today Deposits' }}</h5>--}}
            {{--                                        <p class="mb-0 font-12 text-muted">Today's Deposits</p>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}

            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $data ?? 'Today Data' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Data</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $airtime ?? 'airtime' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Airtime</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $tv ?? 'tv' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's CableTv</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $betting ?? 'Today Betting' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Betting</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $electricity ?? 'Today electricity' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Electricity</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $rch ?? 'rch' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Exam</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-briefcase text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $airtime2wallet ?? 'airtime2wallet' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Airtime2wallet</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon-contain">
                                <div class="row">
                                    <div class="col-2 align-self-center"><i class="fas fa-wallet text-success"></i>
                                    </div>
                                    <div class="col-10 text-right">
                                        <h5 class="mt-0 mb-1">{{ $today_deposits ?? 'today_deposits' }}</h5>
                                        <p class="mb-0 font-12 text-muted">Today's Funding</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop

@section('before-scripts')
    {{--    <script>--}}
    {{--        (gradientStroke1 = (ctx = document.getElementById("lineChar").getContext("2d")).createLinearGradient(0, 0, 0, 300)).addColorStop(0, "#008cff"), gradientStroke1.addColorStop(1, "rgba(22, 195, 233, 0.1)"), gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300), gradientStroke2.addColorStop(0, "#ec536c"), gradientStroke2.addColorStop(1, "rgba(222, 15, 23, 0.1)");--}}
    {{--        var myChart = new Chart(ctx, {--}}
    {{--            type: "line",--}}
    {{--            data: {--}}
    {{--                labels: ["6days Ago", "5days Ago", "4days Ago", "3days Ago", "2days Ago", "Yesterday", "Today"],--}}
    {{--                datasets: [{--}}
    {{--                    label: "Transactions",--}}
    {{--                    data: [{{$d6_transaction}}, {{$d5_transaction}}, {{$d4_transaction}}, {{$d3_transaction}}, {{$d2_transaction}}, {{$yesterday_transaction}}, {{$today_transaction}}],--}}
    {{--                    pointBorderWidth: 0,--}}
    {{--                    pointHoverBackgroundColor: gradientStroke1,--}}
    {{--                    backgroundColor: gradientStroke1,--}}
    {{--                    borderColor: "transparent",--}}
    {{--                    borderWidth: 1--}}
    {{--                }]--}}
    {{--            },--}}
    {{--            options: {--}}
    {{--                legend: {--}}
    {{--                    position: "bottom",--}}
    {{--                    display: 1--}}
    {{--                },--}}
    {{--                tooltips: {--}}
    {{--                    displayColors: 1,--}}
    {{--                    intersect: 1--}}
    {{--                },--}}
    {{--                elements: {--}}
    {{--                    point: {--}}
    {{--                        radius: 0--}}
    {{--                    }--}}
    {{--                },--}}
    {{--                scales: {--}}
    {{--                    xAxes: [{--}}
    {{--                        ticks: {--}}
    {{--                            max: 100,--}}
    {{--                            min: 20,--}}
    {{--                            stepSize: 10--}}
    {{--                        },--}}
    {{--                        gridLines: {--}}
    {{--                            display: 1,--}}
    {{--                            color: "#FFFFFF"--}}
    {{--                        },--}}
    {{--                        ticks: {--}}
    {{--                            display: 1,--}}
    {{--                            fontFamily: "'Rubik', sans-serif"--}}
    {{--                        }--}}
    {{--                    }],--}}
    {{--                    yAxes: [{--}}
    {{--                        gridLines: {--}}
    {{--                            color: "#fff",--}}
    {{--                            display: 1--}}
    {{--                        },--}}
    {{--                        ticks: {--}}
    {{--                            display: 1,--}}
    {{--                            fontFamily: "'Rubik', sans-serif"--}}
    {{--                        }--}}
    {{--                    }]--}}
    {{--                }--}}
    {{--            }--}}
    {{--        });--}}
    {{--    </script>--}}
@stop
