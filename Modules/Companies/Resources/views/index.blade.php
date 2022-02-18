@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Empresas</h1>
            <div class="page-header-actions d-flex">
                <a href="{{route('companies.create')}}"
                   class="stretched-link d-flex align-items-center justify-content-end pointer">
                    <span class="link-button-dependent red"> Cadastrar empresa </span>
                    <a role="button" class="ml-10 rounded-add pointer"><i class="o-add-1" aria-hidden="true"></i>
                    </a>
                </a>
            </div>
        </div>
        <div class="page-content container">
            <div class="card shadow">
                <div class="page-invoice-table table-responsive">
                    <table id="companies_table" class="table table-striped" style="width:100%;">
                        <thead>
                        <tr class="text-center">
                            <td class="table-title"><b>Razão Social</b></td>
                            <td class="table-title"><b>Status</b></td>
                            <td class="table-title" style="width: 130px"><b>Opções</b></td>
                        </tr>
                        </thead>
                        <tbody id="companies_table_data">
                        {{-- js carrega... --}}
                        </tbody>
                    </table>
                </div>

                <!-- Modal com detalhes do usuário -->
                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true"
                     aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 id="modal_detalhes_titulo" class="modal-title"
                                    style="width: 100%; text-align:center"></h4>
                            </div>
                            <div id="modal_detalhes_body" class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->

                <!-- Modal padrão para excluir -->
                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true"
                     aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog  modal-dialog-centered  modal-simple">
                        <div class="modal-content">
                            <div class="modal-header text-center">
                                <a class="pointer close" role="button" data-dismiss="modal"
                                   aria-label="Close" id="close-modal-delete">
                                    <i class="material-icons md-16">close</i>
                                </a>
                            </div>
                            <div id="modal_excluir_body" class="modal-body text-center p-20">
                                <div class="d-flex justify-content-center">
                                    <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;">
                                        highlight_off </i>
                                </div>
                                <h3 class="black"> Você tem certeza? </h3>
                                <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                            </div>
                            <div class="modal-footer d-flex align-items-center justify-content-center">
                                <button id="bt-cancelar" type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                                    <b>Cancelar</b>
                                </button>
                                <button id="bt-delete" type="button" class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" style="width: 20%;">
                                    <b class="mr-2">Excluir </b>
                                    <span class="o-bin-1"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end modal -->

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('/modules/companies/js/index.js?v=' . versionsFile()) }}"></script>
    @endpush

@endsection

