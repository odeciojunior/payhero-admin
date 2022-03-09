@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css?v=' . versionsFile()) }}">
        <link rel="stylesheet" href="{{ mix('modules/products/css/edit.min.css') }}">
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
                                 src="{{ mix('modules/global/img/svg/caixinha.svg') }}"
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
                        <button type="submit" class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px" src="{{ mix('modules/global/img/svg/check-all.svg') }}">Salvar</button>
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
                <div class="modal-dialog  modal-dialog-centered  modal-simple">
                    <div class="modal-content">
                        <div id="modal_converter_body" class="modal-body text-center p-20">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h3> Você tem certeza? </h3>
                            <div class="justify-content-center">
                                <svg width="88" height="88" viewBox="0 0 88 88" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="88" height="88" rx="44" fill="#F3F3F3"/>
                                    <path d="M30.1081 23C27.287 23 25 25.287 25 28.1081V33.7838C25 36.6049 27.287 38.8919 30.1081 38.8919H51.6757C54.4968 38.8919 56.7838 36.6049 56.7838 33.7838V28.1081C56.7838 25.287 54.4968 23 51.6757 23H30.1081ZM28.4054 28.1081C28.4054 27.1677 29.1677 26.4054 30.1081 26.4054H51.6757C52.6161 26.4054 53.3784 27.1677 53.3784 28.1081V33.7838C53.3784 34.7242 52.6161 35.4865 51.6757 35.4865H30.1081C29.1677 35.4865 28.4054 34.7242 28.4054 33.7838V28.1081ZM35.6703 52.5135C34.7926 52.5135 34.0811 53.2758 34.0811 54.2162C34.0811 55.1566 34.7926 55.9189 35.6703 55.9189H46.1135C46.9912 55.9189 47.7027 55.1566 47.7027 54.2162C47.7027 53.2758 46.9912 52.5135 46.1135 52.5135H35.6703ZM55.3148 53.5245L55.1499 53.7155C54.5454 54.32 53.5995 54.3749 52.9329 53.8803L52.742 53.7155L48.2014 49.1749C47.5969 48.5704 47.542 47.6245 48.0366 46.9579L48.2014 46.7669L52.742 42.2264C53.4069 41.5614 54.485 41.5614 55.1499 42.2264C55.7544 42.8309 55.8094 43.7768 55.3148 44.4434L55.1499 44.6344L53.5173 46.2709L56.9215 46.2697C58.3908 46.2697 59.5994 45.153 59.7447 43.722L59.7593 43.4319V38.3238C59.7593 37.3834 60.5217 36.6211 61.462 36.6211C62.3241 36.6211 63.0365 37.2616 63.1492 38.0927L63.1647 38.3238V43.4319C63.1647 46.7522 60.5728 49.4672 57.3018 49.6637L56.9215 49.6751L53.5195 49.6763L55.1499 51.3075C55.7544 51.912 55.8094 52.8579 55.3148 53.5245ZM56.7838 59.8919V55.2921L56.7551 55.3208C55.834 56.2419 54.5748 56.6158 53.3784 56.4436V59.8919C53.3784 60.8323 52.6161 61.5946 51.6757 61.5946H30.1081C29.1677 61.5946 28.4054 60.8323 28.4054 59.8919V48.5405C28.4054 47.6002 29.1677 46.8378 30.1081 46.8378H45.5971C45.7264 46.4023 45.9318 45.9844 46.2132 45.6051L46.2632 45.5378L46.5372 45.2204L48.3251 43.4324H30.1081C27.287 43.4324 25 45.7194 25 48.5405V59.8919C25 62.713 27.287 65 30.1081 65H51.6757C54.4968 65 56.7838 62.713 56.7838 59.8919Z" fill="#3772FF"/>
                                </svg>
                            </div>
                            <p class="title"> Converter produto físico para digital </p>
                            <p class="aviso"> Essa ação não poderá ser desfeita! </p>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-center">
                            <button id="bt_cancelar" type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;; background-color: #F3F3F3; color: #636363; padding: 20px 0">
                                <b>Cancelar</b>
                            </button>
                            <button id="bt_converter" type="button" class="col-4 btn border-0 btn-outline btn-converte-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%; background-color: #3772FF; color: #FCFCFD; padding: 20px 0">
                                <b class="mr-2">Converter </b>
                                <!-- <span class="o-bin-1"></span> -->
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
        <script src="{{ mix('modules/products/js/products.min.js') }}"></script>
        <script src="{{asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js?v=' . versionsFile()) }}"></script>
        <script src="{{asset('modules/global/adminremark/global/js/Plugin/dropify.js?v=' . versionsFile()) }}"></script>
    @endpush

@endsection
