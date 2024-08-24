<!doctype html>
<html lang="en">
<head> 
    <meta charset="utf-8">
    <title>{{env('APP_NAME')}}</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('public/img/logo_white.png')}}" type="image/x-icon" />

    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/plugins/ionicons/dist/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/plugins/icon-kit/dist/css/iconkit.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/css/theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
    <script src="{{ asset('public/src/js/vendor/modernizr-2.8.3.min.js') }}"></script>
    <style>
        .auth-wrapper {
            position: relative;
            overflow: hidden;
            background:transparent !important;
        }

        #myVideo {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
        }
        .authentication-form{
            background-color:#ffffffcf !important;
        }
        .error-message {
            color: red;
            font-size: 0.875em;
            display: none;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <video autoplay muted loop id="myVideo">
            <source src="https://www.memcouae.ae/video/02.mp4" type="video/mp4">
            Your browser does not support HTML5 video.
        </video>
        <div class="container-fluid h-100">
            <div class="row flex-row h-100">
                <div class="col-xl-4 col-lg-4 col-md-4 m-auto">
                    <div class="authentication-form mx-auto">
                        <div class="logo-centered">
                            <a href="{{route('login')}}">
                                <img height="80" src="{{ asset('public/img/logo_white.png') }}" alt="" class="logo-spin">
                            </a>
                        </div>

                        <p>Welcome back! </p>
                        <form id="loginForm" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div id="loginFields">
                                <div class="form-group">
                                    <input id="email" type="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" name="email" value="" required autocomplete="email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback error-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" value="" required>
                                    @error('password')
                                        <span class="invalid-feedback error-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div id="loginError" class="error-message mb-2 mt-2"></div>
                                <div class="sign-btn text-center">
                                    <button id="submitLogin" class="btn btn-info w-100">Send OTP</button>
                                </div>
                            </div>
                            <div id="otpFields" style="display: none;">
                                <div class="form-group">
                                    <p class="alert alert-secondary text-success">OTP Code sent to your email.</p>
                                    <input id="otp" type="text" placeholder="Enter OTP" class="form-control" name="otp" required>
                                    <span id="otpError" class="error-message mb-2 mt-2">Invalid OTP Code Entered</span>
                                </div>
                                <div class="sign-btn text-center">
                                    <button id="verifyOtp" class="btn btn-info w-100">Verify OTP</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('public/src/js/vendor/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('public/plugins/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('public/plugins/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('public/plugins/screenfull/dist/screenfull.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#submitLogin').click(function(event) {
                event.preventDefault();
                var email = $('#email').val();
                var password = $('#password').val();
                
                $.ajax({
                    url: '{{ route('login.attempt') }}',
                    type: 'POST',
                    data: {
                        email: email,
                        password: password,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#loginError').hide(); // Hide previous error messages
                        if (response.status === 'success') {
                            $('#loginFields').hide();
                            $('#otpFields').show();
                        } else {
                            $('#loginError').text(response.message).show(); // Display error message
                        }
                    },
                    error: function(xhr) {
                        $('#loginError').text('An error occurred.').show(); // Display error message
                    }
                });
            });

            $('#verifyOtp').click(function(event) {
                event.preventDefault();
                var otp = $('#otp').val();
                
                $.ajax({
                    url: '{{ route('login.verifyOtp') }}',
                    type: 'POST',
                    data: {
                        otp: otp,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#otpError').hide(); // Hide previous error messages
                        if (response.status === 'success') {
                            window.location.href = response.redirect; // Redirect on successful OTP verification
                        } else {
                            $('#otpError').show(); // Show OTP error message
                        }
                    },
                    error: function(response) {
                        $('#otpError').text(response.message).show(); // Display error message
                    }
                });
            });
        });
    </script>
</body>
</html>
