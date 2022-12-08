@extends('layouts.layouts')
@section('title', 'Payment Gateway')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4 font-13">Payment Gateway Controller</p>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>Payment Gateway</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Switch</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($payment as $pay)
                            <tr>
                                <link rel="stylesheet" href="{{asset('style.css')}}">
                                <!--Only for demo purpose - no need to add.-->
                                <link rel="stylesheet" href="{{asset('demo.css')}}"/>
                                <td> {{$pay->name}} </td>
                                <td><a href="{{route('editpayment', $pay->id)}}"
                                       {{$pay->value}}class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a>
                                </td>
                                <td>@if($pay->status==1)<h6 class="btn-success">Active</h6>@else<h6 class="btn-warning">
                                        Inactive</h6> @endif</td>
                                <td>
                                    <label class="toggleSwitch nolabel">
                                        <input type="checkbox" name="status" value="0" id="myCheckBox"
                                               {{$pay->status ==1?'checked':''}}
                                               {{--                                            @if($pay->status==1?'checked':'')--}}
                                               onclick="window.location='{{route('switch', $pay->id)}}'"/>
                                        <!--                                            <button  type="submit" class="btn-info col-lg">Update</button>-->
                                        <span>
                                                <span>OFF</span>
                                                <span>ON</span>
                                            </span>

                                        <a></a>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
