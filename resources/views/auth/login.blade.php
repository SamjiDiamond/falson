<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <title>PLANETF | Login</title>
    <meta content="Admin Dashboard" name="description">
    <meta content="5Star Company" name="author">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="img/mcd_logo.png">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <!-- Toastr style -->
    <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <!-- Toastr script -->
    <script src="js/plugins/toastr/toastr.min.js"></script>
</head>
<body class="fixed-left">
<div class="accountbg"></div>
<div class="wrapper-page">
    <div class="card">
        <div class="card-body">
            <div class="text-center m-b-15"><a href="{{route('login')}}" class="logo logo-admin"><img src="img/PlanetfLogo.png" height="80" alt="logo"></a><h3>Welcome to PLANETF</h3></div>
            <div class="p-3">
                @error('email')
                <span class="invalid-feedback text-danger " role="alert">
                                    </span>
                <script type="text/javascript">
                    toastr.options = {
                        closeButton: true,
                        progressBar: true,
                        showMethod: 'slideDown',
                        timeOut: 4000
                    };
                    toastr.error('{{ $message }}', 'Login Error' );</script>
                @enderror

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ session('error') }}</strong> Change a few things up and try submitting again.
                    </div>
                    <script type="text/javascript">
                        toastr.options = {
                            closeButton: true,
                            progressBar: true,
                            showMethod: 'slideDown',
                            timeOut: 4000
                        };
                        toastr.error('{{ session("error") }}', 'Login Error' );
                    </script>
                @endif

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
                        toastr.success('{{ session('success') }}', 'Success' );
                    </script>
                @endif
                <form class="form-horizontal m-t-20" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group row">
                        <div class="col-12"><input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12"><input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="remember" id="remember"  {{ old('remember') ? 'checked' : '' }}> <label class="custom-control-label" for="remember">{{ __('Remember Me') }}</label></div>
                        </div>
                    </div>
                    <div class="form-group text-center row m-t-20">
                        <div class="col-12"><button class="btn btn-success btn-block waves-effect waves-light" type="submit">{{ __('Login') }}</button></div>
                    </div>
                    <div class="form-group m-t-10 mb-0 row">
                        <div class="col-sm-7 m-t-20"><p class="m-t"> <small>&copy; {{\Carbon\Carbon::now()->format('Y')}}</small> </p></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- jQuery  --><script src="assets/js/jquery.min.js"></script><script src="assets/js/popper.min.js"></script><script src="assets/js/bootstrap.min.js"></script><script src="assets/js/modernizr.min.js"></script><script src="assets/js/detect.js"></script><script src="assets/js/fastclick.js"></script><script src="assets/js/jquery.slimscroll.js"></script><script src="assets/js/jquery.blockUI.js"></script><script src="assets/js/waves.js"></script><script src="assets/js/jquery.nicescroll.js"></script><script src="assets/js/jquery.scrollTo.min.js"></script><!-- App js --><script src="assets/js/app.js"></script>
</body>
</html>
