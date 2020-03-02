@extends('affiliates::layouts.master')

@section('content')
    <style>
        @media (min-width: 1200px) {
            .container-lg {
                max-width: 700px;
            }
        }
    </style>
    <div class="page-content container col-md-6 container-lg" style='display:none;'>
        <div class="card shadow p-30 div-project" style='display:none;'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='row mx-10'>
                        <div class='col-md-12'>
                            <label class='page-title project-header' style='font-size: 2em'></label>
                        </div>
                        <div class='col-md-6'>
                            <p class="card-text sm mt-10 mx-5" id="created_by"></p>
                            <img class='project-image img-fluid rounded'>
                        </div>
                        <div class='col-md-6 mt-md-70 mt-sm-20 mt-20 text-center'>
                            <b>Descrição:</b>
                            <p class='text-about-project'></p>
                        </div>
                    </div>
                    <div class="nav-tabs-horizontal mt-20" data-plugin="tabs">
                        <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                            <li class="nav-item" role="presentation" id='nav_users'>
                                <a class="nav-link active" data-toggle="tab" href="#tab_terms" aria-controls="tab_terms" role="tab">Termos de afiliação
                                </a>
                            </li>
                            <li class="nav-item" role="presentation" id="nav_documents">
                                <a class="nav-link" data-toggle="tab" href="#tab_about" aria-controls="tab_about" role="tab">
                                    Sobre
                                </a>
                            </li>
                        </ul>
                        <div class="p-30 pt-20">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab_terms" role="tabpanel">
                                    <p class='text-terms'></p>
                                </div>
                                <div class="tab-pane fade" id="tab_about" role="tabpanel">
                                    {{--                                    <p class='text-about-project'></p>--}}
                                    <p class='percentage-affiliate'></p>
                                    <p class='cookie_duration'></p>
                                    <p class='url_page'></p>
                                    <p class='contact'></p>
                                    <p class='support_phone'></p>
                                    <p class='created_at'></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-12 my-10 text-right div-button'>
                    </div>
                </div>
            </div>
        </div>
    {{--        <div class='alert alert-danger text-center font-size-18 div-disabled-url-affiliates' style='display:none;'>Link para afiliação não disponível</div>--}}
    <!-- Modal affiliates -->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_store_affiliate"
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
                    <div class="pt-10 pr-20 pl-20 modal-affiliate-body">
                        <h3 class="black text-center">Selecione a empresa</h3>
                        {{--                        <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>--}}
                        <div class='form-group mt-20'>
                            <label for='companies'>Empresa:</label>
                            <select id='companies' name='companies' class='form-control'></select>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: 15px">
                        <button id="btn-store-affiliation" type="button" class="btn btn-success">Enviar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical text-center" id='modal-not-companies'
             aria-hidden="true"
             aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-lg d-flex justify-content-center">
                <div class="modal-content w-450" id="conteudo_modal_add">
                    <div class='header-modal simple-border-bottom'>
                        <h2 id='modal-tile' class='modal-title'>Ooooppsssss!</h2>
                    </div>
                    <div class='modal-body simple-border-bottom' style='padding-bottom: 1%; padding-top: 1% ;'>
                        <div class='swal2-icon swal2-error swal2-animate-error-icon' style='display:flex;'>
                            <span class='swal2-x-mark'>
                                <span class='swal2-x-mark-line-left'></span>
                                <span class='swal2-x-mark-line-right'></span>
                            </span>
                        </div>
                        <h3 align='center'>Você não cadastrou nenhuma empresa</h3>
                        <h5 align='center'>
                            Deseja cadastrar uma empresa?
                            <a class='red pointer' href='/companies'>Clique aqui</a>
                        </h5>
                    </div>
                    <div class="modal-footer" style="margin-top: 15px">
                        <div style='width:100%; text-align: center; padding-top: 3%;'>
                          <span class='btn btn-danger' data-dismiss='modal' style='font-size: 25px;'>
                                Retornar
                          </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Not Company -->
        <!-- End Modal -->
    </div>
    @push('scripts')
        <script src="{{asset('modules/affiliates/js/index.js?v=2') }}"></script>
    @endpush
@endsection
