@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/projects/index.min.css') }}">
        <style>
            .card {
                min-height: 403px;
            }

            .card-title {
                min-height: 56px;
            }
        </style>
    @endpush

    <!-- PAGINA INTEIRA -->
    <div class="page"
         style="padding-bottom: 0;">

    @include('layouts.company-select', ['version' => 'mobile'])

    <!-- CONTAINER DO CONTEUDO DA PAGINA -->
        <div class="page-content container pr-5"
             style="padding-top: 0">

            <!-- CABECALHO -->
            <div class="page-header container">
                <div class="row align-items-center justify-content-between"
                     style="min-height:50px">

                    <!-- TITULO DA PAGINA -->
                    <div class="col-12 col-sm-7 col-md-6 mb-10 mb-sm-0">
                        <h1 class="page-title">Minhas Lojas</h1>
                        <div class="page-header-actions"></div>
                    </div>

                    <!-- BOTOES DO CABECALHO -->
                    <div class="col-12 col-sm-5 col-md-6">

                        <!-- 1.BOTAO TOGGLE / 2.BOTAO ADICIONAR -->
                        <div class="row no-gutters align-items-center justify-content-sm-end justify-content-between">

                            <!-- BOTAO EXIBIR/ESCONDER EXCLUIDOS -->
                            <div id="button_toggle"
                                 class="d-flex mr-60">
                                <label class="switch mr-3">
                                    <input type="checkbox"
                                           class='check'
                                           value='{{ auth()->user()->deleted_project_filter }}'
                                           name="deleted_project_filter"
                                           id="deleted_project_filter">
                                    <span class="slider round"></span>
                                </label>
                                <div for='deleted_project_filter'
                                     class="mt-3"><b>Exibir lojas excluídas nos filtros</b>
                                </div>
                            </div>

                            <!-- BOTAO ADICIONAR LOJA -->
                            <button id="new-store-button"
                                    data-toggle="modal"
                                    data-target="#new-store-modal"
                                    class="btn btn-floating btn-primary"
                                    style="position: relative; float: right; display: none;"
                                    data-placement="bottom"
                                    title="Adicionar Loja">
                                <span style="color: white; font-size: 35px"
                                      class='o-add-1'></span>
                            </button>

                        </div>
                    </div>

                </div>
            </div>

            <!-- CONTAINER QUE EXIBE OS CARS -->
            <div id="data-table-projects"
                 class="row page-header container mt-10 mt-sm-0 pr-0 pt-15 pb-0">
                {{-- GERADO POR JAVASCRIPT --}}
            </div>

            <!-- LEGENDA DRAG DROP -->
            <div id="subtitle_drag_drop"
                 class="container pl-30 pt-25 d-none">
                <div class="px-0">
                    Você pode reordenar suas lojas utilizando o arrastar e soltar
                    <img src="build/layouts/projects/img/dragItem.svg"
                         class="ml-5" />
                </div>
            </div>
        </div>

        @include('projects::create-store-modal')

        @include('projects::empty')
        @include('projects::empty-company')
    </div>

    @push('scripts')
        <script src="{{ mix('build/layouts/projects/index.min.js') }}"></script>
    @endpush
@endsection
