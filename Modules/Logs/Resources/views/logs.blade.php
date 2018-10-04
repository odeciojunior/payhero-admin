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
              <td>evento</td>
              <td>user_agent</td>
              <td>hora_acesso</td>
              <td>horario</td>
              <td>hora_encerramento</td>
              <td>hora_submit</td>
              <td>forward</td>
              <td>referencia</td>
              <td>nome</td>
              <td>email</td>
              <td>cpf</td>
              <td>celular</td>
              <td>entrega</td>
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
            {data: 'plano', name: 'plano'},
            {data: 'evento', name: 'evento'},
            {data: 'user_agent', name: 'user_agent'},
            {data: 'hora_acesso', name: 'hora_acesso'},
            {data: 'horario', name: 'horario'},
            {data: 'hora_encerramento', name: 'hora_encerramento'},
            {data: 'forward', name: 'forward'},
            {data: 'hora_submit', name: 'hora_submit'},
            {data: 'referencia', name: 'referencia'},
            {data: 'nome', name: 'nome'},
            {data: 'email', name: 'email'},
            {data: 'cpf', name: 'cpf'},
            {data: 'celular', name: 'celular'},
            {data: 'entrega', name: 'entrega'},
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

