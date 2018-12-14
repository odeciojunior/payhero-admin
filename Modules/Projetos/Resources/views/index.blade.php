@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Meus projetos</h1>
        <div class="page-header-actions">
        </div>
    </div>

    <div class="page-content container-fluid">
        <div class="row">
          @foreach($projetos as $projeto)
            <div class="col-3">
              <div class="card" style="border: 1px solid gray">
                  <img class="card-img-top img-fluid w-full" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto['foto'] !!}" alt="Imagem não encontrada" style="height: 180px;width: 90%; margin: 8px 0 8px 0">
                  <div class="card-block">
                    <a href='/projetos/projeto/{!! $projeto['id'] !!}'>
                        <h4 class="card-title">{!! $projeto['nome'] !!}</h4>
                        <p class="card-text">{!! $projeto['descricao'] !!}</p>
                    </a>
                    <hr>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href="/projetos/editar/{!! $projeto['id'] !!}" class='btn btn-outline btn-primary editar_projeto' data-placement='top' data-toggle='tooltip' title='Editar'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_projeto' data-placement='top' data-toggle='tooltip' title='Excluir' projeto="{!! $projeto['id'] !!}">
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Modal com detalhes do projeto -->
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

        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_excluir" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-simple">
              <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                    <h4 id="modal_excluir_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
                  </div>
                  <div id="modal_excluir_body" class="modal-body">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    <a id="excluir_projeto" class="btn btn-success">Confirmar</a>
                  </div>
              </div>
            </div>
          </div>

        </div>
  </div>

  <script>

    $(document).ready( function(){

        $('.excluir_projeto').on('click', function(){

            var projeto = $(this).attr('projeto');

            var titulo = $(this).parent().parent().find('.card-title').html();

            $('#modal_excluir_titulo').html('Excluir o projeto '+titulo+'?');

            $('#excluir_projeto').attr('href','/projetos/deletarprojeto/'+projeto);

        });

    });

  </script>


@endsection

