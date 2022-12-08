@extends('layouts.layouts')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Recharge Card</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="/home">Home</a>
                </li>
                <li>
                    <a>Recharge Card</a>
                </li>
                <li class="active">
                    <strong>Customized Recharge Card</strong>
                </li>
            </ol>
        </div>

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5></h5>
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

                            <table class="table table-striped table-bordered table-hover dataTables-example" >
                                <div class="col-lg-12">

                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('rechargecard') }}">
                                        @csrf
                                    <div class="form-group">

                                        <div align="center" class="col-sm-10 col-lg-12">

                                            <div class="form-group @error('username') has-error @enderror">
                                                <label class="col-sm-2 control-label">Username</label>
                                                <div class="col-sm-10">
                                            <div class="input-group m-b"><span class="input-group-addon">@</span> <input type="text" name="user_name" placeholder="Enter Username" class="form-control @error('username') is-invalid @enderror"></div>
                                                    @error('username')
                                                    <div class="alert alert-danger alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group @error('quantity') has-error @enderror">
                                                <label class="col-sm-2 control-label">Quantity</label>
                                                <div class="col-sm-10">
                                            <div class="input-group m-b"><span class="input-group-addon">#</span> <input type="number" name="quantity" class="form-control" placeholder="Enter Amount" min="1" max="100"></div>
                                                    @error('quantity')
                                                    <div class="alert alert-danger alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group @error('amount') has-error @enderror">
                                                <label class="col-sm-2 control-label">Amount</label>

                                                <div class="col-sm-10"><select class="form-control m-b" name="amount">
                                                        <option>100</option>
                                                        <option>200</option>
                                                        <option>500</option>
                                                    </select>

                                                </div>
                                                @error('amount')
                                                <div class="alert alert-danger alert-dismissable">
                                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group"><label class="col-sm-2 control-label">Network</label>

                                                <div class="col-sm-10"><select class="form-control m-b" name="network">
                                                        <option value="01">MTN</option>
                                                        <option value="02">GLO</option>
                                                        <option value="03">ETISALAT</option>
                                                        <option value="04">AIRTEL</option>
                                                    </select>

                                                </div>
                                            </div>

                                            <button class="btn btn-primary" type="submit">Add Fund</button>

                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

            @endsection
