<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="no-js css-menubar">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible"
          content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <!-- CSRF Token -->
    <meta name="csrf-token"
          content="{{ csrf_token() }}">
    <title>@whitelabel('app_name') - @yield('title')</title>
    <!-- Fonts -->
    <link rel="dns-prefetch"
          href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,700,800&display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <!-- Favicon -->
    <link rel="apple-touch-icon"
          sizes="180x180"
          href="@whitelabelLogo('icon')">
    <link rel="icon"
          type="image/png"
          sizes="32x32"
          href="@whitelabel('favicon')">
    <link rel="icon"
          type="image/png"
          sizes="16x16"
          href="@whitelabel('favicon')">
    <link rel="mask-icon"
          href="@whitelabelLogo('icon')"
          color="@whitelabelColor('primary')">
    <meta name="msapplication-TileColor"
          content="@whitelabelColor('primary')">
    <meta name="theme-color"
          content="@whitelabelColor('primary')">

    <!-- Styles -->
    <link rel='stylesheet'
          href='{{ mix('build/layouts/auth/auth.min.css') }}'>
          
    <!-- Whitelabel Dynamic Styles -->
    @whitelabelStyles
    
    <!-- Whitelabel Dynamic CSS -->
    <link rel="stylesheet"
          href="{{ route('whitelabel.css') }}">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <script src='{{ mix('build/layouts/auth/auth.min.js') }}'></script>
    <script>
        Breakpoints();
    </script>
</head>

<body style='background:none;padding:0%'>
<img src="{{ asset('build/global/adminremark/assets/images/gradient-bg.png') }}"
     style='width:100%;height:100%;position:fixed;z-index:-100;left:0%;top:0%;'
     alt="Background">
{{-- <div class="loading">
<div class="loader">
</div>
</div> --}}
<style>
    body {
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
        background: url(https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/gradient-bg.png) bottom left no-repeat;
    }
</style>
@yield('content')

<script>
    (function(document, window, $) {
        "use strict";

        var Site = window.Site;
        $(document).ready(function() {
            Site.run();
        });
    })(document, window, jQuery);
</script>
@stack('scripts')

<x-sentry />

</body>

</html>
