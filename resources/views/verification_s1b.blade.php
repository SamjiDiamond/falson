@extends('layouts.layouts')
@section('title', 'Verification > Server1b ')
@section('parentPageTitle', 'Transaction')

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

                    <form class="form-horizontal" method="POST" action="{{ route('verification_server1b') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend"><span class="input-group-text">REF</span></div>
                                    <input type="text" name="ref" placeholder="Enter server reference" class="form-control @error('ref') is-invalid @enderror" required>
                                    <button class="btn btn-gradient-primary waves-effect waves-light" type="submit" style="align-self: center; align-content: center"><i class="fa fa-search"></i>Verify</button>
                                </div>
                                @error('ref')
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('after-style')
    <!-- Sweet Alert -->
    <link href="assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    @stop

@section('before-scripts')
    <!-- Sweet-Alert  --><script src="assets/plugins/sweet-alert2/sweetalert2.min.js"></script>
    <script>
        @if($response ?? '')
            swal({
                title: "{{$status}}",
                type: @if($status=="Approved") "success" @else "error" @endif,
                html: '{{$description}}',
                showCloseButton: @if($status=="Approved") !0 @else 0 @endif,
                showCancelButton: @if($status!="Approved") !0 @else 0 @endif,
                confirmButtonClass: "btn btn-success",
                cancelButtonClass: "btn btn-danger ml-2",
                confirmButtonText: '<i class="fa fa-thumbs-up"></i> Great!',
                cancelButtonText: '<i class="fa fa-thumbs-down"></i>'
            })
        @endif
    </script>
    @stop
