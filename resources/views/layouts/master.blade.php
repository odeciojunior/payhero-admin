<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <title>CloudFox - Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="cloudfox">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="theme-color" content="#ffffff">

    <!-- csrf token used for ajax requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('modules/global/assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('modules/global/assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('modules/global/assets/img/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('modules/global/assets/img/safari-pinned-tab.svg') }}" color="#5bbad5">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark2/assets/css/site.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/loading.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/checkAnimation.css') }}">

    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery-mmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/jquery-imgareaselect/css/imgareaselect-default.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/css/sweetalert2.min.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/css/daterangepicker.css') }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/web-icons/web-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/font-awesome/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/newFonts.css') }}">

    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/global/css/materialdesignicons.min.css') }}">

    <!-- New CSS --> 
    <link rel="stylesheet" href="{{ asset('modules/global/css/new-site.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/global.css') }}">

    @stack('css')

    <!-- Scripts -->
    @if(env('APP_ENV', 'production') == 'production')
        {{--  <script src="https://browser.sentry-cdn.com/5.6.0/bundle.min.js" integrity="sha384-9aGOmRDrtIQRcZmYbrNQmfS1dW44OCMtOlQ3JFUYCdCpxTJQ8vK+//K35AKgZh96" crossorigin="anonymous"></script>  --}}
        <script src="{{ asset('modules/global/assets/js/sentry-bundle.min.js') }}"></script>
    @endif

    <script src="{{ asset('modules/global/adminremark/global/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/breakpoints/breakpoints.js') }}"></script>
    <script>
        Breakpoints();
    </script>

</head>

<body class="animsition site-navbar-small dashboard">
<div id='loadingOnScreen' style='height:100%; width:100%; position:absolute'>
</div>
<style>
    body {
        background-color: #f1f4f5;
        font-family: 'Muli', sans-serif !important;
    }
</style>
@include("layouts.menu-principal")

@yield('content')

<!-- Plugins -->
<script src="{{ asset('modules/global/adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/popper-js/umd/popper.min.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery.mmenu.min.all.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/matchheight/jquery.matchHeight-min.js') }}"></script>
<script src="{{ asset('modules/global/assets/js/jquery.mask.min.js') }}"></script>
<script src="{{ asset('modules/global/assets/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Component.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Plugin.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Base.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Config.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/js/Section/Menubar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/js/Section/Sidebar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/js/Section/PageAside.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/js/Section/GridMenu.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/js/Site.js') }}"></script>
<script src="{{ asset('modules/global/adminremark2/assets/examples/js/dashboard/v1.js') }}"></script>
<script src="{{ asset('modules/global/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js') }}"></script>
<script src="{{ asset('modules/global/js/global.js') }}"></script>

@stack('scripts')

@if(env('APP_ENV', 'production') == 'production')

    <script>
        Sentry.init({dsn: 'https://86728bcdb6544260b6d4a9648e4aeb08@sentry.io/1526015'});
    </script>

     <script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/q35ubavq';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();</script>

     <script>
           window.Intercom('boot', {
               app_id: "q35ubavq",
               user_id: "{!! \Auth::user()->id !!}",
               name: "{!! \Auth::user()->name !!}",
               email: "{!! \Auth::user()->email !!}", 
           });
     </script>

     {{--  <script src="https://js.pusher.com/4.4/pusher.min.js"></script>  --}}
     <script src="{{ asset('modules/global/assets/js/pusher.min.js') }}"></script>

@endif

<script src="{{ asset('modules/global/js/notifications.js') }}"></script>

</body>
</html>
