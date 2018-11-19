@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">

        <div class="page-header">
            <h1 class="page-title">Projeto {{ $projeto->nome }}</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/projetos">
                    Meus projetos
                </a>
            </div>
        </div>

        <div class="page-content container-fluid">
            <div class="panel pt-10 p-10" data-plugin="matchHeight">

                <div class="col-xl-12">
                    <div class="example-wrap">
                        <div class="nav-tabs-horizontal" data-plugin="tabs">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_info_geral"
                                    aria-controls="tab_info_geral" role="tab">Informações gerais</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_produtos"
                                    aria-controls="tab_produtos" role="tab">Produtos</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_dominios"
                                    aria-controls="tab_cupons" role="tab">Domínios</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_layouts"
                                    aria-controls="tab_cupons" role="tab">Layouts</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_pixels"
                                    aria-controls="tab_pixels" role="tab">Pixels</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_brindes"
                                    aria-controls="tab_brindes" role="tab">Brindes</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_cupons"
                                    aria-controls="tab_cupons" role="tab">Cupons de desconto</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_planos"
                                    aria-controls="tab_planos" role="tab">Planos</a></li>
                            </ul>
                            <div class="tab-content pt-20">
                                <div class="tab-pane active" id="tab_info_geral" role="tabpanel">
                                    <div class="col-md-10">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr>
                                                    <td><b>Nome</b></td>
                                                    <td>{{ $projeto->nome }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Descrição</b></td>
                                                    <td>{{ $projeto->descricao }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Visibilidade</b></td>
                                                    <td>{{ ($projeto->visibilidade == 'publico') ? 'Projeto público' : 'Projeto privado' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Status</b></td>
                                                    <td>{{ $projeto->status ? 'Ativo' : 'Inativo' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <img src="{{ $foto }}" alt="Imagem não encontrada" style="height: 200px; width: 200px"/>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab_produtos" role="tabpanel">
                                    <table id="tabela_produtos" class="table-bordered table-hover w-full" style="margin-top: 20px">
                                        <thead class="bg-blue-grey-100">
                                            <th>Nome</th>
                                            <th>Descrição</th>
                                            <th>Categoria</th>
                                            <th>Formato</th>
                                            <th>Quantidade</th>
                                            <th>Status</th>
                                            <th style="min-width: 159px;max-width:161px;width:160px">Detalhes</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab_planos" role="tabpanel">
                                    <table id="tabela_planos" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <thead class="bg-blue-grey-100">
                                            <th>Nome</th>
                                            <th>Descrição</th>
                                            <th>Código</th>
                                            <th>Preço</th>
                                            <th style="min-width: 159px;max-width:161px;width:160px">Detalhes</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab_pixels" role="tabpanel">
                                    <table id="tabela_pixels" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <thead class="bg-blue-grey-100">
                                            <th>Nome</th>
                                            <th>Código</th>
                                            <th>Plataforma</th>
                                            <th>Status</th>
                                            <th style="min-width: 159px;max-width:161px;width:160px">Detalhes</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab_brindes" role="tabpanel">
                                    <table id="tabela_brindes" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <thead class="bg-blue-grey-100">
                                            <th>Título</th>
                                            <th>Descrição</th>
                                            <th>Tipo</th>
                                            <th style="min-width: 159px;max-width:161px;width:160px">Detalhes</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab_cupons" role="tabpanel">
                                    <table id="tabela_cuponsdesconto" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <thead class="bg-blue-grey-100">
                                            <th>Nome</th>
                                            <th>Descrição</th>
                                            <th>Tipo</th>
                                            <th>Valor</th>
                                            <th>Código</th>
                                            <th>Status</th>
                                            <th style="min-width: 159px;max-width:161px;width:160px">Detalhes</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab_dominios" role="tabpanel">
                                    <table id="tabela_dominios" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <thead class="bg-blue-grey-100">
                                            <th>Domínio</th>
                                            <th>Layout</th>
                                            <th>Empresa</th>
                                            <th style="width: 100px">Opções</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab_layouts" role="tabpanel">
                                    <table id="tabela_layouts" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <thead class="bg-blue-grey-100">
                                            <th>Descrição</th>
                                            <th>Logo 1</th>
                                            <th>Estilo</th>
                                            <th>Cor 1</th>
                                            <th>Cor 2</th>
                                            <th>Botões</th>
                                            <th style="width: 110px">Opções</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal detalhes -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                      <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                            <h4 id="modal_detalhes_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
                          </div>
                          <div id="modal_detalhes_body" class="modal-body">
            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                          </div>
                        </div>
                      </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

  <script>

    $(document).ready( function(){

        var id_projeto = '{{ $projeto->id }}';

        $("#tabela_produtos").DataTable( {
            bLengthChange: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/produtos/data-source',
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: function(data){
                    return data.descricao.substr(0,25); 
                }, name: 'descricao'},
                { data: 'categoria_nome', name: 'categoria_nome'},
                { data: function(data){
                    if(data.formato == 1)
                      return 'Físico';
                    if(data.formato == 0)
                      return 'Digital';
                    return 'null';
                }, name: 'formato'},
                { data: 'quntidade', name: 'quntidade'},
                { data: function(data){
                  if(data.disponivel == 1)
                  return 'Disponível';
                if(data.disponivel == 0)
                  return 'Indisponível';
                return 'null';
                }, name: 'disponivel'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
            ],
            "language": {
                "sProcessing":    "Carregando...",
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
            },
            "drawCallback": function() {

                $('.detalhes_produto').on('click', function() {
                    var produto = $(this).attr('produto');
                    $('#modal_detalhes_titulo').html('Detalhes da produto');
                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    var data = { id_produto : produto };
                    $.post("/produtos/detalhe", data)
                    .then( function(response, status){
                        $('#modal_detalhes_body').html(response);
                    });
                });
            }

        });

        $("#tabela_planos").DataTable( {
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/planos/data-source',
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: function(data){
                    if(data.descricao == null)
                        return '';
                    else
                        return data.descricao.substr(0,25);   
                }, name: 'descricao'},
                { data: 'cod_identificador', name: 'cod_identificador'},
                { data: 'preco', name: 'preco'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
            ],
            "language": {
                "sProcessing":    "Carregando...",
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
            },
            "drawCallback": function() {

                $('.detalhes_plano').on('click', function() {
                    var plano = $(this).attr('plano');
                    $('#modal_detalhes_titulo').html('Detalhes da plano');
                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    var data = { id_plano : plano };
                    $.post("/planos/detalhe", data)
                    .then( function(response, status){
                        $('#modal_detalhes_body').html(response);
                    });
                });
            }

        });

        $("#tabela_pixels").DataTable( {

            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/pixels/data-source',
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: 'cod_pixel', name: 'cod_pixel'},
                { data: 'plataforma', name: 'plataforma'},
                { data: function(data){
                    if(data.status == 1){
                      return 'Ativo';
                    }
                    else{
                      return 'Inativo';
                    }
                }, name: 'status'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
            ],
            "language": {
                "sProcessing":    "Carregando...",
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
            },
            "drawCallback": function() {

                $('.detalhes_pixel').on('click', function() {
                    var pixel = $(this).attr('pixel');
                    $('#modal_detalhes_titulo').html('Detalhes da pixel');
                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    var data = { id_pixel : pixel };
                    $.post("/pixels/detalhe", data)
                    .then( function(response, status){
                        $('#modal_detalhes_body').html(response);
                    });
                });
            }

        });

        $("#tabela_brindes").DataTable( {

            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/brindes/data-source',
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'titulo', name: 'titulo'},
                { data: 'descricao', name: 'descricao'},
                { data: 'tipo_descricao', name: 'tipo_descricao'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
            ],
            "language": {
                "sProcessing":    "Carregando...",
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
            },
            "drawCallback": function() {

                $('.detalhes_brinde').on('click', function() {
                    var brinde = $(this).attr('brinde');
                    $('#modal_detalhes_titulo').html('Detalhes da brinde');
                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    var data = { id_brinde : brinde };
                    $.post("/brindes/detalhe", data)
                    .then( function(response, status){
                        $('#modal_detalhes_body').html(response);
                    });
                });
            }

        });

        $("#tabela_cuponsdesconto").DataTable( {

            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/cuponsdesconto/data-source',
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: 'descricao', name: 'descricao'},
                { data: 'tipo', name: 'tipo'},
                { data: 'valor', name: 'valor'},
                { data: 'cod_cupom', name: 'cod_cupom'},
                { data: 'status', name: 'status'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
            ],
            "language": {
                "sProcessing":    "Carregando...",
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
            },
            "drawCallback": function() {

                $('.detalhes_cupom').on('click', function() {
                    var cupom = $(this).attr('cupom');
                    $('#modal_detalhes_titulo').html('Detalhes da cupom');
                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    var data = { id_cupom : cupom };
                    $.post("/cuponsdesconto/detalhe", data)
                    .then( function(response, status){
                        $('#modal_detalhes_body').html(response);
                    });
                });
            }

        });

        $("#tabela_dominios").DataTable( {

            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/dominios/data-source',
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'dominio', name: 'dominio'},
                { data: 'layout_descricao', name: 'layout_descricao'},
                { data: 'empresa_nome', name: 'empresa_nome'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
            ],
            "language": {
                "sProcessing":    "Carregando...",
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
            },
            "drawCallback": function() {
            }

        });

        $("#tabela_layouts").DataTable( {

            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '/layouts/data-source',
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'descricao', name: 'descricao'},
                { data: 'logo', name: 'logo'},
                { data: 'estilo', name: 'estilo'},
                { data: 'cor1', name: 'cor1'},
                { data: 'cor2', name: 'cor2'},
                { data: 'botao', name: 'botao'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
            ],
            "language": {
                "sProcessing":    "Carregando...",
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
            },
            "drawCallback": function() {

            }

        });


        $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {

            $($.fn.dataTable.tables( true ) ).css('width', '100%');
            $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();

        }); 

    });

  </script>


@endsection

