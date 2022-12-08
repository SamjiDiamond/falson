@extends('layouts.layout')

@section('content')
<div class="content-wrapper">
            <div class="container-fluid">
            
                <!-- Title & Breadcrumbs-->
                <div class="row page-breadcrumbs">
                    <div class="col-md-12 align-self-center">
                        <h4 class="theme-cl">Dashboard</h4>
                        
                    </div>
                </div>
                <!-- Title & Breadcrumbs-->
                
                <!-- row -->
                <div class="row">
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="icon ti-user blue-cl font-30"></i>
                                </div>
                                <div class="widget-detail">
                                    <h4 class="mb-1">{{ $total_user ?? 'Total User Calculated' }}</h4>
                                    <span>Total Users</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="icon ti-user blue-cl font-30"></i>
                                </div>
                                <div class="widget-detail">
                                    <h4 class="mb-1">{{ $user_metric1 ?? 'Active User Calculated' }}</h4>
                                    <span>Active | Inactive Users</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="icon ti-bar-chart blue-cl font-30"></i>
                                </div>
                                <div class="widget-detail">
                                    <h4 class="mb-1">{{ $user_metric2 ?? 'User Metrics Calculated' }}</h4>
                                    <span>Clients | Agents | Resellers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="icon ti-signal blue-cl font-30"></i>
                                </div>
                                <div class="widget-detail">
                                    <h4 class="mb-1">{{ $online_user ?? 'Online User Calculated' }}</h4>
                                    <span>Online Users Today</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
                <!-- /row -->
                
                <!-- row -->
                <div class="row">
                        
                    <div class="col-md-3 col-sm-6">
                        <div class="widget simple-widget">
                            <div class="rwidget-caption info">
                                <div class="row">
                                    <div class="col-4 padd-r-0">
                                        <i class="cl-info icon ti-wallet"></i>
                                    </div>
                                    <div class="col-8">
                                        <div class="widget-detail">
                                            <h5><b>{{ $total_transaction ?? 'Total Transactions Calculated' }}</b></h5>
                                            <span>Total Transactions</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="widget-line">
                                            <span style="width:100%;" class="bg-info widget-horigental-line"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="widget simple-widget">
                            <div class="widget-caption danger">
                                <div class="row">
                                    <div class="col-4 padd-r-0">
                                        <i class="cl-danger icon ti-info-alt"></i>
                                    </div>
                                    <div class="col-8">
                                        <div class="widget-detail">
                                            <h5><b>{{ $fail_transaction ?? 'Failed transaction Calculated' }}</b></h5>
                                            <span>Unsuccessful Transactions</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="widget-line">
                                            <span style="width:100%;" class="bg-danger widget-horigental-line"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="widget simple-widget">
                            <div class="widget-caption warning">
                                <div class="row">
                                    <div class="col-4 padd-r-0">
                                        <i class="cl-success icon ti-shopping-cart-full"></i>
                                    </div>
                                    <div class="col-8">
                                        <div class="widget-detail">
                                            <h5><b>{{ $successful_transaction ?? 'Successful Transaction Calculated' }}</b></h5>
                                            <span>Successful Transactions</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="widget-line">
                                            <span style="width:100%;" class="bg-success widget-horigental-line"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="widget simple-widget">
                            <div class="widget-caption purple">
                                <div class="row">
                                    <div class="col-4 padd-r-0">
                                        <i class="cl-purple icon ti-credit-card"></i>
                                    </div>
                                    <div class="col-8">
                                        <div class="widget-detail">
                                            <h5><b>{{ $total_deposits ?? 'Total Calculated' }}</b></h5>
                                            <span>Total Deposits</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="widget-line">
                                            <span style="width:100%;" class="bg-purple widget-horigental-line"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /row -->


                <!-- row -->
                <div class="row">
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="icon ti-wallet green-cl font-30"></i>
                                </div>
                                <div class="widget-detail">
                                    <h4 class="mb-1">{{ $server1 ?? 'Total server1 Calculated' }}</h4>
                                    <span>Sever 1 Balance</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="icon ti-wallet blue-cl font-30"></i>
                                </div>
                                <div class="widget-detail">
                                    <h4 class="mb-1">{{ $server2 ?? 'Total Sever2 Calculated' }}</h4>
                                    <span>Sever 2 Balance</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="icon ti-wallet purple-cl font-30"></i>
                                </div>
                                <div class="widget-detail">
                                    <h4 class="mb-1">{{ $server3 ?? 'Total server3 Calculated' }}</h4>
                                    <span>Server 3 Balance</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="icon ti-signal blue-cl font-30"></i>
                                </div>
                                <div class="widget-detail">
                                    <h4 class="mb-1">{{ $liquidity ?? 'Total Liquidity Calculated' }}%</h4>
                                    <span>Liquidity Ratio</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
                <!-- /row -->
                
                
                <div class="row">
                
                    <!-- Bar Chart -->
                    <div class="col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Payments Chart</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="bar-chart" width="800" height="450"></canvas>
                            </div>
                        </div>
                    </div>
                    

                    <!-- Bar Chart Horizental -->
                    <div class="col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Customer's Chart</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="bar-chart-horizontal" width="800" height="450"></canvas>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- /.row -->

                
                <!-- row -->
                <div class="row">

                    <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Recent Transactions</h4>
                        </div>
                        <div class="card-body">

                            <ul class="list-group list-group-divider list-group-full">
                                @foreach($transactions as $trans)
                                    <li class="list-group-item">
                                        {{$trans->description}}

                                        @if ($trans->status =="delivered" || $trans->status =="successful" )
                                        <span class="float-right text-success"><i class="fa fa-caret-up"></i></span>
                                        <div class="font-13  text-success"><b>{{$trans->status}}</b> | {{$trans->amount}} | {{$trans->date}} | {{$trans->ip_address}}
                                        </div>

                                        @elseif ($trans->status =="not_delivered")
                                        <span class="float-right text-danger"><i class="fa fa-caret-down"></i></span>
                                        <div class="font-13  text-danger"><b>{{$trans->status}}</b> | {{$trans->amount}} | {{$trans->date}} | {{$trans->ip_address}}
                                        @endif

                                    </li>
                                 @endforeach
                            </ul>
                            <div class="card-footer text-center">
                                <a href="{{url('admin/posts')}}">View Transactions</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">New Users</h4>
                        </div>
                        <div class="table-responsive">
                            <ul class="media-list media-list-divider m-0">
                                @foreach($users as $user)
                                    <li class="media">
                                        <a class="media-img" href="javascript:;">
                                            <img src="./assets/img/image.jpg" width="50px;" /> 
                                        </a>
                                        <div class="media-body">
                                            <div class="media-heading">
                                                <a href="javascript:;">{{$user->user_name}}</a>
                                                <span class="font-16 float-right">₦{{$user->wallet}}</span>
                                            </div>
                                            <div class="font-13">{{$user->email}}</div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{url('admin/earnings/list')}}">View Users</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Server Log</h4>
                        </div>
                        <div class="ibox-body">
                            <ul class="list-group list-group-divider list-group-full">
                                @foreach($serverlog as $log)
                                    <li class="media">
                                        <div class="media-body">
                                            <div class="media-heading">
                                                <a href="javascript:;" style="font-weight: bolder;">{{$log->user_name}}</a>
                                                <span class="font-16 float-right">{{$log->amount}}</span>
                                            </div>
                                            <div class="font-13">Service: {{$log->service}} | Recipient: {{$log->phone}}</div>
                                            <div class="font-13">Wallet: #{{$log->wallet}} | Date: {{$log->date}}</div>
                                            <div class="font-13">Ip: {{$log->ip_address}} | Coded: {{$log->coded}} | Version: {{$log->version}}</div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{url('admin/quiz/results')}}">View Logs</a>
                        </div>
                    </div>
                </div>
                
                    
                </div>
                <!-- /.row -->
                

            </div>  
            <!-- /.content-wrapper -->
@endsection
