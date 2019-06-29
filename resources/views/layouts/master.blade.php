<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="cloudfox">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('modules/global/assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('modules/global/assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('modules/global/assets/img/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('modules/global/assets/img/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="theme-color" content="#ffffff">
    <title>CloudFox - Admin</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark2/assets/css/site.min.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/assets/css/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/loading.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery-mmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/jquery-imgareaselect/css/imgareaselect-default.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/web-icons/web-icons.min.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.materialdesignicons.com/3.7.95/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/global/jquery-imgareaselect/css/imgareaselect-default.css') }}">
    <!-- New CSS -->
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/new-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/new-site.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/finances.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/reports.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/global.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@stack('css')

<!-- Scripts -->
    <script src="{{ asset('modules/global/adminremark/global/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/breakpoints/breakpoints.js') }}"></script>
    <script>
        Breakpoints();
    </script>
    <!-- Datatables -->
    <script src="http://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
</head>
<body class="animsition site-navbar-small dashboard">
<div class="loading">
    <div class="loader">
    </div>
</div>
<style>
    body {
        background-color: #f1f4f5;
        font-family: 'Muli', sans-serif !important;
    }
</style>
@include("layouts.menu-principal")

@yield('content')


<!-- Core  -->
<script src="{{ asset('modules/global/adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/popper-js/umd/popper.min.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
<!-- Plugins -->
<script src="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery.mmenu.min.all.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/matchheight/jquery.matchHeight-min.js') }}"></script>
<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
<script src="{{ asset('modules/global/assets/js/sweetalert2.all.min.js') }}"></script>
<!-- Scripts -->
<script src="{{ asset('modules/global/adminremark/global/js/Component.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Plugin.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Base.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Config.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/js/Section/Menubar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/js/Section/Sidebar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/js/Section/PageAside.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/js/Section/GridMenu.js') }}"></script>
<!-- Page -->
<script src="{{ asset('modules/global/adminremark2/assets/js/Site.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/examples/js/dashboard/v1.js') }}"></script>
<script src="{{ asset('modules/global/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js') }}"></script>
<script src="{{ asset('modules/global/assets/js/notificacoes.js') }}"></script>
<script src="{{ asset('modules/global/js/global.js') }}"></script>
@stack('scripts')
<script>
    window.intercomSettings = {
        app_id: "q35ubavq",
        user_id: "{!! Hashids::encode(\Auth::user()->name) !!}",
        name: "{!! \Auth::user()->name !!}",
        email: "{!! \Auth::user()->email !!}",
        created_at: "1312182000"
    };
</script>
<script>
    (function () {
        var w = window;
        var ic = w.Intercom;
        if (typeof ic === "function") {
            ic('reattach_activator');
            ic('update', w.intercomSettings);
        } else {
            var d = document;
            var i = function () {
                i.c(arguments);
            };
            i.q = [];
            i.c = function (args) {
                i.q.push(args);
            };
            w.Intercom = i;
            var l = function () {
                var s = d.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = 'https://widget.intercom.io/widget/q35ubavq';
                var x = d.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(s, x);
            };
            if (w.attachEvent) {
                w.attachEvent('onload', l);
            } else {
                w.addEventListener('load', l, false);
            }
        }
    })();
</script>
</body>
</html>
