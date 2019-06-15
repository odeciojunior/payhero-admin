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
                <i class="icon wb-bell" aria-hidden="true"></i>
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
              <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
                data-animation="scale-up" role="button">
                <span class="avatar avatar-online">
                  <!-- <img src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_USER.\Auth::user()->photo)!!}" alt="..."> -->
                  <img src="{{ asset('modules/global/assets/img/user.jpg') }}" alt="">
                  <i></i>
                </span>
              </a>
              <div class="dropdown-menu" role="menu">
                <a class="dropdown-item" href="{!! route('profile.index') !!}" role="menuitem">
                  <i class="icon wb-user" aria-hidden="true"></i> 
                  Perfil 
                </a>
                @if(Auth::user()->hasRole('administrador empresarial'))
                  <a class="dropdown-item" href="{{ route('companies.index') }}" role="menuitem">
                    <i class="icon wb-home" aria-hidden="true"></i>
                    Empresas
                  </a>
                @endif
                <div class="dropdown-divider" role="presentation"></div>
                <a class="dropdown-item" href="" role="menuitem" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="icon wb-power" aria-hidden="true"></i> Logout </a>
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
 
<div class="site-menubar">
    <ul class="site-menu" style="margin-top:10px">
      <li class="site-menu-item has-sub">
        <a class="animsition-link"  href="{{ route('dashboard') }}">
           <img src="{{ asset('modules/global/assets/img/svg/dashboard.svg') }}">
            <span class="site-menu-title">Dashboard</span>
        </a>
      </li>
      <li class="site-menu-item has-sub disabled">
        <a class="animsition-link disabled"  href="{{ route('showcase') }}">
        <img src="{{ asset('modules/global/assets/img/svg/vitrine.svg') }}">
            <span class="site-menu-title">Vitrine (em breve)</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
        <a href="javascript:void(0)">
        <img src="{{ asset('modules/global/assets/img/svg/vendas.svg') }}">
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
        <img src="{{ asset('modules/global/assets/img/svg/projetos.svg') }}" style="width:17px;">
            <span class="site-menu-title">Projetos</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
          <a class="animsition-link" href="{{ route('products.index') }}">
        <img src="{{ asset('modules/global/assets/img/svg/produtos.svg') }}">
            <span class="site-menu-title">Produtos</span>
        </a>
      </li>
      <li class="site-menu-item has-sub  disabled">
        <a class="animsition-link disabled"  href="{{ route('attendance') }}">
        <img src="{{ asset('modules/global/assets/img/svg/atendimento.svg') }}">
            <span class="site-menu-title">Atendimento (em breve)</span>
        </a>
      </li>
      <li class="site-menu-item has-sub  disabled">
        <a href="javascript:void(0)" class="disabled">
        <img src="{{ asset('modules/global/assets/img/svg/afiliados.svg') }}">
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
        <a href="javascript:void(0)">
        <img src="{{ asset('modules/global/assets/img/svg/financas.svg') }}">
            <span class="site-menu-title">Finanças</span>
            <span class="site-menu-arrow"></span>
        </a>
        <ul class="site-menu-sub">
          <li class="site-menu-item">
            <a class="animsition-link" href="{{ route('extrato') }}">
              <span class="site-menu-title">Extrato</span>
            </a>
          </li>
          <li class="site-menu-item">
            <a class="animsition-link" href="{{ route('transferencias') }}">
              <span class="site-menu-title">Transferências</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="site-menu-item has-sub">
        <a class="animsition-link" href="{!! route('tools') !!}">
        <img src="{{ asset('modules/global/assets/img/svg/configuracao.svg') }}">
            <span class="site-menu-title">Ferramentas</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
        <a class="animsition-link" href="{{ route('apps') }}">
        <img src="{{ asset('modules/global/assets/img/svg/aplicativos.svg') }}">
            <span class="site-menu-title">Aplicativos</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
        <a class="animsition-link" href="{{ route('invitations.invites') }}">
            <i class="site-menu-icon wb-user-add" aria-hidden="true"></i>
            <span class="site-menu-title">Convites</span>
        </a>
      </li>

      <li class="site-menu-item has-sub">
        <a class="animsition-link  lh-10" href="#">
        <img src="">
            <span class="site-menu-title footer-nav-link">Suporte</span>
        </a>
      </li>

      <li class="site-menu-item has-sub ">
        <a class="animsition-link lh-10" href="#">
        <img src="">
            <span class="site-menu-title footer-nav-link">Termos e Políticas</span>
        </a>
      </li>


    </ul>
</div>
