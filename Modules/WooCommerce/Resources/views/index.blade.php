@extends("layouts.master")
@push('css')
    {{-- <link rel="stylesheet" href="{{ asset('/modules/convertax/css/index.css') }}"> --}}
     <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=03') !!}">
     <style>
        .o-arrow-right-1 {
            font-size: 30px;
        }

        .o-arrow-right-1::before {
            transform: rotate(180deg);
        }
        .gray:hover{
            color:#a1a1a1 !important;
        }
     </style>
@endpush
@section('content')

    <!-- Page -->
    <div class="page">
        <div style="display: none" class="page-header container">
            <div class="row jusitfy-content-between" style="min-height:56px">
                
                <div class="col-lg-8">
                    <h1 class="page-title my-10" style="min-height: 28px">
                        <a class="gray" href="/apps">
                            <span class="o-arrow-right-1 font-size-30 ml-2 gray" aria-hidden="true"></span>
                            Integrações com WooCommerce
                        </a>
                    </h1>
                </div>
                
                <div class="col text-right" id="integration-actions" style="display:none">
                    <a data-toggle="modal" id='btn-integration-model' class="btn btn-floating btn-primary ml-10"
                       style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-add-1" aria-hidden="true"></i>
                    </a>

                    {{-- <a data-toggle="modal" id="button-information" data-target='#modal_explicacao' class="btn btn-floating"
                       style="background-color: #2E85EC;position: relative;float: right;color: white; display:none;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-question-1 white font-size-30" aria-hidden="true"></i>
                    </a> --}}



                    <div class="w-200 mt-2" style="">
                            <div class="d-flex align-items-center">
                                <span class="o-download-cloud-1 mr-2"></span>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      Download plugin
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                      
                                      <a class="dropdown-item" href="https://sirius.cloudfox.net/modules/woocommerce/plugins/plugin_cloudfox.zip">Adiciona ao carrinho de compras</a>
                                      <a class="dropdown-item" href="https://sirius.cloudfox.net/modules/woocommerce/plugins/plugin_cloudfox_skip_to_checkout.zip">Envia direto pro checkout</a>
                                    </div>
                                  </div>
                              
                            </div>
                        </div>

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
                            @include('woocommerce::create')
                        </div>
                        <div class="modal-footer" style="margin-top: 15px">
                            <button id="bt_integration" type="button" class="btn btn-success"
                                    data-dismiss="modal"></button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        {{-- End Modal  --}}

        <!-- Modal Explicação -->
            <div class="modal fade modal-3d-flip-vertical" id="modal_explicacao" aria-hidden="true"
                 aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" id="conteudo_modal_explicacao">
                        <div class="panel-group panel-group-continuous m-0" id="acordionHelp"
                             aria-multiselectable="true" role="tablist">
                            
                            <div class="panel">
                                <div class="panel-heading" id="exampleHeadingFirst" role="tab">
                                    <a class="panel-title collapsed" data-parent="#acordionHelp" data-toggle="collapse"
                                       href="#exampleCollapseFirst" aria-controls="exampleCollapseFirst"
                                       aria-expanded="false">
                                        <strong>Primeiro passo</strong>
                                    </a>
                                </div>
                            </div>
                        </div>
                               
                    </div>
                </div>
            </div>
            <!-- End Modal -->

        </div>
        @include('companies::empty')
        @include('companies::not_company_approved_getnet')
        @include('woocommerce::not-integration')
    </div>

    @push('scripts')
        <script src="{{ asset('modules/woocommerce/js/index.js?v='. uniqid()) }}"></script>
    @endpush

@endsection

