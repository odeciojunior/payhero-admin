@extends("layouts.master")

@section('content')

  {{-- token datatables ??? 8e9710ced8ef52bb3eebfb2e472c6fb193555b8a --}}

  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <div class="row" data-plugin="matchHeight" data-by-row="true">

        <table id="tabela_vendas" class="table table-hover table-bordered table-striped datatable">
          <thead>
            <tr>
              <td>status</td>
              <td>forma_pagamento</td>
              <td>valor_total_pago</td>
              <td>valor_recebido_mercado_pago</td>
              <td>valor_plano</td>
              <td>valor_frete</td>
              <td>cod_cupom</td>
              <td>meio_pagamento</td>
              <td>data_inicio</td>
              <td>data_finalizada</td>
              <td>comprador</td>
              <td>mercado_pago_id</td>
              <td>mercado_pago_status</td>
              <td>qtd_parcela</td>
              <td>bandeira</td>
              <td>entrega</td>
              <td>valor_cupom</td>
              <td>tipo_cupom</td>
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
            {data: 'status', name: 'status'},
            {data: 'forma_pagamento', name: 'forma_pagamento'},
            {data: 'valor_total_pago', name: 'valor_total_pago'},
            {data: 'valor_recebido_mercado_pago', name: 'valor_recebido_mercado_pagov'},
            {data: 'valor_plano', name: 'valor_plano'},
            {data: 'valor_frete', name: 'valor_frete'},
            {data: 'cod_cupom', name: 'cod_cupom'},
            {data: 'meio_pagamento', name: 'meio_pagamento'},
            {data: 'data_inicio', name: 'data_inicio'},
            {data: 'data_finalizada', name: 'data_finalizada'},
            {data: 'comprador', name: 'comprador'},
            {data: 'mercado_pago_id', name: 'mercado_pago_id'},
            {data: 'mercado_pago_status', name: 'mercado_pago_status'},
            {data: 'qtd_parcela', name: 'qtd_parcela'},
            {data: 'bandeira', name: 'bandeira'},
            {data: 'entrega', name: 'entrega'},
            {data: 'valor_cupom', name: 'valor_cupom'},
            {data: 'tipo_cupom', name: 'tipo_cupom'},
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

