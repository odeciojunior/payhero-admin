@extends("layouts.master")

@push('css')
        <link rel="stylesheet" href="{{ mix('modules/melhorenvio/css/index.min.css') }}">
@endpush

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container" style="display:none !important;">
            <div class="row jusitfy-content-between" style="min-height:56px">
                <div class="col-lg-8  align-items-center">
                    <h1 class="page-title my-10" style="min-height: 28px">
                        <a href="/apps">
                            <span class="o-arrow-right-1 font-size-30 ml-2" aria-hidden="true"></span>
                            Integrações Melhor Envio
                        </a>
                    </h1>
                </div>
                <div class="col text-right" id="integration-actions">
                    <a data-toggle="modal" data-target="#modal-add-integration" id='btn-integration-model'
                       class="btn btn-floating btn-primary ml-10"
                       style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-add-1 text-white" aria-hidden="true"></i>
                    </a>
                    <a target="_blank" href="{{route('melhorenvio.tutorial')}}"
                       class="btn btn-floating"
                       style="background-color: #2E85EC;position: relative;float: right;color: white;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-question-1 white font-size-30" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="page-content container">

            <div class="row" id="content">
                {{-- js load dynamically --}}
            </div>

        </div>

        @include('melhorenvio::not-integration')
    </div>


    {{-- Modal add-edit integration --}}
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-add-integration" tabindex="-1">
        <div class="modal-dialog modal-lg d-flex justify-content-center">
            <div class="modal-content w-450" id="conteudo_modal_add">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title font-weight-700">Adicionar nova integração com Melhor Envio</h4>
                </div>
                <div class="pt-10 pr-20 pl-20 modal_integracao_body">
                    <div class="row">
                        <div class="input-group col-12">
                            <label for="name">Nome</label>
                            <div class="d-flex input-group">
                                <input type="text" class="input-pad" name="name" id="name"
                                       placeholder="Ex.: MelhorEnvio #1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: 15px">
                    <button id="btn-save" class="btn btn-success">Realizar integração</button>
                    <button class="btn btn-primary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- End Modal  --}}

    <!-- Modal delete integration -->
    <div id="modal-delete-integration" class="modal fade modal-3d-flip-vertical">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button class="close" data-dismiss="modal">
                        <span>×</span>
                    </button>
                </div>
                <div class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;">highlight_off</i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button type="button" class="btn btn-outline btn-cancel-modal border-0 col-4 d-flex justify-content-center align-items-center" data-dismiss="modal">
                        <b>Cancelar</b>
                    </button>
                    <button data-dismiss="modal" id="btn-delete-confirm" class="btn btn-outline btn-delete-modal border-0 col-4 d-flex justify-content-center align-items-center">
                        <b class="mr-2">Excluir </b>
                        <span class="o-bin-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End modal -->

    @push('scripts')
        <script src="{{ mix('modules/melhorenvio/js/index.min.js') }}"></script>
    @endpush

@endsection

