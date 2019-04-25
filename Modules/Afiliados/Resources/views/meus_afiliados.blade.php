@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Meus afiliados</h1>
        <div class="page-header-actions">
        </div>
    </div>

    <div class="page-content container-fluid">
        <div class="panel pt-30 p-30" data-plugin="matchHeight">

            <div class="nav-tabs-horizontal" data-plugin="tabs">
                <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                    <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_meus_afiliados"
                        aria-controls="tab_meus_afiliados" role="tab">Meus afiliados</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_solicitacoes_pendentes"
                        aria-controls="tab_solicitacoes_pendentes" role="tab">Solicitações pendentes</a></li>
                </ul>
                <div class="tab-content pt-20">
                    <div class="tab-pane active" id="tab_meus_afiliados" role="tabpanel">
                    
                        <table id="tabela_meus_afiliados" class="table-bordered table-hover w-full" style="margin-top: 80px">
                            <thead class="bg-blue-grey-100">
                                <tr>
                                    <td>Afiliado</td>
                                    <td>Projeto</td>
                                    <td>Porcentagem</td>
                                    <td style="width: 260px">Opções</td>
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
                                    <td>Usuário</td>
                                    <td>Projeto</td>
                                    <td>Data de solicitação</td>
                                    <td>Porcentagem</td>
                                    <td>Detalhes</td>
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

            <!-- Modal remover afiliado do projeto -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_remover_afiliado" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="width: 100%; text-align:center">Remover afiliado do projeto?</h4>
                        </div>
                        <div class="modal-footer">
                            <button id="bt_remover_afiliado" type="button" class="btn btn-success" data-dismiss="modal">Confirmar</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->

            <!-- Modal cancelar solicitação -->
            <div class="modal fade modal-3d-flip-vertical" id="modal_cancelar_solicitacao" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="width: 100%; text-align:center">Cancelar solicitação de afiliação ?</h4>
                        </div>
                        <div class="modal-body" style="padding: 30px">

                        </div>
                        <div class="modal-footer">
                            <button id="bt_cancelar_solicitacao" type="button" class="btn btn-success" data-dismiss="modal">Confirmar</button>
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

        var id_afiliado = '';

        $("#tabela_meus_afiliados").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/afiliados/meusafiliados/data-source',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            columns: [
                { data: 'name', name: 'name'},
                { data: 'nome', name: 'nome'},
                { data: 'porcentagem', name: 'porcentagem'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false },
            ],
            "language": {
                "sProcessing":    "Carregando...",
                "lengthMenu": "Apresentando _MENU_ registros por página",
                "zeroRecords": "Nenhum registro encontrado",
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

                $('.detalhes_afiliado').on('click', function(){

                    var afiliado = $(this).attr('afiliado');
        
                    $('#modal_detalhes_titulo').html('Detalhes do afiliado');
        
                    $('#modal_detalhes_body').html('Carregando...');

                    {{--  $.ajax({
                        method: "GET",
                        url: "/afiliados/getafiliadosprojeto/"+projeto,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_detalhes_body').html(data);
                        }
                    });  --}}

                });

                $(".remover_afiliado").on("click",function(){

                    var afiliado = $(this).attr('afiliado');
                    id_afiliado = afiliado;
                });

            }

        });

        $("#tabela_minhas_afiliacoes_pendentes").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/afiliados/minhassolicitacoesafiliados/data-source',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            },
            columns: [
                { data: 'name', name: 'name'},
                { data: 'nome', name: 'nome'},
                { data: 'data_solicitacao', name: 'data_solicitacao'},
                { data: 'porcentagem_afiliados', name: 'porcentagem_afiliados'},
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

                $('.confirmar_afiliacao').on('click', function(){

                    var solicitacao_afiliacao = $(this).attr('solicitacao_afiliacao');
        
                    $.ajax({
                        method: "POST",
                        url: "/afiliados/minhasafiliacoespendentes/confirmar",
                        data: { id: solicitacao_afiliacao },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            location.reload();
                        }
                    });

                });

                var solicitacao_afiliacao = '';

                $(".cancelar_solicitacao").on("click", function(){

                    solicitacao_afiliacao = $(this).attr('solicitacao_afiliacao');

                    $("#bt_cancelar_solicitacao").on("click", function(){

                        $('.loading').css("visibility", "visible");
    
                        $.ajax({
                            method: "POST",
                            url: "/afiliados/negarsolicitacao",
                            data: { id_solicitacao: solicitacao_afiliacao },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                $('.loading').css("visibility", "hidden");
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                $('.loading').css("visibility", "hidden");
                                alertPersonalizado('success','Solicitação negada!');
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            },
                        });
                    });

                });

            }

        });

        $("#bt_remover_afiliado").on("click", function(){

            $.ajax({
                method: "POST",
                url: "/afiliados/excluirafiliacao",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { afiliado: id_afiliado },
                error: function(){
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    if(data == 'sucesso'){
                        window.location = '/afiliados/meusafiliados';
                    }
                }
            });

        });


        function alertPersonalizado(tipo, mensagem){

            swal({
                position: 'bottom',
                type: tipo,
                toast: 'true',
                title: mensagem,
                showConfirmButton: false,
                timer: 6000
            });
        }

    });

  </script>

@endsection

