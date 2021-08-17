@extends('layouts.master')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/attendance/css/index.css?v='.uniqid()) }}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v='.uniqid()) }}">
        <link rel="stylesheet" type="text/css"
              href="{{asset('/modules/global/adminremark/global/vendor/slick-carousel/slick.min.css')}}"/>
    @endpush

    <div class="page mb-0">
        <div class="page-header container pb-10" style="display: none; !important;">
            <h1 class="page-title">Atendimento</h1>
        </div>
        <div id="project-not-empty" style="display: none; !important;">
            <div class="page-content container">
                <div class="row">
                    <div class="col-md-3 col-6 pr-20">
                        <div class="card border orange">
                            <span class="title">Em aberto</span>
                            <div>
                                <span class="number">22</span>
                                <span>(22% de 100)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 px-20">
                        <div class="card border purple">
                            <span class="title">Em mediação</span>
                            <div>
                                <span class="number">02</span>
                                <span>(2%)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 px-20">
                        <div class="card border green">
                            <span class="title">Resolvidos</span>
                            <div>
                                <span class="number">76</span>
                                <span>(76%)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 pl-20">
                        <div class="card border blue">
                            <span class="title">Total</span>
                            <div>
                                <span class="number">100</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col d-flex">
                        <div class="filter-icon">
                            <i class="material-icons">filter_list</i>
                            <span>Filtros</span>
                        </div>
                        <div class="filter-container">
                            <span class="filter-badge active">
                                    Reclamação
                                </span>
                            <span class="filter-badge">
                                    Dúvida
                                </span>
                            <span class="filter-badge">
                                    Sugestão
                                </span>
                            <span class="filter-badge">
                                    7 dias
                                </span>
                            <span class="filter-badge">
                                    15 dias
                                </span>
                            <span class="filter-badge">
                                    30 dias
                                </span>
                            <span class="vertical-line"></span>
                            <span class="filter-badge editable" data-target="#input-code">
                                    Código
                                </span>
                            <span class="filter-badge editable" data-target="#input-cpf">
                                    CPF
                                </span>
                            <span class="filter-badge editable" data-target="#input-name">
                                    Nome
                            </span>
                        </div>
                        <div class="filter-badge-input" id="input-code">
                            <input class="input-pad" placeholder="Código do ticket">
                            <button class="btn btn-primary">
                                <i class="material-icons">check</i>
                            </button>
                        </div>
                        <div class="filter-badge-input" id="input-cpf">
                            <input class="input-pad" placeholder="CPF do cliente">
                            <button class="btn btn-primary">
                                <i class="material-icons">check</i>
                            </button>
                        </div>
                        <div class="filter-badge-input" id="input-name">
                            <input class="input-pad" placeholder="Nome do cliente">
                            <button class="btn btn-primary">
                                <i class="material-icons">check</i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col">
                        <div class="card p-0">
                            <div class="tickets-grid">
                                <div class="search-container">
                                    <span>Tickets</span>
                                    <select class="input-select">
                                        <option value="" selected>abertos</option>
                                        <option value="">resolvidos</option>
                                        <option value="">em mediação</option>
                                        <option value="">todos</option>
                                    </select>
                                    <button class="btn-search">
                                        <i class="material-icons">search</i>
                                    </button>
                                    <div class="search-box">
                                        <input class="input-pad" placeholder="Digite o nome ou o CPF">
                                        <button class="btn btn-primary">
                                            <i class="material-icons">check</i>
                                        </button>
                                    </div>
                                </div>
                                <div class="ticket-header">
                                    <button class="ticket-back">
                                        <span class="material-icons ml-10">arrow_back_ios</span>
                                    </button>
                                    <div class="ticket-customer">
                                        Nelson C Lima
                                    </div>
                                    <div class="ticket-status">
                                        <span class="ticket-status-icon open small"></span>
                                        <span class="ticket-status-text">Finalizado</span>
                                    </div>
                                    <div class="ticket-category">
                                        <span class="ticket-category-text">Reclamação</span> aberta em <span
                                            class="ticket-start-date">02/03/2021</span> para <span
                                            class="ticket-project">4FunStore</span>
                                    </div>
                                </div>
                                <div class="tickets-container">
                                    @for($i = 0; $i < 5; $i++)
                                        <div class="ticket-item {{$i==0?'active': ''}}">
                                            <div class="px-30 pt-1">
                                                @php($a = random_int(1, 4))
                                                <span
                                                    class="ticket-status-icon {{$a == 1 ? 'open' : ($a == 2 ? 'closed' : ($a == 3 ? 'mediation' : ($a == 4 ? 'answered' : ''))) }}"></span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="customer-name">Adriano Pontes</div>
                                                <small class="ticket-subject">Problema com código de
                                                    rastreio</small>
                                                <div class="ticket-last-message">Não consigo acessar a página de
                                                    rastreio do meu pedido, acredito que tenha ocorrido algum engano
                                                    no...
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                <div class="messages-container">
                                    <div class="ticket-date">Atendimento aberto em <b class="date">21/04/2021</b> às <b class="time">15h32</b></div>
                                    @for($i = 0; $i < 10; $i++)
                                        @php($a = rand(1, 3))
                                        <div
                                            class="ticket-message {{$a == 2 ? 'admin' : ($a == 3 ? 'cloudfox' : '') }}">
                                            Não consigo acessar a página de
                                            rastreio do meu pedido, acredito que tenha ocorrido algum engano na
                                            hora do envio.
                                            <div class="ticket-message-date">21/04 às 15h32</div>
                                        </div>
                                    @endfor
                                </div>
                                <div class="pagination-container">
                                    <div class="current-page-text">visualizando <span class="per-page">5</span> de
                                        <span class="total">40</span></div>
                                    <div class="pagination">
                                        <a class="active" href="" >1</a>
                                        <a href="" >1</a>
                                        <a href="" >1</a>
                                        <a href="" >1</a>
                                        <a href="" >1</a>
                                        <a href="" >1</a>
                                        <a href="" >1</a>
                                    </div>
                                </div>
                                <div class="write-container">
                                    <div class="write-inputs">
                                        <div class="inputs-container">
                                        <textarea class="input-pad write-area" rows="1"
                                                  placeholder="Digite sua mensagem..."></textarea>
                                            <div class="attachments-container" style="display:none !important;"></div>
                                            <div class="write-buttons">
                                                <input type="file" accept="image/*,.txt,.pdf,.doc,.docx" id="input-file" class="d-none">
                                                <input type="file" accept="image/*" id="input-image" class="d-none">
                                                <button id="btn-emoji">
                                                    <i class="material-icons">sentiment_satisfied_alt</i>
                                                </button>
                                                <button id="btn-file">
                                                    <i class="material-icons">attach_file</i>
                                                </button>
                                                <button id="btn-image">
                                                    <i class="material-icons">image</i>
                                                </button>
                                            </div>
                                        </div>
                                        <button class="btn-send">
                                            <i class="material-icons">send</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('projects::empty')

    @push('scripts')
        <script src="{{asset('/modules/global/adminremark/global/vendor/slick-carousel/slick.min.js')}}"></script>
        <script src="{{asset('/modules/tickets/js/emoji-button.min.js')}}"></script>
        <script src="{{asset('/modules/tickets/js/index.js?v=' . uniqid())}}"></script>
    @endpush

@endsection
