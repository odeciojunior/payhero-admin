@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/products/create.min.css') }}">
    @endpush

    <!-- Page -->
    <div class="page"
         style="margin-bottom: 0 !important; display:none !important;">
        <div class="page-header container">
            <h1 class="page-title my-10"
                style="min-height: 28px; color: #707070;">Novo produto físico</h1>
            <p class="desc mt-10 text-muted"> Preencha os dados sobre seu produto atentamente. </p>
        </div>
        <div class="page-content container pb-20">
            <form id='my-form-add-product'>
                <div class="panel pt-10 pb-20"
                     data-plugin="matchHeight"
                     style="border-radius: 16px">
                    <h4 class="px-40">1. Informações básicas</h4>
                    <hr class="my-20">
                    <div class="px-40 row justify-content-between align-items-baseline">
                        <div class="form-group col-12 col-md-4">
                            <div class="d-flex flex-column"
                                 id="div_img"
                                 style="position: relative">
                                <div class="d-flex flex-column"
                                     id="div_digital_product_upload">
                                    <label for="product_photo">Imagem do produto</label>
                                    <input type="file"
                                           id="product_photo"
                                           name="product_photo"
                                           data-height="651"
                                           data-max-width="651"
                                           data-max-file-size="10M"
                                           data-allowed-file-extensions="jpg jpeg png">
                                    <small class="text-center text-muted mt-15">Sugerimos PNG ou JPG com 650px x 650px
                                        (1:1).</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">Nome do produto</label>
                                    <input name="name"
                                           type="text"
                                           class="input-pad"
                                           id="name"
                                           placeholder="Digite o nome">
                                </div>
                                <div class="form-group col-12">
                                    <label for="description">Descrição</label>
                                    <textarea style="height: 140px;"
                                              name="description"
                                              type="text"
                                              class="input-pad"
                                              id="description"
                                              placeholder="Descrição apresentada no checkout"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-20">
                    <h4 class="px-40"> 2. Logística </h4>
                    <hr class="my-20">
                    <div class="px-40 row justify-content-between">
                        <div class="col-12 col-md-4 col-lg-2 col-xl-4 text-center">
                            <img id="caixinha-img"
                                 src="{{ mix('build/global/img/svg/caixinha.svg') }}"
                                 class="img-fluid"
                                 alt="novo produto fisico">
                        </div>

                        <div class="row col-12 col-md-8 col-lg-10 col-xl-8">
                            <div class="col-12 col-sm-6 col-lg-3 pl-0">
                                <label for="height">Altura</label>
                                <div class="form-group input-group">
                                    <input name="height"
                                           type="text"
                                           class="form-control"
                                           id="height"
                                           placeholder="Ex: 150"
                                           data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">CM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 pl-0">
                                <label for="width">Largura</label>
                                <div class="form-group input-group">
                                    <input name="width"
                                           type="text"
                                           class="form-control"
                                           id="width"
                                           placeholder="Ex: 135"
                                           data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">CM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 pl-0">
                                <label for="length">Comprimento</label>
                                <div class="form-group input-group">
                                    <input name="length"
                                           type="text"
                                           class="form-control"
                                           id="length"
                                           placeholder="Ex: 150"
                                           value="{{-- {!! $product->width !!} --}}"
                                           data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">CM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 pl-0">
                                <label for="weight">Peso</label>
                                <div class="form-group input-group">
                                    <input name="weight"
                                           type="text"
                                           class="form-control"
                                           id="weight"
                                           placeholder="Ex: 950"
                                           data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold px-20">G</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-12 mt-0">
                                <small class="text-muted">
                                    Clique <a href="http://www2.correios.com.br/sistemas/precosprazos/Formato.cfm"
                                       target="_blank"
                                       style="color: #2E85EC; font-weight: bold;">aqui</a> para consultar as regras de
                                    dimensões dos Correios.
                                </small>
                                <br>
                                <small class="text-muted">
                                    Informações utilizadas para calcular o valor do frete PAC e SEDEX, se não utilizar esses
                                    fretes ignore essas informações
                                </small>
                                <div class="d-flex flex-row align-items-center mt-10 px-2 py-1 rounded"
                                     style="width: fit-content; background: #F4F6FB;">
                                    <img src="{{ mix('build/global/img/icon-info-plans-c.svg') }}"
                                         width="9"
                                         height="9"
                                         style="margin-top: -1px" />
                                    <span class="font-size-10 ml-2"
                                          style="color: #636363">Em vendas de produtos físicos, será solicitado o rastreio
                                        para liberação da comissão da venda.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row pr-15 form-buttons">
                    <a type="button"
                       class="btn btn-cancelar"
                       href="/products">Cancelar</a>
                    <button type="submit"
                            class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px"
                             src="{{ mix('build/global/img/svg/check-all.svg') }}">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ mix('build/layouts/products/create-physical.min.js') }}"></script>
    @endpush
@endsection
