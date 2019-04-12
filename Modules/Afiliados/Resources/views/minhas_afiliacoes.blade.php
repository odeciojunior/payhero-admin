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
                        <div class="row">
                            @if(count($projetos) == 0)
                                <div class="alert alert-success" role="alert" style="width:100%; text-align:center">
                                    Nenhuma afiliação encontrada.
                                </div>
                            @else
                                @foreach($projetos as $projeto)
                                    <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                        <div class="card" style="border: 1px solid #E6E6FA">
                                            <a href='/afiliados/minhasafiliacoes/{!! $projeto['id_afiliacao'] !!}'>
                                                <img class="card-img-top img-fluid w-full" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto['foto'] !!}" alt="Imagem não encontrada" style="height: 180px;width: 90%; margin: 8px 0 8px 0">
                                            </a>
                                            <div class="card-block">
                                                <a href='/afiliados/minhasafiliacoes/{!! $projeto['id_afiliacao'] !!}' class="text-center">
                                                    <hr>
                                                    <h4 class="card-title">{!! $projeto['nome'] !!}</h4>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_solicitacoes_pendentes" role="tabpanel">
                        <table id="tabela_minhas_afiliacoes_pendentes" class="table-bordered table-hover w-full" style="margin-top: 80px">
                            <thead class="bg-blue-grey-100">
                                <tr>
                                    <td>Projeto</td>
                                    <td>Data de solicitação</td>
                                    <td>Status</td>
                                    <td style="width: 140px">Opções</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal com detalhes da afiliacao -->
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
                { data: 'detalhes', name: 'detalhes'},
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
                
                var solicitacao_afiliacao = '';

                $(".cancelar_solicitacao").on("click", function(){

                    solicitacao_afiliacao = $(this).attr('solicitacao_afiliacao');

                    $("#bt_cancelar_solicitacao").on("click", function(){

                        $('.loading').css("visibility", "visible");
    
                        $.ajax({
                            method: "POST",
                            url: "/afiliados/cancelarsolicitacao",
                            data: { id_solicitacao: solicitacao_afiliacao },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                $('.loading').css("visibility", "hidden");
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','Solicitação cancelada!');
                                $('.loading').css("visibility", "hidden");
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            },
                        });
                    });

                });
            }

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

