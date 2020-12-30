@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div style="display: none" class="page-header container pb-0">
            <div class="row align-items-center mb-30" style="min-height:4rem">
                <div class="col-lg-6">
                    <h1 class="page-title">Produtos</h1>
                </div>
                <div id='div-create' class="col-lg-6" style="display:none">
                    <a href="/products/create" class="btn btn-floating btn-primary"
                       style="position: relative; float: right">
                        <i class="icon wb-plus" aria-hidden="true" style="margin-top:8px"></i>
                    </a>
                </div>
            </div>
        </div>
        <div id="project-not-empty" style="display:none">
           <div style="display: none" class="page-header container pb-0">
                <div class="card shadow p-20" id="filter-products">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label for="type-products">Tipo</label>
                                <select class="form-control" id='type-products'>
                                    <option value="0">Meus Produtos</option>
                                    <option value="1">Produtos Shopify</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6" id='is-projects'>
                            <div class="form-group">
                                <label id="select-projects-label" class="disabled" for="select-projects">Projeto</label>
                                <select id='select-projects' class="form-control disabled" disabled>
                                    <option>Carregando...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label for="name">Nome do produto</label>
                                <input id="name" class="input-pad form-control" placeholder="Nome" maxlength="100">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6" style="margin-top: 30px">
                            <button id="btn-filtro" class="btn btn-primary w-full">
                                <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                            </button>
                        </div>
                    </div>
                </div>
           </div>
            <div class="page-content container">
                <div id='data-table-products' class='row'>
                </div>
                <div id='pagination-products' class='float-right margin-chat-pagination' style="margin-bottom: 20px"></div>
                <div class='products-is-empty' style='display:none;'>
                    @push('css')
                        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
                    @endpush
                    <div class="content-error text-center pt-0">
                        <img src="{!! asset('modules/global/img/empty.svg') !!}" width="150px">
                        <h1 class="big gray">Zero produtos por aqui!</h1>
                        {{--                    <div class='product-is-empty-cla'>--}}
                        {{--                        <p class="gray"> Vamos adicionar seu primeiro produto? </p>--}}
                        {{--                        <a href="/products/create" class="btn btn-primary">Novo Produto</a>--}}
                        {{--                    </div>--}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>

    @push('scripts')
        <script src='{{asset('/modules/products/js/index.js?v=' . random_int(100, 10000))}}'></script>
    @endpush

@endsection
