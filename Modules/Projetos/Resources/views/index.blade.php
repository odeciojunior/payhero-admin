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
      {{--  <div class="panel pt-30 p-30" data-plugin="matchHeight">

        <table id="tabela_projetos" class="table-bordered table-hover w-full" style="margin-top: 20px">
          <thead class="bg-blue-grey-100">
            <tr>
              <td>Nome</td>
              <td>Descrição</td>
              <td style="max-width: 160px">Detalhes</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>  --}}


        <div class="row">
          @foreach($projetos as $projeto)
            <div class="col-3" style="height: 300px">
              <div class="card">
                <img class="card-img-top img-fluid w-full" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto->foto !!}" alt="Imagem não encontrada" style="height: 180px">
                <div class="card-block">
                  <a href='/projetos/projeto/{!! $projeto['id'] !!}'>
                    <h4 class="card-title">{!! $projeto['nome'] !!}</h4>
                    <p class="card-text">{!! $projeto['descricao'] !!}</p>
                  </a>
                  <a href="#" class="btn btn-primary">Button</a>
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
                <form id="form_excluir_projeto" method="GET" action="/deletarprojeto">
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

        </div>
    {{--  </div>  --}}
  </div>

  <script>

    $(document).ready( function(){

        $("#tabela_projetos").DataTable( {

            processing: true,
            serverSide: true,
            ajax: {
                url: '/projetos/data-source',
                type: 'POST'
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: 'descricao', name: 'descricao'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false },
            ],
            "language": {
                "sProcessing":    "Procesando...",
                "lengthMenu": "Apresentando _MENU_ registros por página",
                "zeroRecords": "Nenhum registro encontrado no banco de dados",
                "info": "Apresentando página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro encontrado no banco de dados",
                "infoFiltered": "(filtrado por _MAX_ registros)",
                "sInfoPostFix":   "",
                "sSearch":        "Procurar :",
                "sUrl":           "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Carregando...",
                "oPaginate": {
                    "sFirst":    "Primeiro",
                    "sLast":    "Último",
                    "sNext":    "Próximo",
                    "sPrevious": "Anterior",
                },
            },
            "drawCallback": function() {

                $('.detalhes_projeto').on('click', function() {

                    var projeto = $(this).attr('projeto');

                    $('#modal_detalhes_titulo').html('Detalhes da projeto');

                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                    var data = { id_projeto : projeto };

                    $.post("/projetos/detalhe", data)
                    .then( function(response, status){

                        $('#modal_detalhes_body').html(response);
                    });

                });

                $('.excluir_projeto').on('click', function(){

                    var id_projeto = $(this).attr('projeto');

                    $('#form_excluir_projeto').attr('action','/projetos/deletarprojeto/'+id_projeto);

                    var name = $(this).closest("tr").find("td:first-child").text();

                    $('#modal_excluir_titulo').html('Excluir o projeto '+name+'?');

                });
            }

        });

    });

  </script>


@endsection

