<!doctype html>
<html lang="en">
<head> 
    <meta charset="utf-8">
    <title>{{env('APP_NAME', 'Memco')}} - Login</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('public/img/logo_white.png')}}" type="image/x-icon" />

    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/plugins/icon-kit/dist/css/iconkit.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/css/theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
    
    <style>
        .auth-wrapper {
            position: relative;
            overflow: hidden;
            background: transparent !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #myVideo {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
            object-fit: cover;
            filter: brightness(0.65);
        }

        .authentication-form {
            background-color: rgba(255, 255, 255, 0.5) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.6);
            padding: 35px 30px !important;
            max-width: 440px;
            width: 100%;
            margin: auto;
        }

        .logo-centered {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-centered img {
            max-height: 75px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
        }

        .form-group {
            position: relative;
            margin-bottom: 18px;
        }

        .form-control {
            border-radius: 8px !important;
            height: 46px;
            border: 1px solid #cbd5e1;
            padding-left: 40px;
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.9) !important;
        }

        .form-control:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2);
            background: #ffffff !important;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 15px;
            color: #64748b;
            font-size: 1rem;
            z-index: 10;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 15px;
            color: #64748b;
            cursor: pointer;
            z-index: 10;
        }

        .btn-info {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
            border-radius: 8px !important;
            padding: 12px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
            transition: all 0.2s ease;
        }

        .btn-info:hover:not(:disabled) {
            background-color: #0369a1 !important;
            border-color: #0369a1 !important;
            box-shadow: 0 6px 14px rgba(2, 132, 199, 0.4);
        }

        .btn-info:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .alert-box {
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.875rem;
            margin-bottom: 15px;
            display: none;
        }

        .back-link {
            display: inline-block;
            margin-top: 14px;
            color: #334155;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .back-link:hover {
            color: #0284c7;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <video autoplay muted loop id="myVideo">
            <source src="https://www.memcouae.ae/video/02.mp4" type="video/mp4">
            Your browser does not support HTML5 video.
        </video>
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-6 col-sm-10 m-auto">
                    <div class="authentication-form">
                        <div class="logo-centered">
                            <a href="{{route('login')}}">
                                <img height="80" src="{{ asset('public/img/logo_white.png') }}" alt="Logo" class="logo-spin">
                            </a>
                        </div>

                        <h4 class="text-center font-weight-bold text-dark mb-1">Welcome Back</h4>
                        <p class="text-center text-muted mb-4" style="font-size: 0.9rem;">Sign in to your MEMCO portal</p>

                        <!-- Alert Box for Error or Info -->
                        <div id="alertBox" class="alert alert-danger alert-box" role="alert">
                            <span id="alertText"></span>
                        </div>

                        <form id="loginForm" method="POST" action="{{ route('login') }}" onsubmit="return false;">
                            @csrf
                            
                            <!-- STEP 1: EMAIL & PASSWORD -->
                            <div id="loginFields">
                                <div class="form-group">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input id="email" type="email" placeholder="Email Address" class="form-control" name="email" required autofocus autocomplete="email">
                                </div>
                                
                                <div class="form-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input id="password" type="password" placeholder="Password" class="form-control" name="password" required autocomplete="current-password">
                                    <i class="fas fa-eye toggle-password" id="togglePasswordBtn"></i>
                                </div>

                                <div class="sign-btn text-center mt-4">
                                    <button id="submitLogin" type="button" class="btn btn-info w-100">
                                        <span id="submitLoginText">Send OTP</span>
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 2: OTP VERIFICATION -->
                            <div id="otpFields" style="display: none;">
                                <div class="alert alert-info py-2 px-3 mb-3" style="background: rgba(224, 242, 254, 0.9); border-color: #bae6fd; color: #0369a1; font-size: 0.85rem; border-radius: 8px;">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    <span id="otpNotice">OTP Code sent to your email.</span>
                                </div>

                                <div class="form-group">
                                    <i class="fas fa-key input-icon"></i>
                                    <input id="otp" type="text" placeholder="Enter 6-digit OTP" class="form-control" name="otp" required>
                                </div>

                                <div class="sign-btn text-center mt-3">
                                    <button id="verifyOtp" type="button" class="btn btn-info w-100">
                                        <span id="verifyOtpText">Verify OTP & Sign In</span>
                                    </button>
                                </div>

                                <div class="text-center mt-2">
                                    <a id="backBtn" class="back-link">
                                        <i class="fas fa-arrow-left mr-1"></i> Change Email / Back
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts with Fallbacks -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>window.jQuery || document.write('<script src="{{ asset("public/src/js/vendor/jquery-3.3.1.min.js") }}"><\/script>')</script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var submitBtn = document.getElementById('submitLogin');
            var verifyBtn = document.getElementById('verifyOtp');
            var togglePasswordBtn = document.getElementById('togglePasswordBtn');
            var backBtn = document.getElementById('backBtn');

            // Password Toggle
            if (togglePasswordBtn) {
                togglePasswordBtn.addEventListener('click', function() {
                    var passInput = document.getElementById('password');
                    if (passInput.type === 'password') {
                        passInput.type = 'text';
                        this.className = 'fas fa-eye-slash toggle-password';
                    } else {
                        passInput.type = 'password';
                        this.className = 'fas fa-eye toggle-password';
                    }
                });
            }

            // Back button
            if (backBtn) {
                backBtn.addEventListener('click', function() {
                    hideAlert();
                    document.getElementById('otpFields').style.display = 'none';
                    document.getElementById('loginFields').style.display = 'block';
                    document.getElementById('email').focus();
                });
            }

            function showAlert(msg, isSuccess) {
                var alertBox = document.getElementById('alertBox');
                var alertText = document.getElementById('alertText');
                alertText.innerText = msg;
                if (isSuccess) {
                    alertBox.className = 'alert alert-success alert-box';
                } else {
                    alertBox.className = 'alert alert-danger alert-box';
                }
                alertBox.style.display = 'block';
            }

            function hideAlert() {
                document.getElementById('alertBox').style.display = 'none';
            }

            // Step 1: Submit Login & Request OTP
            if (submitBtn) {
                submitBtn.addEventListener('click', handleLoginAttempt);
            }

            // Handle Enter Key in input fields
            document.getElementById('loginForm').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (document.getElementById('otpFields').style.display === 'block') {
                        handleOtpVerification();
                    } else {
                        handleLoginAttempt();
                    }
                }
            });

            function handleLoginAttempt() {
                var email = document.getElementById('email').value.trim();
                var password = document.getElementById('password').value;

                if (!email || !password) {
                    showAlert('Please enter both email and password.', false);
                    return;
                }

                hideAlert();
                submitBtn.disabled = true;
                document.getElementById('submitLoginText').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending OTP...';

                var csrfToken = document.querySelector('input[name="_token"]').value;

                $.ajax({
                    url: '{{ route("login.attempt") }}',
                    type: 'POST',
                    data: {
                        email: email,
                        password: password,
                        _token: csrfToken
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            document.getElementById('loginFields').style.display = 'none';
                            document.getElementById('otpFields').style.display = 'block';
                            
                            var otpVal = response.otp || '112233';
                            document.getElementById('otp').value = otpVal;
                            document.getElementById('otpNotice').innerText = 'OTP Code sent to your email! (Default OTP: ' + otpVal + ')';
                            document.getElementById('otp').focus();
                        } else {
                            showAlert(response.message || 'Invalid email or password.', false);
                            submitBtn.disabled = false;
                            document.getElementById('submitLoginText').innerHTML = 'Send OTP';
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Authentication failed.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert(msg, false);
                        submitBtn.disabled = false;
                        document.getElementById('submitLoginText').innerHTML = 'Send OTP';
                    }
                });
            }

            // Step 2: Verify OTP
            if (verifyBtn) {
                verifyBtn.addEventListener('click', handleOtpVerification);
            }

            function handleOtpVerification() {
                var otp = document.getElementById('otp').value.trim();

                if (!otp) {
                    showAlert('Please enter the OTP code.', false);
                    return;
                }

                hideAlert();
                verifyBtn.disabled = true;
                document.getElementById('verifyOtpText').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verifying...';

                var csrfToken = document.querySelector('input[name="_token"]').value;

                $.ajax({
                    url: '{{ route("login.verifyOtp") }}',
                    type: 'POST',
                    data: {
                        otp: otp,
                        _token: csrfToken
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            document.getElementById('verifyOtpText').innerHTML = '<i class="fas fa-check mr-2"></i> Redirecting...';
                            window.location.href = response.redirect;
                        } else {
                            showAlert(response.message || 'Invalid OTP Code.', false);
                            verifyBtn.disabled = false;
                            document.getElementById('verifyOtpText').innerHTML = 'Verify OTP & Sign In';
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Verification failed.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert(msg, false);
                        verifyBtn.disabled = false;
                        document.getElementById('verifyOtpText').innerHTML = 'Verify OTP & Sign In';
                    }
                });
            }
        });
    </script>
</body>
</html>
