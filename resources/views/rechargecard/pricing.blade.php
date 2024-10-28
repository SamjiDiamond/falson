@extends('layouts.layouts')
@section('title', 'Pricing')
@section('parentPageTitle', 'Recharge Card')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            <strong>{{ session('success') }}</strong>
                        </div>
                        <script type="text/javascript">
                            toastr.options = {
                                closeButton: true,
                                progressBar: true,
                                showMethod: 'slideDown',
                                timeOut: 4000
                            };
                            toastr.success('{{ session('success') }}', 'Success');
                        </script>
                    @endif
                    <p class="text-muted mb-4 font-13">Price List</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Plan Name</th>
                                <th>Setup Fee</th>
                                <th>MTN Price</th>
                                <th>GLO Price</th>
                                <th>AIRTEL Price</th>
                                <th>9MOBILE Price</th>
                                <th>Status</th>
                                <th>Date Modified</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="gradeX">
                                @foreach($data as $da)
                                    <td>{{$da['id']}}</td>
                                    <td class="center">{{$da['plan']}}</td>
                                    <td>{{$da['amount']}}</td>
                                    <td>{{$da['mtn']}}</td>
                                    <td>{{$da['glo']}}</td>
                                    <td>{{$da['airtel']}}</td>
                                    <td>{{$da['ninemobile']}}</td>
                                    <td class="center">
                                        @if($da->status==1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Inactive</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{$da['updated_at']}}
                                    </td>


                                    <td class="center">
                                        @can('reseller_airtime-action')
                                            <a href="{{route('rechargecard.pricingModify',$da->id )}}"
                                               class="btn btn-secondary">Modify</a>
                                        @endcan
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
@endsection
