<!DOCTYPE html>
<html class="no-js">

<head>
    <title>@whitelabel('app_name') - Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible"
          content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
          content="@whitelabel('app_name') - Admin Panel">
    <meta name="app-debug"
          content="{{ getenv('APP_DEBUG') }}">
    <meta name="msapplication-TileColor"
          content="@whitelabelColor('primary')">
    <meta name="theme-color"
          content="@whitelabelColor('primary')">
    <meta name="csrf-token"
          content="{{ csrf_token() }}">
    @if (getenv('APP_ENV') === 'production' && getenv('APP_DEBUG') === 'false')
        <meta http-equiv="Content-Security-Policy"
              content="upgrade-insecure-requests">
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
          content="Bearer {{ auth()->check()? auth()->user()->createToken('Laravel Password Grant Client', ['admin'])->accessToken: '' }}">
    <meta name="current-url"
          content="{{ env('APP_URL') }}">
    @php
        $user_id = auth()->user()->id;
        if (auth()->user()->is_cloudfox) {
            $user_id = auth()->user()->logged_id;
        }
    @endphp
    <meta name="user-id"
          content="{{ hashids_encode($user_id) }}">
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
    <!-- Stylesheets -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Inter">
    <link rel="stylesheet"
          href="{{ mix('build/layouts/master/master.min.css') }}">
    
    <!-- Whitelabel Dynamic Styles -->
    @whitelabelStyles
    
    <!-- Whitelabel Dynamic CSS -->
    <link rel="stylesheet"
          href="{{ route('whitelabel.css') }}">
    
    @stack('css')

    <!-- End Google Tag Manager -->
    <script src="{{ mix('build/layouts/master/master.min.js') }}"></script>
    <script>
        Breakpoints();
    </script>
</head>

<body class="animsition site-navbar-small dashboard site-menubar-fold site-menubar-hide">

@include('layouts.bonus-balance')

@include('layouts.menu-principal')

<div class="top-alert-container">
    <div class="top-alert warning col-sm-12 col-md-5"
         id="document-pending"
         style="display:none;">
        <div class="top-alert-message-container">
            <div class="col-4 text-center">
                <img class="top-alert-img"
                     alt="">
            </div>
            <div class="col-8 pr-20 d-flex flex-wrap">
                <span class="top-alert-message">Existem itens pendentes em seu cadastro</span>
                <a href="/companies"
                   data-url-value="/companies"
                   class="top-alert-action redirect-to-accounts">Corrigir
                    documento</a>
            </div>
            <a class="top-alert-close">
                <i class="material-icons">close</i>
            </a>
        </div>
    </div>
</div>

<input type="hidden"
       id="accountStatus">

<div class="alert-demo-account"
     style="display:none">
    <div class="row no-gutters">
        <img src="/build/global/img/alert-demo-left.png"
             class="mr-20">
        Esta é uma conta demonstrativa
        <img src="/build/global/img/alert-demo-rigth.png"
             class="ml-20">
    </div>
</div>

@yield('content')

@include('utils.alert-demo-account')

<!-- Plugins -->
<script src="{{ mix('build/layouts/master/plugins.min.js') }}"></script>

@if ((!auth()->user()->account_is_approved && auth()->user()->id == auth()->user()->account_owner_id) ||
    auth()->user()->is_cloudfox)
    @include('utils.documents-pending')
    <script>
        verifyDocumentPending();
    </script>
@endif

<x-sentry />

@stack('scripts')
@stack('scriptsModal')

@if (env('APP_ENV', 'production') == 'production')
    <script src="{{ mix('build/layouts/master/production.min.js') }}"></script>

    @php
        $user = \Auth::user();
    @endphp
    @if (!empty($user))
        <script>
            window.intercomSettings = {
                api_base: "https://api-iam.intercom.io",
                app_id: "zuy8geqt",
                name: "{{ $user->name }}", // Nome completo
                email: "{{ $user->email }}", // Endereço de e-mail
                created_at: "{{ $user->created_at }}" // Data de assinatura como registro de data e hora do Unix
            };
        </script>

        <script>
            // We pre-filled your app ID in the widget URL: 'https://widget.intercom.io/widget/zs3bxybw'
            (function() {
                var w = window;
                var ic = w.Intercom;
                if (typeof ic === "function") {
                    ic("reattach_activator");
                    ic("update", w.intercomSettings);
                } else {
                    var d = document;
                    var i = function() {
                        i.c(arguments);
                    };
                    i.q = [];
                    i.c = function(args) {
                        i.q.push(args);
                    };
                    w.Intercom = i;
                    var l = function() {
                        var s = d.createElement("script");
                        s.type = "text/javascript";
                        s.async = true;
                        s.src = "https://widget.intercom.io/widget/zuy8geqt";
                        var x = d.getElementsByTagName("script")[0];
                        x.parentNode.insertBefore(s, x);
                    };
                    if (document.readyState === "complete") {
                        l();
                    } else if (w.attachEvent) {
                        w.attachEvent("onload", l);
                    } else {
                        w.addEventListener("load", l, false);
                    }
                }
            })();
        </script>
    @endif
@endif

</body>

</html>
