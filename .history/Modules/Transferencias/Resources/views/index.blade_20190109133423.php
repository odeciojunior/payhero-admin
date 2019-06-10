@extends("layouts.master")

@section('content')

  <!-- Page --> 
  <div class="page">
    <div class="page-header">
        <h1 class="page-title">Transferências</h1>
    </div>

    <div class="page-content container-fluid">
        <div class="panel pt-30 p-30" data-plugin="matchHeight">
            <div style="float: right; padding: 5px">
                <select id="select_empresas" class="form-control">
                    <option value="">Informações da empresa Empresa 1</option>
                </select>
            </div>
            <div class="row">
            </div>
            <div class="row" style="margin-top: 30px">
                <div class="col-4">
                    <div class="card card-shadow" style="border: 1px solid green">
                        <div class="card-header bg-green-600 white px-30 py-10">
                            <span>Disponível para saque</span>
                        </div>
                        <div class="card-block px-30 py-10">
                            <div class="row">
                                <div class="col-9">
                                    <div class="blue-grey-700" style="font-size: 30px">
                                        R$ 2.899,44
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-8 text-center">
                    <h3> Nova Transferência </h3>
                </div>
            </div>
            <div class="row no-gutters">
                <div class="col-4">
                    <div class="card card-shadow" style="border: 1px solid blue">
                        <div class="card-header bg-blue-600 white px-30 py-10">
                            <span>Disponível para antecipação</span>
                        </div>
                        <div class="card-block px-30 py-10">
                            <div class="row">
                                <div class="col-9">
                                    <div class="blue-grey-700" style="font-size: 30px">
                                        R$ 2.899,44
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-8 text-center">
                    <h3> </h3>
                </div>
            </div>
            <div class="row no-gutters">
                <div class="col-4">
                    <div class="card card-shadow" style="border: 1px solid grey">
                        <div class="card-header bg-grey-600 white px-30 py-10">
                            <span>Aguardando liberação</span>
                        </div>
                        <div class="card-block px-30 py-10">
                            <div class="row">
                                <div class="col-9">
                                    <div class="blue-grey-700" style="font-size: 30px">
                                        R$ 2.899,44
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-8 text-center">
                    <h3> </h3>
                </div>
            </div>
            
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

            <!-- Modal de confirmação da exclusão do produto -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_excluir" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                <div class="modal-content">
                    <form id="form_excluir_produto" method="GET" action="/deletarproduto">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                        <h4 id="modal_excluir_titulo" class="modal-title" style="width: 100%; text-align:center">Excluir ?</h4>
                    </div>
                    <div id="modal_excluir_body" class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-success">Confirmar</button>
                    </div>
                    </form>
                </div>
                </div>
            </div>
            <!-- End Modal -->

            </div>
        </div>
    </div>


  <script>


  </script>


@endsection

