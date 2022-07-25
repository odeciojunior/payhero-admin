@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ mix('build/layouts/products/edit.min.css') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title my-10" style="min-height: 28px">
                <a href="/products">
                    <span class="o-arrow-right-1 font-size-30 ml-2" aria-hidden="true"></span>
                    Editar produto físico
                </a>
            </h1>
            <p class="desc mt-10 text-muted"> Preencha os dados sobre seu produto atentamente. </p>
        </div>
        <div class="page-content container pb-20">
            <form id='my-form'>
                @method('PUT')
                <div class="panel pt-10 pb-20" style="border-radius: 16px">
                    <h4 class="px-40">1. Informações básicas</h4>
                    <hr class="my-20">
                    <div class="px-40 row justify-content-between align-items-baseline">
                        <div class="form-group col-12 col-md-4">
                            <div class="d-flex flex-column" id="div_img" style="position: relative">
                                <div class="d-flex flex-column" id="div_digital_product_upload">
                                    <label for="product_photo">Imagem do produto</label>
                                    <input type="file" id="product_photo" name="product_photo" data-height="651" data-max-width="651" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                                    <small class="text-center text-muted mt-15">Sugerimos PNG ou JPG com 650px x 650px (1:1).</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">Nome do produto</label>
                                    <input name="name" type="text" class="input-pad" id="name" placeholder="Digite o nome">
                                </div>
                                <div class="form-group col-12">
                                    <label for="description">Descrição</label>
                                    <textarea style="height: 140px;" name="description" type="text" class="input-pad" id="description" placeholder="Descrição apresentada no checkout"></textarea>
                                </div>
                                <div class="form-group col-12" id="sku" style="display: none !important;">
                                    <label>SKU</label>
                                    <input type="text" class="input-pad gray mb-2" readonly/>
                                    <p> Editável somente no Shopify. </p>
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
                                    <input name="height" type="text" class="form-control" id="height" placeholder="Ex: 150" data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">CM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 pl-0">
                                <label for="width">Largura</label>
                                <div class="form-group input-group">
                                    <input name="width" type="text" class="form-control" id="width" placeholder="Ex: 135" data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">CM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 pl-0">
                                <label for="length">Comprimento</label>
                                <div class="form-group input-group">
                                    <input name="length" type="text" class="form-control" id="length" placeholder="Ex: 150" data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">CM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 pl-0">
                                <label for="weight">Peso</label>
                                <div class="form-group input-group">
                                    <input name="weight" type="text" class="form-control" id="weight" placeholder="Ex: 950" data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold px-20">G</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-12 mt-0">
                                <small class="text-muted">
                                    Clique <a href="http://www2.correios.com.br/sistemas/precosprazos/Formato.cfm" target="_blank" style="color: #2E85EC; font-weight: bold;">aqui</a> para consultar as regras de dimensões dos Correios.
                                </small>
                                <br>
                                <small class="text-muted">
                                    Informações utilizadas para calcular o valor do frete PAC e SEDEX, se não utilizar esses fretes ignore essas informações
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-buttons">
                    <div>
                        <a class="btn btn-lg btn-excluir delete-product" data-toggle="modal" data-target="#modal-delete" href="#"><span class="o-bin-1 mr-2"></span>Excluir produto</a>
                    </div>

                    <div>
                        <a class="btn btn-lg btn-converter converte-product" data-toggle="modal" data-target="#modal-converte" href="#">
                            <svg width="17" class="svg-converter" height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.25 0C1.00736 0 0 1.00736 0 2.25V4.75C0 5.99264 1.00736 7 2.25 7H11.75C12.9926 7 14 5.99264 14 4.75V2.25C14 1.00736 12.9926 0 11.75 0H2.25ZM1.5 2.25C1.5 1.83579 1.83579 1.5 2.25 1.5H11.75C12.1642 1.5 12.5 1.83579 12.5 2.25V4.75C12.5 5.16421 12.1642 5.5 11.75 5.5H2.25C1.83579 5.5 1.5 5.16421 1.5 4.75V2.25ZM4.7 13C4.3134 13 4 13.3358 4 13.75C4 14.1642 4.3134 14.5 4.7 14.5H9.3C9.6866 14.5 10 14.1642 10 13.75C10 13.3358 9.6866 13 9.3 13H4.7ZM13.3529 13.4453L13.2803 13.5294C13.0141 13.7957 12.5974 13.8199 12.3038 13.602L12.2197 13.5294L10.2197 11.5294C9.9534 11.2632 9.9292 10.8465 10.1471 10.5529L10.2197 10.4688L12.2197 8.46877C12.5126 8.17587 12.9874 8.17587 13.2803 8.46877C13.5466 8.73503 13.5708 9.1517 13.3529 9.44531L13.2803 9.52943L12.5612 10.2503L14.0607 10.2498C14.7079 10.2498 15.2402 9.75788 15.3042 9.12756L15.3107 8.99976V6.74976C15.3107 6.33554 15.6464 5.99976 16.0607 5.99976C16.4404 5.99976 16.7542 6.28191 16.8038 6.64799L16.8107 6.74976V8.99976C16.8107 10.4623 15.669 11.6582 14.2282 11.7447L14.0607 11.7498L12.5622 11.7503L13.2803 12.4688C13.5466 12.735 13.5708 13.1517 13.3529 13.4453ZM14 16.25V14.2239L13.9874 14.2365C13.5816 14.6423 13.027 14.8069 12.5 14.7311V16.25C12.5 16.6642 12.1642 17 11.75 17H2.25C1.83579 17 1.5 16.6642 1.5 16.25V11.25C1.5 10.8358 1.83579 10.5 2.25 10.5H9.07252C9.12946 10.3082 9.21994 10.1241 9.34391 9.95701L9.36592 9.92736L9.4866 9.78756L10.2742 9H2.25C1.00736 9 0 10.0074 0 11.25V16.25C0 17.4926 1.00736 18.5 2.25 18.5H11.75C12.9926 18.5 14 17.4926 14 16.25Z" fill="#707070"/></svg>
                            Converter em digital</a>
                    </div>

                    <div>
                        <a class="btn btn-lg btn-cancelar" href="/products">Cancelar</a>
                        <button type="submit" class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px" src="{{ mix('build/global/img/svg/check-all.svg') }}">Salvar</button>
                    </div>
                </div>
            </form>
            <!-- Modal padrão para excluir -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
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
                            <button id="bt_excluir" type="button" class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                                <b class="mr-2">Excluir </b>
                                <span class="o-bin-1"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Converter em digital -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-converte" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered  modal-simple" style="max-width: 450px">
                    <div class="modal-content p-20">
                        <div id="modal_converter_body" class="text-center">
                            <div class="d-flex flex-row-reverse simple-border-bottom pb-15">
                                <h3 style="position: fixed; left: 0; right: 0;"> Você tem certeza? </h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -3px; font-size: 32px">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="justify-content-center my-30">
                                <svg width="91" height="90" viewBox="0 0 91 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.49756" y="0.5" width="89" height="89" rx="44.5" fill="#F4F6FB"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M57.5797 23.9995H26.0123L26.0123 34.8188L57.5797 34.8188L57.5797 23.9995ZM26.0123 20.9995C24.3554 20.9995 23.0123 22.3427 23.0123 23.9995V34.8188C23.0123 36.4757 24.3554 37.8188 26.0123 37.8188H57.5797C59.2366 37.8188 60.5797 36.4757 60.5797 34.8188V23.9995C60.5797 22.3427 59.2366 20.9995 57.5797 20.9995H26.0123ZM67.5 34.4995C68.3284 34.4995 69 35.1711 69 35.9995V45.9995C69 48.4848 66.9852 50.4995 64.5 50.4995H57.85L61.3808 52.7316C62.081 53.1743 62.2898 54.1008 61.8471 54.8011C61.4044 55.5013 60.4779 55.7101 59.7777 55.2674L54.5428 51.9579C52.3698 50.5842 52.3698 47.4149 54.5428 46.0411L59.7777 42.7316C60.4779 42.2889 61.4044 42.4977 61.8471 43.198C62.2898 43.8982 62.081 44.8247 61.3808 45.2674L57.85 47.4995H64.5C65.3284 47.4995 66 46.8279 66 45.9995V35.9995C66 35.1711 66.6715 34.4995 67.5 34.4995ZM25.9951 47.7446C25.9951 46.9162 26.6667 46.2446 27.4951 46.2446H49.2105C50.039 46.2446 50.7105 45.5731 50.7105 44.7446C50.7105 43.9162 50.039 43.2446 49.2105 43.2446H27.4951C25.0098 43.2446 22.9951 45.2593 22.9951 47.7446V64.5002C22.9951 66.9855 25.0098 69.0002 27.4951 69.0002H57.0853C59.5706 69.0002 61.5853 66.9855 61.5853 64.5002V58.0187C61.5853 57.1903 60.9138 56.5187 60.0853 56.5187C59.2569 56.5187 58.5853 57.1903 58.5853 58.0187V64.5002C58.5853 65.3286 57.9138 66.0002 57.0853 66.0002H27.4951C26.6667 66.0002 25.9951 65.3286 25.9951 64.5002V47.7446ZM34.3817 56.1227C34.3817 55.3028 35.0463 54.6382 35.8662 54.6382H48.715C49.5349 54.6382 50.1995 55.3028 50.1995 56.1227C50.1995 56.9426 49.5349 57.6073 48.715 57.6073H35.8662C35.0463 57.6073 34.3817 56.9426 34.3817 56.1227Z" fill="#2E85EC"/>
                                    <rect x="1.49756" y="0.5" width="89" height="89" rx="44.5" stroke="#2E85EC"/>
                                </svg>
                            </div>
                            <p class="title"> Converter produto físico para digital? </p>
                            <div class="d-flex flex-column align-items-center mt-25">
                                <p class="aviso" style="width: 60%">Confirme a conversão do produto e insira o arquivo digital.</p>
                                <p class="aviso" style="width: 60%"> Essa ação não poderá ser desfeita! </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center pt-10" style="column-gap: 16px">
                            <button id="bt_cancelar" type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;; background-color: #F3F3F3; color: #636363; padding: 20px 0">
                                <b>Cancelar</b>
                            </button>
                            <button id="bt_converter" type="button" class="col-4 btn border-0 btn-outline btn-converte-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%; background-color: #3772FF; color: #FCFCFD; padding: 20px 0">
                                <b class="mr-2">Converter </b>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Produto digital -->
            <div id="modal-plan-already-created" class="modal fade" role="dialog" data-backdrop="static">
                <div class="modal-dialog p-2">
                    <!-- Modal content-->
                    <div class="modal-content p-4">
{{--                        <h4 class="modal-title font-size-20 text-center">Encontramos dados que precisam ser atualizados!</h4>--}}
                        <i class="material-icons gradient text-center" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                        <span class="py-1 text-center">
                        Impossível alterar pois já existe um plano cadastrado com esse produto, você precisa excluir o plano para depois alterá-lo para Digital.
                    </span>
                        <div class="modal-body p-2 text-center">
                            <a class='btn btn-close-modal-plan mt-10 btn-success text-white' data-dismiss="modal">
                                <b>Fechar</b></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ mix('build/layouts/products/edit.min.js') }}"></script>
    @endpush

@endsection
