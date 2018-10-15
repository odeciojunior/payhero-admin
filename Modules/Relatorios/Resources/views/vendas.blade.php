@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <div class="panel pt-30 p-30 " data-plugin="matchHeight">

        <table id="tabela_vendas" class="tablesaw table-striped tablesaw-swipe table-hover">
          <thead >
            <tr>
              <td>Transação</td>
              <td>Produto</td>
              <td>Comprador</td>
              <td>Forma de Pagamento</td>
              <td>Status</td>
              <td>Data de pagamento</td>
              <td>Data Finalizada</td>
              <td>Valor Venda</td>
              <td>Detalhes</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>

        <!-- Modal -->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
          <div class="modal-dialog modal-simple">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                <h4 id="modal_venda_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
              </div>
              <div id="modal_venda_body" class="modal-body">

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
  </div>


  <script>


    $(document).ready( function(){

      $('#tabela_vendas').DataTable( {
          processing: true,
          serverSide: true,
          order: [ [ 0, 'desc' ] ],
          ajax: {
              url: '/relatorios/vendas/data-source',
              type: 'POST'
          },
          columns: [
              {data: 'id', name: 'id'},
              {data: 'plano_nome', name: 'plano_nome'},
              {data: 'nome', name: 'nome'},
              {data: 'forma_pagamento', name: 'forma_pagamento'},
              {data: 'mercado_pago_status', name: 'mercado_pago_status'},
              {data: 'data_inicio', name: 'data_inicio'},
              {data: 'data_finalizada', name: 'data_finalizada'},
              {data: 'valor_plano', name: 'valor_plano'},
              {data: 'detalhes', name: 'detalhes', orderable: false, searchable: false },
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
          "initComplete": function() {

              $('.detalhes_venda').on('click', function() {

                  var venda = $(this).attr('venda');

                  $('#modal_venda_titulo').html('Detalhes da venda #' + venda);

                  $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                  var data = { id_venda : venda };

                  $.post("/relatorios/venda/detalhe", data)
                   .then( function(response, status){

                      $('#modal_venda_body').html(response);

                  });

              });
          },
          "drawCallback": function() {

            $('.detalhes_venda').on('click', function() {

                var venda = $(this).attr('venda');

                $('#modal_venda_titulo').html('Detalhes da venda #' + venda);

                $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                var data = { id_venda : venda };

                $.post("/relatorios/venda/detalhe", data)
                 .then( function(response, status){

                    $('#modal_venda_body').html(response);

                });

            });
        }


      });

    });

  </script>


@endsection
