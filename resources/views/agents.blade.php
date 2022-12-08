@extends('layouts.layouts')
@section('title', 'Agents')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4 font-13">The list of approved agents.</p>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Business Name</th>
                            <th>DOB</th>
                            <th>Phone Number</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    @if($user->photo!=null)
                                        <img alt="image" class="rounded-circle thumb-sm mr-1" src="{{route('show.avatar', $user->photo}}">
                                    @else
                                        <img alt="image" class="rounded-circle thumb-sm mr-1" src="/img/mcd_logo.png">
                                    @endif
                                    {{$user->user_name }}</td>
                                <td>{{$user->company_name }}</td>
                                <td>{{$user->dob }}</td>
                                <td>{{$user->phoneno}}</td>
                                <td><a href="profile/{{ $user->user_name }}" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a></td>
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
