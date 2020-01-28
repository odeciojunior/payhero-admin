@extends("layouts.master")
@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/digitalmanager/css/index.css') }}">
    {{-- <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}"> --}}
@endpush
@section('content')
    <div id='project-content'>
        <div class='page'>
            <div class="page-header container">
                <div class="row jusitfy-content-between">
                    <div class="col-lg-8">
                        <h1 class="page-title">Integrações Digital Manager Guru</h1>
                    </div>
                    <div class="col text-right" id="integration-actions" style="display:none">
                        <a id='btn-add-integration' class="btn btn-floating btn-danger"
                           style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                            <i class="icon wb-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class='page-content container' id='project-integrated'>
                <div class='col-md-12'>
                    <div class="row" id="content">
                    </div>
                </div>
                <!-- Modal add integração -->
                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_integracao"
                     aria-hidden="true"
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
                                @include('digitalmanager::create')
                                @include('digitalmanager::edit')
                            </div>
                            <div class="modal-footer" style="margin-top: 15px">
                                <button id="bt_integration" type="button" class="btn btn-success"
                                        data-dismiss="modal"></button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->
                <div id="modal-project" class="modal fade modal-3d-flip-vertical " role="dialog" tabindex="-1">
                    <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple ">
                        <div id="conteudo_modal_add" class="modal-content p-10">
                            <div class="header-modal simple-border-bottom">
                                <h2 id="modal-project-title" class="modal-title"></h2>
                            </div>
                            <div id="modal_project_body" class="modal-body simple-border-bottom"
                                 style='padding-bottom:1%;padding-top:1%;'>
                            </div>
                            <div id='modal-withdraw-footer' class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('projects::empty')

            <div id="no-integration-found" class='row justify-content-center' style="display:none; width:100%;">
                <div class="content-error text-center">
                    <img src="{!! asset('modules/global/img/emptyconvites.svg') !!}" width="250px">
                    <h1 class="big gray"><strong>Nenhuma integração encontrada!</strong></h1>
                    <p class="desc gray">Integre seus projetos com Digital Manager Guru de forma totalmente automatizada!</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="/modules/digitalmanager/js/index.js?v=1"></script>
    @endpush
@endsection
