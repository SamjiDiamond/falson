@extends('layouts.layouts')
@section('title', 'Profile')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">

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

        <div class="col-12">
            <div class="card">
                <div class="card-body met-pro-bg">
                    <div class="met-profile" >
                        <div class="row" style='background-image: url("/assets/images/pattern.png"); padding: 20px; color: white'>
                            <div class="col-lg-4 align-self-center mb-3 mb-lg-0">
                                <div class="met-profile-main">
                                    <div class="met-profile-main-pic">
                                        @if($user->photo!=null)
                                            <img src="{{route('show.avatar', $user->photo)}}" alt="img" class="img img-thumbnail">
                                        @else
                                            <img alt="image" class="img img-thumbnail" src="/img/mcd_logo.png">
                                        @endif
                                        <span class="fro-profile_main-pic-change"><i class="fas fa-camera"></i></span></div>
                                    <div class="met-profile_user-detail">
                                        <h4 class="met-user-name" style="color: white">{{$user->user_name}}</h4>
                                        <p class="mb-0 met-user-name-post">{{$user->full_name}}</p>
                                        <p class="mb-0 met-user-name-post text-muted">{{$user->company_name}} ({{$user->status}})</p>
                                    </div>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-4 ml-auto">
                                <ul class="list-unstyled personal-detail">
                                    <li class=""><i class="dripicons-phone mr-2 text-info font-18"></i> <b>Phone </b> : {{$user->phoneno}}</li>
                                    <li class="mt-2"><i class="dripicons-mail text-info font-18 mt-2 mr-2"></i> <b>Email </b> : {{$user->email}}</li>
                                    <li class="mt-2"><i class="dripicons-location text-info font-18 mt-2 mr-2"></i> <b>Location</b> : {{$user->address}}</li>
                                    <li class="mt-2"><i class="dripicons-calendar text-info font-18 mt-2 mr-2"></i> <b>DOB</b> : {{$user->dob}}</li>
                                    <li class="mt-2"><i class="dripicons-calendar text-info font-18 mt-2 mr-2"></i> <b>Reg. Date</b> : {{$user->reg_date}}</li>
                                    <li class="mt-2"><i class="dripicons-calendar text-info font-18 mt-2 mr-2"></i> <b>Last Login</b> : {{$user->last_login}}</li>
{{--                                    <li class="mt-2"><i class="dripicons-wallet text-info font-18 mt-2 mr-2"></i> <b>Virtual Account</b> : {{$user->account_number}}</li>--}}
                                </ul>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                    <!--end f_profile-->
                </div>
                <!--end card-body-->
                <div class="card-body">
                    <ul class="nav nav-pills mb-0" id="pills-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="general_detail_tab" data-toggle="pill" href="#general_detail">General</a></li>
                        <li class="nav-item"><a class="nav-link" id="activity_detail_tab" data-toggle="pill" href="#activity_detail">Transactions</a></li>
                        <li class="nav-item"><a class="nav-link" id="portfolio_detail_tab" data-toggle="pill" href="#portfolio_detail">Wallet</a></li>
                        <li class="nav-item"><a class="nav-link" id="settings_detail_tab" data-toggle="pill" href="#settings_detail">Charges</a></li>
                        <li class="nav-item"><a class="nav-link" id="email_tab" data-toggle="pill" href="#email_detail">Email</a></li>
{{--                        <li class="nav-item"><a class="nav-link" id="sms_tab" data-toggle="pill" href="#sms_detail">SMS</a></li>--}}
                        <li class="nav-item"><a class="nav-link" id="pushnoti_tab" data-toggle="pill" href="#pushnoti_detail">Push Notification</a></li>
                        <li class="nav-item"><a class="nav-link" id="login_tab" data-toggle="pill" href="#login_detail">Login Attempts</a></li>
                        <li class="nav-item"><a class="nav-link" id="information_tab" data-toggle="pill" href="#information">Information</a></li>
                        <li class="nav-item"><a class="nav-link" id="vaccount_tab" data-toggle="pill" href="#vaccount_detail">Virtual Accounts</a></li>
                        <li class="nav-item"><a class="nav-link" id="cg_tab" data-toggle="pill" href="#cg_detail">CG Wallets</a></li>
{{--                        <li class="nav-item"><a class="nav-link" id="crypto_tab" data-toggle="pill" href="#crypto_detail">Crypto Request</a></li>--}}
                    </ul>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-12">
            <div class="tab-content detail-list" id="pills-tabContent">
                <div class="tab-pane fade show active" id="general_detail">
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-body">
{{--                                    <div class="d-flex justify-content-between">--}}
{{--                                        <img src="../assets/images/widgets/monthly-re.png" alt="" height="75">--}}
{{--                                        <div class="align-self-center">--}}
{{--                                            <h2 class="mt-0 mb-2 font-weight-semibold">$955<span class="badge badge-soft-success font-11 ml-2"><i class="fas fa-arrow-up"></i> 8.6%</span></h2>--}}
{{--                                            <h4 class="title-text mb-0">Monthly Revenue</h4>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="d-flex justify-content-between bg-purple p-3 mt-3 rounded">
                                        <div>
                                            <h4 class="mb-1 font-weight-semibold text-white">&#8358;{{number_format($user->wallet)}}</h4>
                                            <p class="text-white mb-0">Wallet Balance</p>
                                        </div>
{{--                                        <div>--}}
{{--                                            <h4 class="mb-1 font-weight-semibold text-white">&#8358;{{$user->bonus}}</h4>--}}
{{--                                            <p class="text-white mb-0">Bonus Balance</p>--}}
{{--                                        </div>--}}
                                    </div>

{{--                                    <div class="d-flex justify-content-between bg-purple p-3 mt-3 rounded">--}}
{{--                                        <div>--}}
{{--                                            <h4 class="mb-1 font-weight-semibold text-white">&#8358;{{number_format($user->agent_commision)}}</h4>--}}
{{--                                            <p class="text-white mb-0">Commission</p>--}}
{{--                                        </div>--}}
{{--                                        <div>--}}
{{--                                            <h4 class="mb-1 font-weight-semibold text-white">{{$user->points}}</h4>--}}
{{--                                            <p class="text-white mb-0">Points</p>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                </div>
                                <!--end card-body-->
                            </div>

                            <!--end card-->
                            <div class="card">
                                <div class="card-body dash-info-carousel">
{{--                                    <h4 class="mt-0 header-title mb-4">New Leads</h4>--}}
                                    <div id="carousel_1" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <div class="media">
                                                    <div class="media-body align-self-center">
                                                        <h4 class="mt-0 mb-1 title-text text-dark">{{$user->gnews}}</h4>
                                                        <p class="text-muted mb-0">Annoucement</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="carousel-item">
                                                <div class="media">
                                                    <div class="media-body align-self-center">
                                                        <h4 class="mt-0 mb-1 title-text">{{$user->target}}</h4>
                                                        <p class="text-muted mb-0">Target</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a class="carousel-control-prev" href="#carousel_1" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a><a class="carousel-control-next" href="#carousel_1" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span> <span class="sr-only">Next</span></a>
                                    </div>
                                </div>
                            </div>
                            <!--end card-->

                            <div class="card">
                                <div class="card-body dash-info-carousel">

                                    <div class="d-flex justify-content-between">
{{--                                        @if(strpos($user->target, "Agent in progress") !== false)--}}
{{--                                            <div class="col-md-6">--}}

{{--                                                <form method="POST" action="/request_approve">--}}
{{--                                                    @csrf--}}
{{--                                                    <input type="hidden" name="type" value="agent" />--}}
{{--                                                    <input type="hidden" name="user_name" value="{{$user->user_name}}" />--}}
{{--                                                    <button type="submit" class="btn btn-gradient-primary btn-sm">Approve Agent</button>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        @elseif(strpos($user->target, "Reseller in progress") !== false)--}}
{{--                                            <div class="col-md-6">--}}
{{--                                                <form method="POST" action="/request_approve">--}}
{{--                                                    @csrf--}}
{{--                                                    <input type="hidden" name="type" value="reseller" />--}}
{{--                                                    <input type="hidden" name="user_name" value="{{$user->user_name}}" />--}}
{{--                                                    <button type="button" class="btn btn-gradient-primary btn-sm">Approve Reseller</button>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        @endif--}}

                                        <a href="{{route('adminBannUnbann', $user->id)}}" type="button" class="btn @if($user->fraud == "" || $user->fraud == null) btn-gradient-danger @else btn-gradient-success  @endif  btn-sm">@if($user->fraud == "" || $user->fraud == null)Bann User @else UnBann User  @endif</a>

                                        @if($user->status == "admin" || $user->status == "superadmin")
                                            <a href="{{route('adminPasswordReset', $user->id)}}" class="btn btn-gradient-danger btn-sm">Reset Admin Password</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!--end card-->

                            @if($user->referral!=null)
                            <div class="card profile-card">
                                <div class="card-body p-0">
                                    <div class="media p-3 align-items-center">
                                        <img src="../assets/images/users/user-4.jpg" alt="user" class="rounded-circle thumb-xl">
                                        <div class="media-body ml-3 align-self-center">
                                            <h5 class="pro-title mt-0">{{$user->referral}} <span class="badge badge-warning font-10">New Agent</span></h5>
                                            <p class="mb-2 text-muted">@SaraHopkins.com</p>
                                            <ul class="list-inline list-unstyled profile-socials mb-0">
                                                <li class="list-inline-item"><a href="#" class=""><i class="fab fa-facebook-f bg-soft-primary"></i></a></li>
                                                <li class="list-inline-item"><a href="#" class=""><i class="fab fa-twitter bg-soft-secondary"></i></a></li>
                                                <li class="list-inline-item"><a href="#" class=""><i class="fab fa-dribbble bg-soft-pink"></i></a></li>
                                            </ul>
                                        </div>
                                        <div class="action-btn"><a href="#" class=""><i class="fas fa-pen text-info mr-2"></i></a> <a href="#" class=""><i class="fas fa-trash-alt text-danger"></i></a></div>
                                    </div>
                                </div>
                                <!--end card-body-->
                            </div>
                            <!--end card-->
                            @endif

                        </div>
                        <!--end col-->
                        <div class="col-lg-8">
                            <div class="card">
                                <div>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="p-4 bg-light text-center align-item-center">
                                                <h1 class="font-weight-semibold">{{($tt/100)}}</h1>
                                                <h4 class="header-title">Overall Performance</h4>
                                            </div>
                                            <ul class="list-unstyled mt-3">
                                                <li class="mb-2">
                                                    <span class="text-dark">Data</span> <small class="float-right text-muted ml-3 font-14">{{$tdt}}</small>
                                                    <div class="progress mt-2" style="height:5px;">
                                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{($tdt/100)}}%; border-radius:5px;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="text-dark">Airtime</span> <small class="float-right text-muted ml-3 font-14">{{$tat}}</small>
                                                    <div class="progress mt-2" style="height:5px;">
                                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{($tat/100)}}%; border-radius:5px;" aria-valuenow="18" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="text-dark">TV</span> <small class="float-right text-muted ml-3 font-14">{{$tpt}}</small>
                                                    <div class="progress mt-2" style="height:5px;">
                                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{($tpt/100)}}%; border-radius:5px;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="text-dark">Recharge Card</span> <small class="float-right text-muted ml-3 font-14">{{$tct}}</small>
                                                    <div class="progress mt-2" style="height:5px;">
                                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{($tct/100)}}%; border-radius:5px;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <span class="text-dark">Result Checker</span> <small class="float-right text-muted ml-3 font-14">{{$trt}}</small>
                                                    <div class="progress mt-2" style="height:5px;">
                                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{($trt/100)}}%; border-radius:5px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                    <!--end card-->
                                </div>
                                <!--end col-->
                            </div>

                            <div class="button-list btn-social-icon">
                               <b>Referrals</b> :

                                @foreach($referrals as $referral)
                                    @if($referral->photo!=null)
                                        <a href="{{$referral->user_name}}" class="btn btn-pink btn-circle ml-2">
                                            <img alt="image" class="card-img img" src="https://mcd.5starcompany.com.ng/app/avatar/samji.JPG">
                                            {{$referral->user_name}}
                                        </a>
                                    @else
                                        <a href="{{$referral->user_name}}" class="btn btn-pink btn-circle ml-2">{{$referral->user_name}}</a>
                                    @endif

                                @endforeach
                            </div>
                            <!--end card-->
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end general detail-->
                <div class="tab-pane fade" id="activity_detail">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">Transactions</h4>
                                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>id</th>
                                                <th>Username</th>
                                                <th>Amount</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>I. Wallet</th>
                                                <th>F. Wallet</th>
                                                <th>I.P</th>
                                                <th>Server</th>
                                                <th>Ref</th>
                                                <th>Date</th>
                                                <th>Note</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($td as $dat)
                                                <tr>
                                                    <td>{{$dat->id}}</td>
                                                    <td>{{$dat->user_name}}
                                                    </td>
                                                    <td>{{$dat->amount}}</td>
                                                    <td>{{$dat->description}}</td>
                                                    <td class="center">

                                                        @if($dat->status=="delivered" || $dat->status=="Delivered" || $dat->status=="ORDER_RECEIVED" || $dat->status=="ORDER_COMPLETED")
                                                            <span class="badge badge-success">{{$dat->status}}</span>
                                                        @elseif($dat->status=="not_delivered" || $dat->status=="Not Delivered" || $dat->status=="Error" || $dat->status=="ORDER_CANCELLED" || $dat->status=="Invalid Number" || $dat->status=="Unsuccessful")
                                                            <span class="badge badge-warning">{{$dat->status}}</span>
                                                        @else
                                                            <span class="badge badge-info">{{$dat->status}}</span>
                                                        @endif

                                                    </td>
                                                    <td>{{$dat->i_wallet}}</td>
                                                    <td>{{$dat->f_wallet}}</td>
                                                    <td>{{$dat->ip_address}}</td>
                                                    <td>{{$dat->server}}</td>
                                                    <td>{{$dat->ref}}</td>
                                                    <td>{{$dat->date}}</td>
                                                    <td>{{$dat->extra}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        {{ $td->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!--end row-->
                </div>
                <!--end education detail-->
                <div class="tab-pane fade" id="portfolio_detail">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">Wallet Table</h4>
                                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>id</th>
                                                <th>Username</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Medium</th>
                                                <th>Reference</th>
                                                <th>O. Wallet</th>
                                                <th>N. Wallet</th>
                                                <th>Version</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($wd as $dat)
                                                <tr>
                                                    <td>{{$dat->id}}</td>
                                                    <td>{{$dat->user_name}}</td>
                                                    <td>&#8358;{{$dat->amount}}</td>
                                                    <td>{{$dat->status}}</td>
                                                    <td>{{$dat->medium}}</td>
                                                    <td>{{$dat->ref}}</td>
                                                    <td>&#8358;{{$dat->o_wallet}}</td>
                                                    <td>&#8358;{{$dat->n_wallet}}</td>
                                                    <td>{{$dat->version}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        {{ $wd->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!--end row-->
                </div>
                <!--end portfolio detail-->
                <div class="tab-pane fade" id="settings_detail">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    {{--                    <h4 class="mt-0 header-title">General Market History</h4>--}}
                                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>id</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Narration</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($pld as $dat)
                                                <tr>
                                                    <td>{{$dat->id}}</td>
                                                    <td>
                                                        @if($dat->type=="income")
                                                            <span class="badge badge-success">{{$dat->type}}</span>
                                                        @else
                                                            <span class="badge badge-warning">{{$dat->type}}</span>
                                                        @endif
                                                    </td>
                                                    <td>&#8358;{{$dat->amount}}</td>
                                                    <td> {{$dat->narration}} </td>
                                                    <td>{{$dat->date}}</td>
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
                </div>
                <!--end settings detail-->

                <div class="tab-pane fade" id="email_detail">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="general-label">
                                        <form class="form-horizontal" method="POST" action="{{ route('user.email') }}">
                                            @csrf
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <div class="input-group mt-2">
                                                        <input type="hidden" name="user_name" value="{{$user->user_name}}" />
                                                        <textarea name="message" class="form-control" aria-label="With textarea" placeholder="Message"></textarea>
                                                    </div>
                                                    <div class="input-group mt-2">
                                                        <button class="btn btn-gradient-primary waves-effect waves-light" type="submit" style="align-self: center; align-content: center"><i class="fa fa-paper-plane"></i> Send Message</button>
                                                    </div>
                                                    @error('ref')
                                                    <div class="alert alert-danger alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    {{--                    <h4 class="mt-0 header-title">General Market History</h4>--}}
                                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>Email</th>
                                                <th>Message</th>
                                                <th>Response</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($email as $email)
                                                <tr>
                                                    <td>{{$email->email}}</td>
                                                    <td> {{$email->message}} </td>
                                                    <td>{{$email->response}}</td>
                                                    <td>{{$email->created_at}}</td>
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
                </div>
                <!--end email detail-->

                <div class="tab-pane fade" id="sms_detail">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="general-label">
                                        <form class="form-horizontal" method="POST" action="{{ route('user.sms') }}">
                                            @csrf
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <div class="input-group mt-2">
                                                        <input type="hidden" name="phoneno" value="{{$user->phoneno}}" />
                                                        <input type="hidden" name="user_name" value="{{$user->user_name}}" />
                                                        <textarea name="message" class="form-control" aria-label="With textarea" placeholder="Message" maxlength="160"></textarea>
                                                    </div>
                                                    <div class="input-group mt-2">
                                                        <button class="btn btn-gradient-primary waves-effect waves-light" type="submit" style="align-self: center; align-content: center"><i class="fa fa-paper-plane"></i> Send Message</button>
                                                    </div>
                                                    @error('ref')
                                                    <div class="alert alert-danger alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    {{--                    <h4 class="mt-0 header-title">General Market History</h4>--}}
                                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>Phone No</th>
                                                <th>Message</th>
                                                <th>Response</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($sms as $sms)
                                                <tr>
                                                    <td>{{$sms->phoneno}}</td>
                                                    <td> {{$sms->message}} </td>
                                                    <td>{{$sms->response}}</td>
                                                    <td>{{$sms->created_at}}</td>
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
                </div>
                <!--end sms detail-->

                <div class="tab-pane fade" id="pushnoti_detail">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="general-label">
                                        <form class="form-horizontal" method="POST" action="{{ route('user.pushnotif') }}">
                                            @csrf
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <div class="input-group mt-2">
                                                        <input type="hidden" name="user_name" value="{{$user->user_name}}" />
                                                        <textarea name="message" class="form-control" aria-label="With textarea" placeholder="Message" maxlength="160"></textarea>
                                                    </div>
                                                    <div class="input-group mt-2">
                                                        <button class="btn btn-gradient-primary waves-effect waves-light" type="submit" style="align-self: center; align-content: center"><i class="fa fa-paper-plane"></i> Send Message</button>
                                                    </div>
                                                    @error('ref')
                                                    <div class="alert alert-danger alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end row-->
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    {{--                    <h4 class="mt-0 header-title">General Market History</h4>--}}
                                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>Message</th>
                                                <th>Response</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($push as $pus)
                                                <tr>
                                                    <td> {{$pus->message}} </td>
                                                    <td>{{$pus->response}}</td>
                                                    <td>{{$pus->created_at}}</td>
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
                </div>
                <!--end push detail-->


                <div class="tab-pane fade" id="login_detail">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    {{--                    <h4 class="mt-0 header-title">General Market History</h4>--}}
                                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>IP Address</th>
                                                <th>Device</th>
                                                <th>Status</th>
                                                <th>Provider</th>
                                                <th>Date</th>
                                                <th>City</th>
                                                <th>Region</th>
                                                <th>Country</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($login as $logins)
                                                <tr>
                                                    <td> {{$logins->ip_address}} </td>
                                                    <td>{{$logins->device}}</td>
                                                    <td>{{$logins->status}}</td>
                                                    <td>{{$logins->provider}}</td>
                                                    <td>{{$logins->created_at}}</td>
                                                    <td>{{$logins->city}}</td>
                                                    <td>{{$logins->region}}</td>
                                                    <td>{{$logins->country}}</td>
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
                </div>
                <!--end login detail-->


                <div class="tab-pane fade" id="crypto_detail">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    {{--                    <h4 class="mt-0 header-title">General Market History</h4>--}}
                                    {{--                    <p class="text-muted mb-4 font-13">Use <code>pencil icon</code> to view user profile.</p>--}}
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
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
                                                    <td>{{$cryptos->transid}} </td>
                                                    <td>BTC</td>
                                                    <td>{{$cryptos->address}}</td>
                                                    <td>{{$cryptos->receive_fee}}</td>
                                                    <td>{{$cryptos->created_at}}</td>
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
                </div>

                <div class="tab-pane fade" id="cg_detail">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">CG Wallets</h4>
                                    <p class="text-muted mb-4 font-13">Find the list of user CG wallets below</p>
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Balance (In GB)</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($cgs as $cg)
                                                <tr>
                                                    <td>{{$cg->name}} </td>
                                                    <td>{{$cg->balance}}</td>
                                                    <td>
                                                        @if($cg->status=="1")
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-warning">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>{{$cg->created_at}}</td>
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
                </div>

                <div class="tab-pane fade" id="vaccount_detail">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">User Virtual Accounts</h4>
                                    <p class="text-muted mb-4 font-13">Find the list of virtual account below</p>
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>Account Name</th>
                                                <th>Account Number</th>
                                                <th>Bank Name</th>
                                                <th>Provider</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($vaccounts as $vacct)
                                                <tr>
                                                    <td>{{$vacct->account_name}} </td>
                                                    <td>{{$vacct->account_number}}</td>
                                                    <td>{{$vacct->bank_name}}</td>
                                                    <td>{{$vacct->provider}}</td>
                                                    <td>{{$vacct->created_at}}</td>
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
                </div>

                <div class="tab-pane fade" id="information">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-10">
                                            <h4 class="mt-0 header-title">User Information</h4>
                                        </div>

                                        <div class="col-2">
                                            <form class="form-horizontal" method="POST" action="{{ route('userPasswordReset') }}">
                                                @csrf
                                                <input type="hidden" name="id" class="form-control" value="{{$user->id}}">
                                                <button class="btn btn-gradient-danger btn-large ml-5" type="submit" style="align-self: center; align-content: center">Password Reset</button>
                                            </form>
                                        </div>

                                    </div>


                                    <p class="text-muted mb-4 font-13">Edit User information <code>below</code>.</p>

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

                                    <form class="form-horizontal" method="POST" action="{{ route('updateProfile') }}">
                                        @csrf
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <div class="input-group mt-2">
                                                    <div class="input-group-prepend"><span class="input-group-text">Full Name</span></div>
                                                    <input type="hidden" name="id" class="form-control" value="{{$user->id}}">
                                                    <input type="text" name="full_name" placeholder="Enter Full Name" class="form-control" value="{{$user->full_name}}">
                                                </div>

                                                <div class="input-group mt-2">
                                                    <div class="input-group-prepend"><span class="input-group-text">Business Name</span></div>
                                                    <input type="text" name="company_name" class="form-control" placeholder="Enter Business Name" value="{{$user->company_name}}">
                                                </div>


                                                <div class="input-group mt-2">
                                                    <div class="input-group-prepend"><span class="input-group-text">BVN</span></div>
                                                    <input type="text" name="bvn" class="form-control" placeholder="Enter BVN" value="{{$user->bvn}}">
                                                </div>


                                                <div class="input-group mt-2">
                                                    <div class="input-group-prepend"><span class="input-group-text">Email</span></div>
                                                    <input type="email" name="email" class="form-control" placeholder="Enter Email" value="{{$user->email}}">
                                                </div>


                                                <div class="input-group mt-2">
                                                    <div class="input-group-prepend"><span class="input-group-text">Phone Number</span></div>
                                                    <input type="text" name="phoneno" class="form-control" placeholder="Enter phone number" value="{{$user->phoneno}}">
                                                </div>

                                                <div class="input-group mt-2">
                                                    <div class="input-group-prepend"><span class="input-group-text">Status</span></div>
                                                    <select class="custom-select form-control" name="status">
                                                        <option value="client" @if($user->status == "client") selected @endif >Client</option>
                                                        <option value="reseller" @if($user->status == "reseller") selected @endif>Reseller</option>
                                                        <option value="superadmin" @if($user->status == "superadmin") selected @endif>Superadmin</option>
                                                        <option value="admin" @if($user->status == "admin") selected @endif>admin</option>
                                                    </select>
                                                </div>

                                                <div class="input-group mt-2">
                                                    <div class="input-group-prepend"><span class="input-group-text">Address</span></div>
                                                    <input type="text" name="address" class="form-control" placeholder="Enter address" value="{{$user->address}}">
                                                </div>


                                                <div class="input-group mt-2">
                                                    <div class="input-group-prepend"><span class="input-group-text">Target</span></div>
                                                    <textarea name="target" rows="3" class="form-control" placeholder="Enter target">{{$user->target}}</textarea>
                                                </div>

                                                <div class="input-group mt-5" style="align-content: center">
                                                    <button class="btn btn-gradient-primary btn-large mr-4" type="submit" style="align-self: center; align-content: center">Update Profile</button>
                                                </div>

                                            </div>
                                        </div>
                                        <!--end row-->
                                    </form>

                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>



            </div>
            <!--end tab-content-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@stop
