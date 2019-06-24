@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header">
            <h1 class="page-title">Empresas</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{route('companies.create')}}">
                    <i class='icon wb-user-add' aria-hidden='true'></i> Cadastrar empresa
                </a>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30">
                <div class="page-invoice-table table-responsive">
                    <table id="companies_table" class="table text-right  table-hover" style="width:100%;">
                        <thead style="text-align:center">
                            <tr>
                                <th style='vertical-align: middle' class="table-title"><b>Nome fantasia</b></th>
                                <th style='vertical-align: middle' class="table-title"><b>CNPJ/CPF</b></th>
                                <th style='vertical-align: middle' class="table-title"><b>Status</b></th>
                                <th style='vertical-align: middle' class="table-title" style="width: 160px">
                                    <b>Opções</b>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="companies_table_data">
                            {{-- js carrega... --}}
                        </tbody>
                    </table>
                </div>
                <ul id="pagination" class="pagination-sm m-30" style="margin-top:10px;position:relative;float:right">
                    {{-- js carrega... --}}
                </ul>
                <!-- Modal com detalhes do usuário -->
                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 id="modal_detalhes_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
                            </div>
                            <div id="modal_detalhes_body" class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->
                <!-- Modal de confirmação da exclusão do usuário -->
                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_excluir" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                            <form id="form_excluir_empresa" method="GET">
                                <div class="modal-header">
                                    <button id="fechar_modal_excluir" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>

                                    <h4 id="modal_excluir_titulo" class="modal-title" style="width: 100%; text-align:center">Excluir ?</h4>
                                </div>
                                <div id="modal_excluir_body" class="modal-body">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                    <button id='bt_excluir' type="submit" class="btn btn-success">Confirmar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('/modules/companies/js/index.js') }}"></script>
    @endpush


@endsection

