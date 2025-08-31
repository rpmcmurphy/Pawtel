<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pawtel - Authentication')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Vite Assets -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @stack('styles')
</head>

<body class="pawtel-theme auth-page">
    <!-- Auth Background -->
    <div class="auth-background">
        <div class="paw-print paw-1"></div>
        <div class="paw-print paw-2"></div>
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
    </div>

    <div class="auth-container">
        <div class="auth-header text-center mb-4">
            <a href="{{ route('home') }}" class="logo">
                <i class="fas fa-paw"></i>
                Pawtel
            </a>
        </div>

        <div class="auth-card">
            @include('layouts.partials.flash-messages')
            @yield('content')
        </div>

        <div class="auth-footer text-center mt-4">
            <p class="text-muted">&copy; {{ date('Y') }} Pawtel. All rights reserved.</p>
        </div>
    </div>

    @stack('scripts')
</body>

</html>

{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pawtel - Authentication')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700;800&family=Delius:wght@400&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body class="pawtel-theme auth-page">
    <!-- Auth Background -->
    <div class="auth-background">
        <div class="paw-print paw-1"></div>
        <div class="paw-print paw-2"></div>
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
    </div>

    <div class="auth-container">
        <div class="auth-header text-center mb-4">
            <a href="{{ route('home') }}" class="logo">
                <i class="fas fa-paw"></i>
                Pawtel
            </a>
        </div>

        <div class="auth-card">
            @include('layouts.partials.flash-messages')
            @yield('content')
        </div>

        <div class="auth-footer text-center mt-4">
            <p class="text-muted">&copy; {{ date('Y') }} Pawtel. All rights reserved.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>

</html> --}}
