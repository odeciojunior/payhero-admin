@extends('layouts.master')
@push('css')
    <link rel="stylesheet"
          href="{{ mix('build/layouts/wooCommerce/index.min.css') }}">
    <style>
        .o-arrow-right-1 {
            font-size: 30px;
        }

        .o-arrow-right-1::before {
            transform: rotate(180deg);
        }

        .gray:hover {
            color: #a1a1a1 !important;
        }

        /* Page titles */
        .page-title>a,
        .page-title>a>span {
            color: #707070
        }
    </style>
@endpush
@section('content')
    <!-- Page -->
    <div class="page">

        @include('layouts.company-select', ['version' => 'mobile'])

        <div class="page-header container">
            <div class="row jusitfy-content-between"
                 style="min-height:56px">

                <div class="col-lg-8">
                    <h1 class="page-title my-10"
                        style="min-height: 28px">
                        <a class="gray"
                           href="/apps">
                            <span class="o-arrow-right-1 font-size-30 ml-2 gray"
                                  aria-hidden="true"></span>
                            Integrações com WooCommerce
                        </a>
                    </h1>
                </div>

                <div class="col text-right"
                     id="integration-actions"
                     style="display:none">
                    <a data-toggle="modal"
                       id='btn-integration-model'
                       class="btn btn-floating btn-primary ml-10"
                       style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-add-1"
                           aria-hidden="true"></i>
                    </a>

                    {{-- <a data-toggle="modal" id="button-information" data-target='#modal_explicacao' class="btn btn-floating"
                       style="background-color: #2E85EC;position: relative;float: right;color: white; display:none;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-question-1 white font-size-30" aria-hidden="true"></i>
                    </a> --}}

                    <div class="w-200 mt-2"
                         style="">
                        <div class="d-flex align-items-center">
                            <span class="o-download-cloud-1 mr-2"></span>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle"
                                        type="button"
                                        id="dropdownMenuButton"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                    Download plugin
                                </button>
                                <div class="dropdown-menu"
                                     aria-labelledby="dropdownMenuButton">

                                    <a class="dropdown-item"
                                       href="https://sirius.cloudfox.net/build/layouts/woocommerce/plugins/plugin_cloudfox.zip">Adiciona
                                        ao carrinho de compras</a>
                                    <a class="dropdown-item"
                                       href="https://sirius.cloudfox.net/build/layouts/woocommerce/plugins/plugin_cloudfox_skip_to_checkout.zip">Envia
                                        direto para o checkout</a>

                                    <a class="dropdown-item"
                                       href="https://sirius.cloudfox.net/build/layouts/woocommerce/plugins/plugin-cloudfox-geteway.zip">
                                        Checkout API</a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="page-content container">
            <div class="row"
                 id="content">
                {{-- js load dynamically --}}
            </div>

            {{-- Modal add-edit integration --}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical"
                 id="modal_add_integracao"
                 aria-hidden="true"
                 aria-labelledby="exampleModalTitle"
                 role="dialog"
                 tabindex="-1">
                <div class="modal-dialog modal-lg d-flex justify-content-center">
                    <div class="modal-content w-450"
                         id="conteudo_modal_add">
                        <div class="modal-header">
                            <button type="button"
                                    class="close"
                                    data-dismiss="modal"
                                    aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title"
                                style="font-weight: 700;"></h4>
                        </div>
                        <div class="pt-10 pr-20 pl-20 modal_integracao_body">
                            @include('woocommerce::create')
                        </div>
                        <div class="modal-footer"
                             style="margin-top: 15px">
                            <button id="bt_integration"
                                    type="button"
                                    class="btn btn-success"
                                    data-dismiss="modal"></button>
                            <button type="button"
                                    class="btn btn-primary"
                                    data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Modal --}}

            <!-- Modal Explicação -->
            <style>
                .bt-action {
                    cursor: pointer;
                    background-image: url('/build/layouts/apps/imgs/bt-action.svg');
                    padding-left: 46px;
                    background-repeat: no-repeat;
                    background-position: left;
                    height: 38px;
                }

                .bt-action:hover,
                .bt-action:focus {
                    background-image: url('/build/layouts/apps/imgs/bt-action-hover.svg');
                    border: none;
                }

                .bt-action:active {
                    border: none;
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
            <div class="modal fade modal-slide-bottom"
                 id="modal_edit"
                 aria-hidden="true"
                 aria-labelledby="exampleModalTitle"
                 role="dialog"
                 tabindex="-1">
                <div class="modal-dialog modal-dialog-centered"
                     style="width: 517px; min-height: 453px">
                    <div class="modal-content"
                         id=""
                         style="border-radius: 12px">

                        <div style="width: 517px; height:70px; font-size: 22px; font-weight: bold; color:#636363"
                             class=""
                             id="">
                            <div style="width: 72px; height:70px; float: left; border-right: 1px solid #F4F4F4"
                                 class=""
                                 id="">
                                <svg style="position: absolute; top:24px; left:22px"
                                     width="28"
                                     height="29"
                                     viewBox="0 0 28 29"
                                     fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.1666 3.08331H5.83325C3.84992 3.08331 2.33325 4.59998 2.33325 6.58331V19.4166C2.33325 21.4 3.84992 22.9166 5.83325 22.9166H10.4999L17.4999 26.4166L16.3333 22.9166H22.1666C24.1499 22.9166 25.6666 21.4 25.6666 19.4166V6.58331C25.6666 4.59998 24.1499 3.08331 22.1666 3.08331ZM20.2999 8.33331C19.8333 9.26665 19.3666 10.7833 19.1333 12.8833C18.7833 14.9833 18.6666 16.5 18.7833 17.6666C18.7833 18.0166 18.7833 18.25 18.6666 18.4833C18.5499 18.7166 18.3166 18.95 17.9666 18.95C17.6166 18.95 17.2666 18.8333 16.9166 18.4833C15.7499 17.3166 14.8166 15.45 14.1166 13.1166C13.2999 14.75 12.7166 15.9166 12.2499 16.7333C11.5499 18.1333 10.8499 18.8333 10.3833 18.95C10.0333 18.95 9.79992 18.7166 9.44992 18.1333C8.86659 16.5 8.16659 13.2333 7.46659 8.56665C7.46659 8.21665 7.46659 7.98331 7.69992 7.74998C7.81659 7.51665 8.16659 7.39998 8.51659 7.28331C9.09992 7.28331 9.56659 7.51665 9.56659 8.21665C9.91659 10.9 10.3833 13.1166 10.8499 14.8666L13.6499 9.61665C13.8833 9.14998 14.1166 8.91665 14.5833 8.91665C15.1666 8.91665 15.5166 9.26665 15.6333 9.96665C15.9833 11.6 16.3333 13 16.7999 14.2833C17.1499 11.1333 17.7333 8.79998 18.4333 7.39998C18.6666 7.04998 18.8999 6.81665 19.2499 6.81665C19.4833 6.81665 19.8333 6.93331 20.0666 7.04998C20.2999 7.28331 20.4166 7.51665 20.4166 7.74998C20.4166 7.98331 20.4166 8.21665 20.2999 8.33331Z"
                                          fill="black" />
                                </svg>
                            </div>
                            <span style="line-height:72px; margin-left: 25px ">

                                Gerenciar integração
                            </span>

                            <svg data-dismiss="modal"
                                 aria-label="Close"
                                 style="position: absolute; top:27px; right:24px"
                                 class="close"
                                 width="16"
                                 height="17"
                                 viewBox="0 0 16 17"
                                 fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 1.75L1 15.75M1 1.75L15 15.75L1 1.75Z"
                                      stroke="#636363"
                                      stroke-width="2"
                                      stroke-miterlimit="10"
                                      stroke-linecap="round"
                                      stroke-linejoin="round" />
                            </svg>

                        </div>

                        <div style="padding: 25px 25px 15px 25px; border-top: 1px solid #F4F4F4"
                             class=""
                             id="">
                            <img style="height: 51px; width:51px; border-radius: 8px"
                                 id="project-img"
                                 class=""
                                 src=""
                                 alt="" />
                            <span id="project-name"
                                  style="font-size: 16px; color:#636363; font-weight: bold; margin-left: 17px">

                            </span>

                            <div style="font-size: 16px; color:#636363; font-weight: bold; padding-top:22px">
                                Sincronizar
                            </div>
                            <small>Escolha a opção que deseja sincronizar</small>

                            <div style="padding-top: 18px;">

                                <div id="bts-holder"
                                     class="d-flex justify-content-between">

                                    {{-- <div class="col-sm"> --}}
                                    <div tabindex="1"
                                         class="bt-action sync-products">
                                        <span style="line-height:36px; font-size: 16px; font-weight: bold; color:#2E85EC">
                                            Produtos
                                        </span>
                                    </div>
                                    {{-- </div> --}}
                                    {{-- <div class="col-sm"> --}}

                                    <div tabindex="2"
                                         class="bt-action sync-tracking">
                                        <span style="line-height:36px; font-size: 16px; font-weight: bold; color:#2E85EC">
                                            Rastreios
                                        </span>
                                    </div>
                                    {{-- </div> --}}
                                    {{-- <div class="col-sm"> --}}

                                    <div tabindex="3"
                                         class="bt-action sync-webhooks">
                                        <span style="line-height:36px; font-size: 16px; font-weight: bold; color:#2E85EC">
                                            Webhooks
                                        </span>
                                    </div>
                                    {{-- </div> --}}
                                </div>

                                <div id="bts-confirm"
                                     class="row"
                                     style="display: none; margin-top: 16px ">
                                    <div class="col-sm ">

                                        <div
                                             style="background-color: #F2F8FF; border: 1px solid #2E85EC;
                                                box-sizing: border-box; font-size: 14px;
                                                border-radius: 4px; padding: 16px; color:#2E85EC">

                                            <div
                                                 style="font-weight: bold;

                                                line-height: 18px;
                                                color: #2E85EC;">
                                                A sincronização pode demorar algumas horas.</div>

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div style="padding-top: 28px;">

                                <div id="open-keys"
                                     style="font-weight: bold; cursor: pointer;">

                                    <svg style="margin-right: 10px"
                                         width="36"
                                         height="37"
                                         viewBox="0 0 36 37"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <rect y="0.75"
                                              width="36"
                                              height="36"
                                              rx="8"
                                              fill="#FAFAFA" />
                                        <path d="M23 14.75C23 15.3023 22.5523 15.75 22 15.75C21.4477 15.75 21 15.3023 21 14.75C21 14.1977 21.4477 13.75 22 13.75C22.5523 13.75 23 14.1977 23 14.75ZM20.5 10.75C17.4239 10.75 15 13.1739 15 16.25C15 16.6467 15.04 17.046 15.1215 17.4246C15.1797 17.6945 15.1138 17.9291 14.9802 18.0627L10.4393 22.6036C10.158 22.8849 10 23.2664 10 23.6642V25.25C10 26.0784 10.6716 26.75 11.5 26.75H13.5C14.3284 26.75 15 26.0784 15 25.25V24.75H16C16.5523 24.75 17 24.3023 17 23.75V22.75H18C18.5523 22.75 19 22.3023 19 21.75V21.5699C19.4935 21.7036 20.0069 21.75 20.5 21.75C23.5761 21.75 26 19.3261 26 16.25C26 13.1739 23.5761 10.75 20.5 10.75ZM16 16.25C16 13.7261 17.9761 11.75 20.5 11.75C23.0239 11.75 25 13.7261 25 16.25C25 18.7739 23.0239 20.75 20.5 20.75C19.8408 20.75 19.2274 20.6547 18.7236 20.4028C18.5686 20.3253 18.3845 20.3336 18.2371 20.4247C18.0897 20.5158 18 20.6767 18 20.85V21.75H17C16.4477 21.75 16 22.1977 16 22.75V23.75H15C14.4477 23.75 14 24.1977 14 24.75V25.25C14 25.5261 13.7761 25.75 13.5 25.75H11.5C11.2239 25.75 11 25.5261 11 25.25V23.6642C11 23.5316 11.0527 23.4044 11.1464 23.3107L15.6873 18.7698C16.1194 18.3377 16.2094 17.7262 16.0991 17.214C16.0335 16.9095 16 16.5811 16 16.25Z"
                                              fill="#2E85EC" />
                                    </svg>

                                    Atualizar chaves de acesso

                                    <svg id="arrow-up"
                                         style="float: right; margin-top:14px; display:none"
                                         width="12"
                                         height="8"
                                         viewBox="0 0 12 8"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.793 7.01729C12.0787 6.71737 12.0672 6.24263 11.7672 5.95694L6.51678 0.955606C6.22711 0.679683 5.77187 0.679683 5.4822 0.955606L0.231735 5.95694C-0.0681872 6.24263 -0.0797238 6.71737 0.205968 7.01729C0.491659 7.31721 0.966392 7.32875 1.26631 7.04306L5.99949 2.53447L10.7327 7.04306C11.0326 7.32875 11.5073 7.31721 11.793 7.01729Z"
                                              fill="#212121" />
                                    </svg>

                                    <svg id="arrow-down"
                                         style="float: right; margin-top:14px; "
                                         width="12"
                                         height="7"
                                         viewBox="0 0 12 7"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.793 0.482712C12.0787 0.782634 12.0672 1.25737 11.7672 1.54306L6.51678 6.54439C6.22711 6.82032 5.77187 6.82032 5.4822 6.54439L0.231735 1.54306C-0.0681872 1.25737 -0.0797238 0.782634 0.205968 0.482712C0.491659 0.182789 0.966392 0.171253 1.26631 0.456944L5.99949 4.96553L10.7327 0.456944C11.0326 0.171253 11.5073 0.18279 11.793 0.482712Z"
                                              fill="#212121" />
                                    </svg>

                                </div>
                                <div id="keys-content"
                                     style="padding-top: 10px ; display:none; overflow: hidden;">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Consumer Key</label>
                                        <input id="consumer-k"
                                               type="text"
                                               class="form-control"
                                               id="exampleInputEmail1"
                                               aria-describedby=""
                                               placeholder="">

                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Consumer Secret</label>
                                        <input id="consumer-s"
                                               type="text"
                                               class="form-control"
                                               id="exampleInputEmail1"
                                               aria-describedby=""
                                               placeholder="">

                                    </div>
                                </div>

                            </div>

                        </div>

                        <div id="footer"
                             style="border-top: 1px solid #F4F4F4; padding:20px; text-align:center">
                            <button id="bt-close"
                                    data-dismiss="modal"
                                    aria-label="Close"
                                    class="btn btn-primary"
                                    style="padding: 12px 32px; font-size: 16px; font-weight: 600">Fechar</button>
                            <button id='bt-update-keys'
                                    class="btn btn-primary"
                                    style="display: none; padding: 12px 32px; font-size: 16px; font-weight: 600">Atualizar
                                e
                                fechar</button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- End Modal -->

            {{-- Modal confirm --}}
            <div class="modal fade modal-slide-bottom"
                 id="modal-confirm"
                 aria-hidden="true"
                 aria-labelledby="exampleModalTitle"
                 role="dialog"
                 tabindex="-1">
                <div class="modal-dialog modal-dialog-centered"
                     style="width: 517px; min-height: 453px">
                    <div class="modal-content"
                         id=""
                         style="border-radius: 12px">

                        <div style="width: 517px; height:70px; font-size: 22px; font-weight: bold; color:#636363"
                             class=""
                             id="">
                            <div style="width: 72px; height:70px; float: left; border-right: 1px solid #F4F4F4"
                                 class=""
                                 id="">

                                <svg style="position: absolute; top:24px; left:22px"
                                     width="28"
                                     height="29"
                                     viewBox="0 0 28 29"
                                     fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.1666 3.08331H5.83325C3.84992 3.08331 2.33325 4.59998 2.33325 6.58331V19.4166C2.33325 21.4 3.84992 22.9166 5.83325 22.9166H10.4999L17.4999 26.4166L16.3333 22.9166H22.1666C24.1499 22.9166 25.6666 21.4 25.6666 19.4166V6.58331C25.6666 4.59998 24.1499 3.08331 22.1666 3.08331ZM20.2999 8.33331C19.8333 9.26665 19.3666 10.7833 19.1333 12.8833C18.7833 14.9833 18.6666 16.5 18.7833 17.6666C18.7833 18.0166 18.7833 18.25 18.6666 18.4833C18.5499 18.7166 18.3166 18.95 17.9666 18.95C17.6166 18.95 17.2666 18.8333 16.9166 18.4833C15.7499 17.3166 14.8166 15.45 14.1166 13.1166C13.2999 14.75 12.7166 15.9166 12.2499 16.7333C11.5499 18.1333 10.8499 18.8333 10.3833 18.95C10.0333 18.95 9.79992 18.7166 9.44992 18.1333C8.86659 16.5 8.16659 13.2333 7.46659 8.56665C7.46659 8.21665 7.46659 7.98331 7.69992 7.74998C7.81659 7.51665 8.16659 7.39998 8.51659 7.28331C9.09992 7.28331 9.56659 7.51665 9.56659 8.21665C9.91659 10.9 10.3833 13.1166 10.8499 14.8666L13.6499 9.61665C13.8833 9.14998 14.1166 8.91665 14.5833 8.91665C15.1666 8.91665 15.5166 9.26665 15.6333 9.96665C15.9833 11.6 16.3333 13 16.7999 14.2833C17.1499 11.1333 17.7333 8.79998 18.4333 7.39998C18.6666 7.04998 18.8999 6.81665 19.2499 6.81665C19.4833 6.81665 19.8333 6.93331 20.0666 7.04998C20.2999 7.28331 20.4166 7.51665 20.4166 7.74998C20.4166 7.98331 20.4166 8.21665 20.2999 8.33331Z"
                                          fill="black" />
                                </svg>

                            </div>
                            <span style="line-height:72px; margin-left: 25px ">

                                Confirme a sincronização
                            </span>

                            <svg data-dismiss="modal"
                                 aria-label="Close"
                                 style="position: absolute; top:27px; right:24px"
                                 class="close"
                                 width="16"
                                 height="17"
                                 viewBox="0 0 16 17"
                                 fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 1.75L1 15.75M1 1.75L15 15.75L1 1.75Z"
                                      stroke="#636363"
                                      stroke-width="2"
                                      stroke-miterlimit="10"
                                      stroke-linecap="round"
                                      stroke-linejoin="round" />
                            </svg>

                        </div>

                        <div style="padding: 25px 25px 15px 25px; border-top: 1px solid #F4F4F4"
                             class=""
                             id="">

                            <div class="text-center">
                                <svg width="93"
                                     height="93"
                                     viewBox="0 0 93 93"
                                     fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="46.5"
                                            cy="46.5"
                                            r="46.5"
                                            fill="#F2F8FF" />
                                    <path d="M57.6587 28.6022C56.9697 29.4907 57.1435 30.7603 58.0469 31.4379C62.952 35.1171 65.8857 40.8175 65.8857 47C65.8857 57.0785 58.1348 65.3764 48.1759 66.4424L50.0311 64.6167C50.8345 63.8266 50.8345 62.5455 50.0311 61.7554C49.3008 61.0371 48.1579 60.9718 47.3526 61.5595L47.1219 61.7554L41.6361 67.1507C40.9058 67.869 40.8394 68.9931 41.437 69.7851L41.6361 70.0121L47.1219 75.4074C47.9252 76.1975 49.2277 76.1975 50.0311 75.4074C50.7614 74.6891 50.8278 73.5651 50.2303 72.773L50.0311 72.5461L47.9733 70.526C60.3051 69.539 70 59.383 70 47C70 39.5416 66.4568 32.657 60.5419 28.2203C59.6385 27.5427 58.3476 27.7136 57.6587 28.6022ZM41.9689 18.5926C41.1655 19.3827 41.1655 20.6638 41.9689 21.4539L44.024 23.4742C31.6935 24.4625 22 34.6179 22 47C22 54.1339 25.24 60.7512 30.7245 65.2069C31.6004 65.9185 32.8971 65.797 33.6206 64.9355C34.3442 64.074 34.2207 62.7987 33.3447 62.0871C28.7966 58.3921 26.1143 52.9138 26.1143 47C26.1143 36.9225 33.8637 28.6251 43.8213 27.5579L41.9689 29.3833C41.1655 30.1734 41.1655 31.4545 41.9689 32.2446C42.7723 33.0347 44.0748 33.0347 44.8781 32.2446L50.3639 26.8493C51.1672 26.0591 51.1672 24.7781 50.3639 23.9879L44.8781 18.5926C44.0748 17.8025 42.7723 17.8025 41.9689 18.5926Z"
                                          fill="#2E85EC" />
                                </svg>

                            </div>

                            <div class="text-center p-20"
                                 style=" margin-bottom: 16px; font-weight: bold;
                            font-size: 18px;
                            line-height: 150%; ">

                                Você tem certeza que quer sincronizar <span style="font-weight: bold"
                                      id="sync-name"></span>?

                                <div style="height: 8px"></div>

                                <span style="font-weight: normal;
                                font-size: 14px;
                                line-height: 150%;"
                                      id="sync-desc"></span>

                            </div>

                        </div>

                        <div id="footer"
                             style="border-top: 1px solid #F4F4F4; padding:20px; text-align:center">
                            <button id="bt-close-confirm"
                                    data-dismiss="modal"
                                    aria-label="Close"
                                    class="btn btn-primary"
                                    style="
                                width:80px; margin-right:8px;
                                padding: 12px 0; font-size: 16px; font-weight: 600; color:#636363; background-color: #FAFAFA; border:none; font-weight: bold">Voltar</button>
                            <button id='bt-confirm'
                                    class="btn btn-primary"
                                    style=" padding: 12px 16px; font-size: 16px; font-weight: 600">Sincronizar</button>
                        </div>

                    </div>
                </div>
            </div>
            {{-- end modal --}}

        </div>

    </div>

    @include('utils.empty-companies-error')
    @include('utils.companies-not-approved-getnet')
    @include('woocommerce::not-integration')

    </div>

    @push('scripts')
        <script src="{{ mix('build/layouts/wooCommerce/index.min.js') }}"></script>
    @endpush
@endsection
