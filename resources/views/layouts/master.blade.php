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
    <meta name="access-token" content="Bearer {{ auth()->check() && auth()->user()->status != 3 ? auth()->user()->createToken("Laravel Password Grant Client", ['admin'])->accessToken : ''  }}">
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
    <link rel="stylesheet" href="{{ asset('modules/global/css/loading.css?v=2') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/checkAnimation.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/ribbon.css') }}">
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery-mmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/jquery-imgareaselect/css/imgareaselect-default.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/css/sweetalert2.min.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/sortable/sortable.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/web-icons/web-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/font-awesome/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/newFonts.css?v=1') }}">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/global/css/materialdesignicons.min.css') }}">
    <!-- New CSS -->
    <link rel="stylesheet" href="{{ asset('modules/global/css/new-site.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/global.css?v=9') }}">
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

<div class="top-alert-container">
    <div class="top-alert warning" id="document-pending" style="display:none;">
        <div class="top-alert-message-container">
            <span class="top-alert-message">Existem itens pendentes em seu cadastro</span>
            <a href="/companies" class="top-alert-action">Finalizar cadastro</a>
        </div>
        <a class="top-alert-close">
            <i class="material-icons">close</i>
        </a>
    </div>
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
<script src="{{ asset('modules/global/adminremark/global/vendor/sortable/Sortable.js') }}"></script>
<script src="{{ asset('modules/global/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js') }}"></script>
<script src="{{ asset('modules/global/js/global.js?v=13') }}"></script>
<script>
    verifyDocumentPending();
</script>


@stack('scripts')

@if(env('APP_ENV', 'production') == 'production')

    <script src="{{ asset('modules/global/js-extra/pusher.min.js') }}"></script>

    <script src="{{ asset('modules/global/js/notifications.js?v=9') }}"></script>

    <script type="text/javascript"> 

        window.$crisp=[];
        window.CRISP_WEBSITE_ID="96ad410d-c6cf-4ffa-9763-be123b05acbd";

        (function(){ 
            d=document;
            s=d.createElement("script"); 
            s.src="https://client.crisp.chat/l.js"; 
            s.async=1;
            $crisp.push(["set", "user:email", '{{ auth()->user()->email }}']);
            $crisp.push(["set", "user:nickname", '{{ auth()->user()->name }}'])
            d.getElementsByTagName("head")[0].appendChild(s);        
        })(); 

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
