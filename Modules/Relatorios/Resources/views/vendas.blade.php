@extends("layouts.master")

@section('content')

  {{-- token datatables ??? 8e9710ced8ef52bb3eebfb2e472c6fb193555b8a --}}

  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <div class="row" data-plugin="matchHeight" data-by-row="true">

        <table id="tabela_vendas" class="tablesaw table-bordered table-hover datatTable" style="width: 100%">
          <thead>
            <tr>
              <td>Transação</td>
              <td>Produto</td>
              <td>Comprador</td>
              <td>Forma de Pagamento</td>
              <td>Status</td>
              <td>Data de pagamento</td>
              <td>Data Finalizada</td>
              <td>Valor Venda</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- End Page -->

  <script>

    $(document).ready( function(){
      $('#tabela_vendas').DataTable( {
        processing: true,
        serverSide: true,
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
        }
      });
    });

  </script>


@endsection

