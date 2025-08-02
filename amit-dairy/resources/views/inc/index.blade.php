@extends('layouts.master')

@section('title', 'Admin Login')

@push('styles')
    <style>
        body {
            background-color: #6f3d3d;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 30px;
            color: #6f3d3d;
        }

        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-header img {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }

        .login-header h5 {
            font-weight: 600;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-login {
            background-color: #6f3d3d;
            border: none;
        }

        .btn-login:hover {
            background-color: #5d4037;
        }

        .footer {
            text-align: center;
            color: #d7ccc8;
            padding: 15px 0;
            font-size: 14px;
        }

        .alert-danger {
            font-size: 14px;
            padding: 8px 12px;
            margin-top: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('assets/img/logo.webp') }}" alt="Logo">
                <h5>Welcome to Amit Dairy &amp; Sweets</h5>
                <p class="text-muted">Please sign in to continue</p>
            </div>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ url('admin/login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" id="email"
                        placeholder="Enter your email">
                </div>

                <div class="mb-3">
                    <label for="password-input" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password-input"
                        placeholder="Enter your password">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-login text-white">Login</button>
                </div>
            </form>
        </div>
    </div>
@endsection
