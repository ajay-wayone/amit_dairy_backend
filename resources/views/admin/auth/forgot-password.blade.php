<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Forgot Password - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Admin Dashboard" name="description" />
    <meta content="Amit Dairy" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/assets/images/favicon.ico') }}">
    <link href="{{ asset('assets/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="auth-body-bg">
    <div class="home-btn d-none d-sm-block">
        <a href="{{ route('admin.login') }}"><i class="mdi mdi-home-v2 h2 text-white"></i></a>
    </div>
    <div>
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-lg-4">
                    <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                        <div class="w-100">
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <div>
                                        <div class="text-center">
                                            <div>
                                                <a href="{{ route('admin.login') }}" class="logo">
                                                    <img src="{{ asset('assets/assets/images/logo.webp') }}"
                                                        height="50" alt="logo">
                                                </a>
                                            </div>
                                            <h4 class="font-size-18 mt-4">Forgot Password</h4>
                                            <p class="text-muted">Enter your email to receive OTP</p>
                                        </div>

                                        <div class="p-2 mt-5">
                                            @if (session('success'))
                                                <div class="alert alert-success">{{ session('success') }}</div>
                                            @endif
                                            @if (session('error'))
                                                <div class="alert alert-danger">{{ session('error') }}</div>
                                            @endif

                                            <form class="form-horizontal" method="POST"
                                                action="{{ route('admin.send-otp') }}">
                                                @csrf
                                                <div class="form-group auth-form-group-custom mb-4">
                                                    <i class="ri-mail-line auti-custom-input-icon"></i>
                                                    <label for="email">Email</label>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        id="email" name="email" placeholder="Enter your email"
                                                        value="{{ old('email') }}" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mt-4 text-center">
                                                    <button
                                                        class="btn btn-outline-warning w-md waves-effect waves-light"
                                                        type="submit">
                                                        Send OTP
                                                    </button>
                                                </div>

                                                <div class="mt-4 text-center">
                                                    <a href="{{ route('admin.login') }}"
                                                        class="text-muted ">
                                                        <i class="mdi mdi-arrow-left me-1"></i> Back to Login
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="bg-overlay"
                        style="
    background-image: url('{{ asset('assets/assets/images/small/img-5.jpg') }}');
    background-size: cover;
    background-position: center;
    opacity: 0.7;
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
">
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/assets/js/app.js') }}"></script>
</body>

</html>
