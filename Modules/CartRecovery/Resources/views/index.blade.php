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
              <td>Link</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>

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
                { data: 'client', name: 'client'},
                { data: 'email_status', name: 'email_status'},
                { data: 'sms_status', name: 'sms_status'},
                { data: 'recovery_status', name: 'recovery_status'},
                { data: 'value', name: 'value'},
                { data: 'link', name: 'link', },
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
            }

        });

    });

  </script>


@endsection

