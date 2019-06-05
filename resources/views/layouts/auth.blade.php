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
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">
    
    <link rel="apple-touch-icon" href="{{ asset('adminremark/assets/images/apple-touch-icon.png') }}">
    <link rel="shortcut icon"  href="{{ asset('adminremark/assets/images/favicon.ico') }}">
    
    <!-- Styles -->

    <link rel="stylesheet" href="{{ asset('adminremark/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/assets/css/new-login.css') }}">


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

    <script src="{{ asset('adminremark/global/vendor/breakpoints/breakpoints.js') }}"></script>
    <script>
      Breakpoints();
    </script>

  </head>
  <body>

  <style>
      body {
	font-family: 'Muli', sans-serif;
	min-height: 100vh;
	background: url({{ asset('adminremark/assets/images/gradient-bg.png') }}) bottom left no-repeat;
}
</style>
    @yield('content')

    <!-- <script src="{{ asset('adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/jquery/jquery.js') }}"></script>

    <script src="{{ asset('adminremark/global/vendor/popper-js/umd/popper.min.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/animsition/animsition.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/ashoverscroll/jquery-asHoverScroll.js') }}"></script>
    
    <script src="{{ asset('adminremark/global/vendor/switchery/switchery.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/intro-js/intro.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/screenfull/screenfull.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
        <script src="{{ asset('adminremark/global/vendor/jquery-placeholder/jquery.placeholder.js') }}"></script>
    
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
    
    <script src="{{ asset('adminremark/assets/js/Site.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/asscrollable.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/slidepanel.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/switchery.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/jquery-placeholder.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/material.js') }}"></script> -->

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

