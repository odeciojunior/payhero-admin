@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Recuperação de carrinhos abandonados</h1>
    </div>

    <div class="page-content container-fluid">
      <div class="panel pt-30 p-30" data-plugin="matchHeight">

        <table id="tabela_carrinhosabandonados" class="table table-bordered table-hover w-full" style="margin-top: 80px">
          <thead class="bg-blue-grey-100">
            <tr>
              <td>Data</td>
              <td>Comprador</td>
              <td>Status email</td>
              <td>Status sms</td>
              <td>Status de recuperação</td>
              <td>Valor</td>
              <td style="width: 60px">Opções</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>

        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_opcoes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
          <div class="modal-dialog modal-simple">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" style="width: 100%; text-align:center">Recuperação de carrinho</h4>
              </div>
              <div id="modal_opcoes_body" class="modal-body">

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
        </div>

    </div>
  </div>


  <script>

    $(document).ready( function(){

        $("#tabela_carrinhosabandonados").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/recuperacaocarrinho/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST'
            },
            columns: [
                { data: 'created_at', name: 'created_at'},
                { data: 'comprador', name: 'comprador'},
                { data: 'status_email', name: 'status_email'},
                { data: 'status_sms', name: 'status_sms'},
                { data: 'status_recuperacao', name: 'status_recuperacao'},
                { data: 'valor', name: 'valor'},
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

                $('.opcoes_checkout').on('click', function() {

                    var checkout = $(this).attr('checkout');

                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                    $.ajax({
                        method: "POST",
                        url: "/recuperacaocarrinho/opcoes",
                        data: { id_checkout: checkout },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            //
                        },
                        success: function(data){
                            $("#modal_opcoes_body").html(data);
                        }
                    });

                });
             }

        });

    });

  </script>


@endsection

