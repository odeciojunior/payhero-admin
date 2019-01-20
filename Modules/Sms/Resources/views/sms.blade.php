@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-content container-fluid">
      <div class="panel pt-30 p-30" data-plugin="matchHeight">

        <h3 style="margin: 30px 0 20px 0">Histórico de SMS</h3>

          <div class="row">

            <div class="col-12">
                <table id="tabela_sms" class="display w-full table-stripped" style="width:100%">
                    <thead>
                        <th>Plano</th>
                        <th>Tipo</th>
                        <th>Número</th>
                        <th>Mensagem</th>
                        <th>Data</th>
                        <th>Evento</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="modal_detalhes_historico" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-simple">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" style="width: 100%; text-align:center">Detalhes da compra</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-hover table-stripped table-bordered" style="margin: 60px 0 40px 0">
                            <tbody id="detalhes_body">
                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer text-center">
                        <button type="button" class="btn btn-danger" style="width: 30%; margin: auto" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>

  <script>

    $(document).ready( function(){

        $("#tabela_sms").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            serverSide: true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax: {
                url: '/sms/dadosmensagens',
                type: 'POST'
            },
            columns: [
                { data: 'plano', name: 'plano', orderable: "false"},
                { data: 'tipo', name: 'tipo', orderable: "false"},
                { data: 'para', name: 'para', orderable: "false"},
                { data: 'mensagem', name: 'mensagem', orderable: "false"},
                { data: 'data', name: 'data', orderable: "false"},
                { data: 'evento', name: 'evento', orderable: "false"},
                { data: 'status', name: 'status', orderable: "false"},
                {{--  { data: function(data){
                  if(data.recipient_id != '')
                    return 'Ativa';
                  else
                    return 'Inativa';
                }, name: 'recipient_id'},  --}}
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
        });

    });

  </script>


@endsection

