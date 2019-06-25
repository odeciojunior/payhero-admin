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
            <li id="notifications_button" class="nav-item dropdown">
              <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" title="Notifications"
                aria-expanded="false" data-animation="scale-up" role="button">
                <svg version="1.1" class="topbar-icon mr-0" id="Camada_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                <style type="text/css">
                </style>
                {{--  <path class="st0" d="M24.5,23.2c-1.7-1.7-2.7-4.1-2.7-6.5l0-4c0-3-1.9-5.6-4.8-6.5l0-1.5c0-1.1-0.9-2-2-2c-1.1,0-2,0.9-2,2l0,1.5
                  C10.1,7,8.2,9.6,8.2,12.6l0,4c0,0.8-0.1,1.6-0.3,2.4c-0.4,1.6-1.2,3-2.4,4.1c-0.3,0.3-0.4,0.8-0.2,1.2C5.4,24.7,5.6,24.9,6,25
                  c0.1,0,0.2,0,0.3,0l6,0c0.2,1.1,1,2,2,2.3c0.8,0.2,1.6,0.1,2.3-0.4c0.7-0.4,1.1-1.1,1.2-1.9l6,0c0.5,0,0.9-0.3,1.1-0.8
                  C24.9,23.8,24.8,23.4,24.5,23.2z M14.1,3.7c0.3-0.3,0.7-0.4,1.2-0.3c0.5,0.1,0.9,0.6,0.9,1.2l0,1.3c0,0-0.1,0-0.1,0
                  c-0.1,0-0.3,0-0.4-0.1c-0.1,0-0.1,0-0.2,0c-0.1,0-0.3,0-0.4,0c0,0-0.1,0-0.1,0c0,0-0.1,0-0.1,0c-0.2,0-0.3,0-0.5,0c0,0-0.1,0-0.1,0
                  c-0.2,0-0.3,0-0.5,0.1c0,0,0,0,0,0l0-1.3C13.8,4.3,13.9,3.9,14.1,3.7z M17,25c-0.2,0.9-1,1.6-2,1.6c-1,0-1.8-0.7-2-1.6L17,25z
                  M23.7,24.2l-17.5,0C6.1,24.2,6,24.2,6,24c0-0.1,0-0.2,0.1-0.3c1.2-1.2,2.1-2.8,2.6-4.5C8.9,18.4,9,17.5,9,16.6l0-4
                  c0-2.7,1.9-5.1,4.5-5.8l0,0c0.4-0.1,0.8-0.1,1.1-0.2c0.1,0,0.2,0,0.4,0c0.4,0,0.8,0,1.2,0.1c0.1,0,0.2,0,0.3,0.1l0.1,0
                  c2.6,0.7,4.5,3.1,4.5,5.8l0,4c0,2.7,1.1,5.2,2.9,7.1c0.1,0.1,0.1,0.2,0.1,0.3C24,24.1,23.9,24.2,23.7,24.2L23.7,24.2z"/>
                </svg>  --}}
                <span class="badge badge-pill badge-danger up" id="qtd_notificacoes">{!! count(\Auth::user()->unreadNotifications) !!}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-right dropdown-menu-media" role="menu">
                <div class="dropdown-menu-header">
                  <h5>NOTIFICAÇÕES</h5>
                </div>
                <div class="list-group">
                  <div data-role="container">
                    <div data-role="content">
                      @if(count(\Auth::user()->unreadNotifications) > 0)
                        @foreach(\Auth::user()->unreadNotifications as $notification)
                          @if($notification->type == 'Modules\Checkout\Notifications\VendaNotificacao')
                            <a class="list-group-item dropdown-item" href="/relatorios/vendas" role="menuitem">
                              <div class="media">
                                <div class="pr-10">
                                  <i class="icon wb-shopping-cart bg-green-600 green icon-circle" sty aria-hidden="true"></i>
                                </div>
                                <div class="media-body">
                                  <h6 class="media-heading">{!! $notification->data['qtd'] !!}
                                      {!! $notification->data['qtd'] > 1 ? 'novas vendas' : 'nova venda' !!}
                                  </h6>
                                  <time class="media-meta" datetime="2018-06-11T18:29:20+08:00">{!! date('d/m/Y H:m:s', strtotime($notification->updated_at)) !!}</time>
                                </div>
                              </div>
                            </a>
                          @elseif($notification['type'] == 'Modules\Notifications\Notifications\NewAffiliationRequest')
                            <a class="list-group-item dropdown-item" href="/afiliados/meusafiliados" role="menuitem">
                              <div class="media">
                                <div class="pr-10">
                                  <i class="icon wb-users bg-green-600 green icon-circle" sty aria-hidden="true"></i>
                                </div>
                                <div class="media-body">
                                  <h6 class="media-heading">{!! $notification->data['qtd'] !!}
                                      {!! $notification->data['qtd'] > 1 ? 'novas solicitações de afiliação' : 'nova solicitação de afiliação' !!}
                                  </h6>
                                  <time class="media-meta" datetime="2018-06-11T18:29:20+08:00">{!! date('d/m/Y H:m:s', strtotime($notification->updated_at)) !!}</time>
                                </div>
                              </div>
                            </a>
                          @elseif($notification['type'] == 'Modules\Notifications\Notifications\NewAffiliation')
                            <a class="list-group-item dropdown-item" href="/afiliados/meusafiliados" role="menuitem">
                              <div class="media">
                                <div class="pr-10">
                                  <i class="icon wb-users bg-green-600 green icon-circle" sty aria-hidden="true"></i>
                                </div>
                                <div class="media-body">
                                  <h6 class="media-heading">{!! $notification->data['qtd'] !!}
                                      {!! $notification->data['qtd'] > 1 ? 'novas afiliações' : 'nova afiliação' !!}
                                  </h6>
                                  <time class="media-meta" datetime="2018-06-11T18:29:20+08:00">{!! date('d/m/Y H:m:s', strtotime($notification->updated_at)) !!}</time>
                                </div>
                              </div>
                            </a>
                          @elseif($notification['type'] == 'Modules\Notifications\Notifications\ApprovedAffiliation')
                            <a class="list-group-item dropdown-item" href="/afiliados/minhasafiliacoes" role="menuitem">
                              <div class="media">
                                <div class="pr-10">
                                  <i class="icon wb-users bg-green-600 green icon-circle" sty aria-hidden="true"></i>
                                </div>
                                <div class="media-body">
                                  <h6 class="media-heading">{!! $notification->data['qtd'] !!}
                                      {!! $notification->data['qtd'] > 1 ? 'novas afiliações aprovadas' : 'nova afiliação aprovada' !!}
                                  </h6>
                                  <time class="media-meta" datetime="2018-06-11T18:29:20+08:00">{!! date('d/m/Y H:m:s', strtotime($notification->updated_at)) !!}</time>
                                </div>
                              </div>
                            </a>
                          @endif
                        @endforeach
                      @else
                        <a class="list-group-item dropdown-item" href="javascript:void(0)" role="menuitem">
                          <div class="media">
                            <div class="pr-10">
                              <i class="icon wb-check bg-green-600 icon-circle" aria-hidden="true"></i>
                            </div>
                            <div class="media-body">
                              <h6 class="media-heading">Nenhuma nova notificação</h6>
                            </div>
                          </div>
                        </a>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
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
                <svg version="1.1" class="topbar-icon" id="Camada_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                  <style type="text/css">
                    .st0{stroke:#76838f;stroke-width:0.5;stroke-miterlimit:10;}
                  </style>
                  {{--  <path class="st0" d="M15,1.7C7.4,1.7,1.3,7.9,1.3,15.4S7.4,29.1,15,29.1c0.4,0,0.8,0,1.2-0.1c0.4,0,0.8-0.1,1.1-0.2l0.1,0
                    c7-1.3,11.8-7.7,11.1-14.7C28,7.1,22.1,1.8,15,1.7z M13.9,28.2L13.9,28.2c-0.4,0-0.7-0.1-1.1-0.1c0,0,0,0-0.1,0
                    c-0.3-0.1-0.7-0.1-1-0.2l-0.1,0c-0.3-0.1-0.6-0.2-1-0.3c0,0-0.1,0-0.1,0c-0.3-0.1-0.6-0.2-0.9-0.4c0,0-0.1,0-0.1-0.1
                    c-0.3-0.1-0.6-0.3-0.9-0.5c0,0-0.1,0-0.1-0.1C8.3,26.4,8,26.2,7.8,26c0,0-0.1-0.1-0.1-0.1c-0.3-0.2-0.5-0.4-0.8-0.6l-0.1-0.1v-3.9
                    c0-2.8,2.2-5,5-5h6.4c2.8,0,5,2.3,5,5v3.8l-0.1,0.1c-0.3,0.2-0.5,0.4-0.8,0.6c0,0-0.1,0-0.1,0.1c-0.3,0.2-0.5,0.4-0.8,0.5
                    c0,0-0.1,0-0.1,0.1c-0.3,0.2-0.6,0.3-0.9,0.5c0,0-0.1,0-0.1,0c-0.3,0.1-0.6,0.3-0.9,0.4c0,0-0.1,0-0.1,0c-0.3,0.1-0.6,0.2-1,0.3
                    l-0.1,0c-0.3,0.1-0.7,0.2-1,0.2c0,0,0,0-0.1,0c-0.3,0.1-0.7,0.1-1.1,0.1h0c-0.4,0-0.7,0.1-1.1,0.1S13.9,28.2,13.9,28.2z M24.1,24.4
                    v-3c0-3.3-2.7-5.9-5.9-5.9h-6.4c-3.3,0-5.9,2.7-5.9,5.9v3C1,19.4,1,11.3,6,6.4c5-4.9,13-4.9,18,0C29,11.3,29,19.4,24.1,24.4 L24.1,24.4z"/>
                  <path class="st0" d="M15,5.4c-2.5,0-4.6,2-4.6,4.6s2,4.6,4.6,4.6s4.6-2,4.6-4.6C19.6,7.4,17.5,5.4,15,5.4z M15,13.6 c-2,0-3.7-1.6-3.7-3.7S13,6.3,15,6.3s3.7,1.6,3.7,3.7C18.6,12,17,13.6,15,13.6z"/>  --}}
                </svg>

                  Perfil 
                </a>
                <a class="dropdown-item" href="{!! route('companies.index') !!}" role="menuitem">
                <svg version="1.1"  class="topbar-icon" id="Camada_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                  viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                <style type="text/css">
                </style>
                {{--  <path class="st0" d="M24.7,9H22V8.2c0-0.2-0.2-0.4-0.4-0.4h-0.4v-2c0-1.5-1.2-2.7-2.7-2.7h-7.1c-1.5,0-2.7,1.2-2.7,2.7v2H8.2
                  C8,7.8,7.8,8,7.8,8.2V9H5.1c-1.1,0-2,0.9-2,2V18c0,0.8,0.5,1.5,1.2,1.8v4.9c0,1.1,0.9,2,2,2h17.3c1.1,0,2-0.9,2-2v-4.9
                  c0.7-0.3,1.2-1,1.2-1.8v-7.1C26.7,9.8,25.8,9,24.7,9z M18.4,9h-7.1 M19.2,8.6h2V9h-2V8.6z M9.4,7.8v-2c0-1.1,0.9-2,2-2h7.1
                  c1.1,0,2,0.9,2,2v2 M8.6,8.6h2V9h-2V8.6z M24.7,24.7c0,0.7-0.5,1.2-1.2,1.2H6.2c-0.7,0-1.2-0.5-1.2-1.2V20h8.6v1.2
                  c0,0.7,0.5,1.2,1.2,1.2s1.2-0.5,1.2-1.2V20h8.6V24.7z M15.3,21.1c0,0.2-0.2,0.4-0.4,0.4s-0.4-0.2-0.4-0.4v-3.5h0.8V21.1z M25.9,18
                  c0,0.7-0.5,1.2-1.2,1.2h-8.6v-2c0-0.2-0.2-0.4-0.4-0.4h-1.6c-0.2,0-0.4,0.2-0.4,0.4v2H5.1c-0.7,0-1.2-0.5-1.2-1.2v-7.1
                  c0-0.7,0.5-1.2,1.2-1.2h19.6c0.7,0,1.2,0.5,1.2,1.2V18z"/>  --}}
                </svg>

                  Empresas 
                </a>  
                <div class="dropdown-divider" role="presentation"></div>
                <a class="dropdown-item" href="" role="menuitem" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <svg version="1.1" class="topbar-icon" id="Camada_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                  viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                <style type="text/css">
                </style>
                {{--  <path class="st0" d="M28.6,15.1c0.1-0.2,0-0.4-0.1-0.5l-3.6-3.7c-0.2-0.2-0.5-0.2-0.6,0c-0.2,0.2-0.2,0.5,0,0.6l2.9,2.9h-5.7v-12
                  C21.4,1.7,20.7,1,20,1H3.5C3,0.9,2.4,1.1,2,1.4C1.6,1.7,1.4,2.2,1.4,2.8v21.6c0,0.6,0.4,1.2,1,1.3l11.3,3.2c0.4,0.1,0.8,0,1.2-0.2
                  c0.3-0.3,0.5-0.7,0.5-1.1V26H20c0.8,0,1.4-0.6,1.4-1.4v-9.3h5.7l-2.9,2.9c-0.1,0.1-0.2,0.3-0.1,0.5c0,0.2,0.2,0.3,0.3,0.3
                  c0.2,0,0.3,0,0.4-0.1l3.6-3.7C28.5,15.2,28.6,15.2,28.6,15.1z M20.4,2.4v12h-5v-9c0-0.6-0.3-1.1-0.9-1.3c0,0-0.1,0-0.1,0L6.8,2H20
                  C20.2,2,20.4,2.2,20.4,2.4z M14.5,27.6c0,0.1-0.1,0.3-0.2,0.4c-0.1,0.1-0.3,0.1-0.4,0.1L2.6,24.9c-0.2-0.1-0.3-0.2-0.3-0.4V2.8
                  c0-0.3,0.1-0.5,0.3-0.7S3.1,1.9,3.4,2L14.2,5c0.2,0.1,0.3,0.2,0.3,0.4V27.6z M20.4,24.7c0,0.3-0.2,0.5-0.5,0.5h-4.5v-9.7h5V24.7z"/>  --}}
                </svg>

                Logout </a>
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
        <a class="animsition-link"  href="{{ route('dashboard') }}">
           <img id="dashboard_img" src="{{ asset('modules/global/assets/img/svg/dashboard.svg') }}">
            <span class="site-menu-title">Dashboard</span>
        </a>
      </li>
      <li class="site-menu-item has-sub disabled">
        <a class="animsition-link disabled"  href="{{ route('showcase') }}">
        <img id="showcase_img" src="{{ asset('modules/global/assets/img/svg/vitrine.svg') }}">
            <span class="site-menu-title">Vitrine (em breve)</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
        <a href="javascript:void(0)">
        <img id="sales_img" src="{{ asset('modules/global/assets/img/svg/vendas.svg') }}">
            <span class="site-menu-title">Vendas</span>
            <span class="site-menu-arrow"></span>
        </a>
        <ul class="site-menu-sub">
          <li class="site-menu-item has-sub">
            <a class="animsition-link" href="{!! route('sales') !!}">
              <span class="site-menu-title">Visão geral</span>
            </a>
          </li>
          <li class="site-menu-item">
            <a class="animsition-link" href="{!! route('cartrecovery') !!}">
              <span class="site-menu-title">Carrinhos abandonados</span>
            </a>
          </li>
          <li class="site-menu-item">
            <a class="animsition-link" href="#" style="pointer-events: none;cursor: default;">
              <span class="site-menu-title">Central de reembolso</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="site-menu-item has-sub">
        <a class="animsition-link"  href="/projects">
        <img id="projects_img" src="{{ asset('modules/global/assets/img/svg/projetos.svg') }}" style="width:17px;">
            <span class="site-menu-title">Projetos</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
          <a class="animsition-link" href="{{ route('products.index') }}">
        <img id="products_img" src="{{ asset('modules/global/assets/img/svg/produtos.svg') }}">
            <span class="site-menu-title">Produtos</span>
        </a>
      </li>
      <li class="site-menu-item has-sub  disabled">
        <a class="animsition-link disabled"  href="{{ route('attendance') }}">
        <img id="attendance_img" src="{{ asset('modules/global/assets/img/svg/atendimento.svg') }}">
            <span class="site-menu-title">Atendimento (em breve)</span>
        </a>
      </li>
      <li class="site-menu-item has-sub  disabled">
        <a href="javascript:void(0)" class="disabled">
        <img id="affiliates_img" src="{{ asset('modules/global/assets/img/svg/afiliados.svg') }}">
            <span class="site-menu-title">Afiliados (em breve)</span>
            <span class="site-menu-arrow"></span>
        </a>
        <ul class="site-menu-sub">
          <li class="site-menu-item">
            <a class="animsition-link" href="{!! route('afiliados.minhasafiliacoes') !!}">
              <span class="site-menu-title">Minhas afiliações</span>
            </a>
          </li>
          <li class="site-menu-item">
            <a class="animsition-link" href="{!! route('afiliados.meusafiliados') !!}">
              <span class="site-menu-title">Meus afiliados</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="site-menu-item has-sub">
        <a class="animsition-link" href="{!! route('finances') !!}">
        <img id="finances_img" src="{{ asset('modules/global/assets/img/svg/financas.svg') }}">
            <span class="site-menu-title">Finanças</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
        <a class="animsition-link" href="{!! route('reports.index') !!}">
        <img id="reports_img" src="{{ asset('modules/global/assets/img/svg/configuracao.svg') }}">
            <span class="site-menu-title">Relatórios</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
        <a class="animsition-link" href="{{ route('apps') }}">
        <img id="apps_img" src="{{ asset('modules/global/assets/img/svg/aplicativos.svg') }}">
            <span class="site-menu-title">Aplicativos</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
        <a class="animsition-link" href="{{ route('invitations.invites') }}">
        <img id="apps_img" src="{{ asset('modules/global/assets/img/svg/convites.svg') }}">
            <span class="site-menu-title">Convites</span>
        </a>
      </li>

      {{--  <li class="site-menu-item has-sub">
        <a class="animsition-link  lh-10" href="#">
        <img src="">
            <span class="site-menu-title footer-nav-link">Suporte</span>
        </a>
      </li>  --}}

      <li class="site-menu-item has-sub ">
        <a target="_blank" href="https://cloudfox.net/terms">
            <span class="site-menu-title footer-nav-link">Termos e Políticas</span>
        </a>
      </li>
    </ul>
</div>
