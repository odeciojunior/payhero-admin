@extends("layouts.master")

@push('css')
        <link rel="stylesheet" href="{{ asset('/modules/melhorenvio/css/index.css?v=' . uniqid()) }}">
@endpush

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row jusitfy-content-between" style="min-height:56px">
                <div class="col-lg-8  align-items-center">
                    <h1 class="page-title my-10" style="min-height: 28px">
                        <a class="gray gray-hover" href="/apps">
                            <span class="o-arrow-right-1 font-size-30 ml-2  gray-hover"></span>
                            Integrações com Melhor Envio
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
                                    <div class="row mt-30">
                                        <div class="input-group col-12">
                                            <label for="client-id">Client ID</label>
                                            <div class="d-flex input-group">
                                                <input type="text" class="input-pad" name="client_id"
                                                       id="client-id"
                                                       placeholder="Client ID (fornecido após cadastro do app)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-30">
                                        <div class="input-group col-12">
                                            <label for="client_id">Client Secret</label>
                                            <div class="d-flex input-group">
                                                <input type="text" class="input-pad" id="client-secret"
                                                       placeholder="Client Secret (fornecido após cadastro do app)">
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

        </div>
        @include('melhorenvio::not-integration')
    </div>

    @push('scripts')
        <script src="{{ asset('modules/melhorenvio/js/index.js?v='. uniqid()) }}"></script>
    @endpush

@endsection

