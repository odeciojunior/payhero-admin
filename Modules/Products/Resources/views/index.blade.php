@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ mix('build/layouts/products/index.min.css') }}">
@endpush

@section('content')

<!-- Page -->
<div class="page" style="margin-bottom: 0 !important">
    <div style="display: none" class="page-header container">
        <div class="row align-items-center" style="min-height:4rem">
            <div class="col-6">
                <h1 class="page-title">Produtos</h1>
            </div>
            <div id='div-create' class="col-6" style="display:none;">
                <a data-toggle="modal" data-target="#new-product-modal" class="btn btn-floating btn-primary" style="position: relative; float: right; box-shadow: none; width: 47px; height: 47px">
                    <span class="o-add-1"></span>
                </a>
            </div>
        </div>
    </div>

    <div id="project-not-empty" style="display:none !important;">
        <div style="display: none !important; padding-top:0" class="page-header container">
            <div class="card" id="filter-products">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="mb-10">
                            <label for="type-products">Tipo</label>
                            <select class="sirius-selectz" id='type-products'>
                                <option value="0">Meus Produtos</option>
                                {{-- <option value="1">Produtos Shopify</option> --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="mb-10">
                            <label for="name">Nome do produto</label>
                            <input id="name" class="" placeholder="Digite o nome" maxlength="100" style="min-height: 49px;">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div id="projects-list" class="mb-10 d-none">
                            {{-- <label id="select-projects-label" for="select-projects">Lojas</label>
                            <select id='select-projects' class="sirius-select" disabled>
                                <option>Carregando...</option>
                            </select> --}}
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mt-auto">
                        <button type=submit id="btn-filtro" class="btn btn-primary w-full mb-10">
                            <img style="height: 12px; margin-right: 4px" src="{{ mix('build/global/img/svg/check-all.svg') }}">Aplicar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container pb-40">
            <div id='data-table-products' class='row g-2'></div>
            <div class="row justify-content-center justify-content-md-end">
                <div id='pagination-products' class="pl-5 pr-md-15 mb-20"></div>
            </div>
            <div class='products-is-empty' style='display:none;'>
                @push('css')
                <link rel="stylesheet" href="{!! mix('build/layouts/products/index.min.css') !!}">
                @endpush
                <div class="content-error text-center pt-0">
                    <img src="{!! mix('build/global/img/produtos.svg') !!}" width="156px">
                    <h1 class="big gray">Zero produtos por aqui!</h1>
                    {{-- <div class='product-is-empty-cla'>--}}
                    {{-- <p class="gray"> Vamos adicionar seu primeiro produto? </p>--}}
                    {{-- <a href="/products/create" class="btn btn-primary">Novo Produto</a>--}}
                    {{-- </div>--}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="new-product-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" style="width: 350px;max-width: 350px;" role="document">
            <div class="modal-content s-border-radius">
                <div class="modal-header simple-border-bottom px-20">
                    <h4 class="col-12 modal-title text-center" style="color:#787878; font: normal normal bold 20px Muli;">Criar novo produto</h4>
                    <button type="button" class="close" data-dismiss="modal" style="margin: -10px 0px -15px -5px;" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-20">
                    <div class="container-fluid">
                        <div class="row text-center">
                            <div class="col-6 d-flex flex-column align-items-center">
                                <a href="/products/create/physical" class="mb-2 new-product-icon">
                                    <img src="{{ mix('build/global/img/svg/caixa-fisica.svg') }}" data-value="product_physical" alt="novo produto fisico">
                                </a>
                                <p>Físico</p>
                            </div>
                            <div class="col-6 d-flex flex-column align-items-center">
                                <a href="/products/create/digital" class="mb-2 new-product-icon">
                                    <img src="{{ mix('build/global/img/svg/phone.svg') }}" data-value="product_digital" alt="novo produto digital">
                                </a>
                                <p>Digital</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal padrão para excluir -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true" aria-labelledby="excluirModal" role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div id="modal_excluir_body" class="modal-body text-center p-20">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class=" justify-content-center">
                        <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id="bt_cancelar" type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                        <b>Cancelar</b>
                    </button>
                    <button type="button" class="bt_excluir_modal col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                        <b class="mr-2" style="color: #fff">Excluir </b>
                        <span class="o-bin-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Quando não tem loja cadastrado  --}}
    @include('projects::empty')
    {{-- FIM loja nao existem lojas--}}
</div>

@push('scripts')
    <script src="{{ mix('build/layouts/products/index.min.js') }}"></script>
@endpush

@endsection
