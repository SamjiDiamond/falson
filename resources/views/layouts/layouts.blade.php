<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <title>PLANETF | Dashboard</title>
    <meta content="Admin Dashboard" name="description">
    <meta content="5Star Company" name="author">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="img/mcd_logo.png">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <!-- DataTables -->
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <!-- Responsive datatable examples -->
    <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css">
    @yield('after-style')
</head>
<body class="fixed-left">
<!-- Loader -->
<div id="preloader">
    <div id="status">
{{--        <lottie-player src="/assets/cheapprogress.json" background="transparent"  speed="0.5"  style="width: 150px; height: 150px;" loop autoplay></lottie-player>--}}
        <div class="spinner"></div>
    </div>
</div>
<!-- Begin page -->
<div id="wrapper">
    <!-- ========== Left Sidebar Start ========== -->
    <div class="left side-menu">
        <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect"><i class="ion-close"></i></button><!-- LOGO -->
        <div class="topbar-left">
            <div class="text-center bg-logo">
                <a href="#" class="logo"><img src="/img/mcd_logo.png" height="30px" width="30px" alt="user" class="rounded-circle img-thumbnail mb-1"/> PLANETF</a><!-- <a href="index.html" class="logo"><img src="assets/images/logo.png" height="24" alt="logo"></a> -->
            </div>
        </div>
        <div class="sidebar-user">
            @if(\Illuminate\Support\Facades\Auth::user()->photo)
                <img src="{{route('show.avatar', \Illuminate\Support\Facades\Auth::user()->photo)}}" alt="user" class="rounded-circle img-thumbnail mb-1">
            @else
                <img src="/img/mcd_logo.png" alt="user" class="rounded-circle img-thumbnail mb-1">
            @endif

            <h6 class="">{{\Illuminate\Support\Facades\Auth::user()->full_name}}</h6>
            <p class="online-icon text-dark"><i class="mdi mdi-record text-success"></i>online</p>
            <ul class="list-unstyled list-inline mb-0 mt-2">
                @can('users-profile-view')
                    <li class="list-inline-item"><a href="/profile/{{\Illuminate\Support\Facades\Auth::user()->user_name}}" class="" data-toggle="tooltip" data-placement="top" title="Profile"><i class="dripicons-user text-purple"></i></a></li>
                @endcan
                <li class="list-inline-item"><a href="{{route('change_password')}}" class="" data-toggle="tooltip" data-placement="top" title="Change Password"><i class="dripicons-lock text-purple"></i></a></li>
                @can('settings-view')
                    <li class="list-inline-item"><a href="{{route('allsettings')}}" class="" data-toggle="tooltip" data-placement="top" title="Settings"><i class="dripicons-gear text-dark"></i></a></li>
                @endcan
                <li class="list-inline-item"><a href="/logout" class="" data-toggle="tooltip" data-placement="top" title="Log out"><i class="dripicons-power text-danger"></i></a></li>
            </ul>
        </div>
        <div class="sidebar-inner slimscrollleft">
            <div id="sidebar-menu">
                <ul>
                    <li class="menu-title">Main</li>
                    <li><a href="/home" class="waves-effect"><i class="dripicons-device-desktop"></i> <span>Dashboard</span></a></li>
                    @can('audit-view')
                        <li><a href="{{route('audits')}}" class="waves-effect"><i class="dripicons-alarm"></i> <span>Audits</span></a></li>
                    @endcan
                    @can('announcement-create')
                        <li><a href="{{route('addgnews')}}" class="waves-effect"><i class="dripicons-bell"></i> <span>Announcement</span></a></li>
                    @endcan

                    @canany(['all-transactions-view','data-transactions-view','airtime-transactions-view', 'tv-transactions-view', 'electricity-transactions-view','resultchecker-transactions-view', 'funding-transactions-view', 'pending-transactions-view', 'find-transactions-view', 'add-airtime-transaction-view', 'add-data-transaction-view', 'reverse-transaction-view' ])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-blog"></i><span> Transactions </span>
                            {{--                            <span class="badge badge-pill badge-info float-right">8</span>--}}
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                        </a>
                        <ul class="list-unstyled">
                            @can('all-transactions-view')
                                <li><a href="{{route('transaction')}}">All Transaction History</a></li>
                            @endcan

                            @can('data-transactions-view')
                                <li><a href="{{route('transaction_data')}}">Data Transaction History</a></li>
                            @endcan

                            @can('airtime-transactions-view')
                                <li><a href="{{route('transaction_airtime')}}">Airtime Transaction History</a></li>
                            @endcan

                            @can('tv-transactions-view')
                                <li><a href="{{route('transaction_tv')}}">Cable Transaction History</a></li>
                            @endcan

                            @can('electricity-transactions-view')
                                <li><a href="{{route('transaction_electricity')}}">Electricity Transaction History</a></li>
                            @endcan

                            @can('resultchecker-transactions-view')
                                <li><a href="{{route('transaction_resultchecker')}}">ResultChecker Transaction History</a></li>
                            @endcan

                            @can('funding-transactions-view')
                                <li><a href="{{route('transaction_funding')}}">Funding Transaction History</a></li>
                            @endcan

                            @can('pending-transactions-view')
                                <li><a href="{{route('trans_pending')}}">Pending Transactions</a></li>
                            @endcan

                            @can('find-transactions-view')
                                <li><a href="{{route('findtransaction')}}">Find Transaction</a></li>
                            @endcan

                            @can('add-airtime-transaction-view')
                                <li><a href="/addtransaction">Add Airtime Transaction</a></li>
                            @endcan

                            @can('add-data-transaction-view')
                                <li><a href="/adddatatransaction">Add Data Transaction</a></li>
                            @endcan

                            @can('reverse-transaction-view')
                                <li><a href="/reversal">Reverse Transaction</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcanany

                    @canany('credit-or-debit-user-view','wallet-view')
                        <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="dripicons-wallet"></i><span> Wallet </span>
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                            {{--                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>--}}
                        </a>
                        <ul class="list-unstyled">
                            @can('credit-or-debit-user-view')
                                <li><a href="{{route('addfund')}}">Credit/Debit User</a></li>
                            @endcan
                            @can('wallet-view')
                                <li><a href="/wallet">Wallet</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcanany

                    @canany(['roles-view','admins-view'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="dripicons-anchor"></i><span> Roles & Admin</span>
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                            {{--                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>--}}
                        </a>
                        <ul class="list-unstyled">
                            @can('roles-view')
                                <li><a href="{{route('roles.list')}}">Roles</a></li>
                            @endcan

                            @can('admins-view')
                                <li><a href="{{route('admin.role')}}" class="waves-effect"><i class="dripicons-user"></i> <span>Admins</span></a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcanany

                    @canany(['airtime2cash-settings-view','airtime2cash-view'])
                        <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="dripicons-view-list"></i><span>Airtime Converter </span>
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                            {{--                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>--}}
                        </a>
                        <ul class="list-unstyled">
                            @can('airtime2cash-settings-view')
                                <li><a href="{{route('transaction.airtime2cashSettings')}}">Settings</a></li>
                            @endcan
                            @can('airtime2cash-view')
                                <li><a href="{{route('transaction.airtime2cash')}}">Airtime Converter</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcanany

                    @canany(['create-cg-bundle-view','cg-bundle-list-view','sell-cg-bundle-view','cg-bundle-transactions-view'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="dripicons-wallet"></i><span>CG Bundle </span>
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                            {{--                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>--}}
                        </a>
                        <ul class="list-unstyled">
                            @can('create-cg-bundle-view')
                                <li><a href="{{route('cgbundle.index')}}">Create CG Bundle</a></li>
                            @endcan

                            @can('cg-bundle-list-view')
                                <li><a href="{{route('cgbundle.list')}}">CG Bundle List</a></li>
                            @endcan

                            @can('sell-cg-bundle-view')
                                <li><a href="{{route('cgbundle.apply')}}">Sell CG Bundle</a></li>
                            @endcan

                            @can('debit-cg-bundle-action')
                                <li><a href="{{route('cgbundle.debit')}}">Debit CG Bundle</a></li>
                            @endcan

                            @can('cg-bundle-transactions-view')
                                <li><a href="{{route('cgbundle.trans')}}">CG Transactions</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcanany

                    @canany('users-list-view','users-search-view','login-attempt-view','resellers-view','dormant-users-view')
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-user-group"></i> <span>Users </span>
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            @can('users-list-view')
                                <li><a href="/users">Users</a></li>
                            @endcan

                            @can('users-search-view')
                                <li><a href="/finduser">Search User(s)</a></li>
                            @endcan
{{--                            <li><a href="/agentpayment">Agent Payment</a></li>--}}
                            @can('login-attempt-view')
                                <li><a href="/loginattempts">Login Attempts</a></li>
                            @endcan
{{--                            <li><a href="/agents">Agents</a></li>--}}
                            @can('resellers-view')
                                <li><a href="/resellers">Resellers</a></li>
                            @endcan
{{--                            <li><a href="/pending_request">Pending Request</a></li>--}}
{{--                            <li><a href="/gmblocked">GM Blocked</a></li>--}}
                            @can('dormant-users-view')
                                <li><a href="/dormantusers">Dormant Users</a></li>
                            @endcan

{{--                            @if(\Illuminate\Support\Facades\Auth::user()->status == "superadmin")--}}
{{--                                <li><a href="/referral_upgrade">Referral Upgrade</a></li>--}}
{{--                            @endif--}}
                        </ul>
                    </li>
                    @endcanany

{{--                    @if(\Illuminate\Support\Facades\Auth::user()->status == "superadmin")--}}
                    @can('payment-gateway-view')
                        <li><a href="{{route('paymentgateway')}}" class="waves-effect"><i class="dripicons-card"></i> <span>Payment Gateway
                            </span></a></li>
                    @endcan

                    @can('sliders-view')

                        <li><a href="{{route('sliders.index')}}" class="waves-effect"><i class="dripicons-bookmark"></i> <span>Slider
{{--                                <span class="badge badge-pill badge-primary float-right">7</span>--}}
                            </span></a></li>
                    @endcan

                    @canany(['airtime-control-view','data-plans-view', 'tv-plan-view', 'electricity-view','other-service-view'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-gear"></i><span> Services Control</span><span
                                    class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                @can('airtime-control-view')
                                    <li><a href="{{route('airtimecontrol')}}">Airtime Control</a></li>
                                @endcan

                                @can('data-plans-view')
                                    <li><a href="{{route('dataplans', 'MTN')}}">All MTN Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['MTN', "1"])}}">HW MTN Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['MTN', "3"])}}">IYII MTN Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['MTN', "4"])}}">OGDAMS MTN Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['MTN', "5"])}}">UZOBEST MTN Data Plans</a></li>

                                    <li><a href="{{route('dataplans', 'AIRTEL')}}">All AIRTEL Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['AIRTEL', "1"])}}">HW AIRTEL Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['AIRTEL', "3"])}}">IYII AIRTEL Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['AIRTEL', "4"])}}">OGDAMS AIRTEL Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['AIRTEL', "5"])}}">UZOBEST AIRTEL Data Plans</a></li>

                                    <li><a href="{{route('dataplans', 'GLO')}}">All GLO Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['GLO', "1"])}}">HW GLO Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['GLO', "3"])}}">IYII GLO Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['GLO', "4"])}}">OGDAMS GLO Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['GLO', "5"])}}">UZOBEST GLO Data Plans</a></li>

                                    <li><a href="{{route('dataplans', '9MOBILE')}}">All 9MOBILE Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['9MOBILE', "1"])}}">HW 9MOBILE Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['9MOBILE', "3"])}}">IYII 9MOBILE Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['9MOBILE', "4"])}}">OGDAMS 9MOBILE Data Plans</a></li>
                                    <li><a href="{{route('server_dataplans', ['9MOBILE', "5"])}}">UZOBEST 9MOBILE Data Plans</a></li>
                                @endcan

                                @can('tv-plan-view')
                                    <li><a href="{{route('tvcontrol')}}">TV Plans</a></li>
                                @endcan

                                @can('electricity-view')
                                    <li><a href="{{route('electricitycontrol')}}">Electricity Control</a></li>
                                @endcan

                                @can('other-service-view')
                                    <li><a href="{{route('otherservices')}}">Other Services</a></li>
                                @endcan
                            </ul>
                        </li>

                    @endcanany

                    @canany(['reseller_airtime-view','reseller_data-view','reseller_tv-view'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-gear"></i><span> Reseller Control</span><span
                                    class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                @can('reseller_airtime-view')
                                <li><a href="{{route('reseller.airtimecontrol')}}">Airtime Control</a></li>
                                @endcan

                                @can('reseller_data-view')
                                        <li><a href="{{route('reseller.dataList', 'MTN')}}">All MTN Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['MTN', "1"])}}">HW MTN Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['MTN', "3"])}}">IYII MTN Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['MTN', "4"])}}">OGDAMS MTN Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['MTN', "5"])}}">UZOBEST MTN Data Plans</a></li>

                                        <li><a href="{{route('reseller.dataList', 'AIRTEL')}}">All AIRTEL Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['AIRTEL', "1"])}}">HW AIRTEL Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['AIRTEL', "3"])}}">IYII AIRTEL Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['AIRTEL', "4"])}}">OGDAMS AIRTEL Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['AIRTEL', "5"])}}">UZOBEST AIRTEL Data Plans</a></li>

                                        <li><a href="{{route('reseller.dataList', 'GLO')}}">All GLO Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['GLO', "1"])}}">HW GLO Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['GLO', "3"])}}">IYII GLO Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['GLO', "4"])}}">OGDAMS GLO Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['GLO', "5"])}}">UZOBEST GLO Data Plans</a></li>

                                        <li><a href="{{route('reseller.dataList', '9MOBILE')}}">All 9MOBILE Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['9MOBILE', "1"])}}">HW 9MOBILE Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['9MOBILE', "3"])}}">IYII 9MOBILE Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['9MOBILE', "4"])}}">OGDAMS 9MOBILE Data Plans</a></li>
                                        <li><a href="{{route('reseller.server_dataList', ['9MOBILE', "5"])}}">UZOBEST 9MOBILE Data Plans</a></li>

                                @endcan

                                @can('reseller_tv-view')
                                    <li><a href="{{route('reseller.tvcontrol')}}">TV Plans</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-card"></i><span> Verification </span><span
                                class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
{{--                            <li><a href="{{route('verification_s1')}}">Server 1</a></li>--}}
                            <li><a href="{{route('verification_s2')}}">Server 2</a></li>
                            <li><a href="{{route('verification_s3')}}">Server 3</a></li>
                        </ul>
                    </li>


                @canany(['report-yearly-view','report-monthly-view','report-daily-view'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-card"></i><span> Reports </span><span
                                class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            @can('report-yearly-view')
                            <li><a href="{{route('report_yearly')}}">Yearly Report</a></li>
                            @endcan

                            @can('report-monthly-view')
                            <li><a href="{{route('report_monthly')}}">Monthly Report</a></li>
                            @endcan

                            @can('report-daily-view')
                            <li><a href="{{route('report_daily')}}">Daily Report</a></li>
                            @endcan
                        </ul>
                    @endcanany

                    {{--                    </li>--}}

                    @can('faq-view')
                        <li><a href="{{route('faqs.index')}}" class="waves-effect"><i class="dripicons-archive"></i>
                            <span>FAQs</span></a></li>
                    @endcan
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- end sidebarinner -->
    </div>
    <!-- Left Sidebar End --><!-- Start right Content here -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <!-- Top Bar Start -->
            <div class="topbar">
                <nav class="navbar-custom">
                    <ul class="list-inline float-right mb-0">
                        <!-- language-->
{{--                        <li class="list-inline-item dropdown notification-list hide-phone">--}}
{{--                            <a class="nav-link dropdown-toggle arrow-none waves-effect text-white" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">English <img src="assets/images/flags/us_flag.jpg" class="ml-2" height="16" alt=""></a>--}}
{{--                            <div class="dropdown-menu dropdown-menu-right language-switch"><a class="dropdown-item" href="#"><img src="assets/images/flags/italy_flag.jpg" alt="" height="16"><span>Italian </span></a><a class="dropdown-item" href="#"><img src="assets/images/flags/french_flag.jpg" alt="" height="16"><span>French </span></a><a class="dropdown-item" href="#"><img src="assets/images/flags/spain_flag.jpg" alt="" height="16"><span>Spanish </span></a><a class="dropdown-item" href="#"><img src="assets/images/flags/russia_flag.jpg" alt="" height="16"><span>Russian</span></a></div>--}}
{{--                        </li>--}}
{{--                        <li class="list-inline-item dropdown notification-list">--}}
{{--                            <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"><i class="dripicons-mail noti-icon"></i> <span class="badge badge-danger noti-icon-badge">5</span></a>--}}
{{--                            <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">--}}
{{--                                <!-- item-->--}}
{{--                                <div class="dropdown-item noti-title">--}}
{{--                                    <h5><span class="badge badge-danger float-right">745</span>Messages</h5>--}}
{{--                                </div>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon"><img src="assets/images/users/avatar-2.jpg" alt="user-img" class="img-fluid rounded-circle"></div>--}}
{{--                                    <p class="notify-details"><b>Charles M. Jones</b><small class="text-muted">Dummy text of the printing and typesetting industry.</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon"><img src="assets/images/users/avatar-3.jpg" alt="user-img" class="img-fluid rounded-circle"></div>--}}
{{--                                    <p class="notify-details"><b>Thomas J. Mimms</b><small class="text-muted">You have 87 unread messages</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon"><img src="assets/images/users/avatar-4.jpg" alt="user-img" class="img-fluid rounded-circle"></div>--}}
{{--                                    <p class="notify-details"><b>Luis M. Konrad</b><small class="text-muted">It is a long established fact that a reader will</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- All--> <a href="javascript:void(0);" class="dropdown-item notify-item border-top">View All</a>--}}
{{--                            </div>--}}
{{--                        </li>--}}
{{--                        <li class="list-inline-item dropdown notification-list">--}}
{{--                            <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"><i class="dripicons-bell noti-icon"></i> <span class="badge badge-success noti-icon-badge">2</span></a>--}}
{{--                            <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">--}}
{{--                                <!-- item-->--}}
{{--                                <div class="dropdown-item noti-title">--}}
{{--                                    <h5><span class="badge badge-danger float-right">87</span>Notification</h5>--}}
{{--                                </div>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon bg-primary"><i class="mdi mdi-cart-outline"></i></div>--}}
{{--                                    <p class="notify-details"><b>Your order is placed</b><small class="text-muted">Dummy text of the printing and typesetting industry.</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon bg-success"><i class="mdi mdi-message"></i></div>--}}
{{--                                    <p class="notify-details"><b>New Message received</b><small class="text-muted">You have 87 unread messages</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon bg-warning"><i class="mdi mdi-glass-cocktail"></i></div>--}}
{{--                                    <p class="notify-details"><b>Your item is shipped</b><small class="text-muted">It is a long established fact that a reader will</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- All--> <a href="javascript:void(0);" class="dropdown-item notify-item border-top">View All</a>--}}
{{--                            </div>--}}
{{--                        </li>--}}
                        <li class="list-inline-item dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                @if(\Illuminate\Support\Facades\Auth::user()->photo)
                                    <img src="{{route('show.avatar', \Illuminate\Support\Facades\Auth::user()->photo)}}" alt="user" class="rounded-circle">
                                @else
                                    <img src="img/mcd_logo.png" alt="user" class="rounded-circle">
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5>Welcome</h5>
                                </div>
                                @can('users-profile-view')
                                    <a class="dropdown-item" href="/profile/{{\Illuminate\Support\Facades\Auth::user()->user_name}}"><i class="mdi mdi-account-circle m-r-5 text-muted"></i> Profile</a>
                                @endcan

                                @can('settings-view')
                                    <a class="dropdown-item" href="{{route('allsettings')}}"><span class="badge badge-success float-right">5</span><i class="mdi mdi-settings m-r-5 text-muted"></i> Settings</a>
                                @endcan
                                <a class="dropdown-item" href="{{route('change_password')}}"><i class="mdi mdi-lock-open-outline m-r-5 text-muted"></i> C. Password</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/logout"><i class="mdi mdi-logout m-r-5 text-muted"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                    <ul class="list-inline menu-left mb-0">
                        <li class="float-left"><button class="button-menu-mobile open-left waves-light waves-effect"><i class="mdi mdi-menu"></i></button></li>
{{--                        <li class="hide-phone app-search">--}}
{{--                            <form role="search" class=""><input type="text" placeholder="Search..." class="form-control"> <a href="#"><i class="fas fa-search"></i></a></form>--}}
{{--                        </li>--}}
                    </ul>
                    <div class="clearfix"></div>
                </nav>
            </div>
            <!-- Top Bar End -->
            <div class="page-content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <div class="btn-group float-right">
                                    <ol class="breadcrumb hide-phone p-0 m-0">
                                        @if (trim($__env->yieldContent('parentPageTitle')))
                                            <li class="breadcrumb-item"><a href="#">@yield('parentPageTitle')</a></li>
                                        @endif
                                        @if (trim($__env->yieldContent('title')))
                                            <li class="breadcrumb-item active">@yield('title')</li>
                                        @endif
                                    </ol>
                                </div>
                                @if (trim($__env->yieldContent('title')))
                                    <h4 class="page-title">@yield('title')</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- end page title end breadcrumb -->
                    @yield('content')
                </div>
                <!-- Page content Wrapper -->
            </div>
            <!-- container -->
            </div>
            <!-- content -->
            <footer class="footer">
                © 2020 PLANETF
            </footer>
        </div>
        <!-- End Right content here -->
    </div>
    <!-- END wrapper --><!-- jQuery  --><script src="/assets/js/jquery.min.js"></script><script src="/assets/js/popper.min.js"></script><script src="/assets/js/bootstrap.min.js"></script><script src="/assets/js/modernizr.min.js"></script><script src="/assets/js/detect.js"></script><script src="/assets/js/fastclick.js"></script><script src="/assets/js/jquery.slimscroll.js"></script><script src="/assets/js/jquery.blockUI.js"></script><script src="/assets/js/waves.js"></script><script src="/assets/js/jquery.nicescroll.js"></script><script src="/assets/js/jquery.scrollTo.min.js"></script><script src="/assets/plugins/chart.js/chart.min.js"></script><script src="/assets/pages/dashboard.js"></script><!-- App js --><script src="/assets/js/app.js"></script>
<script src="/assets/js/jquery.scrollTo.min.js"></script><!-- Required datatable js --><script src="/assets/plugins/datatables/jquery.dataTables.min.js"></script><script src="/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script><!-- Buttons examples --><script src="/assets/plugins/datatables/dataTables.buttons.min.js"></script><script src="/assets/plugins/datatables/buttons.bootstrap4.min.js"></script><script src="/assets/plugins/datatables/jszip.min.js"></script><script src="/assets/plugins/datatables/pdfmake.min.js"></script><script src="/assets/plugins/datatables/vfs_fonts.js"></script><script src="/assets/plugins/datatables/buttons.html5.min.js"></script><script src="/assets/plugins/datatables/buttons.print.min.js"></script><script src="/assets/plugins/datatables/buttons.colVis.min.js"></script><!-- Responsive examples --><script src="/assets/plugins/datatables/dataTables.responsive.min.js"></script><script src="/assets/plugins/datatables/responsive.bootstrap4.min.js"></script><!-- Datatable init js --><script src="/assets/pages/datatables.init.js"></script>
@yield('before-scripts')
</body>
</html>
