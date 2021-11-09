@extends('layouts.master')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/attendance/css/index.css?v='.uniqid()) }}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v='.uniqid()) }}">
        <link rel="stylesheet" type="text/css" href="{{asset('/modules/global/adminremark/global/vendor/slick-carousel/slick.min.css')}}"/>
        <link rel="stylesheet" href="{{ asset('/modules/global/jquery-daterangepicker/daterangepicker.min.css') }}">
    @endpush

    <div class="page mb-0" id="project-not-empty" style="display: none !important;">
        <div class="page-header container pb-0" style="display: none !important;">
            <h1 class="page-title">Atendimento</h1>
            <div class="d-flex align-items-center flex-wrap my-15">
                <div class="d-flex align-items-center mb-md-0 mb-15">
                    <span class="badge badge-primary mr-10">NOVO!</span>
                    <span>Uma nova central para você responder as solicitações de seus clientes.</span>
                </div>
                <select class="header-select ml-auto" id="project-select">
                    <option value="">Todos os projetos</option>
                </select>
            </div>
        </div>
        <div class="page-content container">
                <div class="row">
                    <div class="col-md-3 col-6 pr-20">
                        <div class="card border orange">
                            <span class="title">Em aberto</span>
                            <div id="ticket-open">
                                <span class="number"></span>
                                <span class="detail"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 px-20">
                        <div class="card border purple">
                            <span class="title">Em mediação</span>
                            <div id="ticket-mediation">
                                <span class="number"></span>
                                <span class="detail"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 px-20">
                        <div class="card border green">
                            <span class="title">Resolvidos</span>
                            <div id="ticket-closed">
                                <span class="number"></span>
                                <span class="detail"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 pl-20">
                        <div class="card border blue">
                            <span class="title">Total</span>
                            <div id="ticket-total">
                                <span class="number"></span>
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
                            <span id="category-complaint" class="filter-badge">Reclamação</span>
                            <span id="category-doubt" class="filter-badge">Dúvida</span>
                            <span id="category-suggestion" class="filter-badge">Sugestão</span>
                            <span class="vertical-line"></span>
                            <span id="filter-name" class="filter-badge editable" data-target="#input-name">Nome</span>
                            <span id="filter-document" class="filter-badge editable" data-target="#input-document">CPF/CNPJ</span>
                            <span class="vertical-line"></span>
                            <span id="filter-answer" class="filter-badge editable" data-target="#input-answer">Respostas</span>
                            <span class="vertical-line"></span>
                            <span id="filter-date" class="filter-badge editable daterange">Selecionar</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col">
                    <div class="filter-badge-input" id="input-document">
                        <input class="input-pad" placeholder="CPF/CNPJ do cliente">
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
                    <div class="filter-badge-input" id="input-answer">
                        <select id="answered" class="select-pad">
                            <option value="not-answered">Não respondidos</option>
                            <option value="last-answer-customer">Última resposta do cliente</option>
                            <option value="last-answer-admin">Última resposta minha</option>
                        </select>
                        <button class="btn btn-primary">
                            <i class="material-icons">check</i>
                        </button>
                    </div>
                </div>
                </div>
                <div class="row mt-10">
                    <div class="col">
                        <div class="card p-0">
                            <div class="tickets-grid empty">
                                <div class="tickets-grid-left">
                                    <div class="search-container">
                                        <span>Tickets</span>
                                        <select id="filter-status" class="input-select">
                                            <option value="" selected>todos</option>
                                            <option value="1">abertos</option>
                                            <option value="2">resolvidos</option>
                                            <option value="3">em mediação</option>
                                        </select>
                                        <button class="btn-search">
                                            <i class="material-icons">search</i>
                                        </button>
                                        <div class="search-box">
                                            <input id="name-or-document" class="input-pad" placeholder="Digite o nome ou o CPF/CNPJ">
                                            <button class="btn btn-primary">
                                                <i class="material-icons">check</i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tickets-container">
                                        {{-- loaded via js --}}
                                    </div>
                                    <div class="pagination-container" style="display:none">
                                        <div class="current-page-text">
                                            visualizando <span class="per-page">5</span> de
                                            <span class="total">40</span>
                                        </div>
                                        <div class="pagination" id="tickets-pagination">
                                            {{-- loaded via js --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="tickets-grid-right">
                                    <div class="ticket-header">
                                        <button class="ticket-back">
                                            <span class="material-icons ml-10">arrow_back_ios</span>
                                        </button>
                                        <div class="ticket-customer"></div>
                                        <div class="ticket-status" style="display:none">
                                            <span class="ticket-status-icon small"></span>
                                            <span class="ticket-status-text"></span>
                                        </div>
                                        <div class="ticket-category" style="display:none">
                                            <span class="ticket-category-text"></span> aberta em
                                            <span class="ticket-start-date"></span> para
                                            <span class="ticket-project"></span>
                                        </div>
                                    </div>
                                    <div class="messages-container">
                                        {{-- loaded via js --}}
                                    </div>
                                    <div class="write-container" style="display:none;">
                                        <div class="inputs-container">
                                            <input type="hidden" id="ticket-id">
                                            <div class="attachments-container" style="display:none"></div>
                                            <textarea id="write-area" class="input-pad" rows="1"
                                                      placeholder="Digite sua mensagem..."></textarea>
                                            <div class="write-buttons">
                                                <input type="file" accept="image/*,.txt,.pdf,.doc,.docx" id="input-file"
                                                       class="d-none">
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
                                        <button id="btn-send">
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

    @include('projects::empty')

    @push('scripts')
        <script src="{{asset('/modules/global/adminremark/global/vendor/slick-carousel/slick.min.js')}}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src="{{ asset('modules/global/jquery-daterangepicker/daterangepicker.min.js?v=' . uniqid()) }}"></script>
        <script src="{{asset('/modules/tickets/js/emoji-button.min.js')}}"></script>
        <script src="{{asset('/modules/tickets/js/index.js?v=' . uniqid())}}"></script>
    @endpush

@endsection
