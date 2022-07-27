@extends("layouts.master")

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ mix('build/layouts/projects/index.min.css') }}">
        <style>
            .card{
                min-height:403px;
            }
            .card-title{
                min-height:56px;
            }
        </style>
    @endpush

    <!-- PAGINA INTEIRA -->
    <div class="page" style="padding-bottom: 0;">
        <!-- CONTAINER DO CONTEUDO DA PAGINA -->
        <div class="page-content container pr-5" style="padding-top: 0">

            <!-- CABECALHO -->
            <div style="display: none" class="page-header container">
                <div class="row align-items-center justify-content-between" style="min-height:50px">

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
                            <div id="button_toggle" class="d-flex mr-30">
                                <label class="switch mr-3">
                                    <input type="checkbox" class='check' value='{{auth()->user()->deleted_project_filter}}' name="deleted_project_filter" id="deleted_project_filter">
                                    <span class="slider round"></span>
                                </label>
                                <div for='deleted_project_filter' class="mt-3"><b>Ver excluídas</b></div>
                            </div>

                            <!-- BOTAO ADICIONAR LOJA -->
                            <button id="new-store-button" data-toggle="modal" data-target="#new-store-modal" class="btn btn-floating btn-primary" style="position: relative; float: right; display: none;" data-placement="bottom" title="Adicionar Loja">
                                <span style="color: white; font-size: 35px" class='o-add-1'></span>
                            </button>

                        </div>
                    </div>

                </div>
            </div>

            <!-- CONTAINER QUE EXIBE OS CARS -->
            <div id="data-table-projects" class="row page-header container mt-10 mt-sm-0 pr-0 pt-15 pb-0">
                {{-- GERADO POR JAVASCRIPT --}}
            </div>

            <!-- LEGENDA DRAG DROP -->
            <div id="subtitle_drag_drop" class="container pl-30 pt-25">
                <div class="px-0">
                    Você pode reordenar suas lojas utilizando o arrastar e soltar
                    <img src="build/layouts/projects/img/dragItem.svg" class="ml-5"/>
                </div>
            </div>
        </div>

        <div class="modal fade" id="new-store-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 450px">
                <div class="modal-content" style="border-radius: 8px;">
                    <div class="d-flex flex-row-reverse simple-border-bottom py-10 px-20">
                        <h4 class="new-store-modal-option-title text-center">Criar nova loja</h4>
                        <button type="button" class="new-store-modal-option-close-btn" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="d-flex flex-row justify-content-around" style="padding: 32px 16px">
                        <div class="d-flex flex-column align-items-center new-store-modal-option">
                            <a href="/apps/shopify" class="d-flex justify-content-center align-items-center">
                                <img src="{{ mix('build/global/img/svg/shopify-logo.svg') }}" data-value="product_digital" alt="novo produto digital">
                            </a>
                            <span>Shopify</span>
                        </div>
                        <div class="d-flex flex-column align-items-center new-store-modal-option">
                            <a href="/apps/woocommerce" class="d-flex justify-content-center align-items-center">
                                <img src="{{ mix('build/global/img/svg/woocommerce-logo.svg') }}" data-value="product_digital" alt="novo produto digital">
                            </a>
                            <span>Woocommerce</span>
                        </div>
                        <div class="d-flex flex-column align-items-center new-store-modal-option">
                            <a href="/projects/create" class="d-flex justify-content-center align-items-center">
                                <img src="{{ mix('build/global/img/svg/landing-logo.svg') }}" data-value="product_physical" alt="novo produto fisico">
                            </a>
                            <span>Landing Page</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('projects::empty')
        @include('projects::empty-company')
    </div>

    @push('scripts')
        <script src="{{ mix('build/layouts/projects/index.min.js') }}"></script>
    @endpush
@endsection
