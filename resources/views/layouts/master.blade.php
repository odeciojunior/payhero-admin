<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="CloudFox">
    <meta name="author" content="">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CloudFox @yield('title')</title> 
    <link rel="apple-touch-icon" href="{{ asset('adminremark/assets/images/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('adminremark/assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/loading.css') }}">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/assets/css/site.min.css') }}">
	<link rel='stylesheet' href="{{ asset('/assets/css/sweetalert2.min.css') }}">

    <!-- Datatables -->
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/jquery-datatable/yadcf/jquery.dataTables.yadcf.js') }}">

    <!-- Plugins -->
    {{--  <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/ascolorpicker/asColorPicker.min.css?v4.0.2') }}">      --}}
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

    @yield('styles')
    <!-- Plugins For This Page -->
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/datatables.net-bs4/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/datatables.net-fixedheader-bs4/dataTables.fixedheader.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/datatables.net-fixedcolumns-bs4/dataTables.fixedcolumns.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/datatables.net-rowgroup-bs4/dataTables.rowgroup.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/datatables.net-scroller-bs4/dataTables.scroller.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/datatables.net-select-bs4/dataTables.select.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/datatables.net-responsive-bs4/dataTables.responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/vendor/datatables.net-buttons-bs4/dataTables.buttons.bootstrap4.min.css') }}">

    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('adminremark/assets/examples/css/tables/datatable.min.css') }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('adminremark/global/fonts/weather-icons/weather-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/fonts/web-icons/web-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminremark/global/fonts/brand-icons/brand-icons.min.css') }}">
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>

    <link rel="stylesheet" href="{{ asset('jquery-imgareaselect/css/imgareaselect-default.css') }}">

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

    <div class="loading">
        <div class="loader"></div>
    </div>

    @include("layouts.menu-principal")

    @yield('content')
    <footer class="site-footer">

        <div class="site-footer-right">© 2019 - CloudFox</div>

    </footer>

    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>

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
    {{--  <script src="{{ asset('adminremark/global/vendor/aspieprogress/jquery-asPieProgress.min.js') }}"></script>  --}}
    <script src="{{ asset('adminremark/global/vendor/jvectormap/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/jvectormap/maps/jquery-jvectormap-au-mill-en.js') }}"></script>
    <script src="{{ asset('adminremark/global/vendor/matchheight/jquery.matchHeight-min.js') }}"></script>
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>

    <!-- Scripts -->
    <script src="{{ asset('adminremark/global/js/Component.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Base.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Config.js') }}"></script>
    {{--  <script src="{{ asset('adminremark/global/vendor/clockpicker/bootstrap-clockpicker.min.js?v4.0.2') }}"></script>  --}}
    {{--  <script src="{{ asset('adminremark/global/vendor/ascolor/jquery-asColor.min.js?v4.0.2') }}"></script>  --}}
    {{--  <script src="{{ asset('adminremark/global/vendor/ascolorpicker/jquery-asColorPicker.min.js?v4.0.2') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/ascolorpicker.js') }}"></script>  --}}
    <script src="{{ asset('adminremark/global/js/Plugin/tabs.js') }}"></script>
    
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

    <script src="{{ asset('adminremark/global/js/Plugin/datatables.js') }}"></script>
    <script src="{{ asset('adminremark/assets/examples/js/tables/datatable.js') }}"></script>
    <script src="{{ asset('vendor/jquery-datatable/yadcf/i18n.js') }}"></script>
    <script src="{{ asset('vendor/jquery-datatable/yadcf/jquery.dataTables.yadcf.js') }}"></script>
    <script src="{{ asset('adminremark/global/js/Plugin/matchheight.js') }}"></script>

    <script src="{{ asset('jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js') }}"></script>

    @yield('scripts')

    @if(isset($dataTable))
        {!! $dataTable->scripts() !!}
        <script>

                yadcf.init(window.LaravelDataTables["dataTableBuilder"] ,[{
                        column_number: 0,
                        filter_type: "text"
                    }, {
                        column_number: 1,
                        filter_type: "text"
                    }, {
                        column_number: 2,
                        filter_type: "text"
                    }, {
                        column_number: 3,
                        filter_type: 'select',
                        data: [{
                            value: 'Boleto',
                            label: 'Boleto'
                        }, {
                            value: 'Cartão de crédito',
                            label: 'Cartão de crédito'
                        }],
                    }, {
                        column_number: 4,
                        filter_type: 'select',
                        data: [{
                            value: 'Aprovada',
                            label: 'Aprovada'
                        }, {
                            value: 'Rejeitada',
                            label: 'Rejeitada'
                        }, {
                            value: 'Pendente',
                            label: 'Pendente'
                        }],
                    }, {
                        column_number: 5,
                        filter_type: "text"
                    }, {
                        column_number: 6,
                        filter_type: "text"
                    }, {
                        column_number: 7,
                        filter_type: "text"
                    }, {
                        column_number: 8,
                        filter_type: "text"
                    }, {
                        column_number: 9,
                        filter_type: "text"
                    }
                ]);

                $("th").css('padding','7px');

        </script>
    @endif

    </body>
</html>
