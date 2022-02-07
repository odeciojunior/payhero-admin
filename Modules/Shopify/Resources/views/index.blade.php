@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/shopify/css/index.css?') }}">
    <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css?v=11') }}">

    <style>
        .gray:hover {
            color: #a1a1a1 !important;
        }

    </style>
@endpush

@section('content')

    <!-- Page -->
    <div class="page">
        <div style="display: none !important;" class="page-header container">
            <div class="row jusitfy-content-between" style="min-height:56px">
                <div class="col-lg-8  align-items-center">
                    <h1 class="page-title my-10" style="min-height: 28px">
                        <a class="gray" href="/apps">
                            <span class="o-arrow-right-1 font-size-30 ml-2 gray" aria-hidden="true"></span>
                            Integrações com Shopify
                        </a>
                    </h1>
                </div>
                <div class="col text-right" id="integration-actions" style="display:none">
                    <a data-toggle="modal" id='btn-integration-model' class="btn btn-floating btn-primary ml-10"
                        style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-add-1" aria-hidden="true"></i>
                    </a>
                    <a data-toggle="modal" id="button-information" data-target='#modal_explicacao' class="btn btn-floating"
                        style="background-color: #2E85EC;position: relative;float: right;color: white; display:none;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-question-1 white font-size-30" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="page-content container">
            <div class="row" id="content">
                {{-- js load dynamically --}}
            </div>

            {{-- Modal add-edit integration --}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_integracao" aria-hidden="true"
                aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg d-flex justify-content-center">
                    <div class="modal-content w-450" id="conteudo_modal_add">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="font-weight: 700;"></h4>
                        </div>
                        <div class="pt-10 pr-20 pl-20 modal_integracao_body">
                            @include('shopify::create')
                        </div>
                        <div class="modal-footer" style="margin-top: 15px">
                            <button id="bt_integration" type="button" class="btn btn-success" data-dismiss="modal"></button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Modal --}}

            <!-- Modal Explicação -->
            <div class="modal fade modal-3d-flip-vertical" id="modal_explicacao" aria-hidden="true"
                aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" id="conteudo_modal_explicacao">
                        <div class="panel-group panel-group-continuous m-0" id="acordionHelp" aria-multiselectable="true"
                            role="tablist">
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingFirst" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                        href="#exampleCollapseFirst" aria-controls="exampleCollapseFirst"
                                        aria-expanded="false">
                                        <strong>Primeiro passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseFirst"
                                    aria-labelledby="exampleHeadingFirst" role="tabpanel" style=""
                                    data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span> Crie uma loja no shopify: <a
                                                    onclick='openInNewWindow("https://www.shopify.com/")'
                                                    href='#'>https://www.shopify.com/</a><br>
                                                Caso já tenha sua loja, apenas efetue o <strong>login</strong>.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingSecond" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                        href="#exampleCollapseSecond" aria-controls="exampleCollapseSecond"
                                        aria-expanded="false">
                                        <strong>Segundo passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseSecond"
                                    aria-labelledby="exampleHeadingSecond" role="tabpanel" style=""
                                    data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Após ter se autenticado no shopify, clique em "Apps" <strong
                                                    class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                    src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/shoify-integration-step-1.png'
                                                    style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingThird" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                        href="#exampleCollapseThird" aria-controls="exampleCollapseThird"
                                        aria-expanded="false">
                                        <strong>Terceiro passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseThird"
                                    aria-labelledby="exampleHeadingThird" role="tabpanel" style=""
                                    data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Ao carregar a página, identifique e clique no link "Manage private apps"
                                                <strong class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                    src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/shoify-integration-step-2.png'
                                                    style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingFourth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                        href="#exampleCollapseFourth" aria-controls="exampleCollapseFourth"
                                        aria-expanded="false">
                                        <strong>Quarto passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseFourth"
                                    aria-labelledby="exampleHeadingFourth" role="tabpanel" style=""
                                    data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Aguarde a nova página abrir, e clique no botão "Create a new private app"
                                                <strong class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                    src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/shoify-integration-step-3.png'
                                                    style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingFifth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                        href="#exampleCollapseFifth" aria-controls="exampleCollapseFifth"
                                        aria-expanded="false">
                                        <strong>Quinto passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseFifth"
                                    aria-labelledby="exampleHeadingFifth" role="tabpanel" style=""
                                    data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Na nova página você deverá preencher alguns dados.
                                                <br> <strong>"Private app name"</strong> é o nome do novo aplicativo, para
                                                não confundir, sugerimos que ultilize "cloudfox".
                                                <br> <strong>"Emergency developer email"</strong> é o email para
                                                emergências, preencha-o corretamente.
                                                <img class='img-thumbnail thumbnail'
                                                    src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/shoify-integration-step-4-1.png'
                                                    style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingSixth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                        href="#exampleCollapseSixth" aria-controls="exampleCollapseSixth"
                                        aria-expanded="false">
                                        <strong>Sexto passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseSixth"
                                    aria-labelledby="exampleHeadingSixth" role="tabpanel" style=""
                                    data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Depois de inserir corretamente os dados acima, precisamos que você nos
                                                libere algumas permissões.
                                                <br><strong>Fique bem atento, pois, as permissões listadas a seguir podem
                                                    não estar em ordem, e se não forem liberadas, a integração não
                                                    funcionará corretamente.</strong>
                                                <ul>
                                                    <li>Orders, transactions and fulfillments -> Read and write access</li>
                                                    <li>Products, variants and collections -> Read access</li>
                                                    <li>Theme templates and theme assets -> Read and write access</li>
                                                    <li>Product information -> Read access</li>
                                                    <li>Order editing -> Read and write access</li>
                                                    <li>Inventory -> Read access</li>
                                                </ul>
                                                <img class='img-thumbnail thumbnail'
                                                    src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/shoify-integration-step-4-2.png'
                                                    style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingSeventh" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                        href="#exampleCollapseSeventh" aria-controls="exampleCollapseSeventh"
                                        aria-expanded="false">
                                        <strong>Sétimo passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseSeventh"
                                    aria-labelledby="exampleHeadingSeventh" role="tabpanel" style=""
                                    data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Confira os dados e clique em "save", o botão podera ser encontrado no
                                                final da página.
                                                <br> Uma janela de confirmação aparecerá para você<strong
                                                    class='grad'>(selecione o botão como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail'
                                                    src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/shoify-integration-step-6.png'
                                                    style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingEigth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                        href="#exampleCollapseEigth" aria-controls="exampleCollapseEigth"
                                        aria-expanded="false">
                                        <strong>Oitavo passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseEigth"
                                    aria-labelledby="exampleHeadingEigth" role="tabpanel" style=""
                                    data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>Agora você tem um novo app criado, para vincular com a nossa plataforma,
                                                clique no botão</span>
                                            <a class="btn btn-floating btn-primary"
                                                style="margin:15px;color: white;display: flex;align-items: center;justify-content: center;">
                                                <i class="o-add-1" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingNineth" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                        href="#exampleCollapseNineth" aria-controls="exampleCollapseNineth"
                                        aria-expanded="false">
                                        <strong>Nono passo</strong>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse" id="exampleCollapseNineth"
                                    aria-labelledby="exampleHeadingNineth" role="tabpanel" style=""
                                    data-parent="#acordionHelp">
                                    <div class="panel-body justify-content-center">
                                        <div class="d-flex align-items-center">
                                            <span>O campo "Token (password)" deve ser preenchido com o password do seu
                                                app<strong class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                    src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/shoify-integration-step-7-1.png'
                                                    style='width:100%'>
                                                <br>O campo "URL da sua loja no Shopify" sera a URL da sua loja. (sem o
                                                "https://" nem mesmo o que vier após "myshopify.com")<strong
                                                    class='grad'>(como indica imagem abaixo)</strong>
                                                <img class='img-thumbnail thumbnail'
                                                    src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/shopify-url.png'
                                                    style='width:100%'>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->


            <!-- Modal Explicação -->
            <style>
                .bt-action {
                    cursor: pointer;
                    background-image: url('/modules/apps/imgs/bt-action.svg');
                    padding-left: 46px;
                    background-repeat: no-repeat;
                    background-position: left;
                    height: 38px;
                }

                .bt-action:hover,
                .bt-action:focus {
                    background-image: url('/modules/apps/imgs/bt-action-hover.svg');
                    border: none !important;


                }
                .bt-action:active{
                    border: none !important;
                }

                .form-group label {
                    color: #636363;
                    font-size: 16px;
                    font-weight: 400;
                }

                .form-group input {
                    border-radius: 8px;
                    font-size: 16px;
                    padding: 12px;
                    border: 1px solid #C4C4C4;
                }

            </style>
            <div class="modal fade modal-slide-bottom" id="modal_edit" aria-hidden="true"
                aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" style="width: 517px; min-height: 453px">
                    <div class="modal-content" id="" style="border-radius: 12px">

                        <div style="width: 517px; height:70px; font-size: 22px; font-weight: bold; color:#636363"
                            class="" id="">
                            <div style="width: 72px; height:70px; float: left; border-right: 1px solid #F4F4F4"
                                class="" id="">

                                <svg style="position: absolute; top:24px; left:22px" width="26" height="29"
                                    viewBox="0 0 26 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M16.8933 28.4755L25.312 26.6543C25.312 26.6543 22.274 6.10583 22.2495 5.96933C22.2285 5.834 22.1165 5.74533 22.0033 5.74533C21.8901 5.74533 19.7528 5.58667 19.7528 5.58667C19.7528 5.58667 18.2653 4.10033 18.074 3.9405C18.0215 3.89733 17.9865 3.874 17.9328 3.85417L16.8665 28.4755H16.8933ZM12.6618 13.6892C12.6618 13.6892 11.7168 13.1945 10.5921 13.1945C8.90395 13.1945 8.83745 14.2515 8.83745 14.5257C8.83745 15.963 12.6175 16.5265 12.6175 19.9262C12.6175 22.6037 10.9375 24.3128 8.64379 24.3128C5.89745 24.3128 4.51379 22.6037 4.51379 22.6037L5.26745 20.17C5.26745 20.17 6.71995 21.4137 7.92745 21.4137C8.71495 21.4137 9.06495 20.7778 9.06495 20.3263C9.06495 18.4375 5.96862 18.35 5.96862 15.2408C5.92895 12.631 7.80145 10.0888 11.6001 10.0888C13.0666 10.0888 13.7876 10.51 13.7876 10.51L12.6851 13.6775L12.6618 13.6892ZM12.0318 1.46833C12.1905 1.46833 12.348 1.51267 12.5043 1.62583C11.3563 2.16833 10.0963 3.538 9.57829 6.28317C8.81295 6.53167 8.06979 6.75567 7.37445 6.9575C7.97995 4.875 9.44295 1.48 12.0318 1.48V1.46833ZM13.4726 4.90883V5.06633C12.593 5.337 11.6258 5.631 10.6796 5.925C11.2233 3.85183 12.2348 2.83917 13.1121 2.45883C13.3373 3.04333 13.4726 3.83083 13.4726 4.90883ZM14.1015 2.3025C14.9111 2.38883 15.4326 3.314 15.7686 4.35C15.3615 4.483 14.9111 4.6195 14.4176 4.777V4.483C14.4176 3.60567 14.3056 2.8835 14.1015 2.30017V2.3025ZM17.5921 3.80633C17.5688 3.80633 17.5221 3.83083 17.5011 3.83083C17.4801 3.83083 17.164 3.91833 16.6681 4.07583C16.1746 2.63733 15.2961 1.31083 13.7421 1.31083H13.608C13.1576 0.743833 12.614 0.5 12.1426 0.5C8.51895 0.5 6.78762 5.02317 6.24512 7.32033C4.85212 7.74617 3.83829 8.06233 3.72512 8.10667C2.93762 8.35517 2.91545 8.37733 2.82445 9.12167C2.73695 9.66067 0.689453 25.5285 0.689453 25.5285L16.5106 28.5L17.5921 3.80633Z"
                                        fill="#424245" />
                                </svg>

                            </div>
                            <span style="line-height:72px; margin-left: 25px ">

                                Gerenciar integração
                            </span>

                            <svg data-dismiss="modal" aria-label="Close" style="position: absolute; top:27px; right:24px"
                                class="close" width="16" height="17" viewBox="0 0 16 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 1.75L1 15.75M1 1.75L15 15.75L1 1.75Z" stroke="#636363" stroke-width="2"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                        </div>

                        <div style="padding: 25px 25px 15px 25px; border-top: 1px solid #F4F4F4" class=""
                            id="">
                            <img style="height: 51px; width:51px; border-radius: 8px" id="project-img"
                                class="" src="" alt="" />
                            <span id="project-name"
                                style="font-size: 16px; color:#636363; font-weight: bold; margin-left: 17px">

                            </span>

                            <div style="font-size: 16px; color:#636363; font-weight: bold; padding-top:22px">
                                Sincronizar
                            </div>
                            <small>Escolha a opção que deseja sincronizar</small>

                            <div style="padding-top: 18px;">

                                <div id="bts-holder" class="d-flex justify-content-between">

                                    {{-- <div class="col-sm"> --}}
                                        <div tabindex="1" class="bt-action sync-products">
                                            <span
                                                style="line-height:36px; font-size: 16px; font-weight: bold; color:#2E85EC">
                                                Produtos
                                            </span>
                                        </div>
                                    {{-- </div> --}}
                                    {{-- <div class="col-sm"> --}}

                                        <div tabindex="2" class="bt-action sync-template">
                                            <span
                                                style="line-height:36px; font-size: 16px; font-weight: bold; color:#2E85EC">
                                                Template
                                            </span>
                                        </div>
                                    {{-- </div> --}}
                                    {{-- <div class="col-sm"> --}}

                                        <div tabindex="3" class="bt-action sync-tracking">
                                            <span
                                                style="line-height:36px; font-size: 16px; font-weight: bold; color:#2E85EC">
                                                Rastreios
                                            </span>
                                        </div>
                                    {{-- </div> --}}
                                </div>

                                <div id="bts-confirm" class="row" style="display: none; margin-top: 16px ">
                                    <div class="col-sm ">

                                        <div style="background-color: #F2F8FF; border: 1px solid #2E85EC;
                                                        box-sizing: border-box; font-size: 14px;
                                                        border-radius: 4px; padding: 16px; color:#2E85EC">

                                            <div style="font-weight: bold;
                                                        
                                                        line-height: 18px;
                                                        color: #2E85EC;">A sincronização pode demorar algumas horas.</div>

                                            

                                            <button id="bt-confirm_old" data-dismiss=""
                                                style="padding: 9px 32px; font-weight: bold" aria-label=""
                                                class="btn btn-primary" style="">Sim</button>
                                            <button id="bt-cancel" data-dismiss=""
                                                style="margin-left:6px; padding: 9px 32px; color:#636363; background-color: #FFFFFF; border:none; font-weight: bold"
                                                class="btn btn-secondary" style="">Não</button>

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div style="padding-top: 28px;">

                                <div id="" style="font-weight: bold; s">

                                    Token da integração

                                </div>
                                <div id="" style="padding-top: 10px ;">

                                    <div class="input-group mb-3">
                                        <input id="project-token"
                                            style="padding: 12px 16px; border-top-left-radius:8px; border-bottom-left-radius: 8px; "
                                            disabled type="text" class="input-pad form-control" placeholder="" aria-label=""
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button id="token-change"
                                                style="background: #636363; color:white; font-weight: bold"
                                                class="btn btn-outline-secondary" type="button">Alterar</button>
                                        </div>
                                    </div>

                                    <div style="padding:4px 0">

                                        <svg style="margin-right: 2px" width="16" height="17" viewBox="0 0 16 17"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M16 8.5C16 4.08172 12.4183 0.5 8 0.5C3.58172 0.5 0 4.08172 0 8.5C0 12.9183 3.58172 16.5 8 16.5C12.4183 16.5 16 12.9183 16 8.5ZM7.50806 7.41012C7.55039 7.17687 7.75454 6.99999 8 6.99999C8.24546 6.99999 8.44961 7.17687 8.49194 7.41012L8.5 7.49999V12.0021L8.49194 12.092C8.44961 12.3253 8.24546 12.5021 8 12.5021C7.75454 12.5021 7.55039 12.3253 7.50806 12.092L7.5 12.0021V7.49999L7.50806 7.41012ZM7.25 5.24999C7.25 4.83578 7.58579 4.49999 8 4.49999C8.41421 4.49999 8.75 4.83578 8.75 5.24999C8.75 5.66421 8.41421 5.99999 8 5.99999C7.58579 5.99999 7.25 5.66421 7.25 5.24999Z"
                                                fill="#70707E" />
                                        </svg>

                                        <small style="padding-top: 2px ">O Token é a senha (password) da integração com
                                            Shopify</small>
                                    </div>

                                </div>

                                <div id="" style="padding-top: 20px ;">
                                    <span style="font-size: 16px;
                                           line-height: 20px;
                                    
                                           font-weight: bold;

                                           color: #636363;">
                                        Skip to cart
                                    </span>


                                    <label class="switch" style="float: right; margin: -4px 0 0">
                                        <input id="skiptocart-input" type="checkbox" value="0" class="check">
                                        <span class="slider round"></span>
                                    </label>

                                    <div style="margin: 8px 0 8px 0; font-size: 14px;
                                        line-height: 18px;
                                        
                                        color: #9C9C9C;">
                                        <small>
                                            Ao ativar, o clique em “Comprar” na loja encaminha diretamente para o checkout,
                                            sem passar pelo carrinho como padrão.
                                        </small>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div id="footer" style="border-top: 1px solid #F4F4F4; padding:20px; text-align:center">
                            <button id="bt-close" data-dismiss="modal" aria-label="Close" class="btn btn-primary"
                                style="padding: 12px 32px; font-size: 16px; font-weight: 600">Fechar</button>
                            <button id='bt-update-keys' class="btn btn-primary"
                                style="display: none; padding: 12px 32px; font-size: 16px; font-weight: 600">Atualizar e
                                fechar</button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- End Modal -->


            {{-- Modal confirm --}}
            <div class="modal fade modal-slide-bottom" id="modal-confirm" aria-hidden="true"
                aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" style="width: 517px; min-height: 453px">
                    <div class="modal-content" id="" style="border-radius: 12px">

                        <div style="width: 517px; height:70px; font-size: 22px; font-weight: bold; color:#636363"
                            class="" id="">
                            <div style="width: 72px; height:70px; float: left; border-right: 1px solid #F4F4F4"
                                class="" id="">

                                <svg style="position: absolute; top:24px; left:22px" width="26" height="29"
                                    viewBox="0 0 26 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M16.8933 28.4755L25.312 26.6543C25.312 26.6543 22.274 6.10583 22.2495 5.96933C22.2285 5.834 22.1165 5.74533 22.0033 5.74533C21.8901 5.74533 19.7528 5.58667 19.7528 5.58667C19.7528 5.58667 18.2653 4.10033 18.074 3.9405C18.0215 3.89733 17.9865 3.874 17.9328 3.85417L16.8665 28.4755H16.8933ZM12.6618 13.6892C12.6618 13.6892 11.7168 13.1945 10.5921 13.1945C8.90395 13.1945 8.83745 14.2515 8.83745 14.5257C8.83745 15.963 12.6175 16.5265 12.6175 19.9262C12.6175 22.6037 10.9375 24.3128 8.64379 24.3128C5.89745 24.3128 4.51379 22.6037 4.51379 22.6037L5.26745 20.17C5.26745 20.17 6.71995 21.4137 7.92745 21.4137C8.71495 21.4137 9.06495 20.7778 9.06495 20.3263C9.06495 18.4375 5.96862 18.35 5.96862 15.2408C5.92895 12.631 7.80145 10.0888 11.6001 10.0888C13.0666 10.0888 13.7876 10.51 13.7876 10.51L12.6851 13.6775L12.6618 13.6892ZM12.0318 1.46833C12.1905 1.46833 12.348 1.51267 12.5043 1.62583C11.3563 2.16833 10.0963 3.538 9.57829 6.28317C8.81295 6.53167 8.06979 6.75567 7.37445 6.9575C7.97995 4.875 9.44295 1.48 12.0318 1.48V1.46833ZM13.4726 4.90883V5.06633C12.593 5.337 11.6258 5.631 10.6796 5.925C11.2233 3.85183 12.2348 2.83917 13.1121 2.45883C13.3373 3.04333 13.4726 3.83083 13.4726 4.90883ZM14.1015 2.3025C14.9111 2.38883 15.4326 3.314 15.7686 4.35C15.3615 4.483 14.9111 4.6195 14.4176 4.777V4.483C14.4176 3.60567 14.3056 2.8835 14.1015 2.30017V2.3025ZM17.5921 3.80633C17.5688 3.80633 17.5221 3.83083 17.5011 3.83083C17.4801 3.83083 17.164 3.91833 16.6681 4.07583C16.1746 2.63733 15.2961 1.31083 13.7421 1.31083H13.608C13.1576 0.743833 12.614 0.5 12.1426 0.5C8.51895 0.5 6.78762 5.02317 6.24512 7.32033C4.85212 7.74617 3.83829 8.06233 3.72512 8.10667C2.93762 8.35517 2.91545 8.37733 2.82445 9.12167C2.73695 9.66067 0.689453 25.5285 0.689453 25.5285L16.5106 28.5L17.5921 3.80633Z"
                                        fill="#424245" />
                                </svg>

                            </div>
                            <span style="line-height:72px; margin-left: 25px ">

                                Confirme a sincronização
                            </span>

                            <svg data-dismiss="modal" aria-label="Close" style="position: absolute; top:27px; right:24px"
                                class="close" width="16" height="17" viewBox="0 0 16 17" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 1.75L1 15.75M1 1.75L15 15.75L1 1.75Z" stroke="#636363" stroke-width="2"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                        </div>

                        <div style="padding: 25px 25px 15px 25px; border-top: 1px solid #F4F4F4" class=""
                            id="">
                            
                            <div class="text-center">
                                <svg width="93" height="93" viewBox="0 0 93 93" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="46.5" cy="46.5" r="46.5" fill="#F2F8FF"/>
                                    <path d="M57.6587 28.6022C56.9697 29.4907 57.1435 30.7603 58.0469 31.4379C62.952 35.1171 65.8857 40.8175 65.8857 47C65.8857 57.0785 58.1348 65.3764 48.1759 66.4424L50.0311 64.6167C50.8345 63.8266 50.8345 62.5455 50.0311 61.7554C49.3008 61.0371 48.1579 60.9718 47.3526 61.5595L47.1219 61.7554L41.6361 67.1507C40.9058 67.869 40.8394 68.9931 41.437 69.7851L41.6361 70.0121L47.1219 75.4074C47.9252 76.1975 49.2277 76.1975 50.0311 75.4074C50.7614 74.6891 50.8278 73.5651 50.2303 72.773L50.0311 72.5461L47.9733 70.526C60.3051 69.539 70 59.383 70 47C70 39.5416 66.4568 32.657 60.5419 28.2203C59.6385 27.5427 58.3476 27.7136 57.6587 28.6022ZM41.9689 18.5926C41.1655 19.3827 41.1655 20.6638 41.9689 21.4539L44.024 23.4742C31.6935 24.4625 22 34.6179 22 47C22 54.1339 25.24 60.7512 30.7245 65.2069C31.6004 65.9185 32.8971 65.797 33.6206 64.9355C34.3442 64.074 34.2207 62.7987 33.3447 62.0871C28.7966 58.3921 26.1143 52.9138 26.1143 47C26.1143 36.9225 33.8637 28.6251 43.8213 27.5579L41.9689 29.3833C41.1655 30.1734 41.1655 31.4545 41.9689 32.2446C42.7723 33.0347 44.0748 33.0347 44.8781 32.2446L50.3639 26.8493C51.1672 26.0591 51.1672 24.7781 50.3639 23.9879L44.8781 18.5926C44.0748 17.8025 42.7723 17.8025 41.9689 18.5926Z" fill="#2E85EC"/>
                                    </svg>
                                    

                            </div>

                            
                            <div class="text-center p-20" style=" margin-bottom: 16px; font-weight: bold;
                            font-size: 18px;
                            line-height: 150%; ">
                                
                                Você tem certeza que quer sincronizar <span style="font-weight: bold"
                                id="sync-name"></span>?

                                <div style="height: 8px"></div>

                                <span style="font-weight: normal;
                                font-size: 14px;
                                line-height: 150%;" id="sync-desc"></span>

                            </div>





                        </div>

                        <div id="footer" style="border-top: 1px solid #F4F4F4; padding:20px; text-align:center">
                            <button id="bt-close-confirm" data-dismiss="modal" aria-label="Close" class="btn btn-primary"
                                style="padding: 12px 32px; font-size: 16px; font-weight: 600; color:#636363; background-color: #FFFFFF; border:none; font-weight: bold">Voltar</button>
                            <button id='bt-confirm' class="btn btn-primary"
                                style=" padding: 12px 32px; font-size: 16px; font-weight: 600">Sincronizar</button>
                        </div>

                    </div>
                </div>
            </div>
            {{-- end modal --}}

        </div>
        @include('companies::empty')
        @include('companies::not_company_approved_getnet')
        @include('shopify::not-integration')
    </div>

    @push('scripts')
        <script src="{{ asset('modules/shopify/js/index.js?v=' . uniqid()) }}"></script>
    @endpush

@endsection
