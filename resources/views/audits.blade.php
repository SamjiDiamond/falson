@extends('layouts.layouts')
@section('title', 'Audits')
@section('parentPageTitle', 'System Control')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>User</th>
                                <th>Model</th>
                                <th>Action</th>
                                <th>Note</th>
                                <th>Time</th>
                                <th>Old Values</th>
                                <th>New Values</th>
                                <th>Url</th>
                                <th>Ip Address</th>
                                <th>Device</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($audits as $audit)
                                <tr>
                                    <td>{{ $audit->id }}</td>
                                    <td>{{ $audit->user->email }}</td>
                                    <td>{{ explode("\\",$audit->auditable_type)[2] }}</td>
                                    <td>{{ $audit->event }}</td>
                                    <td>{{ $audit->tags }}</td>
                                    <td>{{ $audit->created_at }}</td>
                                    <td>
                                        <table class="table table-bordered table-hover" style="width:100%">
                                            @foreach($audit->old_values as $attribute  => $value)
                                                <tr>
                                                    <td><b>{{ $attribute  }}</b></td>
                                                    <td>{{ $value }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                    <td>
                                        <table class="table table-bordered table-hover" style="width:100%">
                                            @foreach($audit->new_values as  $attribute  => $value)
                                                <tr>
                                                    <td><b>{{  $attribute  }}</b></td>
                                                    <td>{{ $value }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                    <td>{{ $audit->url }}</td>
                                    <td>{{ $audit->ip_address }}</td>
                                    <td>{{ $audit->user_agent }}</td>
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
@endsection
