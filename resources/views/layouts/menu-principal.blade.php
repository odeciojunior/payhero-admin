<!-- BARRA SUPERIOR DE NAVEGACAO -->
<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega"
     role="navigation">

    <!-- BOTAO DE HAMBURGUER NO MOBILE-->
    <button type="button"
            class="navbar-toggler hamburger hamburger-close hamburger-arrow-left navbar-toggler-left hided"
            data-toggle="menubar">
        <span class="sr-only">Toggle navigation</span>
        <span class="hamburger-bar"></span>
    </button>

    <!-- Brand LOGO -->
    <div class="navbar-brand navbar-brand-center site-gridmenu-toggle brand-logo-desktop site-menubar"
         data-toggle="gridmenu"
         style="background-color: #191919; overflow: hidden; top:0px; box-shadow:none;">
        <img id="logoIconSirius"
             class="navbar-brand-logo"
             src="@whitelabelLogo('icon')"
             alt="@whitelabel('name')"
             style="height: 1.8rem;">
        <img id="logoSirius"
             class="navbar-brand-logo d-none logo-sirius"
             src="@whitelabelLogo('main')"
             alt="@whitelabel('name')"
             height="26"
             style="margin: 0px 2rem 0px 1rem;">
    </div>

    <div class="navbar-brand navbar-brand-center site-gridmenu-toggle brand-logo-mobile"
         data-toggle="gridmenu">
        <img id="logoIconSirius"
             class="navbar-brand-logo"
             src="@whitelabelLogo('icon')"
             alt="@whitelabel('name')"
             style="height:  2.7rem;">
    </div>

    <!-- BOTAO HAMBURGUER NO DESKTOP -->
    <div>
        <ul class="nav navbar-toolbar hamburger-desk"
            style="margin-left:70px">
            <li class="nav-item hidden-float"
                id="toggleMenubar">
                <a class="nav-link"
                   data-toggle="menubar"
                   href="#"
                   role="button"
                   onclick=" if($(this).hasClass('hided')) $('#logoSirius').css('margin-right','2rem'); else $('#logoSirius').css('margin-right','1rem') ">
                    <i class="icon hamburger hamburger-arrow-left">
                        <span class="sr-only">Toggle menubar</span>
                        <span class="hamburger-bar"></span>
                    </i>
                </a>
            </li>
        </ul>
    </div>

    <!-- BOTAO BONUS BALANCE NO MOBILE -->
    <div class="bonus-balance-menu d-flex justify-content-center align-items-center">
        <button id="bonus-balance"
                class="bonus-balance-button mobile justify-content-center align-items-center"
                style="display: none">
            <svg width="15"
                 height="16"
                 viewBox="0 0 11 12"
                 fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M4 0C4.59735 0 5.13353 0.261878 5.5 0.677088C5.86647 0.261878 6.40265 0 7 0C8.10457 0 9 0.89543 9 2C9 2.36429 8.90261 2.70583 8.73244 3H10C10.5523 3 11 3.44772 11 4V6C11 6.55229 10.5523 7 10 7L9.99978 10C9.99978 11.1046 9.10435 12 7.99978 12H2.99978C1.89521 12 0.999783 11.1046 0.999783 10V7C0.447598 6.99988 0 6.55221 0 6V4C0 3.44772 0.447715 3 1 3H2.26756C2.09739 2.70583 2 2.36429 2 2C2 0.89543 2.89543 0 4 0ZM3 2C3 2.55228 3.44772 3 4 3H5V2C5 1.44772 4.55228 1 4 1C3.44772 1 3 1.44772 3 2ZM6 4L5.99961 6H10V4H6ZM4.99961 4H1V6H1.49978C1.49971 6 1.49986 6 1.49978 6H4.99961V4ZM1.99978 7V10C1.99978 10.5523 2.4475 11 2.99978 11H4.99961V7H1.99978ZM5.99961 11H7.99978C8.55207 11 8.99978 10.5523 8.99978 10V7H5.99961V11ZM8 2C8 1.44772 7.55228 1 7 1C6.44772 1 6 1.44772 6 2V3H7C7.55228 3 8 2.55228 8 2Z"
                      fill="white" />
            </svg>
        </button>
    </div>

    <div class="row no-gutters">
        <div style="margin:auto 100px auto auto">
            <!-- CONVITE PARA INICIAR -->
            @php
                $userModel = new \Modules\Core\Entities\User();
                $account_type = $userModel->present()->getAccountType(auth()->user()->id, auth()->user()->account_owner_id);
                $user = auth()->user();
                $account_is_approved = $user->account_is_approved;
                if ($user->is_cloudfox && $user->logged_id) {
                    $query = $userModel
                        ::select('account_is_approved')
                        ->where('id', $user->logged_id)
                        ->get();
                    $account_is_approved = $query[0]->account_is_approved ?? false;
                }
            @endphp

            @if (!$account_is_approved && $account_type === 'admin')
                <div class="new-register-navbar-open-modal-container">
                    <div class="row new-register-open-modal no-gutters alert-pendings">
                        <span class="new-register-open-modal-btn">
                            <div class="alert-pendings-box-1"> <img src="/build/global/img/alert-pending.png"
                                     style="
                            margin: 0 10px 4px"><span
                                      class="count"></span></div>
                        </span>
                        <div class="alert-pendings-box-2">Verificar pendências</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="row no-gutters ml-auto">

        <!-- CONTAINER DOS ICONES/LINKS DO ANNOUNCEKIT, NOTIFICACOES E USUARIO -->
        <div class="row no-gutters d-flex justify-content-end">

            <!-- HASH USER -->
            <input type='hidden'
                   id='user'
                   value='{{ Vinkla\Hashids\Facades\Hashids::connection('pusher_connection')->encode(auth()->user()->id) }}'>
            <input type='hidden'
                   id='user_hash'
                   value='{{ Vinkla\Hashids\Facades\Hashids::encode(auth()->user()->id) }}'>
            <input type='hidden'
                   id='user_name'
                   value='{{ auth()->user()->name }}'>
            <input type='hidden'
                   id='user_email'
                   value='{{ auth()->user()->email }}'>

            <!-- NAVERBAR FILHA DA CONTAINER -->
            <div class="row no-gutters d-flex"
                 id="site-navbar-collapse">

                @include('layouts.company-select')

                <div id="top-vertical-bar"
                     style="background-color: #f4f4f4; width:2px; margin:15px 7px 15px 0"></div>

                <div class="bonus-balance-menu d-flex justify-content-center align-items-center">
                    <button id="bonus-balance"
                            class="bonus-balance-button desktop justify-content-center align-items-center"
                            style="display: none; margin-right: 0px; margin-left: 17px">
                        <svg width="11"
                             height="12"
                             viewBox="0 0 11 12"
                             fill="none"
                             xmlns="http://www.w3.org/2000/svg"
                             style="margin-right: 5px">
                            <path d="M4 0C4.59735 0 5.13353 0.261878 5.5 0.677088C5.86647 0.261878 6.40265 0 7 0C8.10457 0 9 0.89543 9 2C9 2.36429 8.90261 2.70583 8.73244 3H10C10.5523 3 11 3.44772 11 4V6C11 6.55229 10.5523 7 10 7L9.99978 10C9.99978 11.1046 9.10435 12 7.99978 12H2.99978C1.89521 12 0.999783 11.1046 0.999783 10V7C0.447598 6.99988 0 6.55221 0 6V4C0 3.44772 0.447715 3 1 3H2.26756C2.09739 2.70583 2 2.36429 2 2C2 0.89543 2.89543 0 4 0ZM3 2C3 2.55228 3.44772 3 4 3H5V2C5 1.44772 4.55228 1 4 1C3.44772 1 3 1.44772 3 2ZM6 4L5.99961 6H10V4H6ZM4.99961 4H1V6H1.49978C1.49971 6 1.49986 6 1.49978 6H4.99961V4ZM1.99978 7V10C1.99978 10.5523 2.4475 11 2.99978 11H4.99961V7H1.99978ZM5.99961 11H7.99978C8.55207 11 8.99978 10.5523 8.99978 10V7H5.99961V11ZM8 2C8 1.44772 7.55228 1 7 1C6.44772 1 6 1.44772 6 2V3H7C7.55228 3 8 2.55228 8 2Z"
                                  fill="white" />
                        </svg>
                        <span id="total-bonus-balance">
                            <div class="skeleton skeleton-p"
                                 style="width: 80px; height: 20px; margin-top: 2px"></div>
                        </span>
                    </button>
                </div>

                <!-- MODAL DE NOVIDADE ANNOUCEKIT -->
                <!-- <div id="my-iframe"
                     class="announcekit-widget d-none d-sm-flex align-items-center justify-content-center"
                     style="padding-right: 10px">
                    <span class="nav-link navbar-avatar"
                          data-toggle="dropdown"
                          title="Novidades"
                          id='notification'
                          aria-expanded="false"
                          data-animation="scale-up"
                          role="button"
                          style='padding-right: 10px'>
                        <img class="svg-menu"
                             src="{{ mix('build/global/img/svg/notificacao.svg') }}"
                             alt="Novidades">
                    </span>
                </div> -->

                <!-- BOTOES DE NOTIFICAO E USUARIO -->
                <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">

                    <!-- BOTAO DE USUARIO -->
                    <li class="nav-item dropdown">

                        <!-- FOTO DO USUARIO -->
                        <a class="nav-link navbar-avatar pr-10 pr-sm-35"
                           data-toggle="dropdown"
                           href="#"
                           aria-expanded="false"
                           data-animation="scale-up"
                           role="button"
                           style="margin-left: -2px;">
                            <span class="avatar avatar-online">
                                <img class='img-user-menu-principal'
                                     src="{!! \Auth::user()->photo
                                         ? \Auth::user()->photo
                                         : 'https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/user-default.png' !!}"
                                     onerror="this.onerror=null; this.src='https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/user-default.png'"
                                     alt=""
                                     title="">
                                <i></i>
                            </span>
                        </a>

                        <!-- BOTOES DE OPCAO DOS USUARIO -->
                        <div id="dropdown_profile_card"
                             class="dropdown-menu"
                             role="menu">
                            @if (auth()->user()->is_cloudfox)
                                <a class="dropdown-item disabled"
                                   disabled>
                                    <img height="24"
                                         width="24"
                                         src="{{ mix('build/global/img/icon-info.svg') }}" />
                                    {{ auth()->user()->name }}
                                </a>
                                <div class="dropdown-divider"
                                     role="presentation"></div>
                            @endif
                            <!-- BOTAO DE CONFIGURACOES -->
                            @if (foxutils()->isHomolog())
                                <div data-toggle="tooltip"
                                     data-placement="left"
                                     title="Desabilitado na versão de testes">
                                    <a class="dropdown-item disabled"
                                       disabled>
                                        <img height="24"
                                             width="24"
                                             src="{{ mix('build/global/img/svg/settings.svg') }}" />
                                        Configurações
                                    </a>
                                </div>
                            @else
                                <a id="accounts-service"
                                   class="dropdown-item redirect-to-accounts"
                                   href=""
                                   data-url-value=""
                                   role="menuitem">
                                    <img height="24"
                                         width="24"
                                         src="{{ mix('build/global/img/svg/settings.svg') }}" />
                                    Configurações
                                </a>
                            @endif

                            <!-- DIV DIVISORA DOS ELEMENTOS -->
                            <div class="dropdown-divider"
                                 role="presentation"></div>

                            <!-- BOTAO DE LOGOUT (SAIR DO USUARIO)-->
                            <a class="dropdown-item"
                               href=""
                               role="menuitem"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <img height="24"
                                     width="24"
                                     src="{{ mix('build/global/img/svg/power_settings_new.svg') }}" />
                                Logout
                            </a>

                            <form id="logout-form"
                                  action="/logout"
                                  method="POST"
                                  style="display: none;">
                                <input type="hidden"
                                       name="_token"
                                       value="{{ csrf_token() }}">
                            </form>
                        </div>

                    </li>
                </ul>
            </div>
        </div>
    </div>

</nav>
{{-- SIDE BAR --}}
<div class="site-menubar">
    <ul class="site-menu"
        style="margin-top:10px">
        <!-- hasanyrole('account_owner|admin|finantial') -->
        @can('dashboard')
            <li class="site-menu-item has-sub">
                <a href="{{ route('dashboard.index') }}">
                    <span class="bg-menu">
                        <img class="svg-menu"
                             src="{{ mix('build/global/img/svg/dashboard.svg') }}"
                             alt="Dashboard">
                    </span>
                    <span class="site-menu-title ml-5">Dashboard</span>
                </a>
            </li>
        @endcan

        <!-- hasanyrole('account_owner|admin|attendance|finantial') -->
        @if (auth()->user()->hasAnyPermission(['sales', 'recovery', 'trackings', 'contestations']))
            <li class="site-menu-item has-sub">
                <a href="javascript:void(0)"
                   id="sales-link">
                    <span class="bg-menu">
                        <img src="{{ mix('build/global/img/svg/vendas.svg') }}"
                             alt="Vendas">
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
                    {{-- <li class="site-menu-item">
                        <a href="{{ route('antifraud.index') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Antifraude</span>
                        </a>
                    </li> --}}
                    <li class="site-menu-item">
                        <a href="{{ route('trackings.index') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Rastreamentos</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('contestations.index') }}">
                            <span class="bg-menu"></span>
                            <span class="site-menu-title">Contestações</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        <!-- hasanyrole('account_owner|admin') -->
        {{-- @php

            dd($user->permissions->toArray());
        @endphp --}}
        @can('projects')
            <li class="site-menu-item has-sub">
                <a href="/projects"
                   id="projects-link">
                    <span class="bg-menu">
                        <img src="{{ mix('build/global/img/svg/vitrine.svg') }}"
                             alt="Lojas">
                    </span>
                    <span class="site-menu-title">Lojas</span>
                </a>
            </li>
        @endcan
        <!-- hasanyrole('account_owner|admin')         -->
        @can('products')
            <li class="site-menu-item has-sub">
                <a href="{{ route('products.index') }}"
                   id="products-link">
                    <span class="bg-menu">
                        <img src="{{ mix('build/global/img/svg/produtos.svg') }}"
                             alt="Produtos">
                    </span>
                    <span class="site-menu-title">Produtos</span>
                </a>
            </li>
            @endif
            <!-- hasanyrole('account_owner|admin|attendance') -->
            @can('attendance')
                <li class="site-menu-item has-sub">
                    <a href="{{ route('attendance.index') }}">
                        <span class="bg-menu">
                            <img src="{{ mix('build/global/img/svg/atendimento.svg') }}"
                                 alt="Atendimento">
                        </span>
                        <span class="site-menu-title">Atendimento</span>
                    </a>
                </li>
            @endcan
            <!-- unlessrole('customer-service') -->
            @can('finances')
                @php
                    $user = auth()->user();
                    $showOldFinances = $user->show_old_finances ?? false;
                    if (!$showOldFinances) {
                        $userMaster = \Modules\Core\Entities\User::find($user->account_owner_id);
                        $showOldFinances = $userMaster->show_old_finances ?? false;
                    }
                @endphp
                <li class="site-menu-item has-sub">
                    <a href="{!! route('finances') !!}"
                       id="finances-link">
                        <span class="bg-menu">
                            <img src="{{ mix('build/global/img/svg/financas.svg') }}"
                                 alt="Finanças">
                        </span>
                        <span class="site-menu-title">Finanças</span>
                    </a>
                </li>
            @endcan

            <!-- hasanyrole('account_owner|admin|attendance|finantial') -->
            @if (auth()->user()->hasAnyPermission(['reports']))
                <li class="site-menu-item has-sub">
                    <a href="{{ route('reports.resume') }}"
                       id="reports-link">
                        <span class="bg-menu">
                            <img src="{{ mix('build/global/img/svg/relatorios.svg') }}"
                                 alt="Relatórios">
                        </span>
                        <span class="site-menu-title">Relatórios</span>
                    </a>
                </li>
            @endif

            <!-- hasanyrole('account_owner|admin') -->
            @can('affiliates')
                <li class="site-menu-item has-sub">
                    <a href="{{ route('projectaffiliates') }}"
                       id="affiliates-link">
                        <span class="bg-menu">
                            <img src="{{ mix('build/global/img/svg/afiliados.svg') }}"
                                 alt="Afiliados">
                        </span>
                        <span class="site-menu-title mb-5">Afiliados</span>
                    </a>
                </li>
            @endcan
            <!-- hasanyrole('account_owner|admin') -->
            @can('apps')
                <li class="site-menu-item has-sub">
                    <a href="{{ route('apps') }}"
                       id='apps-link'>
                        <span class="bg-menu">
                            <img src="{{ mix('build/global/img/svg/aplicativos.svg') }}"
                                 alt="Aplicativos">
                        </span>
                        <span class="site-menu-title">Aplicativos</span>
                    </a>
                </li>
            @endcan
            @can('dev')
                <li class="site-menu-item has-sub">
                    <a href="javascript:void(0)"
                       id="api-sirius-link">
                        <span class="bg-menu">
                            <img src="{{ mix('build/global/img/svg/api-sirius-menu.svg') }}"
                                 alt="Dev">
                        </span>
                        <span class="site-menu-title">Dev</span>
                        <span class="site-menu-arrow"></span>
                    </a>
                    <ul class="site-menu-sub">
                        <li class="site-menu-item">
                            <a href="{!! route('integrations.index') !!}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">API</span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a href="{{ route('webhooks.index') }}">
                                <span class="bg-menu"></span>
                                <span class="site-menu-title">Webhooks</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <!-- hasanyrole('account_owner')         -->
                @can('invitations')
                    <li class="site-menu-item has-sub">
                        <a href="{{ route('invitations.index') }}">
                            <span class="bg-menu">
                                <img src="{{ mix('build/global/img/svg/convites.svg') }}"
                                     alt="Convites">
                            </span>
                            <span class="site-menu-title">Convites</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
