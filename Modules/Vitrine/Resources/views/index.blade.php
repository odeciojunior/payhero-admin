@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Vitrine</h1>
    </div>

    <div class="page-content container-fluid">
 
        <div class="row">
          @foreach($projetos as $projeto)
            <div class="col-xl-3 col-md-6 info-panel">
              <div class="card card-shadow">
                  <a class="detalhes_projeto" projeto="{!! $projeto['id'] !!}" data-toggle='modal' data-target='#modal_detalhes' style="height: 180px">
                      <img class="card-img-top img-fluid w-full" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto['foto'] !!}" alt="Imagem não encontrada" style="height: 180px;width: 90%; margin: 8px 0 8px 0">
                  </a>
                  <div class="card-block">
                    <a>
                        <h4 class="card-title">{!! $projeto['nome'] !!}</h4>
                        <p class="card-text">{!! substr($projeto['descricao'],0,50) !!}</p>
                    </a>
                    <hr>
                    <span>
                      <b>Produtor : </b> {!! $projeto['produtor'] !!}
                    </span></br>
                    <span class="font-size-15 gray-600">
                      Comissão por venda de até:
                    </span></br>
                    <span class="font-size-18 green-600">
                      R$ {!! $projeto['maior_comissao'] !!}
                    </span>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Modal com detalhes do projeto -->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                <h4 id="modal_detalhes_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
              </div>
              <div id="modal_detalhes_body" class="modal-body" style="padding: 30px">

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- End Modal -->

      </div>
  </div>

  <script>

    $(document).ready( function(){

        $('.detalhes_projeto').on('click', function(){

            var projeto = $(this).attr('projeto');

            var titulo = $(this).parent().parent().parent().find('.card-title').html();

            $('#modal_detalhes_titulo').html('Detalhes do projeto '+titulo);

            $('#modal_detalhes_body').html('Carregando...');

            $.ajax({
              method: "GET",
              url: "/projetos/getdadosprojeto/"+projeto,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              error: function(){
                  alert('Ocorreu algum erro');
              },
              success: function(data){
                $('#modal_detalhes_body').html(data);
              }
            });

        });

    });

  </script>

@endsection

