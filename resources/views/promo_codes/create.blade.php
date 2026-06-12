@extends('layouts.layouts')
@section('title', 'Reward')
@section('parentPageTitle', 'Promo Code')

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

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{ implode(', ', $errors->all()) }}
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('promo_codes.store') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Target</span></div>
                                    <select class="custom-select form-control" name="target" required>
                                        <option value="">Select target</option>
                                        <option value="all_users" {{ old('target') === 'all_users' ? 'selected' : '' }}>All Users (single shared code)</option>
                                        <option value="single_user" {{ old('target') === 'single_user' ? 'selected' : '' }}>Single User</option>
                                        <option value="resellers" {{ old('target') === 'resellers' ? 'selected' : '' }}>Resellers (Top by sales)</option>
                                        <option value="top_users" {{ old('target') === 'top_users' ? 'selected' : '' }}>Top Users (by sales)</option>
                                        <option value="top_resellers" {{ old('target') === 'top_resellers' ? 'selected' : '' }}>Top Resellers (by sales)</option>
                                        <option value="admins_all" {{ old('target') === 'admins_all' ? 'selected' : '' }}>All Admins</option>
                                        <option value="admins_specific" {{ old('target') === 'admins_specific' ? 'selected' : '' }}>Specific Admins (by username)</option>
                                        <option value="new_users" {{ old('target') === 'new_users' ? 'selected' : '' }}>New Users Auto-Reward (enable/disable)</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Reward Amount</span></div>
                                    <input type="number" step="0.01" min="0" name="amount" placeholder="Enter amount" class="form-control" value="{{ old('amount') }}" required>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Count (for Top lists)</span></div>
                                    <input type="number" min="1" name="count" placeholder="e.g. 10" class="form-control" value="{{ old('count') }}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Single User (username)</span></div>
                                    <input type="text" name="user_name" placeholder="e.g. samji" class="form-control" value="{{ old('user_name') }}">
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">Specific Admins</span></div>
                                    <textarea name="admin_usernames" class="form-control" rows="3" placeholder="Comma-separated usernames">{{ old('admin_usernames') }}</textarea>
                                </div>

                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">New User Reward Enabled</span></div>
                                    <select class="custom-select form-control" name="enabled">
                                        <option value="">Select</option>
                                        <option value="1" {{ old('enabled') === '1' ? 'selected' : '' }}>Enable</option>
                                        <option value="0" {{ old('enabled') === '0' ? 'selected' : '' }}>Disable</option>
                                    </select>
                                </div>

                                <div class="input-group mt-2" style="align-content: center">
                                    <button class="btn btn-gradient-primary btn-large" type="submit"
                                            style="align-self: center; align-content: center">Submit
                                    </button>
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
