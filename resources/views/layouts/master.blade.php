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
    @if(getenv('APP_ENV') === 'production')
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif

    <!-- access token used for api ajax requests -->
    <meta name="access-token" content="Bearer {{ auth()->check() ? auth()->user()->createToken("Laravel Password Grant Client")->accessToken : ''  }}">
    <meta name="current-url" content="{{ env('APP_URL') }}">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('modules/global/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('modules/global/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('modules/global/img/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('modules/global/img/safari-pinned-tab.svg') }}" color="#5bbad5">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/assets/css/site.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/loading.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/checkAnimation.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/ribbon.css') }}">
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
    <link rel="stylesheet" href="{{ asset('modules/global/css/global.css?v=4') }}">
    @stack('css')

    @if(env('APP_ENV', 'production') == 'production')
        <script src="{{ asset('modules/global/js-extra/sentry-bundle.min.js') }}"></script>
        <script>
            Sentry.init({dsn: 'https://4b81ab6a91684acd888b817f34bd755b@sentry.io/1542991'});
        </script>
    @endif
    <script src="{{ asset('modules/global/adminremark/global/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/breakpoints/breakpoints.js') }}"></script>
    <script>
        Breakpoints();
    </script>
</head>
<body class="animsition site-navbar-small dashboard">

{{-- loading --}}
<div id='loadingOnScreen' style='height:100%; width:100%; position:absolute'>
</div>

@include("layouts.menu-principal")

<div class="alert alert-dismissible fade document-pending show" style="display:none;">
    <div class="message-container">
        <span class="message-pending">Existem itens pendentes em seu cadastro</span>
        <a href="/companies" class="btn-finalize">Finalizar cadastro</a>
    </div>
    <a data-dismiss="alert" role="button">
        <i class="material-icons document-pending-close">close</i>
    </a>
</div>

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
<script src="{{ asset('modules/global/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js') }}"></script>
{{--<script src="{{ asset('modules/global/js/global.js?v=4') }}"></script>--}}
<script>
    verifyDocumentPending();
</script>

@stack('scripts')

@if(env('APP_ENV', 'production') == 'production')

    <script src="https://fast.appcues.com/60650.js"></script>

    <script>
        window.Appcues.identify("{{ auth()->user()->id }}", {
            account_id: "{{ auth()->user()->id }}",
            first_name: "{{ auth()->user()->name }}",
            email: "{{ auth()->user()->email }}",
        });
    </script>

    <script src="{{ asset('modules/global/js-extra/pusher.min.js') }}"></script>

    <script src="{{ asset('modules/global/js/notifications.js?v=8') }}"></script>

    <script type='text/javascript' src='https://inveniochatapi.azurewebsites.net/chat.js'></script>

    <script type='text/javascript'>
        chatRobbu.init('D11C0731357383EA', {
            theme: 'sunset',
            delay: 500,
            open: false,
            call: 'Iniciar Atendimento',
            logo: 'https://cloudfox.net/img/cloudfox-100-100.png',
            wallet_customer_code: null,
        });
    </script>
@endif

</body>

</html>

