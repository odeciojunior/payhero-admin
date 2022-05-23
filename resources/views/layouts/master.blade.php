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
            })(window,document,'script','dataLayer','GTM-TDM6SV5');</script>
        <!-- End Google Tag Manager -->
        <script src="{{ mix('build/layouts/master/master.min.js') }}"></script>
        <script>
            Breakpoints();
        </script>

        <style>
            .new-register-overlay {
                background: #f1f4f5;
                position: fixed;
                display: none;
                top: 4.286rem;
                left: 65px;
                right: 0;
                bottom: 0;
                z-index: 1200;
                justify-content: center;
                padding: 40px 48px 48px;
                margin-bottom: 44px;
                overflow-y: auto;
            }

            .new-register-overlay-container {
                width: 612px;
            }
            @media screen and (max-width: 767px) {
                .new-register-overlay {
                    left: 0;
                }

                .new-register-overlay-container {
                    width: 90%;
                    padding: 0px 16px;
                }
            }

            .new-register-overlay-header {
                display: flex;
                flex-direction: row;
                width: 100%;
            }

            .new-register-overlay-title-container {
                display: flex;
                flex-direction: column;
                row-gap: 8px;
                margin-bottom: 48px;
            }

            .new-register-overlay-title {
                color: #0B1D3D;
                font-size: 30px;
                text-align: center;
            }

            .new-register-overlay-subtitle {
                color: #999999;
                font-size: 16px;
                text-align: center;
                padding: 0px 48px;
            }

            .new-register-overlay-body .card {
                border: 1px solid #ffffff;
                border-radius: 8px;
                padding: 24px;
                margin: 0 0 16px 0;
                text-align: left;
                min-height: 0;
                cursor: pointer;
            }
            .new-register-overlay-body .card.extra-informations-user {
                border: 1px solid #2E85EC;
                margin-bottom: 48px;
            }
            .new-register-overlay-body .card.extra-informations-user .icon {
                border-radius: 0;
                background: transparent;
            }
            .new-register-overlay-body .card .icon {
                width: 36px;
                height: 36px;
                background: #F4F6FB;
                border-radius: 100%;
                margin-right: 24px;
            }
            .new-register-overlay-body .card button {
                border: none;
                border-radius: 4px;
                background: #F4F6FB;
                font-weight: 700;
                font-size: 14px;
                color: #3D4456;
                padding: 12px 18px;
                margin-top: 12px;
            }
            .new-register-overlay-body .card.status-check .icon {
                background: #59BF75;
            }
            .new-register-overlay-body .card.status-info .icon {
                background: #2E85EC;
            }
            .new-register-overlay-body .card.status-warning .icon {
                background: #FF9900;
            }
            .new-register-overlay-body .card.status-error .icon {
                background: #E81414;
            }
            .new-register-overlay-body .card .icon img {
                margin: 0 auto;
            }
            .new-register-overlay-body .card .icon span {
                display: block;
                margin: 0 auto;
            }
            .new-register-overlay-body .card .title {
                color: #0B1D3D;
                font-weight: 600;
                font-size: 16px;
                margin: 0 0 4px 0;
                text-align: left;
            }
            .new-register-overlay-body .card .description {
                color: #5B5B5B;
                font-weight: 400;
                font-size: 14px;
                margin: 0;
                text-align: left;
            }

            .init-operation-container {
                margin-bottom: 32px;
            }
            .init-operation-container .title {
                color: #0B1D3D;
                font-weight: 400;
                font-size: 24px;
                text-align: center;
                margin: 0 0 8px 0;
            }
            .init-operation-container .description {
                color: #999999;
                font-size: 16px;
                text-align: center;
                margin: 0
            }

            .init-operation-container .body {
                margin-top: 32px;
            }

            .new-register-overlay-footer {
                margin-top: 44px;
                text-align: center;
            }
            .new-register-overlay-footer .btn {
                font-weight: 600;
                font-size: 16px;
                color: #2E85EC;
                text-align: center;
                padding: 0;
                background: transparent;
                border: none;
                line-height: 1;
            }

            .new_register-menu {
                display: none;
            }
        </style>
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

        @yield('content')

        <!-- New Register Overlay Modal -->
        <div class="new-register-overlay">
            <div class="container new-register-overlay-container">
                <div class="new-register-overlay-header">
                    <div class="new-register-overlay-title-container">
                        <span class="new-register-overlay-title">Bem vindo, <strong>{{ explode(' ', trim(auth()->user()->name))[0] }}</strong></span>
                        <span class="new-register-overlay-subtitle">Você acabou de chegar na Cloudfox e queremos te proporcionar uma experiência única</span>
                    </div>
                </div>
                <div class="new-register-overlay-body">
                    <div class="card extra-informations-user">
                        <!-- JS load -->
                    </div>

                    <div class="init-operation-container">
                        <div class="header">
                            <h1 class="title">Para <b>começar a sua operação</b> na Cloudfox</h1>
                            <p class="description">Criamos um passo a passo para você finalizar o seu cadastro</p>
                        </div>

                        <div class="body">
                            <div class="company-status">
                                <!-- JS load -->
                            </div>

                            <div class="user-status">
                                <!-- JS load -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="new-register-overlay-footer">
                    <button class="btn new-register-close" type="button">Deixar para mais tarde</button>
                </div>
            </div>
        </div>

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

        @if(auth()->user()->account_is_approved === 0)
            <script>
                $(document).ready(function () {
                    $('#new_register_btn').click(function () {
                        $(this).parent().parent().fadeOut('slow'); // hide navbar button

                        let modalOverlay = $('.new-register-overlay');

                        modalOverlay.fadeIn();
                    });
                });
            </script>
        @endif
    </body>
</html>
