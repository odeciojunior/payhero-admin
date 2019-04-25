@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Vitrine</h1>
    </div>

    <div class="page-content container-fluid">
 
        <ul class="blocks blocks-100 blocks-xxl-5 blocks-lg-4 blocks-md-3" data-plugin="masonry">
          @foreach($projetos as $projeto)
            <li class="masonry-item">
              <div class="card card-shadow">
                  <div class="card-header cover">
                    <a class="detalhes_projeto" projeto="{!! $projeto['id'] !!}" data-toggle='modal' data-target='#modal_detalhes'>
                        <img class="ccover-image" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto['foto'] !!}" alt="Imagem não encontrada" style="height: 180px;width: 90%; margin: 8px 0 8px 0">
                    </a>
                  </div>
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
                  <div class="rating text-center" data-score="4" data-number="5" data-read-only="true" data-plugin="rating" title="good" style="width: 100%;margin-bottom: 20px">
                      <i data-alt="1" class="icon wb-star orange-600" title="muito ruim">
                      </i>
                      &nbsp;
                      <i data-alt="2" class="icon wb-star orange-600" title="ruim">
                      </i>
                      &nbsp;
                      <i data-alt="3" class="icon wb-star orange-600" title="regular">
                      </i>
                      &nbsp;
                      <i data-alt="4" class="icon wb-star orange-600" title="bom">
                      </i>
                      &nbsp;
                      <i data-alt="5" class="icon wb-star" title="excelente">
                      </i>
                      <input name="score" type="hidden" value="4" readonly="">
                  </div>
              </div>
            </li>
          @endforeach
        </ul>

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

