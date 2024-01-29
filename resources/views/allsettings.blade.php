@extends('layouts.layouts')
@section('title', 'Settings Control')
@section('parentPageTitle', 'Settings')

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
                    <p class="text-muted mb-4 font-13">Settings List</p>

                        <div class="table-responsive mb-4">
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Date Modified</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="gradeX">
                            @foreach($data as $da)
                                <td class="center">{{str_replace("_", " ",$da['name'])}}</td>
                                <td>{{$da['value']}}</td>

                                <td>

                                    {{$da['updated_at']}}</option>
                                </td>

                                <td class="center">
                                    <a href="{{route('allsettingsEdit',$da->id )}}"  class="btn btn-secondary">Modify</a>
                                </td>

                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                        </div>

                        <div class="row">
                            <a href="{{route('plansRefresh', "data")}}" class="btn btn-danger mr-3">Refresh Data Plans</a>
                            <a href="{{route('plansRefresh', "tv")}}" class="btn btn-danger mr-3">Refresh TV Plans</a>
                            <a href="{{route('plansRefresh', "data_hw")}}" class="btn btn-danger mr-3">Refresh HW Data Plans</a>
{{--                            <a href="{{route('plansRefresh', "electricity")}}" class="btn btn-danger mr-3">Refresh Electricity Plans</a>--}}
                        </div>

                        <div class="row mt-3">
                            <a href="{{route('plansRefresh', "data_iyii_mtn")}}" class="btn btn-gradient-primary mr-3">Refresh IYII Data Plans (MTN)</a>
                            <a href="{{route('plansRefresh', "data_iyii_glo")}}" class="btn btn-gradient-primary mr-3">Refresh IYII Data Plans (GLO)</a>
                            <a href="{{route('plansRefresh', "data_iyii_airtel")}}" class="btn btn-gradient-primary mr-3">Refresh IYII Data Plans (AIRTEL)</a>
                            <a href="{{route('plansRefresh', "data_iyii_9mobile")}}" class="btn btn-gradient-primary mr-3">Refresh IYII Data Plans (9MOBILE)</a>
                        </div>

                        <div class="row mt-3">
                            <a href="{{route('plansRefresh', "data_ogdams_mtn")}}" class="btn btn-gradient-secondary mr-3">Refresh OGDAMS Data Plans (MTN)</a>
                            <a href="{{route('plansRefresh', "data_ogdams_glo")}}" class="btn btn-gradient-secondary mr-3">Refresh OGDAMS Data Plans (GLO)</a>
                            <a href="{{route('plansRefresh', "data_ogdams_airtel")}}" class="btn btn-gradient-secondary mr-3">Refresh OGDAMS Data Plans (AIRTEL)</a>
                            <a href="{{route('plansRefresh', "data_ogdams_9mobile")}}" class="btn btn-gradient-secondary mr-3">Refresh OGDAMS Data Plans (9MOBILE)</a>
                        </div>

                        <div class="row mt-3">
                            <a href="{{route('plansRefresh', "data_uzobest_mtn")}}" class="btn btn-gradient-purple mr-3">Refresh UZOBEST Data Plans (MTN)</a>
                            <a href="{{route('plansRefresh', "data_uzobest_glo")}}" class="btn btn-gradient-purple mr-3">Refresh UZOBEST Data Plans (GLO)</a>
                            <a href="{{route('plansRefresh', "data_uzobest_airtel")}}" class="btn btn-gradient-purple mr-3">Refresh UZOBEST Data Plans (AIRTEL)</a>
                            <a href="{{route('plansRefresh', "data_uzobest_9mobile")}}" class="btn btn-gradientphp -purple mr-3">Refresh UZOBEST Data Plans (9MOBILE)</a>
                        </div>



                </div>
            </div>
        </div>
    </div>
@endsection
