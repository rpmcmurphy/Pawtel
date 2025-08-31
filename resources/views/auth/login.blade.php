@extends('layouts.auth')

@section('title', 'Login - Pawtel')

@section('content')
    <div class="text-center mb-4">
        <h2>Welcome Back!</h2>
        <p class="text-muted">Login to access your account</p>
    </div>

    <form method="POST" action="{{ route('auth.login.post') }}" id="loginForm">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                name="password" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="fas fa-sign-in-alt me-2"></i>
            Login
        </button>
    </form>

    <div class="text-center">
        <p class="mb-2">
            <a href="#" class="text-decoration-none">Forgot your password?</a>
        </p>
        <p class="text-muted">
            Don't have an account?
            <a href="{{ route('auth.register') }}" class="text-decoration-none fw-semibold">Register here</a>
        </p>
    </div>
@endsection
