@extends('layouts.layouts')

@section('content')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Profile</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index.html">Home</a>
                        </li>
                        <li>
                            <a>Users</a>
                        </li>
                        <li class="active">
                            <strong>Profile</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content">
            <div class="row animated fadeInRight">
                <div class="col-md-4">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Profile Detail</h5>
                        </div>
                        <div>
                            <div class="ibox-content no-padding border-left-right">
                                @if($user->photo!=null)
                                    <img alt="image" class="img-responsive" src="{{route('show.avatar', $user->photo}}">
                                @else
                                    <img alt="image" class="img-responsive" src="/img/mcd_logo.png">
                                @endif
                            </div>
                            <div class="ibox-content profile-content">
                                <h4><strong>{{$user->full_name}}</strong> <span class="label label-primary"><i class="fa fa-check"></i> {{$user->status}}</span></h4>
                                <p><i class="fa fa-user"></i> {{$user->user_name}}</p>
                                <p><i class="fa fa-phone"></i> {{$user->phoneno}}</p>
                                <p><i class="fa fa-tag"></i> {{$user->email}}</p>
                                <p><i class="fa fa-calendar-o"></i> {{$user->dob}}</p>
                                <p><i class="fa fa-map-marker"></i> {{$user->address}}</p>
                                <a target="_blank" href="https://firebasestorage.googleapis.com/v0/b/mega-cheap-data.appspot.com/o/doc%2F{{$user->user_name}}.pdf?alt=media&token=b912aad8-f041-4d4e-8d4e-6aaa2e9c0068" class="btn btn-white btn-sm"><i class="fa fa-file"></i> View Document </a>
                                <dl class="dl-horizontal">
                                    <dt>Registered on:</dt> <dd> {{$user->reg_date}}</dd>
                                    <dt>Last Login:</dt> <dd>  {{$user->last_login}}</dd>
                                    <dt>Business Name:</dt> <dd><a href="#" class="text-navy"> {{$user->company_name}}</a> </dd>
                                    <dt>Referral Plan:</dt> <dd>  {{$user->referral_plan}}</dd>
                                    <dt>Version:</dt> <dd> 	v{{$version[0]->version ?? "-"}} </dd>
                                </dl>


                                <dl class="dl-horizontal" >
                                    <dt>Referral:</dt>
                                </dl>
                                <div class="project-people">
                                    @foreach($referrals as $referral)
                                        @if($referral->photo!=null)
                                            <a href="{{$referral->user_name}}"><img alt="image" class="img-circle" src="https://mcd.5starcompany.com.ng/app/avatar/{{$referral->user_name }}.JPG"></a>
                                        @else
                                            <a href="{{$referral->user_name}}">{{$referral->user_name}}</a>
                                        @endif

                                    @endforeach
                                </div>

                                <div class="row m-t-lg" align="center">
                                    <div class="col-md-12">
                                        <h5>
                                            General News
                                        </h5>
                                        <p>
                                            {{$user->gnews}}
                                        </p>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="well">
                                            {{$user->target}}
                                            <div class="pull-right">
                                                <a class="btn btn-xs btn-white"><i class="fa fa-thumbs-up"></i> Target </a>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-warning m-r-sm">{{$user->wallet}}</button>
                                        Wallet
                                        {{--<span><strong>{{$user->wallet}}</strong></span>
                                        <h5>Wallet Balance</h5>--}}
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-warning m-r-sm">{{$user->bonus}}</button>
                                        Bonus
                                        {{--<span><strong>{{$user->bonus}}</strong></span>
                                        <h5>Bonus</h5>--}}
                                    </div>
                                </div>


                                <div class="user-button">
                                    <div class="row">
                                        @if(strpos($user->target, "Agent in progress") !== false)
                                            <div class="col-md-6">

                                                <form method="POST" action="/request_approve">
                                                    @csrf
                                                    <input type="hidden" name="type" value="agent" />
                                                    <input type="hidden" name="user_name" value="{{$user->user_name}}" />
                                                <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fa fa-coffee"></i> Approve Agent</button>
                                                </form>
                                            </div>
                                        @elseif(strpos($user->target, "Reseller in progress") !== false)
                                            <div class="col-md-6">
                                                <form method="POST" action="/request_approve">
                                                    @csrf
                                                    <input type="hidden" name="type" value="reseller" />
                                                    <input type="hidden" name="user_name" value="{{$user->user_name}}" />
                                                <button type="button" class="btn btn-primary btn-sm btn-block"><i class="fa fa-coffee"></i> Approve Reseller</button>
                                                </form>
                                            </div>
                                        @endif

                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-danger btn-sm btn-block"><i class="fa fa-flag-checkered"></i> Flag Fraud</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="wrapper wrapper-content animated fadeInUp">
                                            <ul class="notes">
                                                <li>
                                                    <div>
                                                        <small>Recent</small>
                                                        <h4>Note</h4>
                                                        <p>{{$user->note}} </p>
                                                        <a href="#"><i class="fa fa-trash-o "></i></a>
                                                    </div>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>

                    <div class="row m-t-lg">
                        <div class="col-lg-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-content">
                                    <h5>SMS Log</h5>
                                    <div>
                                        <div class="chat-activity-list">
                                            @foreach($sms as $sms)
                                            <div class="chat-element">
                                                <a href="#" class="pull-left">
                                                    @if($user->photo!=null)
                                                        <img alt="image" class="img-circle" src="{{route('show.avatar', $user->photo}}">
                                                    @else
                                                        <img alt="image" class="img-circle" src="/img/mcd_logo.png">
                                                    @endif
                                                </a>
                                                <div class="media-body ">
                                                    <small class="pull-right text-navy">{{$sms->response}}</small>
                                                    <strong>{{$sms->phoneno}}</strong>
                                                    <p class="m-b-xs">
                                                        {{$sms->message}}
                                                    </p>
                                                    <small class="text-muted">{{$sms->created_at}}</small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="chat-form">
                                        <form name="form" method="POST" action="{{ route('user.sms') }}" role="form">
                                            @csrf
                                            <div class="form-group">
                                                <input type="hidden" name="phoneno" value="{{$user->phoneno}}" />
                                                <input type="hidden" name="user_name" value="{{$user->user_name}}" />
                                                <textarea name="message" class="form-control" placeholder="Message" maxlength="160"></textarea>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-sm btn-primary m-t-n-xs"><strong>Send message</strong></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row m-t-lg">
                        <div class="col-lg-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-content">
                                    <h5>Email Log</h5>
                                    <div>
                                        <div class="chat-activity-list">
                                            @foreach($email as $email)
                                                <div class="chat-element">
                                                    <a href="#" class="pull-left">
                                                        @if($user->photo!=null)
                                                            <img alt="image" class="img-circle" src="{{route('show.avatar', $user->photo}}">
                                                        @else
                                                            <img alt="image" class="img-circle" src="/img/mcd_logo.png">
                                                        @endif
                                                    </a>
                                                    <div class="media-body ">
                                                        <small class="pull-right text-navy">{{$email->response}}</small>
                                                        <strong>{{$email->email}}</strong>
                                                        <p class="m-b-xs">
                                                            {{$email->message}}
                                                        </p>
                                                        <small class="text-muted">{{$email->created_at}}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="chat-form">
                                        <form role="form" method="POST" action="{{ route('user.email') }}">
                                            @csrf
                                            <div class="form-group">
                                                <input type="hidden" name="user_name" value="{{$user->user_name}}" />
                                                <textarea name="message" class="form-control" placeholder="Message"></textarea>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-sm btn-primary m-t-n-xs"><strong>Send message</strong></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row m-t-lg">
                        <div class="col-lg-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-content">
                                    <h5>Push Notification Log</h5>
                                    <div>
                                        <div class="chat-activity-list">
                                            @foreach($push as $pus)
                                                <div class="chat-element">
                                                    <a href="#" class="pull-left">
                                                        @if($user->photo!=null)
                                                            <img alt="image" class="img-circle" src="{{route('show.avatar', $user->photo}}">
                                                        @else
                                                            <img alt="image" class="img-circle" src="/img/mcd_logo.png">
                                                        @endif
                                                    </a>
                                                    <div class="media-body ">
                                                        <small class="pull-right text-navy">sent</small>
                                                        <strong>{{$pus->response}}</strong>
                                                        <p class="m-b-xs">
                                                            {{$pus->message}}
                                                        </p>
                                                        <small class="text-muted">{{$pus->created_at}}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="chat-form">
                                        <form role="form" method="POST" action="{{ route('user.pushnotif') }}">
                                            @csrf
                                            <div class="form-group">
                                                <input type="hidden" name="user_name" value="{{$user->user_name}}" />
                                                <textarea name="message" class="form-control" placeholder="Message"></textarea>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-sm btn-primary m-t-n-xs"><strong>Send message</strong></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-8">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Activites</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#">Config option 1</a>
                                    </li>
                                    <li><a href="#">Config option 2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <div class="row m-t-sm">
                                <div class="col-lg-12">
                                    <div class="panel blank-panel">
                                        <div class="panel-heading">
                                            <div class="panel-options">
                                                <ul class="nav nav-tabs">
                                                    <li class="active"><a href="#tab-1" data-toggle="tab">Transactions({{$tt}})</a></li>
                                                    <li class=""><a href="#tab-2" data-toggle="tab">Wallet({{$tw}})</a></li>
                                                    <li class=""><a href="#tab-3" data-toggle="tab">Charges({{$tpld}})</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="panel-body">

                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab-1">
                                                    <table class="table table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>Status</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                            <th>Prev Balance</th>
                                                            <th>Post Balance</th>
                                                            <th>Desc</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($td as $dat)
                                                            <tr>
                                                                <td>
                                                                    <span class="label label-primary"><i class="fa fa-check"></i> {{$dat->status}}</span>
                                                                </td>
                                                                <td>
                                                                    {{$dat->date}}
                                                                </td>
                                                                <td>
                                                                    {{$dat->amount}}
                                                                </td>
                                                                <td>
                                                                    {{$dat->i_wallet}}
                                                                </td>
                                                                <td>
                                                                    {{$dat->f_wallet}}
                                                                </td>
                                                                <td>
                                                                    <p class="small">
                                                                        {{$dat->description}}
                                                                    </p>
                                                                </td>

                                                            </tr>
                                                        @endforeach


                                                        </tbody>
                                                    </table>

                                                </div>
                                                <div class="tab-pane" id="tab-2">

                                                    <table class="table table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>Status</th>
                                                            <th>Medium</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                            <th>Prev Balance</th>
                                                            <th>Post Balance</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($wd as $dat)
                                                            <tr>
                                                                <td>
                                                                    <span class="label label-primary"><i class="fa fa-check"></i> {{$dat->status}}</span>
                                                                </td>
                                                                <td>
                                                                    {{$dat->medium}}
                                                                </td>
                                                                <td>
                                                                    {{$dat->date}}
                                                                </td>
                                                                <td>
                                                                    {{$dat->amount}}
                                                                </td>
                                                                <td>
                                                                    {{$dat->o_wallet}}
                                                                </td>
                                                                <td>
                                                                    {{$dat->n_wallet}}
                                                                </td>

                                                            </tr>

                                                        @endforeach

                                                        </tbody>
                                                    </table>

                                                </div>

                                                <div class="tab-pane" id="tab-3">

                                                    <table class="table table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>Status</th>
                                                            <th>Type</th>
                                                            <th>Date</th>
                                                            <th>Amount</th>
                                                            <th>Narration</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($pld as $dat)
                                                            <tr>
                                                                <td>
                                                                    <span class="label label-primary"><i class="fa fa-check"></i> successful</span>
                                                                </td>
                                                                <td>
                                                                    {{$dat->type}}
                                                                </td>
                                                                <td>
                                                                    {{$dat->date}}
                                                                </td>
                                                                <td>
                                                                    {{$dat->amount}}
                                                                </td>
                                                                <td>
                                                                    <p class="small">
                                                                        {{$dat->narration}}
                                                                    </p>
                                                                </td>

                                                            </tr>

                                                        @endforeach

                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>


@endsection
