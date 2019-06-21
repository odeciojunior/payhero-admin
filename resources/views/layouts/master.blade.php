<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="cloudfox">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('adminremark/assets/images/cloudfox_logo.png') }}">
     <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">

    <title>Cloudfox</title>

     <!-- Datatables -->
     <!-- <link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"> -->

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark2/assets/css/site.min.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/assets/css/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/loading.css') }}">

    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/asscrollable/asScrollable.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/switchery/switchery.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/intro-js/introjs.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/slidepanel/slidePanel.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery-mmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/flag-icon-css/flag-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/jquery-imgareaselect/css/imgareaselect-default.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/font-awesome/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/assets/examples/css/uikit/icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/assets/examples/css/advanced/masonry.min.css') }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/weather-icons/weather-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/web-icons/web-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/brand-icons/brand-icons.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">    
    <link rel="stylesheet" href="{{ asset('modules/global/jquery-imgareaselect/css/imgareaselect-default.css') }}">

    <!-- New CSS -->
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/new-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/new-site.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/finances.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/assets/css/global.css') }}">

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
            {{--  <img src="{{asset('modules/global/gif/cloudfox-loading-1.gif')}}">  --}}
        </div>
    </div>

    <style>
        body { font-family: 'Muli', sans-serif !important;}
    </style>

    @include("layouts.menu-principal")

    @yield('content')

    <!-- <footer class="site-footer">
        <div class="site-footer-right">© 2019 - CloudFox | help@cloudfox.app - <a href="/terms" target="_blank" style="color:#e54724">Terms & Conditions</a></div>
    </footer> -->

    <!-- Core  -->
    <script src="{{ asset('modules/global/adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/popper-js/umd/popper.min.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>

    <!-- Plugins -->
    <script src="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery.mmenu.min.all.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/switchery/switchery.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/intro-js/intro.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/screenfull/screenfull.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/skycons/skycons.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/matchheight/jquery.matchHeight-min.js') }}"></script>
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
    <script src="{{ asset('modules/global/assets/js/sweetalert2.all.min.js') }}"></script>

    <!-- Scripts -->
    <script src="{{ asset('modules/global/adminremark/global/js/Component.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Base.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Config.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/tabs.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/masonry/masonry.pkgd.min.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/masonry.js') }}"></script>

    <script src="{{ asset('modules/global/adminremark2/assets/js/Section/Menubar.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark2/assets/js/Section/Sidebar.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark2/assets/js/Section/PageAside.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark2/assets/js/Section/GridMenu.js') }}"></script>

    <!-- Config -->
    <script src="{{ asset('modules/global/adminremark/global/js/config/colors.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark2/assets/js/config/tour.js') }}"></script>

    <!-- Page -->
    <script src="{{ asset('modules/global/adminremark2/assets/js/Site.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/asscrollable.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/slidepanel.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/switchery.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/js/Plugin/matchheight.js') }}"></script>

    <script src="{{ asset('modules/global/adminremark2/assets/examples/js/dashboard/v1.js') }}"></script>

    <script src="{{ asset('modules/global/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js') }}"></script>
    <script src="{{ asset('modules/global/assets/js/notificacoes.js') }}"></script>
    <script src="{{ asset('modules/global/js/global.js') }}"></script>

    @stack('scripts')

  </body>
</html>
