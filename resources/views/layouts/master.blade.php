{{-- <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Module Cliente</title>
    </head>
    <body>
        @yield('content')
    </body>
</html> --}}


<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">
    
    <title>Dashboard | Remark Admin Template</title>
    <link rel="apple-touch-icon" href="{{ asset('adminremark/assets/images/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('adminremark/assets/images/favicon.ico') }}">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/assets/css/site.min.css') }}">
    
    <!-- Datatables -->
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/asscrollable/asScrollable.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/switchery/switchery.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/intro-js/introjs.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/slidepanel/slidePanel.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/flag-icon-css/flag-icon.css') }}">
        <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/chartist/chartist.css') }}">
        <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/jvectormap/jquery-jvectormap.css') }}">
        <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css') }}">
        <link rel="stylesheet" href="{{ asset('adminremark/assets/examples/css/dashboard/v1.css') }}">
    
    
    <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('adminremark/global/fonts/weather-icons/weather-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/fonts/web-icons/web-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/fonts/brand-icons/brand-icons.min.css') }}">
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
    
    <!--[if lt IE 9]>
    <script src="../../global/vendor/html5shiv/html5shiv.min.js"></script>
    <![endif]-->
    
    <!--[if lt IE 10]>
    <script src="../../global/vendor/media-match/media.match.min.js"></script>
    <script src="../../global/vendor/respond/respond.min.js"></script>
    <![endif]-->
    
    <!-- Scripts -->
    <script src="{{ asset('adminremark/global/vendor/jquery/jquery.js') }}"></script>

    <!-- Datatables -->
    <script src="http://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <script src="{{ asset('adminremark/global/vendor/breakpoints/breakpoints.js') }}"></script>
    <script>
      Breakpoints();
    </script>
  </head>
  <body class="animsition dashboard">
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    @include("layouts.menu-lateral")
   

     <!-- Page -->   
        @yield('content')
    <!-- End Page -->


    <!-- Footer -->
    <footer class="site-footer">
      <div class="site-footer-legal">© 2018 <a href="http://themeforest.net/item/remark-responsive-bootstrap-admin-template/11989202">Remark</a></div>
      <div class="site-footer-right">
        Crafted with <i class="red-600 wb wb-heart"></i> by <a href="https://themeforest.net/user/creation-studio">Creation Studio</a>
      </div>
    </footer>
    <!-- Core  -->
    <script src="{{ asset('adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
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
        <script src="{{ asset('adminremark/global/vendor/skycons/skycons.js') }}"></script>
        <script src="{{ asset('adminremark/global/vendor/chartist/chartist.min.js') }}"></script>
        <script src="{{ asset('adminremark/global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.js') }}"></script>
        <script src="{{ asset('adminremark/global/vendor/aspieprogress/jquery-asPieProgress.min.js') }}"></script>
        <script src="{{ asset('adminremark/global/vendor/jvectormap/jquery-jvectormap.min.js') }}"></script>
        <script src="{{ asset('adminremark/global/vendor/jvectormap/maps/jquery-jvectormap-au-mill-en.js') }}"></script>
        <script src="{{ asset('adminremark/global/vendor/matchheight/jquery.matchHeight-min.js') }}"></script>
    

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
    
        <script src="{{ asset('adminremark/global/js/Plugin/matchheight.js') }}"></script>
        <script src="{{ asset('adminremark/global/js/Plugin/jvectormap.js') }}"></script>
        <script src="{{ asset('adminremark/global/js/Plugin/material.js') }}"></script>
        <script src="{{ asset('adminremark/assets/examples/js/dashboard/v1.js') }}"></script>

   
  </body>
</html>
