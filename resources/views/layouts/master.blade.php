<!DOCTYPE html>
<html class="no-js">

<head>
    <title>Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible"
          content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
          content="Nexuspay">
    <meta name="app-debug"
          content="{{ getenv('APP_DEBUG') }}">
    <meta name="msapplication-TileColor"
          content="#603cba">
    <meta name="theme-color"
          content="#ffffff">
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
          href="{{ mix('build/global/img/logos/2021/favicon/apple-touch-icon.png') }}">
    <link rel="icon"
          type="image/png"
          sizes="32x32"
          href="{{ mix('build/global/img/logos/2021/favicon/favicon-32x32.png') }}">
    <link rel="icon"
          type="image/png"
          sizes="16x16"
          href="{{ mix('build/global/img/logos/2021/favicon/favicon-16x16.png') }}">
    <link rel="mask-icon"
          href="{{ mix('build/global/img/safari-pinned-tab.svg') }}"
          color="#5bbad5">
    <!-- Stylesheets -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Inter">
    <link rel="stylesheet"
          href="{{ mix('build/layouts/master/master.min.css') }}">
    @stack('css')
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-KDD46QX');
    </script>
    <!-- End Google Tag Manager -->
    <script src="{{ mix('build/layouts/master/master.min.js') }}"></script>
    <script>
        Breakpoints();
    </script>
</head>

<body class="animsition site-navbar-small dashboard site-menubar-fold site-menubar-hide">
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KDD46QX"
                height="0"
                width="0"
                style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->

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

    @stack('scripts')
    @stack('scriptsModal')

    @if (env('APP_ENV', 'production') == 'production')
        <script src="{{ mix('build/layouts/master/production.min.js') }}"></script>
        @if (\Auth::user())
            <script type="text/javascript">
                window.$crisp = [];
                window.CRISP_WEBSITE_ID = "906ce701-62be-4b4c-91e1-a8b71a74e82a";
                (function() {
                    d = document;
                    s = d.createElement("script");
                    s.src = "https://client.crisp.chat/l.js";
                    s.async = 1;
                    d.getElementsByTagName("head")[0].appendChild(s);
                })();
            </script>
        @endif
    @endif
</body>

</html>
