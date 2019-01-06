@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Empresas</h1>
        <div class="page-header-actions">
            <a class="btn btn-primary float-right" href="/empresas/cadastro">
                <i class='icon wb-user-add' aria-hidden='true'></i>
                Cadastrar empresa
            </a>
        </div>
    </div>

    <div class="page-content container-fluid">
      <div class="panel pt-30 p-30" data-plugin="matchHeight">

        <table id="tabela_empresas" class="table table-bordered table-hover w-full" style="margin-top: 80px">
          <thead class="bg-blue-grey-100">
            <tr>
              <td>Nome fantasia</td>
              <td>CNPJ/CPF</td>
              <td>Status conta bancária</td>
              <td style="width: 160px">Opções</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>

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
                <form id="form_excluir_empresa" method="GET" action="/deletarempresa">
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

        $("#tabela_empresas").DataTable( {

            processing: true,
            serverSide: true,
            ajax: {
                url: '/empresas/data-source',
                type: 'POST'
            },
            columns: [
                { data: 'nome_fantasia', name: 'nome_fantasia'},
                { data: 'cnpj', name: 'cnpj'},
                { data: function(data){
                  if(data.recipient_id != '')
                    return 'Ativa';
                  else
                    return 'Inativa';
              }, name: 'recipient_id'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false },
            ],
            "language": {
                "sProcessing":    "Procesando...",
                "lengthMenu": "Apresentando _MENU_ registros por página",
                "zeroRecords": "Nenhum registro encontrado",
                "info": "Apresentando página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro encontrado",
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

                $('.detalhes_empresa').on('click', function() {

                    var empresa = $(this).attr('empresa');

                    $('#modal_detalhes_titulo').html('Detalhes da empresa');

                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                    var data = { id_empresa : empresa };

                    $.post("/empresas/detalhe", data)
                    .then( function(response, status){

                        $('#modal_detalhes_body').html(response);

                    });

                });

                $('.excluir_empresa').on('click', function(){

                    var id_user = $(this).attr('empresa');

                    $('#form_excluir_empresa').attr('action','/empresas/deletarempresa/'+id_user);

                    var name = $(this).closest("tr").find("td:first-child").text();

                    $('#modal_excluir_titulo').html('Excluir a empresa '+name+'?');
                });
            }

        });

    });

  </script>


@endsection

