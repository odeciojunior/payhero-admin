<!DOCTYPE html>
<html class="no-js">
<head>
    <title>Sirius</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="cloudfox">
    <meta name="app-debug" content="{{ getenv('APP_DEBUG') }}">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(getenv('APP_ENV') === 'production' && getenv('APP_DEBUG') === 'false')

        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    @elseif(getenv('APP_ENV') === 'production' && getenv('APP_DEBUG') === 'true')
        <style>
            .site-navbar {
                background-color: darkblue !important
            }

            .site-menubar {
                background-color: darkblue !important
            }
        </style>
    @endif

<!-- access token used for api ajax requests -->
    <meta name="access-token"
          content="Bearer {{ auth()->check() ? auth()->user()->createToken("Laravel Password Grant Client", ['admin'])->accessToken : ''  }}">
    <meta name="current-url" content="{{ env('APP_URL') }}">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('modules/global/img/logos/2021/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('modules/global/img/logos/2021/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('modules/global/img/logos/2021/favicon/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('modules/global/img/safari-pinned-tab.svg') }}" color="#5bbad5">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('modules/global/css/normalize.css?v=04') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap-extend.min.css?v=2545') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/assets/css/site.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/loading.css?v=5') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/checkAnimation.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/ribbon.css?v=1') }}">
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.css') }}">

{{--    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery-mmenu.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('modules/global/jquery-imgareaselect/css/imgareaselect-default.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/css/sweetalert2.min.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/sortable/sortable.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/web-icons/web-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/font-awesome/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/newFonts.css?v=2') }}">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/global/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/orion-icons/iconfont.css?v=14') }}">
    <!-- New CSS -->
    <link rel="stylesheet" href="{{ asset('modules/global/css/new-site.css?v=123') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/global.css?v=72') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css?v=33') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/asscrollable/asScrollable.css?v=1') }}">
    @stack('css')
    <script src="{{ asset('modules/global/adminremark/global/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/breakpoints/breakpoints.js') }}"></script>
    <script>
        Breakpoints();
    </script>
    <script src="//fast.appcues.com/60650.js"></script>
</head>
<body class="animsition site-navbar-small dashboard site-menubar-fold site-menubar-hide">

{{-- loading --}}
<div id='loadingOnScreen' style='height:100%; width:100%; position:absolute'>
</div>

@include("layouts.menu-principal")

<div class="top-alert-container">
    <div class="top-alert warning col-sm-12 col-md-5" id="document-pending" style="display:none;">
        <div class="top-alert-message-container">
            <div class="col-4 text-center">
                <img class="top-alert-img" src=" " alt="">
            </div>
            <div class="col-8 pr-20 d-flex flex-wrap">
                <span class="top-alert-message">Existem itens pendentes em seu cadastro</span>
                <a href="/companies" data-url-value="/companies" class="top-alert-action redirect-to-accounts">Corrigir documento</a>
            </div>
            <a class="top-alert-close">
                <i class="material-icons">close</i>
            </a>
        </div>
    </div>
</div>
<input type="hidden" id="accountStatus">
@yield('content')

<!-- Plugins -->
<script src="{{ asset('modules/global/adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/popper-js/umd/popper.min.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.js') }}"></script>
{{--<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>--}}
{{--<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>--}}
{{--<script src="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery.mmenu.min.all.js') }}"></script>--}}
<script src="{{ asset('modules/global/adminremark/global/vendor/matchheight/jquery.matchHeight-min.js') }}"></script>
<script src="{{ asset('modules/global/js-extra/jquery.mask.min.js') }}"></script>
<script src="{{ asset('modules/global/js-extra/jquery.maskMoney.js') }}"></script>
<script src="{{ asset('modules/global/js-extra/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Component.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Plugin.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Base.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Config.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/js/Section/Menubar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/js/Section/Sidebar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/js/Section/PageAside.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/js/Section/GridMenu.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/js/Site.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/examples/js/dashboard/v1.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/sortable/Sortable.js') }}"></script>
<script src="{{ asset('modules/global/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js') }}"></script>
<script src="{{ asset('modules/global/js/global.js?v=570') }}"></script>
<script>
    verifyDocumentPending();
</script>


<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js?v=1') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js?v=1') }}"></script>


@stack('scripts')

@if(env('APP_ENV', 'production') == 'production')

    <script src="{{ asset('modules/global/js-extra/pusher.min.js?v=11') }}"></script>
    <script src="{{ asset('modules/global/js/notifications.js?v=11') }}"></script>


    <style>
        .margin-chat-pagination {
            display:block !important; height:100px  !important;
        }
    </style>

    <script>

        @if(\Auth::user())
            (function(m,a,i,s,_i,_m){
                m.addEventListener('load',function(){m.top.maisim||(function(){_m=a.createElement(i);
                    i=a.getElementsByTagName(i)[0];_m.async=!0;_m.src=s;_m.id='maisim';_m.charset='utf-8';
                    _m.setAttribute('data-token',_i);i.parentNode.insertBefore(_m,i)})()})
            })(window,document,'script','https://app.mais.im/support/assets/js/core/embed.js','273c7ff74192d8dac2ef370dc930d643');
        @endif
    </script>

@endif

</body>
</html>
