@extends('layouts.layouts')
@section('title', 'Create Data Plan')
@section('parentPageTitle', 'Data')

@section('content')

    <div class="col-lg-12">
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

                    <form class="form-horizontal" method="POST" action="{{ route('datanew') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Network </span></div>
                                    <select class="custom-select form-control" name="network">
                                        <option>MTN</option>
                                        <option>GLO</option>
                                        <option>AIRTEL</option>
                                        <option>9MOBILE</option>
                                    </select>
                                </div>


                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Name</span></div>
                                    <input type="text" name="name" placeholder="Enter name" class="form-control" >
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Provider Price </span></div>
                                    <input type="number" name="price" class="form-control" placeholder="Enter Provider price">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Your Price </span></div>
                                    <input type="number" name="pricing" class="form-control" placeholder="Enter Amount">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Coded </span></div>
                                    <input type="text" name="coded" class="form-control" placeholder="Enter unique code">
                                </div>


                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Server </span></div>
                                    <select class="custom-select form-control" name="server">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                    </select>
                                </div>


                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Note </span></div>
                                    <input type="text" name="note" placeholder="Enter Note (Optional)" class="form-control input-lg m-b">
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit" style="align-self: center; align-content: center">Create</button>
                                </div>

                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
