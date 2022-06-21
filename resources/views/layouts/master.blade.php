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
        <meta name="access-token" content="Bearer {{ auth()->check() ? auth()->user()->createToken("Laravel Password Grant Client", ['admin'])->accessToken : ''  }}">
        <meta name="current-url" content="{{ env('APP_URL') }}">
        <meta name="user-id" content="{{ \Vinkla\Hashids\Facades\Hashids::encode(auth()->user()->id) }}">
        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ mix('build/global/img/logos/2021/favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ mix('build/global/img/logos/2021/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ mix('build/global/img/logos/2021/favicon/favicon-16x16.png') }}">
        <link rel="mask-icon" href="{{ mix('build/global/img/safari-pinned-tab.svg') }}" color="#5bbad5">
        <!-- Stylesheets -->
        <link rel="stylesheet" href="{{ mix('build/layouts/master/master.min.css') }}">
        @stack('css')
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-KDD46QX');</script>
        <!-- End Google Tag Manager -->
        <script src="{{ mix('build/layouts/master/master.min.js') }}"></script>
        <script>
            Breakpoints();
        </script>
    </head>
    <body class="animsition site-navbar-small dashboard site-menubar-fold site-menubar-hide">
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TDM6SV5" height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->

        @include("layouts.loading")

        @include("layouts.menu-principal")

        <div class="top-alert-container">
            <div class="top-alert warning col-sm-12 col-md-5" id="document-pending" style="display:none;">
                <div class="top-alert-message-container">
                    <div class="col-4 text-center">
                    <img class="top-alert-img"  alt="">
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

        @php
            $userModel = new \Modules\Core\Entities\User();
            $account_type = $userModel->present()->getAccountType(auth()->user()->id, auth()->user()->account_owner_id);
        @endphp

        @if(!auth()->user()->account_is_approved)
            @include('utils.new-register-link')
        @endif

        @yield('content')

        @if(!auth()->user()->account_is_approved && $account_type === 'admin')
            @include('utils.documents-pending')
        @endif

        <!-- Plugins -->
        <script src="{{ mix('build/layouts/master/plugins.min.js') }}"></script>
        <script> verifyDocumentPending(); </script>

        @stack('scripts')
        @stack('scriptsModal')

        @if(env('APP_ENV', 'production') == 'production')
            <script src="{{ mix('build/layouts/master/production.min.js') }}"></script>

            <style>
                .margin-chat-pagination {
                    display:block !important; height:20px  !important;
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

            @if(\Auth::user())
                <script>
                    (function (o, c, t, a, d, e, s, k) {
                    o.octadesk = o.octadesk || {};
                    s = c.getElementsByTagName("body")[0];
                    k = c.createElement("script");
                    k.async = 1;
                    k.src = t + '/' + a + '?showButton=' +  d + '&openOnMessage=' + e;
                    s.appendChild(k);
                    })(window, document, 'https://chat.octadesk.services/api/widget', 'cloudfoxpagamentos',  true, true);
                </script>

                <script>
                    window.addEventListener('onOctaChatReady', function(e) {
                        octadesk.chat.login({
                            user: {
                                name: '{{ auth()->user()->name }}',
                                email: '{{ auth()->user()->email }}'
                            },
                        })
                    })
                </script>
            @endif

        @endif

        <!-- Announcekiit configuracoes -->
        {{-- resources/modules/global/js/global.js --}}
        <script async src="https://cdn.announcekit.app/widget-v2.js"></script>
    </body>
</html>
