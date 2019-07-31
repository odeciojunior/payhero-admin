<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js css-menubar">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CloudFox @yield('title')</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('modules/global/assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('modules/global/assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('modules/global/assets/img/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('modules/global/assets/img/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="theme-color" content="#ffffff">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/assets/css/new-login.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/assets/css/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/loading.css') }}">
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/breakpoints/breakpoints.js') }}"></script>
    <script src="{{ asset('modules/global/assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('modules/global/js/global.js') }}"></script>
    <script>
        Breakpoints();
    </script>
</head>
<body style='background:none;padding:0%'>
<img src="{{ asset('modules/global/adminremark/assets/images/gradient-bg.png') }}" style='width:100%;height:100%;position:fixed;z-index:-100;left:0%;top:0%;' alt="Background">
{{--<div class="loading">
    <div class="loader">
    </div>
</div>--}}
<style>
    body {
        font-family: 'Muli', sans-serif;
        min-height: 100vh;
        background: url({{ asset('modules/global/adminremark/assets/images/gradient-bg.png') }}) bottom left no-repeat;
    }
</style>
@yield('content')

<!-- <script src="{{ asset('modules/global/adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/jquery/jquery.js') }}"></script>

    <script src="{{ asset('modules/global/adminremark/global/vendor/popper-js/umd/popper.min.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/ashoverscroll/jquery-asHoverScroll.js') }}"></script>
    
    <script src="{{ asset('modules/global/adminremark/global/vendor/switchery/switchery.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/intro-js/intro.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/screenfull/screenfull.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/vendor/jquery-placeholder/jquery.placeholder.js') }}"></script>
    
    <script src="{{ asset('modules/global/adminremark/global/js/Component.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Base.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Config.js') }}"></script>
    
    <script src="{{ asset('modules/global/adminremark/assets/js/Section/Menubar.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/assets/js/Section/GridMenu.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/assets/js/Section/Sidebar.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/assets/js/Section/PageAside.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/assets/js/Plugin/menu.js') }}"></script>
    
    <script src="{{ asset('modules/global/adminremark/global/js/config/colors.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/assets/js/config/tour.js') }}"></script>
    <script>Config.set('assets', '../../assets');</script>
    
    <script src="{{ asset('modules/global/adminremark/assets/js/Site.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/asscrollable.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/slidepanel.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/switchery.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/jquery-placeholder.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/material.js') }}"></script> -->
<script>
    (function (document, window, $) {
        'use strict';

        var Site = window.Site;
        $(document).ready(function () {
            Site.run();
        });
    })(document, window, jQuery);
</script>
@stack('scripts')
</body>
</html>

