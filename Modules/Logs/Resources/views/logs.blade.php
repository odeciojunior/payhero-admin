@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <div class="panel" data-plugin="matchHeight">

        <table id="tabela_vendas" class="table-bordered table-hover w-full">
          <thead class="bg-blue-grey-100">
            <tr>
              <td style='display:none'>id</td>
              <td>sessao_log</td>
              <td>plano</td>
              <td>descrição</td>
              <td>evento</td>
              <td>sistema operacional</td>
              <td>navegador</td>
              <td>horario</td>
              <td>hora_encerramento</td>
              <td>forward</td>
              <td>referencia</td>
              <td>nome</td>
              <td>email</td>
              <td>cpf</td>
              <td>celular</td>
              <td>cidade</td>
              <td>estado</td>
              <td>erro</td>
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
        order: [ [0, 'desc'] ],
        ajax: {
            url: '/logs/data-source',
            type: 'POST'
        },
        columns: [
            {data: 'id', name: 'id', visible: false},
            {data: 'id_sessao_log', name: 'id_sessao_log'},
            {data: 'plano_nome', name: 'plano_nome'},
            {data: 'plano', name: 'plano'},
            {data: 'evento', name: 'evento'},
            {data: 'sistema_operacional', name: 'sistema_operacional'},
            {data: 'navegador', name: 'navegador'},
            {data: 'hora_acesso', name: 'hora_acesso'},
            {data: 'horario', name: 'horario'},
            {data: 'forward', name: 'forward'},
            {data: 'referencia', name: 'referencia'},
            {data: 'nome', name: 'nome'},
            {data: 'email', name: 'email'},
            {data: 'cpf', name: 'cpf'},
            {data: 'celular', name: 'celular'},
            {data: 'cidade', name: 'cidade'},
            {data: 'estado', name: 'estado'},
            {data: 'erro', name: 'erro'},
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

