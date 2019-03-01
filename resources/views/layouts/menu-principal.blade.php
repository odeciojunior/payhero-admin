<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">

    <div class="navbar-header">
      <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
        data-toggle="menubar">
        <span class="sr-only">Toggle navigation</span>
        <span class="hamburger-bar"></span>
      </button>
      <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
        data-toggle="collapse">
        <i class="icon wb-more-horizontal" aria-hidden="true"></i>
      </button>
      <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
        <img class="navbar-brand-logo" src="{{ asset('adminremark/assets/images/logo.png') }}" title="CloudFox">
        <span class="navbar-brand-text hidden-xs-down"> CloudFox</span>
      </div>
    </div>
    <div class="navbar-container container-fluid">
      <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
        <ul class="nav navbar-toolbar">
          <li class="nav-item hidden-float" id="toggleMenubar">
            <a class="nav-link" data-toggle="menubar" href="#" role="button">
              <i class="icon hamburger hamburger-arrow-left">
                <span class="sr-only">Toggle menubar</span>
                <span class="hamburger-bar"></span>
              </i>
            </a>
          </li>
          <li class="nav-item hidden-sm-down" id="toggleFullscreen">
            <a class="nav-link icon icon-fullscreen" data-toggle="fullscreen" href="#" role="button">
              <span class="sr-only">Toggle fullscreen</span>
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
                                <h6 class="media-heading">{!! $notification->data['qtd'] !!} {!! $notification->data['qtd'] > 1 ? 'novas vendas' : 'nova venda' !!}</h6>
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
                <img src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_USER.\Auth::user()->foto)!!}" alt="...">
                <i></i>
              </span>
            </a>
            <div class="dropdown-menu" role="menu">
              <a class="dropdown-item" href="{!! route('perfil') !!}" role="menuitem">
                <i class="icon wb-user" aria-hidden="true"></i> 
                Perfil 
              </a>
              @if(Auth::user()->hasRole('administrador empresarial'))
                <a class="dropdown-item" href="{{ route('empresas') }}" role="menuitem">
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
    </div>
  </nav>   

  <div class="site-menubar">
    <div class="site-menubar-body">
      <div>
        <div>
          <ul class="site-menu" data-plugin="menu">
            <li class="site-menu-category"></li>
            <li class="site-menu-item has-sub">
              <a href="{{ route('dashboard') }}">
                <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                <span class="site-menu-title">Dashboard</span>
              </a>
            </li>
            <li class="site-menu-item has-sub">
              <a href="{{ route('vitrine') }}">
                <i class="site-menu-icon wb-grid-9" aria-hidden="true"></i>
                <span class="site-menu-title">Vitrine</span>
              </a>
            </li>
            <li class="site-menu-item has-sub">
              <a href="javascript:void(0)">
                <i class="site-menu-icon wb-shopping-cart" aria-hidden="true"></i>
                <span class="site-menu-title">Vendas</span>
                <span class="site-menu-arrow"></span>
              </a>
              <ul class="site-menu-sub">
                <li class="site-menu-item">
                  <a class="animsition-link" href="/relatorios/vendas">
                    <span class="site-menu-title">Visão geral</span>
                  </a>
                </li>
                <li class="site-menu-item">
                  <a class="animsition-link" href="{!! route('vendas.recuperacaocarrinho') !!}">
                    <span class="site-menu-title">Recuperação de carrinho</span>
                  </a>
                </li>
                <li class="site-menu-item">
                  <a class="animsition-link" href="#">
                    <span class="site-menu-title">Central de reembolso</span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="site-menu-item has-sub">
              <a href="javascript:void(0)">
                <i class="site-menu-icon wb-clipboard" aria-hidden="true"></i>
                <span class="site-menu-title">Projetos</span>
                <span class="site-menu-arrow"></span>
              </a>
              <ul class="site-menu-sub">
                <li class="site-menu-item">
                  <a class="animsition-link" href="{{ route('projetos.cadastro') }}">
                    <span class="site-menu-title">Cadastrar projeto</span>
                  </a>
                </li>
                <li class="site-menu-item">
                  <a class="animsition-link" href="{{ route('projetos') }}">
                    <span class="site-menu-title">Meus projetos</span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="site-menu-item has-sub">
              <a href="javascript:void(0)">
                <i class="site-menu-icon wb-mobile" aria-hidden="true"></i>
                <span class="site-menu-title">Produtos</span>
                <span class="site-menu-arrow"></span>
              </a>
              <ul class="site-menu-sub">
                <li class="site-menu-item">
                  <a class="animsition-link" href="{{ route('produtos') }}">
                    <span class="site-menu-title">Meus produtos</span>
                  </a>
                </li>
                <li class="site-menu-item">
                  <a class="animsition-link" href="{{ route('produtos.cadastro') }}">
                    <span class="site-menu-title">Cadastrar produto</span>
                  </a>
                </li>
                {{--  <li class="site-menu-item">
                  <a class="animsition-link" href="{{ route('categorias') }}">
                    <span class="site-menu-title">Categorias de produtos</span>
                  </a>
                </li>  --}}
              </ul>
            </li>
            <li class="site-menu-item has-sub">
              <a href="javascript:void(0)">
                <i class="site-menu-icon wb-help-circle" aria-hidden="true"></i>
                <span class="site-menu-title">Atendimento</span>
                <span class="site-menu-arrow"></span>
              </a>
              <ul class="site-menu-sub">
                <li class="site-menu-item">
                  <a class="animsition-link" href="{{ route('atendimento.index') }}">
                    <span class="site-menu-title">Visão geral</span>
                  </a>
                </li>
                <li class="site-menu-item">
                  <a class="animsition-link" href="#">
                    <span class="site-menu-title">Configurações</span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="site-menu-item has-sub">
              <a href="javascript:void(0)">
                <i class="site-menu-icon wb-graph-down" aria-hidden="true"></i>
                <span class="site-menu-title">Afiliados</span>
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
                <i class="site-menu-icon wb-plus" aria-hidden="true"></i>
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
              <a href="{!! route('ferramentas') !!}">
                <i class="site-menu-icon wb-settings" aria-hidden="true"></i>
                <span class="site-menu-title">Ferramentas</span>
              </a>
            </li>
            <li class="site-menu-item has-sub">
              <a href="{{ route('aplicativos') }}">
                <i class="site-menu-icon wb-briefcase" aria-hidden="true"></i>
                <span class="site-menu-title">Aplicativos</span>
              </a>
            </li>
            @if(Auth::user()->hasRole('administrador geral'))
              <li class="site-menu-item has-sub">
                <a href="{{ route('empresas') }}">
                  <i class="site-menu-icon wb-briefcase" aria-hidden="true"></i>
                  <span class="site-menu-title">Empresas</span>
                </a>
              </li>
              <li class="site-menu-item has-sub">
                <a href="javascript:void(0)">
                  <i class="site-menu-icon wb-graph-down" aria-hidden="true"></i>
                  <span class="site-menu-title">Transportadoras</span>
                  <span class="site-menu-arrow"></span>
                </a>
                <ul class="site-menu-sub">
                  <li class="site-menu-item">
                    <a class="animsition-link" href="{{ route('transportadoras') }}">
                      <span class="site-menu-title">Transportadoras</span>
                    </a>
                  </li>
                  <li class="site-menu-item">
                    <a class="animsition-link" href="{{ route('despachos') }}">
                      <span class="site-menu-title">Despachos</span>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="site-menu-item has-sub">
                <a href="{{ route('logs') }}">
                  <i class="site-menu-icon wb-list-bulleted" aria-hidden="true"></i>
                  <span class="site-menu-title">Logs de acesso</span>
                </a>
              </li>
            @endif
            <li class="site-menu-item has-sub">
              <a href="{{ route('convites') }}">
                <i class="site-menu-icon wb-user-add" aria-hidden="true"></i>
                <span class="site-menu-title">Convites</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

