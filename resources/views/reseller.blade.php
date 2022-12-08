@extends('layouts.layouts')
@section('title', 'Resellers')
@section('parentPageTitle', 'User')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
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

                    <p class="text-muted mb-4 font-13">The list of approved resellers.</p>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Business Name</th>
                            <th>Phone Number</th>
                            <th>Level</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                @if($user->photo!=null)
                                    <img alt="image" class="rounded-circle thumb-sm mr-1" src="{{route('show.avatar', $user->photo)}}">
                                @else
                                    <img alt="image" class="rounded-circle thumb-sm mr-1" src="/img/mcd_logo.png">
                                @endif
                                {{$user->user_name }}</td>
                            <td>{{$user->company_name }}</td>
                            <td>{{$user->phoneno }}</td>
                            <td class="center">
                                <form method="post" action="{{route('updateLevel')}}">
                                    @csrf
                                    <input type="hidden" value="{{$user->id}}" name="id">
                                    <select class="custom-select form-control" name="level">
                                        <option {{$user->level == 1 ? "selected" : ''}}>1</option>
                                        <option {{$user->level == 2 ? "selected" : ''}}>2</option>
                                        <option {{$user->level == 3 ? "selected" : ''}}>3</option>
                                        <option {{$user->level == 4 ? "selected" : ''}}>4</option>
                                        <option {{$user->level == 5 ? "selected" : ''}}>5</option>
                                    </select>
                                    <br/>
                                    <br/>
                                    @can('reseller-level-modify')
                                        <button type="submit" class="btn btn-outline-primary btn-sm">Update</button>
                                    @endcan
                                </form>
                            </td>
                            <td>
                                @can('users-profile-view')
                                    <a href="profile/{{ $user->user_name }}" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> View</a>
                                @endcan
                                @can('regenerate-resellers-key-action')
                                    <a href="{{route('regenerateKey', $user->id)}}" class="btn btn-sm btn-danger"><i class="fas fa-recycle"></i> Regenerate Key</a>
                                @endcan
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
