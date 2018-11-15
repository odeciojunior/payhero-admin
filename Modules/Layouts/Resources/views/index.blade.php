@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Layouts</h1>
        <div class="page-header-actions">
            <a class="btn btn-primary float-right" href="/layouts/cadastro">
                <i class='icon wb-user-add' aria-hidden='true'></i>
                Cadastrar
            </a>
        </div>
    </div>

    <div class="page-content container-fluid">
      <div class="panel pt-30 p-30" data-plugin="matchHeight">

        <table id="tabela_layouts" class="table-bordered table-hover w-full" style="margin-top: 80px">
          <thead class="bg-blue-grey-100">
            <tr>
              <td>Descrição</td>
              <td>Logo 1</td>
              <td>Estilo</td>
              <td>Cor 1</td>
              <td>Cor 2</td>
              <td>Botões</td>
              <td style="width: 110px">Opções</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>

        <!-- Modal de confirmação da exclusão do layout -->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_excluir" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-simple">
              <div class="modal-content">
                <form id="form_excluir_layout" method="GET" action="/deletarlayout">
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

        $("#tabela_layouts").DataTable( {

            processing: true,
            serverSide: true,
            ajax: {
                url: '/layouts/data-source',
                type: 'POST'
            },
            columns: [
                { data: 'descricao', name: 'descricao'},
                { data: 'logo', name: 'logo'},
                { data: 'estilo', name: 'estilo'},
                { data: 'cor1', name: 'cor1'},
                { data: 'cor2', name: 'cor2'},
                { data: 'botao', name: 'botao'},
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

                $('.excluir_layout').on('click', function(){

                    var id_layout = $(this).attr('layout');

                    $('#form_excluir_layout').attr('action','/layouts/deletarlayout/'+id_layout);

                    var name = $(this).closest("tr").find("td:first-child").text();

                    $('#modal_excluir_titulo').html('Excluir o layout '+name+'?');
                });
            }

        });

    });

  </script>


@endsection

