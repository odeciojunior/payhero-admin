<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation" style="background-color: white">
    <div class="navbar-header">
        <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided" data-toggle="menubar">
            <span class="sr-only">Toggle navigation</span>
            <span class="hamburger-bar"></span>
        </button>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse" data-toggle="collapse">
            <i class="icon wb-more-horizontal" aria-hidden="true"></i>
        </button>
        <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
            <img class="navbar-brand-logo" src="{{ asset('modules/global/adminremark/assets/images/cloudfox_logo.png') }}" title="Remark">
            <span class="navbar-brand-text hidden-xs-down" style="color: black"> <span style="font-weight: 300;">Cloud</span><strong>Fox</strong></span>
        </div>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-search" data-toggle="collapse">
            <span class="sr-only">Toggle Search</span>
            <i class="icon wb-search" aria-hidden="true"></i>
        </button>
    </div>
    <div class="navbar-container container-fluid">
        <input type='hidden' id='user' value='{{Vinkla\Hashids\Facades\Hashids::connection('pusher_connection')->encode(auth()->user()->id)}}'>
        <!-- Navbar Collapse -->
        <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
            <!-- Navbar Toolbar -->
            <ul class="nav navbar-toolbar">
                <li class="nav-item hidden-float" id="toggleMenubar">
                    <a class="nav-link" data-toggle="menubar" href="#" role="button">
                        <i class="icon hamburger hamburger-arrow-left">
                            <span class="sr-only">Toggle menubar</span>
                            <span class="hamburger-bar"></span>
                        </i>
                    </a>
                </li>
            </ul>
            <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
                <li id="notifications_button" class="nav-item dropdown" disabled='true'>
                    <span class="nav-link" data-toggle="dropdown" title="Notifications" id='notification'
                          aria-expanded="false" data-animation="scale-up" role="button" style='cursor:pointer'>
                        <i class="material-icons">notifications_none</i>
                        <span class="badge badge-danger badge-notification" id="notification-amount">{{count(auth()->user()->unreadNotifications)}}</span>
                    </span>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-media">
                        <div class="dropdown-menu-header">
                            <h5>NOTIFICAÇÕES</h5>
                            <span id='notificationBadge' class="badge badge-pill badge-danger"></span>
                        </div>
                        <div class="list-group scrollable scrollable-vertical" style="position: relative;">
                            <div class="scrollable-container" style="min-height: 140px; width: 358px;">
                                <div id='notificationTemplate' class="scrollable-content" style="width: 358px;">

                                </div>
                            </div>
                        </div>
                        <div class="dropdown-menu-footer">
                            <span class="dropdown-menu-footer-btn" role="button">

                            </span>
                            <span class="dropdown-item" role="menuitem">
                                NOTIFICAÇÕES
                            </span>
                        </div>
                    </div>
                    {{--<div class="dropdown-menu dropdown-menu-right dropdown-menu-media no-border" role="menu">
                        <div class="dropdown-menu-header">
                            <h5>NOTIFICAÇÕES</h5>
                            <span id='notificationBadge' class="badge badge-round badge-danger"></span>
                        </div>
                        <div class="list-group scrollable is-enabled scrollable-vertical">
                            <div data-role="container" style="width: 100%; height: auto;">
                                <div id='notificationTemplate' data-role="content" style="width: 100%; ">

                                    @if(count(\Auth::user()->unreadNotifications) > 0)
                                        @foreach(\Auth::user()->unreadNotifications as $notification)
                                            @include('notifications::' . str_replace("Modules\\Checkout\\Notifications\\","",$notification->type), ['notification' => $notification])
                                        @endforeach
                                    @else
                                        <div class="media d-flex align-items-center p-20">
                                            <div class="pr-10">
                                                <i class="material-icons">check</i>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="">Nenhuma nova notificação</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>--}}
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button">
                <span class="avatar avatar-online">
                  <img src="{!! \Auth::user()->photo ? \Auth::user()->photo : 'https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/user-default.png' !!}" alt="">
                  <i></i>
                </span>
                    </a>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item" href="{!! route('profile.index') !!}" role="menuitem">
                            <i class="material-icons align-middle"> account_circle </i> Perfil
                        </a>
                        <a class="dropdown-item" href="{!! route('companies.index') !!}" role="menuitem">
                            <i class="material-icons align-middle"> business </i> Empresas
                        </a>
                        <div class="dropdown-divider" role="presentation"></div>
                        <a class="dropdown-item" href="" role="menuitem" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="material-icons align-middle"> power_settings_new </i> Logout
                        </a>
                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    </div>
                </li>
            </ul>
        </div>
        <div class="collapse navbar-search-overlap" id="site-navbar-search">
            <form role="search">
                <div class="form-group">
                    <div class="input-search">
                        <i class="input-search-icon wb-search" aria-hidden="true"></i>
                        <input type="text" class="form-control" name="site-search" placeholder="Search">
                        <button type="button" class="input-search-close icon wb-close" data-target="#site-navbar-search" data-toggle="collapse" aria-label="Close"></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- End Site Navbar Seach -->
    </div>
</nav>
<div class="site-menubar">
    <ul class="site-menu" style="margin-top:10px">
        <li class="site-menu-item has-sub">
            <a href="{{ route('dashboard') }}">
                <i class="material-icons align-middle">dashboard</i>
                <span class="site-menu-title ml-5">Dashboard</span>
            </a>
        </li>
        <li class="site-menu-item has-sub disabled">
            <a class="disabled" href="{{ route('showcase') }}">
                <i class="material-icons align-middle">store</i>
                <span class="site-menu-title">Vitrine (em breve)</span>
            </a>
        </li>
        <li class="site-menu-item has-sub">
            <a href="javascript:void(0)" id="sales-link">
                <!-- <i class="material-icons align-middle"><span class="mm-opended-hidden">shopping_basket</span></i> -->
                <svg class="svg-menu align-middle" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M0 0h24v24H0z" fill="none"/>
                    <path d="M17.21 9l-4.38-6.56c-.19-.28-.51-.42-.83-.42-.32 0-.64.14-.83.43L6.79 9H2c-.55 0-1 .45-1 1 0 .09.01.18.04.27l2.54 9.27c.23.84 1 1.46 1.92 1.46h13c.92 0 1.69-.62 1.93-1.46l2.54-9.27L23 10c0-.55-.45-1-1-1h-4.79zM9 9l3-4.4L15 9H9zm3 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                </svg>
                <span class="site-menu-title">Vendas</span>
                <span class="site-menu-arrow"></span>
            </a>
            <ul class="site-menu-sub">
                <li class="site-menu-item has-sub">
                    <a href="{!! route('sales') !!}">
                        <span class="site-menu-title">Visão geral</span>
                    </a>
                </li>
                <li class="site-menu-item">
                    <a href="{!! route('cartrecovery') !!}">
                        <span class="site-menu-title">Recuperação</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="site-menu-item has-sub">
            <a href="/projects" id="projects-link">
                <i class="material-icons">style</i>
                <span class="site-menu-title">Projetos</span>
            </a>
        </li>
        <li class="site-menu-item has-sub">
            <a href="{{ route('products.index') }}" id="products-link">
                <i class="material-icons">laptop</i>
                <span class="site-menu-title">Produtos</span>
            </a>
        </li>
        <li class="site-menu-item has-sub  disabled">
            <a class="disabled" href="{{ route('attendance') }}">
                <i class="material-icons">chat_bubble_outline</i>
                <span class="site-menu-title">Atendimento (em breve)</span>
            </a>
        </li>
        <li class="site-menu-item has-sub  disabled">
            <a href="javascript:void(0)" class="disabled">
                <i class="material-icons">people</i>
                <span class="site-menu-title">Afiliados (em breve)</span>
                <span class="site-menu-arrow"></span>
            </a>
            <ul class="site-menu-sub">
                <li class="site-menu-item">
                    <a href="{!! route('afiliados.minhasafiliacoes') !!}">
                        <span class="site-menu-title">Minhas afiliações</span>
                    </a>
                </li>
                <li class="site-menu-item">
                    <a href="{!! route('afiliados.meusafiliados') !!}">
                        <span class="site-menu-title">Meus afiliados</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="site-menu-item has-sub">
            <a href="{!! route('finances') !!}">
                <i class="material-icons align-middle">local_atm</i>
                <span class="site-menu-title">Finanças</span>
            </a>
        </li>
        <li class="site-menu-item has-sub">
            <a href="{!! route('reports.index') !!}">
                <i class="material-icons">insert_chart_outlined</i>
                <span class="site-menu-title">Relatórios</span>
            </a>
        </li>
        <li class="site-menu-item has-sub">
            <a href="{{ route('apps') }}">
                <i class="material-icons">apps</i>
                <span class="site-menu-title">Aplicativos</span>
            </a>
        </li>
        <li class="site-menu-item has-sub">
            <a href="{{ route('invitations.invites') }}">
                <i class="material-icons">person_add</i>
                <span class="site-menu-title">Convites</span>
            </a>
        </li>
    </ul>
</div>
<!--div class="site-menubar-footer">
      <div class="menu-footer-content hide-menu fold-hide"> CloudFox LLC <br> <a href="#"> Termos e Condições </a>  </div>

    <a href="javascript: void(0);" data-placement="center" data-toggle="tooltip" data-original-title="Settings">
      <span class="icon wb-settings" aria-hidden="true"></span>
      <div class="menu-footer-content hide-menu "> CloudFox LLC <br> <a href="#"> Termos e Condições </a>  </div>
    </a>
    <a href="javascript: void(0);" data-placement="top" data-toggle="tooltip" data-original-title="Lock">
      <span class="icon wb-eye-close" aria-hidden="true"></span>
    </a>
    <a href="javascript: void(0);" class="fold-show" data-placement="top" data-toggle="tooltip" data-original-title="Logout">
      <span class="icon wb-power" aria-hidden="true"></span>
    </a>

</div-->
</div>
<script>
    var links = $('.site-menubar .site-menu-item a');
    $.each(links, function (key, va) {
        if (va.href == document.URL) {
            $(this).addClass('menu-active');
        }
    });

    // $( document ).ready(function() {
    //   if ('mm.panels').hasClass('is-enabled') {
    //     $('.menu-footer').hide;
    //   }

    //   if('mm.panels').hasClass('is-disabled') {
    //     $('.menu-footer').show;
    //   }
    // });
</script>
