@extends("layouts.master")

@section('content')

  <!-- Page --> 
  <div class="page">
    <div class="page-header">
        <h1 class="page-title">Meus produto</h1>
    </div>

    <div class="page-content container-fluid">
      <div class="panel pt-30 p-30" data-plugin="matchHeight">

          @if(count($produtos) == 0)
              <div class="alert alert-warning" role="alert">
                  <strong>Ops!</strong> Você ainda não possui produtos cadastrados.
              </div>
          @else
            <div class="row">
                @foreach($produtos as $produto)
                  <div class="col-3">
                      <div class="card" style="border: 1px solid gray">
                          <img class="card-img-top img-fluid w-full" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO.$produto['foto'] !!}" alt="Imagem não encontrada" style="height: 180px;width: 90%; margin: 8px 0 8px 0">
                          <div class="card-block">
                            <a href="#" class="detalhes_produto" produto="{!! $produto['id'] !!}" data-toggle='modal' data-target='#modal_detalhes'>
                                <h4 class="card-title">{!! $produto['nome'] !!}</h4>
                                <p class="card-text">{!! $produto['descricao'] !!}</p>
                            </a>
                            <hr>
                            <span data-toggle='modal' data-target='#modal_editar'>
                                <a href="/produtos/editar/{!! $produto['id'] !!}" class='btn btn-outline btn-primary editar_produto' data-placement='top' data-toggle='tooltip' title='Editar'>
                                    <i class='icon wb-pencil' aria-hidden='true'></i>
                                </a>
                            </span>
                            <span data-toggle='modal' data-target='#modal_excluir'>
                                <a class='btn btn-outline btn-danger excluir_produto' data-placement='top' data-toggle='tooltip' title='Excluir' produto="{!! $produto['id'] !!}">
                                    <i class='icon wb-trash' aria-hidden='true'></i>
                                </a>
                            </span>
                        </div>
                      </div>
                  </div>
                @endforeach
            </div>
          @endif
    
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

    $(document).ready( function(){

        $('.detalhes_produto').on('click', function() {

            var produto = $(this).attr('produto');

            $('#modal_detalhes_titulo').html('Detalhes do produto');

            $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

            var data = { id_produto : produto };

            $.post("/produtos/detalhe", data)
              .then( function(response, status){

                $('#modal_detalhes_body').html(response);
            });

        });

        $('.excluir_produto').on('click', function(){

            var id_produto = $(this).attr('produto');

            $('#form_excluir_produto').attr('action','/produtos/deletarproduto/'+id_produto);

            var name = $(this).parent().parent().find(".card-title").html();

            $('#modal_excluir_titulo').html('Excluir o produto '+name+'?');

        });

    });

  </script>


@endsection

