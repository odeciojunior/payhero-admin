<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation" style="background-color: white">
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
            <img id="logoIconSirius" class="navbar-brand-logo" src="{{ asset('/modules/global/img/logos/2021/svg/icon-sirius.svg') }}">
            <img id="logoSirius" class="navbar-brand-logo d-none logo-sirius" src="{{ asset('/modules/global/img/logos/2021/svg/sirius-logo.svg') }}" width="100">
        <!-- <span class="navbar-brand-text hidden-xs-down" style="color: black"> <img id="logoSirius" class="navbar-brand-logo"  width="100" height="80" src="{{ asset('/modules/global/img/logos/2021/svg/sirius-logo.svg') }}"> </span> -->
        </div>
        {{--        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-search" data-toggle="collapse">--}}
        {{--            <span class="sr-only">Toggle Search</span>--}}
        {{--            <i class="icon wb-search" aria-hidden="true"></i>--}}
        {{--        </button>--}}
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
                @hasanyrole('account_owner|admin')
                    <li id="notifications_button" class="nav-item dropdown" disabled='true'>
                        <span class="nav-link navbar-avatar" data-toggle="dropdown" title="Notificações" id='notification' aria-expanded="false" data-animation="scale-up" role="button" style='cursor:pointer'>
                            <img class="svg-menu" src="{{ asset('modules/global/img/svg/notificacao.svg') }}" alt="Notificacao">

                            @if( count(auth()->user()->unreadNotifications) > 0)
                                <span class="badge badge-notification" id="notification-amount"></span>
                            @else
                                <span class="badge badge-notification-false" id="notification-amount"></span>
                            @endif
                        </span>
                        <div id="notifications_card" class="dropdown-menu dropdown-menu-right dropdown-menu-media ">
                            <div id='notificationTemplate' class="scrollable-content"  img-empty="{!! asset('modules/global/img/notificacoes.svg')!!}">
                            </div>
                        </div>
                    </li>
                @endhasanyrole
                <li class="nav-item dropdown">
                    <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button">
                        <span class="avatar avatar-online">
                            <img class='img-user-menu-principal' src="{!! \Auth::user()->photo ? \Auth::user()->photo : 'https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/user-default.png' !!}" onerror="this.onerror=null; this.src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/user-default.png'" alt="">
                            <i></i>
                        </span>
                    </a>
                    <div id="dropdown_profile_card" class="dropdown-menu" role="menu">
                        @if(foxutils()->isHomolog())
                            <div data-toggle="tooltip" data-placement="left" title="Desabilitado na versão de testes">
                                <a class="dropdown-item disabled" disabled>
                                    <img height="24" width="24" src="{{ asset('modules/global/img/svg/settings.svg') }}"/>
                                    Configurações
                                </a>
                            </div>
                        @else
                            <a id="accounts-service" class="dropdown-item redirect-to-accounts" href="" data-url-value=""role="menuitem">
                                <img height="24" width="24" src="{{ asset('modules/global/img/svg/settings.svg') }}"/>
                                Configurações
                            </a>
                        @endif
                        <div class="dropdown-divider" role="presentation"></div>
                        <a class="dropdown-item" href="" role="menuitem" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <img height="24" width="24" src="{{ asset('modules/global/img/svg/power_settings_new.svg') }}"/>
                            Logout
                        </a>
                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    {{--        <div class="collapse navbar-search-overlap" id="site-navbar-search">--}}
    {{--            <form role="search">--}}
    {{--                <div class="form-group">--}}
    {{--                    <div class="input-search">--}}
    {{--                        <i class="input-search-icon wb-search" aria-hidden="true"></i>--}}
    {{--                        <input type="text" class="form-control" name="site-search" placeholder="Search">--}}
    {{--                        <button type="button" class="input-search-close icon wb-close" data-target="#site-navbar-search"--}}
    {{--                                data-toggle="collapse" aria-label="Close"></button>--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--            </form>--}}
    {{--        </div>--}}
    <!-- End Site Navbar Seach -->
    </div>
</nav>
{{--SIDE BAR--}}
<div class="site-menubar">
    <ul class="site-menu" style="margin-top:10px">
        <!-- hasanyrole('account_owner|admin|finantial') -->
        @can('dashboard')
            <li class="site-menu-item has-sub">
                <a href="{{ route('dashboard.index') }}">
                    <span class="bg-menu">
                        <img class="svg-menu" src="{{ asset('modules/global/img/svg/dashboard.svg') }}" alt="Dashboard">
                    </span>
                    <span class="site-menu-title ml-5">Dashboard</span>
                </a>
            </li>
        @endcan

{{--        @hasanyrole('account_owner|admin')--}}
{{--            <li class="site-menu-item has-sub disabled">--}}
{{--                <a class="disabled" href="/dashboard">--}}
{{--                    <span class="bg-menu">--}}
{{--                        <img src="{{ asset('modules/global/img/svg/vitrine.svg') }}" alt="Vitrine">--}}
{{--                    </span>--}}
{{--                    <span class="site-menu-title">Vitrine (em breve)</span>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--        @endhasanyrole--}}

        <!-- hasanyrole('account_owner|admin|attendance|finantial') -->
        @if(auth()->user()->hasAnyPermission(['sales','recovery','trackings','contestations']))
            <li class="site-menu-item has-sub">
                <a href="javascript:void(0)" id="sales-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/vendas.svg') }}" alt="Vendas">
                    </span>
                    <span class="site-menu-title">Vendas</span>
                    <span class="site-menu-arrow"></span>
                </a>
                <ul class="site-menu-sub">
                    @can('sales')
                    <li class="site-menu-item">
                        <a href="{!! route('sales.index') !!}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Visão geral</span>
                        </a>
                    </li>
                    @endcan
                    <!-- unlessrole('finantial') -->
                    @can('recovery')
                    <li class="site-menu-item">
                        <a href="{{ route('recovery.index') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Recuperação</span>
                        </a>
                    </li>
                    @endcan
                    {{--
                    <li class="site-menu-item">
                        <a href="{{ route('antifraud.index') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Antifraude</span>
                        </a>
                    </li>
                    --}}
                    @can('trackings')
                    <li class="site-menu-item">
                        <a href="{{ route('trackings.index') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Rastreamentos</span>
                        </a>
                    </li>
                    @endcan
                    <!-- hasanyrole('account_owner|admin|attendance') -->
                    @can('contestations')
                        <li class="site-menu-item">
                            <a href="{{ route('contestations.index') }}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">Contestações</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        <!-- hasanyrole('account_owner|admin') -->
        @can('projects')
            <li class="site-menu-item has-sub">
                <a href="/projects" id="projects-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/vitrine.svg') }}" alt="Lojas">
                    </span>
                    <span class="site-menu-title">Lojas</span>
                </a>
            </li>
        @endcan
        <!-- hasanyrole('account_owner|admin')         -->
        @can('products')
            <li class="site-menu-item has-sub">
                <a href="{{ route('products.index') }}" id="products-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/produtos.svg') }}" alt="Produtos">
                    </span>
                    <span class="site-menu-title">Produtos</span>
                </a>
            </li>
        @endcan
        <!-- hasanyrole('account_owner|admin|attendance') -->
        @can('attendance')
            <li class="site-menu-item has-sub">
                <a href="{{ route('attendance.index') }}">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/atendimento.svg') }}" alt="Atendimento">
                    </span>
                    <span class="site-menu-title">Atendimento</span>
                </a>
            </li>
        @endcan
        <!-- unlessrole('attendance') -->
        @can('finances')
        @php
            $user = auth()->user();
            $showOldFinances = $user->show_old_finances??false;
            if(!$showOldFinances){
                $userMaster = \Modules\Core\Entities\User::find($user->account_owner_id);
                $showOldFinances = $userMaster->show_old_finances??false;
            }
        @endphp
            <li class="site-menu-item has-sub">
                <a href="{!! route('finances') !!}" id="finances-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/financas.svg') }}" alt="Finanças">
                    </span>
                    <span class="site-menu-title">Finanças</span>
                </a>
            </li>

            @endcan

        <!-- hasanyrole('account_owner|admin|attendance|finantial') -->
        @if(auth()->user()->hasAnyPermission(['report_sales','report_checkouts','report_coupons','report_pending','report_blockedbalance']))
            <li class="site-menu-item has-sub">
                <a href="{!! route('reports.new') !!}" id="reports-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/relatorios.svg') }}" alt="Relatórios">
                    </span>
                    <span class="site-menu-title">Relatórios</span>
                    <!-- <span class="site-menu-arrow"></span> -->
                </a>
                <ul class="site-menu-sub">
                    <!-- hasanyrole('account_owner|admin|finantial') -->
                    @can('report_sales')
                        <!-- <li class="site-menu-item has-sub">
                            <a href="{!! route('reports.index') !!}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">Vendas</span>
                            </a>
                        </li> -->
                    @endcan

                    <!-- hasanyrole('account_owner|admin') -->
                    @can('report_checkouts')
                        <!-- <li class="site-menu-item">
                            <a href="{{ route('reports.checkouts') }}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">Acessos</span>
                            </a>
                        </li> -->
                    @endcan

                    <!-- hasanyrole('account_owner|admin|attendance') -->
                    @can('report_coupons')
                        <!-- <li class="site-menu-item">
                            <a href="{{ route('reports.coupons') }}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">Cupons de desconto</span>
                            </a>
                        </li> -->
                    @endcan

                    <!-- hasanyrole('account_owner|admin|finantial') -->
                    @can('report_pending')
                        <!-- <li class="site-menu-item">
                            <a href="{{ route('reports.pending') }}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">Saldo pendente</span>
                            </a>
                        </li> -->
                    @endcan

                    @can('report_blockedbalance')
                        <!-- <li class="site-menu-item">
                            <a href="{{ route('reports.blockedbalance') }}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">Saldo bloqueado</span>
                            </a>
                        </li> -->
                    @endcan
                </ul>
            </li>
        @endhasanyrole

        <!-- hasanyrole('account_owner|admin') -->
        @can('affiliates')
            <li class="site-menu-item has-sub">
                <a href="{{ route('projectaffiliates') }}" id="affiliates-link">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/afiliados.svg') }}" alt="Afiliados">
                    </span>
                    <span class="site-menu-title mb-5">Afiliados</span>
                </a>
            </li>
        @endcan
        <!-- hasanyrole('account_owner|admin') -->
        @can('apps')
            <li class="site-menu-item has-sub">
                <a href="{{ route('apps') }}" id='apps-link'>
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/aplicativos.svg') }}" alt="Aplicativos">
                    </span>
                    <span class="site-menu-title">Aplicativos</span>
                </a>
            </li>
        @endcan
        @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
            <li class="site-menu-item has-sub">
                <a href="{{ route('integrations.index') }}" id='api-sirius-link'>
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/api-sirius-menu.svg') }}" alt="API Sirius">
                    </span>
                    <span class="site-menu-title">API Sirius</span>
                </a>
            </li>
        @endif
        <!-- hasanyrole('account_owner')         -->
        @can('invitations')
            <li class="site-menu-item has-sub">
                <a href="{{ route('invitations.index') }}">
                    <span class="bg-menu">
                        <img src="{{ asset('modules/global/img/svg/convites.svg') }}" alt="Convites">
                    </span>
                    <span class="site-menu-title">Convites</span>
                </a>
            </li>
        @endcan
    </ul>
</div>
