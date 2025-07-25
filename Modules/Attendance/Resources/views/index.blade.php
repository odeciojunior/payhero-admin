@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/attendance/index.min.css') }}">
    @endpush

    <div class="page">

        @include('layouts.company-select', ['version' => 'mobile'])

        <div class="page-header container pb-0"
             style="display: none !important;">
            <div class="row align-items-center justify-content-between"
                 style="min-height:50px">
                <div class="col-md-12">
                    <h1 class="page-title mt-25 mt-md-0">
                        Atendimento <span class="new-circle"></span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="mb-0"
             id="project-not-empty"
             style="display: none !important;">
            <div class="page-header container pb-0"
                 style="display: none !important; padding-top: 0;">
                <div class="d-flex align-items-center flex-wrap mt-15 mb-30">
                    <div class="d-flex align-items-center mb-lg-0 mb-15 mr-auto">
                        <span class="badge badge-primary d-none d-md-block mr-10">NOVO!</span>
                        <span>Uma nova central para você responder as solicitações de seus clientes.</span>
                    </div>
                    <select class="sirius-select"
                            id="project-select">
                        <option value="">Todas lojas</option>
                    </select>
                </div>
            </div>
            <div class="page-content container">
                <div class="row mb-20">
                    <div class="col-md-3 col-6 pr-5 pr-md-15">
                        <div class="card border orange mb-10">
                            <span class="title">Em aberto</span>
                            <div class="tickets-resume"
                                 id="ticket-open">
                                <span class="skeleton-loading"></span>
                                <span class="number"></span>
                                <span class="detail"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 pl-5 pl-md-15">
                        <div class="card border purple mb-10">
                            <span class="title">Em mediação</span>
                            <div class="tickets-resume"
                                 id="ticket-mediation">
                                <span class="skeleton-loading"></span>
                                <span class="number"></span>
                                <span class="detail"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 pr-5 pr-md-15">
                        <div class="card border green mb-10">
                            <span class="title">Resolvidos</span>
                            <div class="tickets-resume"
                                 id="ticket-closed">
                                <span class="skeleton-loading"></span>
                                <span class="number"></span>
                                <span class="detail"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 pl-5 pl-md-15">
                        <div class="card border blue mb-10">
                            <span class="title">Total</span>
                            <div class="tickets-resume"
                                 id="ticket-total">
                                <span class="skeleton-loading"></span>
                                <span class="number"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <div class="filter-icon">
                            <i class="material-icons">filter_list</i>
                        </div>
                        <div class="filter-container vertical-scroll">
                            <span id="filter-plan"
                                  class="filter-badge editable dropdown"
                                  data-target="#input-plan">Por plano</span>
                            <span class="vertical-line"></span>
                            <span id="filter-transaction"
                                  class="filter-badge editable"
                                  data-target="#input-transaction">Transação</span>
                            <span class="vertical-line"></span>
                            <span id="filter-name"
                                  class="filter-badge editable"
                                  data-target="#input-name">Nome</span>
                            <span id="filter-document"
                                  class="filter-badge editable"
                                  data-target="#input-document">CPF/CNPJ</span>
                            <span class="vertical-line"></span>
                            <span id="filter-answer"
                                  class="filter-badge editable"
                                  data-target="#input-answer">Respostas</span>
                            <span class="vertical-line"></span>
                            <span id="filter-date"
                                  class="filter-badge editable daterange">Selecionar</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="filter-badge-input clean"
                             id="input-plan">
                            <select id="plan-select">
                                <option value="">Todos planos</option>
                            </select>
                        </div>
                        <div class="filter-badge-input"
                             id="input-document">
                            <input class="input-pad"
                                   placeholder="CPF/CNPJ do cliente">
                            <button class="btn btn-primary">
                                <i class="material-icons">check</i>
                            </button>
                        </div>
                        <div class="filter-badge-input"
                             id="input-name">
                            <input class="input-pad"
                                   placeholder="Nome do cliente">
                            <button class="btn btn-primary">
                                <i class="material-icons">check</i>
                            </button>
                        </div>
                        <div class="filter-badge-input"
                             id="input-transaction">
                            <input class="input-pad"
                                   placeholder="Transação">
                            <button class="btn btn-primary">
                                <i class="material-icons">check</i>
                            </button>
                        </div>
                        <div class="filter-badge-input"
                             id="input-answer">
                            <select id="answered"
                                    class="sirius-select">
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
                                        <select id="filter-status"
                                                class="input-select">
                                            <option value=""
                                                    selected>todos
                                            </option>
                                            <option value="1">abertos</option>
                                            <option value="2">resolvidos</option>
                                            <option value="3">em mediação</option>
                                        </select>
                                        <button class="btn-search">
                                            <i class="material-icons">search</i>
                                        </button>
                                        <div class="search-box">
                                            <input id="name-or-document"
                                                   class="input-pad"
                                                   placeholder="Digite o nome ou o CPF/CNPJ">
                                            <button class="btn btn-primary">
                                                <i class="material-icons">check</i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="tickets-container-loading">
                                        <div class="ticket-item">
                                            <div class="px-30">
                                                <div class="ticket-status-icon skeleton-loading"></div>
                                            </div>
                                            <div class="d-flex flex-column"
                                                 style="width: 100%">
                                                <div class="skeleton-loading"
                                                     style="width: 25%; height: 20px;"></div>
                                                <div class="skeleton-loading mt-3"
                                                     style="width: 40%; height: 10px;"></div>
                                                <div class="skeleton-loading mt-5"
                                                     style="width: 80%; height: 10px;"></div>
                                            </div>
                                        </div>
                                        <div class="ticket-item">
                                            <div class="px-30">
                                                <div class="ticket-status-icon skeleton-loading"></div>
                                            </div>
                                            <div class="d-flex flex-column"
                                                 style="width: 100%">
                                                <div class="skeleton-loading"
                                                     style="width: 25%; height: 20px;"></div>
                                                <div class="skeleton-loading mt-3"
                                                     style="width: 40%; height: 10px;"></div>
                                                <div class="skeleton-loading mt-5"
                                                     style="width: 80%; height: 10px;"></div>
                                            </div>
                                        </div>
                                        <div class="ticket-item">
                                            <div class="px-30">
                                                <div class="ticket-status-icon skeleton-loading"></div>
                                            </div>
                                            <div class="d-flex flex-column"
                                                 style="width: 100%">
                                                <div class="skeleton-loading"
                                                     style="width: 25%; height: 20px;"></div>
                                                <div class="skeleton-loading mt-3"
                                                     style="width: 40%; height: 10px;"></div>
                                                <div class="skeleton-loading mt-5"
                                                     style="width: 80%; height: 10px;"></div>
                                            </div>
                                        </div>
                                        <div class="ticket-item">
                                            <div class="px-30">
                                                <div class="ticket-status-icon skeleton-loading"></div>
                                            </div>
                                            <div class="d-flex flex-column"
                                                 style="width: 100%">
                                                <div class="skeleton-loading"
                                                     style="width: 25%; height: 20px;"></div>
                                                <div class="skeleton-loading mt-3"
                                                     style="width: 40%; height: 10px;"></div>
                                                <div class="skeleton-loading mt-5"
                                                     style="width: 80%; height: 10px;"></div>
                                            </div>
                                        </div>
                                        <div class="ticket-item">
                                            <div class="px-30">
                                                <div class="ticket-status-icon skeleton-loading"></div>
                                            </div>
                                            <div class="d-flex flex-column"
                                                 style="width: 100%">
                                                <div class="skeleton-loading"
                                                     style="width: 25%; height: 20px;"></div>
                                                <div class="skeleton-loading mt-3"
                                                     style="width: 40%; height: 10px;"></div>
                                                <div class="skeleton-loading mt-5"
                                                     style="width: 80%; height: 10px;"></div>
                                            </div>
                                        </div>
                                        <div class="ticket-item">
                                            <div class="px-30">
                                                <div class="ticket-status-icon skeleton-loading"></div>
                                            </div>
                                            <div class="d-flex flex-column"
                                                 style="width: 100%">
                                                <div class="skeleton-loading"
                                                     style="width: 25%; height: 20px;"></div>
                                                <div class="skeleton-loading mt-3"
                                                     style="width: 40%; height: 10px;"></div>
                                                <div class="skeleton-loading mt-5"
                                                     style="width: 80%; height: 10px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tickets-container">
                                        {{-- loaded via js --}}
                                    </div>
                                    <div class="pagination-container"
                                         style="display:none">
                                        <div class="current-page-text">
                                            visualizando <span class="per-page">5</span> de
                                            <span class="total">40</span>
                                        </div>
                                        <div class="pagination"
                                             id="tickets-pagination">
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
                                        <div class="ticket-status ticket-skeleton-loading">
                                            <span class="ticket-status-icon small skeleton-loading"></span>
                                            <span class="skeleton-loading ticket-status-text-loading"></span>
                                        </div>
                                        <div class="ticket-status d-none">
                                            <span class="ticket-status-icon small"></span>
                                            <span class="ticket-status-text"></span>
                                        </div>
                                        <div class="ticket-category d-none">
                                            <span class="ticket-category-text"></span> aberta em
                                            <span class="ticket-start-date"></span> para
                                            <span class="ticket-project"></span>
                                        </div>
                                        <div class="ticket-sale ticket-skeleton-loading">
                                            <div class="skeleton-loading"
                                                 style="width: 100px; height: 12px;"></div>
                                        </div>
                                        <div class="ticket-sale d-none">
                                            <b>Transação: </b>
                                            <span></span>
                                        </div>
                                    </div>
                                    <div id="messages-container-loading">
                                        <div class="ticket-message skeleton-loading"></div>
                                        <div class="ticket-message skeleton-loading"
                                             style="margin-left: auto"></div>
                                        <div class="ticket-message skeleton-loading"></div>
                                        <div class="ticket-message skeleton-loading"
                                             style="margin-left: auto"></div>
                                    </div>
                                    <div class="messages-container">
                                        {{-- loaded via js --}}
                                    </div>
                                    <div class="write-container"
                                         style="display:none;">
                                        <div class="ticket-closed-info"
                                             style="display:none;">
                                            <b>Atendimento finalizado</b>
                                            <span>Você não pode mais enviar mensagens nesse chat.</span>
                                        </div>
                                        <div class="inputs-container">
                                            <input type="hidden"
                                                   id="ticket-id">
                                            <div class="attachments-container"
                                                 style="display:none"></div>
                                            <textarea id="write-area"
                                                      class="input-pad"
                                                      rows="1"
                                                      placeholder="Digite sua mensagem..."></textarea>
                                            <div class="write-buttons">
                                                <input type="file"
                                                       accept="audio/*,image/*,video/*,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                                                       id="input-file"
                                                       class="d-none">
                                                <input type="file"
                                                       accept="image/*"
                                                       id="input-image"
                                                       class="d-none">
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
            <script src="{{ mix('build/layouts/attendance/index.min.js') }}"></script>
        @endpush
    @endsection
