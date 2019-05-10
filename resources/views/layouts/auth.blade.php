<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js css-menubar">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CloudFox @yield('title')</title> 

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    
    <link rel="apple-touch-icon" href="{{ asset('adminremark/assets/images/apple-touch-icon.png') }}">
    <link rel="shortcut icon"  href="{{ asset('adminremark/assets/images/favicon.ico') }}">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet"href="{{ asset('adminremark/global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/assets/css/site.min.css') }}">
    
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/asscrollable/asScrollable.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/switchery/switchery.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/intro-js/introjs.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/slidepanel/slidePanel.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/flag-icon-css/flag-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark2/assets/examples/css/pages/login-v3.css') }}">

    <link rel="stylesheet" href="{{ asset('adminremark/assets/css/style.css') }}">
    
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('adminremark/global/fonts/material-design/material-design.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/fonts/brand-icons/brand-icons.min.css') }}">
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
    
    <!-- Scripts -->
    <script src="{{ asset('adminremark/global/vendor/breakpoints/breakpoints.js') }}"></script>
    <script>
      Breakpoints();
    </script>

  </head>
  <body class="animsition page-login-v3 layout-full">

    @yield('content')

    <script src="{{ asset('adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/jquery/jquery.js') }}"></script>

    <script src="{{ asset('adminremark/global/vendor/popper-js/umd/popper.min.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/animsition/animsition.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/ashoverscroll/jquery-asHoverScroll.js') }}"></script>
    
    <!-- Plugins -->
    <script src="{{ asset('adminremark/global/vendor/switchery/switchery.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/intro-js/intro.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/screenfull/screenfull.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
        <script src="{{ asset('adminremark/global/vendor/jquery-placeholder/jquery.placeholder.js') }}"></script>
    
    <!-- Scripts -->
    <script src="{{ asset('adminremark/global/js/Component.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Base.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Config.js') }}"></script>
    
    <script src="{{ asset('adminremark/assets/js/Section/Menubar.js') }}"></script>
    <script src="{{ asset('adminremark/assets/js/Section/GridMenu.js') }}"></script>
    <script src="{{ asset('adminremark/assets/js/Section/Sidebar.js') }}"></script>
    <script src="{{ asset('adminremark/assets/js/Section/PageAside.js') }}"></script>
    <script src="{{ asset('adminremark/assets/js/Plugin/menu.js') }}"></script>
    
    <script src="{{ asset('adminremark/global/js/config/colors.js') }}"></script>
    <script src="{{ asset('adminremark/assets/js/config/tour.js') }}"></script>
    <script>Config.set('assets', '../../assets');</script>
    
    <!-- Page -->
    <script src="{{ asset('adminremark/assets/js/Site.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/asscrollable.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/slidepanel.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/switchery.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/jquery-placeholder.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/material.js') }}"></script>

    <script>
      (function(document, window, $){
        'use strict';
    
        var Site = window.Site;
        $(document).ready(function(){
          Site.run();
        });
      })(document, window, jQuery);
    </script>
    
  </body>
</html>

