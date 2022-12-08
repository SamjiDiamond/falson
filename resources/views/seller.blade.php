@extends('layouts.layouts')
@section('title', 'Reseller')
@section('parentPageTitle', 'Reseller')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(isset($status))
                        <h6 class="alert alert-danger">{{$status}}</h6>
                    @endif
                    <p class="text-muted mb-4 font-13">Reseller/Agent Controller</p>
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr>
                                <th>Username</th>
                                <th>Api-Key</th>
                                <th>Api-Status</th>
                                <th>Switch</th>
                                <th>Generate Api</th>
                            </tr>
                            dbteijfi
                            </thead>
                            <tbody>
                            @foreach($reseller as $seller)
                            <tr>
                                <link rel="stylesheet" href="{{asset('style.css')}}">
                                <!--Only for demo purpose - no need to add.-->
                                <link rel="stylesheet" href="{{asset('demo.css')}}"/>
                                <td> {{$seller->user_name}} </td>
                                <td> {{$seller->api_key}}</td>
                                {{--                                <td><a href="{{route('editpayment', $pay->id)}}"--}}
                                {{--                                       {{$pay->value}}class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a>--}}
                                {{--                                </td>--}}
                                <td>@if($seller->fraud=="")<h6 class="btn-success">Active</h6>@else<h6
                                        class="btn-warning">
                                        Block</h6> @endif</td>
                                <td>
                                    <label class="toggleSwitch nolabel">
                                        <input type="checkbox" name="status" value="0" id="myCheckBox"
                                               {{$seller->fraud ==""?'checked':''}}
                                               {{--                                            @if($pay->status==1?'checked':'')--}}
                                               onclick="window.location='{{route('block', $seller->id)}}'"/>
                                        <!--                                            <button  type="submit" class="btn-info col-lg">Update</button>-->
                                        <span>
                                                <span>Non</span>
                                                <span>Active</span>
                                             </span>

                                        <a></a>
                                    </label>
                                </td>
                                <td>
                                    <a href="{{route('apikey', $seller->id)}}" class="btn btn-outline-primary">Generate
                                        Api-key</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$reseller->links()}}

                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
