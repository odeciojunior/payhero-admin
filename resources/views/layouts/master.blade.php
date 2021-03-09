<!DOCTYPE html>
<html class="no-js" lang="en">
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
          content="Bearer {{ auth()->check() && auth()->user()->status != 3 ? auth()->user()->createToken("Laravel Password Grant Client", ['admin'])->accessToken : ''  }}">
    <meta name="current-url" content="{{ env('APP_URL') }}">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('modules/global/img/apple-touch-icon.png?v=1') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('modules/global/img/favicon-32x32.png?v=1') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('modules/global/img/favicon-16x16.png?v=1') }}">
    <link rel="mask-icon" href="{{ asset('modules/global/img/safari-pinned-tab.svg') }}" color="#5bbad5">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap-extend.min.css?v=2545') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/assets/css/site.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/loading.css?v=5') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/checkAnimation.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/ribbon.css') }}">
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
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/orion-icons/iconfont.css?v=06') }}">
    <!-- New CSS -->
    <link rel="stylesheet" href="{{ asset('modules/global/css/new-site.css?v=65') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/global.css?v=60') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css?v=32') }}">
    @stack('css')

    @if(env('APP_ENV', 'production') == 'production' && getenv('APP_DEBUG') === 'false')
        <script src="{{ asset('modules/global/js-extra/sentry-bundle.min.js') }}"></script>
        <script>
            Sentry.init({dsn: {{getenv('SENTRY_LARAVEL_DSN')}});
        </script>
    @endif
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
<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
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
<script src="{{ asset('modules/global/js/global.js?v=552') }}"></script>
<script>
    verifyDocumentPending();
</script>


@stack('scripts')

@if(env('APP_ENV', 'production') == 'production')

    <script src="{{ asset('modules/global/js-extra/pusher.min.js') }}"></script>

    <script src="{{ asset('modules/global/js/notifications.js?v=10') }}"></script>

    <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=3ff6c393-3915-4554-a046-cc8eae241938"> </script>

    <script>

        @if(\Auth::user())

            window.zESettings = {
            webWidget: {
                authenticate: {
                    chat: {
                        jwtFn: function(callback) {
                            fetch('/generate-zend-jwt').then(function(res) {
                                res.text().then(function(jwt) {
                                    console.log("check for execution")
                                    let jwtreplace = jwt.replace('"','', jwt)
                                    jwtreplace = jwtreplace.replace('"','', jwtreplace)
                                    callback(jwtreplace);
                                });
                            });
                        }
                    }
                }
            }
        };
        @endif
    </script>

@endif

</body>

</html>


    <!-- chat huggy abandonado -->
    {{-- <script>
        var $_PowerZAP = {
            defaultCountry: '+55',
            widget_id: '15840',
            company: "19537"
        }; (function(i,s,o,g,r,a,m){
            i[r]={
                context:{
                    id:'74ec354f8be1e7eb7f15b56a6e23fd69'
                }
            };
            a=o;o=s.createElement(o);
            o.async=1;o.src=g;m=s.getElementsByTagName(a)[0];
            m.parentNode.insertBefore(o,m);
        })(window,document,'script','https://js.huggy.chat/widget.min.js?v=8.0.0','pwz');
    </script> --}}
    <!-- End code pzw.io  -->
