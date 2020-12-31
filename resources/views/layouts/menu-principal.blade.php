<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation"
     style="background-color: white">
    <div class="navbar-header">
        <button type="button"
                class="navbar-toggler hamburger hamburger-close hamburger-arrow-left navbar-toggler-left hided"
                data-toggle="menubar">
            <span class="sr-only">Toggle navigation</span>
            <span class="hamburger-bar"></span>
        </button>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
                data-toggle="collapse">
            <i class="icon wb-more-horizontal" aria-hidden="true"></i>
        </button>
        <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
            <img id="logoIconSirius" class="navbar-brand-logo" src="{{ asset('modules/global/adminremark/assets/images/siriusM.svg') }}">
            <img id="logoSirius" class="navbar-brand-logo d-none" src="{{ asset('modules/global/adminremark/assets/images/sirius.svg') }}">
            <!-- <span class="navbar-brand-text hidden-xs-down" style="color: black"> <img id="logoSirius" class="navbar-brand-logo"  width="100" height="80" src="{{ asset('modules/global/adminremark/assets/images/sirius.svg') }}"> </span> -->
        </div>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-search" data-toggle="collapse">
            <span class="sr-only">Toggle Search</span>
            <i class="icon wb-search" aria-hidden="true"></i>
        </button>
    </div>
    <div class="navbar-container container-fluid">
        <input type='hidden' id='user'
               value='{{Vinkla\Hashids\Facades\Hashids::connection('pusher_connection')->encode(auth()->user()->id)}}'>
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
                @if(!auth()->user()->hasRole('attendance'))
                    <li id="notifications_button" class="nav-item dropdown" disabled='true'>
                    <span class="nav-link navbar-avatar" data-toggle="dropdown" title="Notificações" id='notification'
                          aria-expanded="false" data-animation="scale-up" role="button" style='cursor:pointer'>
                        <img class="svg-menu" src="{{ asset('modules/global/img/svg/notificacao.svg') }}" alt="Notificacao">
                        <span class="badge badge-primary badge-notification" id="notification-amount">{{count(auth()->user()->unreadNotifications)}}</span>
                    </span>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-media">
                            <div class="dropdown-menu-header" style='padding:0px 20px;'>
                                <h6><strong>NOTIFICAÇÕES</strong></h6>
                            </div>
                            <div class="list-group scrollable scrollable-vertical" style="position: relative;">
                                <div class="scrollable-container" style="min-height: 250px; width: 358px;">
                                    <div id='notificationTemplate' class="scrollable-content"
                                         style="width: 358px; height:100%">
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-menu-footer"
                                 style='margin:0px;background-image: linear-gradient(11deg, #e6774c, rgb(249, 34, 120))'>
                            </div>
                        </div>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
                       data-animation="scale-up" role="button">
                <span class="avatar avatar-online">
                  <img class='img-user-menu-principal'
                       src="{!! \Auth::user()->photo ? \Auth::user()->photo : 'https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/user-default.png' !!}"
                       onerror="this.onerror=null; this.src='https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/user-default.png'"
                       alt="">
                  <i></i>
                </span>
                    </a>
                    <div class="dropdown-menu" role="menu">
                        <a id="accounts-service" class="dropdown-item redirect-to-accounts" href="" data-url-value="" role="menuitem">
                            <!-- <i class="material-icons align-middle"> settings </i> Configurações -->
                            <img  height="24" width="24" src="{{ asset('modules/global/img/svg/settings.svg') }}" />
                            Configurações
                        </a>
                        <div class="dropdown-divider" role="presentation"></div>
                        <a class="dropdown-item" href="" role="menuitem"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <!-- <i class="material-icons align-middle"> power_settings_new </i> Logout -->
                            <img  height="24" width="24" src="{{ asset('modules/global/img/svg/power_settings_new.svg') }}" />
                            Logout
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
                        <button type="button" class="input-search-close icon wb-close" data-target="#site-navbar-search"
                                data-toggle="collapse" aria-label="Close"></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- End Site Navbar Seach -->
    </div>
</nav>
{{--SIDE BAR--}}
<div class="site-menubar">
    <ul class="site-menu" style="margin-top:10px">
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
            <li class="site-menu-item has-sub">
                <a href="{{ route('dashboard.index') }}">
                    <span class="bg-menu">
                        <img class="svg-menu" src="{{ asset('modules/global/img/svg/dashboard.svg') }}" alt="Dashboard">
                    </span>
                    <span class="site-menu-title ml-5">Dashboard</span>
                </a>
            </li>
        @endif
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
            <li class="site-menu-item has-sub disabled">
                <a class="disabled" href="{{ route('showcase') }}">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/vitrine.svg') }}" alt="Vitrine">
                    </span>
                    <span class="site-menu-title">Vitrine (em breve)</span>
                </a>
            </li>
        @endif
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('attendance'))
            <li class="site-menu-item has-sub">
                <a href="javascript:void(0)" id="sales-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/vendas.svg') }}" alt="Vendas">
                    </span>
                    <span class="site-menu-title">Vendas</span>
                    <span class="site-menu-arrow"></span>
                </a>
                <ul class="site-menu-sub">
                    <li class="site-menu-item">
                        <a href="{!! route('sales.index') !!}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Visão geral</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('recovery.index') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Recuperação</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('antifraud.index') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Antifraude</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('trackings.index') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Rastreamentos</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
            <li class="site-menu-item has-sub">
                <a href="/projects" id="projects-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/projetos.svg') }}" alt="Projetos">
                    </span>
                    <span class="site-menu-title">Projetos</span>
                </a>
            </li>
        @endif
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
            <li class="site-menu-item has-sub">
                <a href="{{ route('products.index') }}" id="products-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/produtos.svg') }}" alt="Produtos">
                    </span>
                    <span class="site-menu-title">Produtos</span>
                </a>
            </li>
        @endif
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('attendance'))
            <li class="site-menu-item has-sub">
                <a href="{{ route('attendance.index') }}">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/atendimento.svg') }}" alt="Atendimento">
                    </span>
                    <span class="site-menu-title">Atendimento</span>
                </a>
            </li>
        @endif
        @if(!auth()->user()->hasRole('attendance'))
            @if(!auth()->user()->has_sale_before_getnet)
                <li class="site-menu-item has-sub">
                    <a href="{!! route('finances') !!}">
                        <div>
                            <img src="{{ asset('modules/global/img/svg/financas.svg') }}" alt="Finanças">
                        </div>
                        <span class="site-menu-title">Finanças</span>
                    </a>
                </li>
            @else
                <li class="site-menu-item has-sub">
                    <a href="javascript:void(0)" id="finances-link">
                        <span class="bg-menu">
                            <img src="{{ asset('modules/global/img/svg/financas.svg') }}" alt="Finanças">
                        </span>
                        <span class="site-menu-title">Finanças</span>
                        <span class="site-menu-arrow"></span>
                    </a>
                    <ul class="site-menu-sub">
                        <li class="site-menu-item has-sub">
                            <a href="{!! route('finances') !!}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">Extrato</span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a href="{{ route('old-finances') }}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">Extrato (antigo)</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        @endif
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
            <li class="site-menu-item has-sub">
                <a href="javascript:void(0)" id="reports-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/relatorios.svg') }}" alt="Relatórios">
                    </span>
                    <span class="site-menu-title">Relatórios</span>
                    <span class="site-menu-arrow"></span>
                </a>
                <ul class="site-menu-sub">
                    <li class="site-menu-item has-sub">
                        <a href="{!! route('reports.index') !!}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Vendas</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('reports.checkouts') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Acessos</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('reports.coupons') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Cupons de desconto</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('reports.pending') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Saldo pendente</span>
                        </a>
                    </li>

                    <li class="site-menu-item">
                        <a href="{{ route('reports.blockedbalance') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Saldo bloqueado</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
            <li class="site-menu-item has-sub">
                <a href="{{ route('projectaffiliates') }}" id="affiliates-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/afiliados.svg') }}" alt="Afiliados">
                    </span>
                    <span class="site-menu-title mb-5">Afiliados</span>
                </a>
            </li>
        @endif
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
            <li class="site-menu-item has-sub">
                <a href="{{ route('apps') }}" id='apps-link'>
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/aplicativos.svg') }}" alt="Aplicativos">
                    </span>
                    <span class="site-menu-title">Aplicativos</span>
                </a>
            </li>
        @endif
        @if(auth()->user()->hasRole('account_owner'))
            <li class="site-menu-item has-sub">
                <a href="{{ route('invitations.index') }}">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/convites.svg') }}" alt="Convites">
                    </span>
                    <span class="site-menu-title">Convites</span>
                </a>
            </li>
        @endif
    </ul>
</div>
