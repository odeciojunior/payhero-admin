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
                <i class="material-icons">notifications_none</i>
                <span class="badge badge-danger badge-notification"  id="qtd_notificacoes">{!! count(\Auth::user()->unreadNotifications) !!}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-right dropdown-menu-media no-border" role="menu">
                <div class="dropdown-menu-header">
                  <h5 class="title-small">Notificações</h5>
                </div>
                <div class="list-group">
                  <div data-role="container" style="width: 100%; height: auto;">
                    <div data-role="content" style="width: 100% height: auto;">
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
                  <i class="material-icons align-middle"> account_circle </i>  Perfil 
                </a>
                <a class="dropdown-item" href="{!! route('companies.index') !!}" role="menuitem">
                  <i class="material-icons align-middle"> business </i> Empresas 
                </a>  
                <div class="dropdown-divider" role="presentation"></div>
                <a class="dropdown-item" href="" role="menuitem" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <i class="material-icons align-middle"> power_settings_new </i>  Logout 
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

 
<!--div class="menu-footer">
      <div class="menu-footer-content"> CloudFox LLC <br> <a href="#"> Termos e Condições </a>  </div>
</div-->

<div class="site-menubar">
    <ul class="site-menu" style="margin-top:10px">
      <li class="site-menu-item has-sub">
        <a href="{{ route('dashboard') }}">
          <i class="material-icons align-middle">dashboard</i>
            <span class="site-menu-title ml-5">Dashboard</span>
        </a>
      </li>
      <li class="site-menu-item has-sub disabled">
        <a class="disabled"  href="{{ route('showcase') }}">
        <i class="material-icons align-middle">store</i>
            <span class="site-menu-title">Vitrine (em breve)</span>
        </a>
      </li>
      <li class="site-menu-item has-sub">
        <a href="javascript:void(0)" id="sales-link">
        <!-- <i class="material-icons align-middle"><span class="mm-opended-hidden">shopping_basket</span></i> -->
        <svg class="svg-menu align-middle" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M17.21 9l-4.38-6.56c-.19-.28-.51-.42-.83-.42-.32 0-.64.14-.83.43L6.79 9H2c-.55 0-1 .45-1 1 0 .09.01.18.04.27l2.54 9.27c.23.84 1 1.46 1.92 1.46h13c.92 0 1.69-.62 1.93-1.46l2.54-9.27L23 10c0-.55-.45-1-1-1h-4.79zM9 9l3-4.4L15 9H9zm3 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>
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
              <span class="site-menu-title">Carrinhos abandonados</span>
            </a>
          </li>
          {{--<li class="site-menu-item">--}}
            {{--<a href="#" style="pointer-events: none;cursor: default;">--}}
              {{--<span class="site-menu-title">Central de reembolso</span>--}}
            {{--</a>--}}
          {{--</li>--}}
        </ul>
      </li>
      <li class="site-menu-item has-sub">
        <a  href="/projects" id="projects-link" >
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
        <a class="disabled"  href="{{ route('attendance') }}">
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

      {{--  <li class="site-menu-item has-sub">
        <a class=" lh-10" href="#">
        <img src="">
            <span class="site-menu-title footer-nav-link">Suporte</span>
        </a>
      </li>  --}}

    </ul>

</div>

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
