@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container pb-0">
            <div class="row align-items-center mb-30" style="min-height:4rem">
                <div class="col-lg-6">
                    <h1 class="page-title">Produtos</h1>
                </div>
                <div id='div-create' class="col-lg-6">
                    <a href="/products/create" class="btn btn-floating btn-danger"
                       style="position: relative; float: right">
                        <i class="icon wb-plus" aria-hidden="true" style="margin-top:8px"></i>
                    </a>
                </div>
            </div>
            <div class="card shadow p-20" id="filter-products">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <label for="type-products">Tipo</label>
                        <select id='type-products'>
                            <option value="0">Meus Produtos</option>
                            <option value="1">Produtos Shopify</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6" id='is-projects'>
                        <label id="select-projects-label" class="disabled" for="select-projects">Projeto</label>
                        <select id='select-projects' class="disabled" disabled>
                            <option>Carregando...</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="name">Nome do produto</label>
                        <input id="name" class="input-pad" placeholder="Digite 3 ou mais caracteres" maxlength="100">
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
            <div id='pagination-products' class='float-right' style="margin-bottom: 20px"></div>
            <div class='products-is-empty' style='display:none;'>
                @push('css')
                    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
                @endpush
                <div class="content-error text-center pt-0">
                    <img src="{!! asset('modules/global/img/emptyprodutos.svg') !!}" width="150px">
                    <h1 class="big gray">Zero produtos por aqui!</h1>
{{--                    <div class='product-is-empty-cla'>--}}
{{--                        <p class="gray"> Vamos adicionar seu primeiro produto? </p>--}}
{{--                        <a href="/products/create" class="btn btn-primary gradient">Novo Produto</a>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='{{asset('/modules/products/js/index.js')}}'></script>
    @endpush

@endsection
