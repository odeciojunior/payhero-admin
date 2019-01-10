@extends("layouts.master")

@section('content')

  <!-- Page --> 
  <div class="page">
    <div class="page-header">
        <h1 class="page-title">Transferências</h1>
    </div>

    <div class="page-content container-fluid">
        <div class="panel pt-30 p-30" data-plugin="matchHeight">
            <div style="float: right; padding: 40px">
                <select id="select_empresas" class="form-control">
                    <option value="">Informações da empresa Empresa 1</option>
                </select>
            </div>
            <br>
            <div class="row">
                <div class="col-3">
                    <div class="card" style="border: 1px solid gray">
                        <img class="card-img-top img-fluid w-full" src="" alt="Imagem não encontrada" style="height: 180px;width: 90%; margin: 8px 0 8px 0">
                        <div class="card-block">
                            <a href="#" class="detalhes_produto" produto="fdsafds" data-toggle='modal' data-target='#modal_detalhes'>
                                <h4 class="card-title">kljdfasf</h4>
                                <p class="card-text">ljflçskf</p>
                            </a>
                            <hr>
                            <span data-toggle='modal' data-target='#modal_editar'>
                                <a href="" class='btn btn-outline btn-primary editar_produto' data-placement='top' data-toggle='tooltip' title='Editar'>
                                    <i class='icon wb-pencil' aria-hidden='true'></i>
                                </a>
                            </span>
                            <span data-toggle='modal' data-target='#modal_excluir'>
                                <a class='btn btn-outline btn-danger excluir_produto' data-placement='top' data-toggle='tooltip' title='Excluir' produto="5632">
                                    <i class='icon wb-trash' aria-hidden='true'></i>
                                </a>
                            </span>
                        </div>
                    </div>
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

