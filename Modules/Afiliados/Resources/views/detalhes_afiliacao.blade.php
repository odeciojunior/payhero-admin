@extends("layouts.master")

@section('styles')

<link rel="stylesheet" href="{{ asset('css/style.css') }}">

@endsection

@section('content')

    <!-- Page -->
    <div class="page">

        <div class="page-header">
            <h1 class="page-title">Afiliação no projeto {{ $projeto['nome'] }}</h1>
        </div>

        <div class="page-content container-fluid">
            <div class="panel pt-10 p-10" data-plugin="matchHeight" style="min-height: 400px">

                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_info_geral"
                            aria-controls="tab_info_geral" role="tab">Informações básicas</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_campanhas"
                            aria-controls="tab_campanhas" role="tab">Campanhas</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_vendas"
                            aria-controls="tab_vendas" role="tab">Vendas</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_materiais_extras"
                            aria-controls="materiais_extras" role="tab">Materiais extras</a></li>    
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_configuracoes"
                            aria-controls="tab_configuracoes" role="tab">Configurações</a></li>
                    </ul>
                    <div class="tab-content pt-20">
                        <div class="tab-pane active" id="tab_info_geral" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-3 col-xl-3">
                                    <img src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto['foto'] !!}" alt="Imagem não encontrada" style="height: 200px; width: 200px"/>
                                </div>
                                <div class="col-lg-9 col-xl-9">
                                    <table class="table table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <td><b>Produtor</b></td>
                                                <td>{!! $produtor !!}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Formato</b></td> 
                                                <td>Físico</td>
                                            <tr>
                                                <td><b>Comissão</b></td>
                                                <td>{!! $projeto['porcentagem_afiliados'] !!}%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_vendas" role="tabpanel">
                            <table id="tabela_vendas_afiliado" class="table-hover table-bordered w-full">
                                <thead>
                                    <th>Transação</th>
                                    <th>Descrição</th>
                                    <th>Comprador</th>
                                    <th>Forma</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Pagamento</th>
                                    <th>Valor líquido</th>
                                    <th>Valor total</th>
                                    <th style="width:160px">Detalhes</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_campanhas" role="tabpanel">
                            <a id="adicionar_campanha" class="btn btn-primary float-right" data-toggle='modal' data-target='#modal_add_campanha' style="color: white">
                                <i class='icon wb-user-add' aria-hidden='true'></i>
                                Adicionar campanha
                            </a>
                            <table id="tabela_campanhas" class="table-hover table-bordered w-full">
                                <thead>
                                    <th>Descrição</th>
                                    <th>Cliques</th>
                                    <th style="width:160px">Opções</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_materiais_extras" role="tabpanel">
                            <div style="margin:15px 20px 0 20px">
                                <table id="tabela_campanhas" class="table table-hover table-bordered w-full">
                                    <thead>
                                        <th>Descrição</th>
                                        <th>Tipo</th>
                                        <th style="width:150px">Ver detalhes</th>
                                    </thead>
                                    <tbody>
                                        @if(count($materiais_extras) == 0)
                                            <tr style="text-align: center">
                                                <td colspan="3"> Nenhum material extra disponível</td>
                                            </tr>
                                        @else
                                            @foreach($materiais_extras as $material_extra)
                                                <tr>
                                                    <td>{!! $material_extra['descricao'] !!}</td>
                                                    <td>{!! $material_extra['tipo'] !!}</td>
                                                    <td><a class="btn btn-success material_extra" material-id="{!! $material_extra['id'] !!}"  data-toggle='modal' data-target='#modal_material_extra'>Ver detalhes</a></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_configuracoes" role="tabpanel" style="padding: 50px">
                            <div class="row">
                                <div class="col-10">
                                    <label for="select_empresas">Minha empresa responsável pelos ganhos</label>
                                    <select id="select_empresas" class="form-control">
                                        @foreach($empresas as $empresa)
                                            <option value="{!! $empresa['id'] !!}" {!! $afiliado['empresa'] == $empresa['id'] ? 'selected' : '' !!} >{!! $empresa['nome_fantasia'] !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-5">
                                    <div style="margin-top:20px">
                                        <input id="afiliado" value="{!! $afiliado['id'] !!}" type="hidden">
                                        <button id="alterar_empresa" class="btn btn-success">Alterar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div style="width:100%">
                                    <button type="button" class="btn btn-danger" style="float: right" data-toggle='modal' data-target='#modal_excluir_afiliacao'>Excluir afiliação</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_campanha" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="page-content container-fluid">
                                        <div class="panel" data-plugin="matchHeight">
                                            <div style="text-align:center;margin-bottom: 50px">
                                                <h4>Cadastrar campanha</h4>
                                            </div>
                                            <input type="hidden" name="afiliado" value="{!! $afiliado['id'] !!}">
                                            <label for="descricao">Nome da campanha</label>
                                            <input class="form-control" name="descricao" id="descricao" placeholder="Descrição da campanha">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button id="cadastrar_campanha" type="button" class="btn btn-success" data-dismiss="modal">Salvar</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_dados_campanha" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button id="fechar_modal_dados" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 id="modal_detalhes_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="page-content container-fluid">
                                        <div id="body_modal_dados" class="panel" data-plugin="matchHeight">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_material_extra" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button id="fechar_modal_dados" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" style="width: 100%; text-align:center">Material extra</h4>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="page-content container-fluid">
                                        <div id="body_modal_material_extra" class="panel" data-plugin="matchHeight">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                    
                <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_excluir_afiliacao" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="page-content container-fluid">
                                        <div class="panel" data-plugin="matchHeight">
                                            <div style="text-align:center;margin-bottom: 50px">
                                                <h4>Excluir afiliação no projeto {{ $projeto['nome'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button id="excluir_afiliacao" type="button" class="btn btn-success" data-dismiss="modal">Excluir</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
    
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function(){

            var id_afiliado = '{!! $id_afiliado !!}';

            $("#tabela_campanhas").DataTable( {
                bLengthChange: false,
                ordering: false,
                processing: true,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: '/afiliados/campanhas/data-source',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: {afiliado: id_afiliado }
                },
                columns: [
                    { data: 'descricao', name: 'descricao'},
                    { data: 'qtd_cliques', name: 'qtd_cliques'},
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

                    $(".dados_campanha").on("click", function(){

                        var titulo = $(this).closest('tr').children('td:first').text();

                        $("#modal_detalhes_titulo").html('Detalhes da campanha ' +titulo);

                        $("#body_modal_dados").html("<div class='text-center'><h5>Carregando...</h5></div>");

                        var id_campanha = $(this).attr('campanha');

                        $.ajax({
                            method: "POST",
                            url: "/afiliados/campanhas/getdadoscampanha",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { campanha: id_campanha },
                            error: function(){
                                alertPersonalizado('error','Ocorreu algum erro ao carregar os dados da campanha');
                            },
                            success: function(data){

                                $("#body_modal_dados").html(data);

                                $("#bt_adicionar_pixel").on('click',function(){
                                    if($('#form_adicionar_pixel:visible').length == 0){
                                        $("#form_adicionar_pixel").show();                                  
                                    }
                                    else{
                                        $("#form_adicionar_pixel").hide();
                                    }
                                });

                                $("#cadastrar_pixel").on("click", function(){

                                    {{--  if($('#nome').val() == '' || $('#cod_pixel').val() == '' || $('#plataforma').val() == '' || $('#status_pixel').val() == ''){
                                        alertPersonalizado('error','Dados informados inválidos');
                                        return false;
                                    }  --}}

                                    var form_data = new FormData(document.getElementById('form_adicionar_pixel'));

                                    $.ajax({
                                        method: "POST",
                                        url: "/pixels/cadastrarpixel",
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data: form_data ,
                                        processData: false,
                                        contentType: false,
                                        cache: false,
                                        error: function(){
                                            alertPersonalizado('error','Ocorreu algum erro');
                                        },
                                        success: function(data){
                                            alertPersonalizado('success','Pixel adicionado!');
                                            $("#fechar_modal_dados").click();
                                        }
                                    });            
                                });

                                $(".excluir_pixel").on("click",function(){

                                    var id_pixel = $(this).attr('pixel');

                                    $.ajax({
                                        method: "GET",
                                        url: "/pixels/deletarpixel/"+id_pixel,
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        error: function(){
                                            alertPersonalizado('error','Ocorreu algum erro');
                                        },
                                        success: function(data){
                                            alertPersonalizado('success','Pixel removido!');
                                            $("#fechar_modal_dados").click();
                                        }
                                    });
            
                                });
                            }
                        });
                    });
                }
            });

            $("#cadastrar_campanha").on("click", function(){

                $.ajax({
                    method: "POST",
                    url: "/afiliados/campanhas/cadastrar",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { afiliado: id_afiliado, descricao: $("#descricao").val() },
                    error: function(){
                        alertPersonalizado('error','Ocorreu algum erro ao cadastrar a nova campanha');
                    },
                    success: function(data){
                        alertPersonalizado('success','Campanha cadastrada');
                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                    }
                });
    
            });

            $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {

                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust();

            });

            $('#alterar_empresa').on('click', function(){

                $.ajax({
                    method: "POST",
                    url: "/afiliados/setempresa",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { empresa: $('#select_empresas').val(), afiliado: $('#afiliado').val() },
                    error: function(){
                        alertPersonalizado('error','Ocorreu algum erro');
                    },
                    success: function(data){
                        alertPersonalizado('success','Empresa alterada com sucesso');
                    }
                });

            });

            $("#tabela_vendas_afiliado").DataTable( {
                bLengthChange: false,
                ordering: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/afiliados/campanhas/vendas',
                    type: 'POST',
                    data: {afiliado: id_afiliado },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                },
                columns: [
                    { data: 'id', name: 'id'},
                    { data: 'descricao', name: 'descricao'},
                    { data: 'comprador', name: 'comprador'},
                    { data: 'forma', name: 'forma'},
                    { data: 'status', name: 'status'},
                    { data: 'data', name: 'data'},
                    { data: 'pagamento', name: 'pagamento'},
                    { data: 'valor_liquido', name: 'valor_liquido'},
                    { data: 'valor_total', name: 'valor_total'},
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
                }    
            });

            $("#excluir_afiliacao").on("click", function(){

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
                            window.location = '/afiliados/minhasafiliacoes';
                        }
                    }
                });

            });

            $(".material_extra").on("click", function(){
                id_material_extra = $(this).attr('material-id');
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
