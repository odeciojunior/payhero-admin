@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Minhas afiliações</h1>
        <div class="page-header-actions">
        </div>
    </div>

    <div class="page-content container-fluid">
        <div class="panel pt-30 p-30" data-plugin="matchHeight">

            <div class="nav-tabs-horizontal" data-plugin="tabs">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_minhas_afiliacoes"
                        aria-controls="tab_minhas_afiliacoes" role="tab">Minhas afiliações</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_solicitacoes_pendentes"
                        aria-controls="tab_solicitacoes_pendentes" role="tab">Solicitações pendentes</a></li>
                </ul>
                <div class="tab-content pt-20">
                    <div class="tab-pane active" id="tab_minhas_afiliacoes" role="tabpanel">
                        <table id="tabela_minhas_afiliacoes" class="table-bordered table-hover w-full" style="margin-top: 80px">
                            <thead class="bg-blue-grey-100">
                                <tr>
                                    <td>Projeto</td>
                                    <td>Porcentagem</td>
                                    <td>Data de afiliação</td>
                                    <td>Opções</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="tab_solicitacoes_pendentes" role="tabpanel">
                        <table id="tabela_minhas_afiliacoes_pendentes" class="table-bordered table-hover w-full" style="margin-top: 80px">
                            <thead class="bg-blue-grey-100">
                                <tr>
                                    <td>Projeto</td>
                                    <td>Data de solicitação</td>
                                    <td>Status</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

          <!-- Modal com detalhes do projeto -->
          <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                  <h4 id="modal_detalhes_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
                </div>
                <div id="modal_detalhes_body" class="modal-body" style="padding: 30px">

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

        @if(\Session::has('success'))

            swal({
                position: 'bottom',
                type: 'success',
                toast: 'true',
                title: "{!! \Session::get('success') !!}",
                showConfirmButton: false,
                timer: 8000
            });

        @endif

        $("#tabela_minhas_afiliacoes").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/afiliados/minhasafiliacoes/data-source',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },  
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: 'porcentagem', name: 'porcentagem'},
                { data: 'data_afiliacao', name: 'data_afiliacao'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false },
            ],
            "language": {
                "sProcessing":    "Carregando...",
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

                $('.detalhes_afiliacao').on('click', function(){

                    var afiliado = $(this).attr('afiliado'); 

                    var titulo = $(this).parent().parent().parent().find('.card-title').html();

                    $('#modal_detalhes_titulo').html('Detalhes da afiliação');

                    $('#modal_detalhes_body').html('Carregando...');

                    $.ajax({
                        method: "GET",
                        url: "/afiliados/getdetalhesafiliacao/"+afiliado,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_detalhes_body').html(data);

                            $('#alterar_empresa').on('click', function(){
                                $.ajax({
                                    method: "POST",
                                    url: "/afiliados/setempresa",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: { empresa: $('#select_empresas').val(), afiliado: $('#afiliado').val() },
                                    error: function(){
                                        alert('Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        alert('empresa alterada');
                                    }
                                });
            
                            });

                        }
                    });

                });
            }

        });

        $("#tabela_minhas_afiliacoes_pendentes").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/afiliados/minhasafiliacoespendentes/data-source',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },  
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: 'data_solicitacao', name: 'data_solicitacao'},
                { data: 'status', name: 'status'},
            ],
            "language": {
                "sProcessing":    "Carregando...",
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

