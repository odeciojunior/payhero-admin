@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/products/css/products.css?v=08') }}">
@endpush

@section('content')

    <!-- Page -->
    <div class="page mb-0">
        <div style="display: none" class="page-header container pb-0">
            <div class="row align-items-center" style="min-height:4rem">
                <div class="col-6">
                    <h1 class="page-title" style="color: #707070">Produtos</h1>
                </div>
                <div id='div-create' class="col-6" style="display:none">
                    <a data-toggle="modal" data-target="#new-product-modal" class="btn btn-floating btn-primary"
                       style="position: relative; float: right; box-shadow: none; width: 47px; height: 47px">
                        <span class="o-add-1"></span>
                    </a>
                </div>
            </div>
        </div>

        <div id="project-not-empty" style="display:none">
           <div style="display: none" class="page-header container pb-0">
                <div class="card" id="filter-products">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-10">
                                <label for="type-products">Tipo</label>
                                <select class="form-control select-pad" id='type-products'>
                                    <option value="0">Meus Produtos</option>
                                    <option value="1">Produtos Shopify</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6" id='is-projects'>
                            <div class="mb-10">
                                <label id="select-projects-label" for="select-projects">Projeto</label>
                                <select id='select-projects' class="form-control select-pad disabled" disabled>
                                    <option>Carregando...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-10">
                                <label for="name">Nome do produto</label>
                                <input id="name" class="form-control input-pad" placeholder="Digite o nome" maxlength="100">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mt-auto">
                            <button id="btn-filtro" class="btn btn-primary w-full mb-10">
                                <img style="height: 12px; margin-right: 4px" src="https://sirius.cloudfox.net/modules/global/img/svg/check-all.svg">Aplicar filtros
                            </button>
                        </div>
                    </div>
                </div>
           </div>
            <div class="page-content container">
                <div id='data-table-products' class='row g-2'></div>
                <div class="row justify-content-center justify-content-md-end">
                    <div id='pagination-products' class="pl-5 pr-md-15 mb-20"></div>
                </div>
                <div class='products-is-empty' style='display:none;'>
                    @push('css')
                        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=03') !!}">
                    @endpush
                    <div class="content-error text-center pt-0">
                        <img src="{!! asset('modules/global/img/produtos.svg') !!}" width="156px">
                        <h1 class="big gray">Zero produtos por aqui!</h1>
                        {{--                    <div class='product-is-empty-cla'>--}}
                        {{--                        <p class="gray"> Vamos adicionar seu primeiro produto? </p>--}}
                        {{--                        <a href="/products/create" class="btn btn-primary">Novo Produto</a>--}}
                        {{--                    </div>--}}
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
                            <img src="{{ asset('/modules/global/img/svg/caixa-fisica.svg') }}"  data-value="product_physical" alt="novo produto fisico">
                          </a>
                          <p>Físico</p>
                        </div>
                        <div class="col-6 d-flex flex-column align-items-center">
                          <a href="/products/create/digital" class="mb-2 new-product-icon">
                            <img src="{{ asset('/modules/global/img/svg/phone.svg') }}" data-value="product_digital" alt="novo produto digital">
                          </a>
                          <p>Digital</p>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
        
        {{-- Quando não tem projeto cadastrado  --}}
        @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>

    @push('scripts')
        <script src='{{ asset('/modules/products/js/index.js?v=02') }}'></script>
    @endpush

@endsection
