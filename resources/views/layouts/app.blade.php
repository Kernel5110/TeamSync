<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'TeamSync es la plataforma definitiva para la gestión de equipos en eventos de innovación y tecnología.')">
    <meta name="keywords" content="@yield('meta_keywords', 'TeamSync, equipos, innovación, tecnología, Inovatec, Hackatec, colaboración, estudiantes, competencias')">
    <meta name="author" content="TeamSync">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'TeamSync - Potencia la colaboración en competencias de innovación')">
    <meta property="og:description" content="@yield('og_description', 'La plataforma definitiva para la gestión de equipos en eventos de innovación y tecnología.')">
    <meta property="og:image" content="{{ asset('img/logo.png') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('twitter_title', 'TeamSync - Potencia la colaboración en competencias de innovación')">
    <meta property="twitter:description" content="@yield('twitter_description', 'La plataforma definitiva para la gestión de equipos en eventos de innovación y tecnología.')">
    <meta property="twitter:image" content="{{ asset('img/logo.png') }}">
    
    <title>@yield('title', 'TeamSync - Potencia la colaboración en competencias de innovación')</title>
    @vite(['resources/css/index.css'])
    @stack('styles')
</head>
<body>
    <x-header />

    <main class="main-content">
        @yield('content')
    </main>

    <x-footer />
</body>
</html>
