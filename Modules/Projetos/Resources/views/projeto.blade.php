@extends("layouts.master")

@section('styles')

<link rel="stylesheet" href="{{ asset('css/style.css') }}">

@endsection

@section('content')

    <!-- Page -->
    <div class="page">

        <div class="page-header">
            <h1 class="page-title">Projeto {{ $projeto->nome }}</h1>
            <div class="page-header-actions">
                <a class="btn btn-success float-right" href="/projetos">
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
                                {{--  <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_produtos"
                                    aria-controls="tab_produtos" role="tab">Produtos</a></li>  --}}
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
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_sms"
                                    aria-controls="tab_cupons" role="tab">Sms</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_planos"
                                    aria-controls="tab_planos" role="tab">Planos</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_parceiros"
                                    aria-controls="tab_parceiros" role="tab">Parceiros</a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_configuracoes"
                                    aria-controls="tab_cofiguracoes" role="tab">Configurações</a></li>    
                            </ul>
                            <div class="tab-content pt-20">
                                <div class="tab-pane active" id="tab_info_geral" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-3 col-xl-3">
                                            <img src="{{ $foto }}" alt="Imagem não encontrada" style="height: 200px; width: 200px"/>
                                        </div>
                                        <div class="col-lg-9 col-xl-9">
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
                                        </div>
                                    </div>
                                </div>
                                {{--  <div class="tab-pane" id="tab_produtos" role="tabpanel">
                                    <table id="tabela_produtos" class="table-bordered table-hover w-full" style="margin-top: 20px">
                                        <a id="add_produto" class="btn btn-primary float-right"  data-toggle='modal' data-target='#modal_add_produto' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar produto
                                        </a>
                                        <thead class="bg-blue-grey-100">
                                            <th>Nome</th>
                                            <th>Descrição</th>
                                            <th>Categoria</th>
                                            <th>Formato</th>
                                            <th>Quantidade</th>
                                            <th>Status</th>
                                            <th style="width: 110px">Detalhes</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>  --}}
                                <div class="tab-pane" id="tab_planos" role="tabpanel">
                                    <table id="tabela_planos" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <a id="adicionar_plano" class="btn btn-primary float-right"  data-toggle='modal' data-target='#modal_add' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar plano
                                        </a>
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
                                        <a id="adicionar_pixel" class="btn btn-primary float-right"  data-toggle='modal' data-target='#modal_add' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar pixel
                                        </a>
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
                                        <a id="adicionar_brinde" class="btn btn-primary float-right"  data-toggle='modal' data-target='#modal_add' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar brinde
                                        </a>
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
                                        <a id="adicionar_cupom" class="btn btn-primary float-right"  data-toggle='modal' data-target='#modal_add' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar cupom
                                        </a>
                                        <thead class="bg-blue-grey-100">
                                            <th>Nome</th>
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
                                        <a id="adicionar_dominio" class="btn btn-primary float-right"  data-toggle='modal' data-target='#modal_add' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar dominio
                                        </a>
                                        <thead class="bg-blue-grey-100">
                                            <th>Domínio</th>
                                            <th>Ip do domínio</th>
                                            <th>Status</th>
                                            <th style="width: 160px">Opções</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab_sms" role="tabpanel">
                                    <table id="tabela_sms" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <a id="adicionar_sms" class="btn btn-primary float-right" data-toggle='modal' data-target='#modal_add' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar sms
                                        </a>
                                        <thead class="bg-blue-grey-100">
                                            <th>Plano</th>
                                            <th>Evento</th>
                                            <th>Tempo</th>
                                            <th>Mensagem</th>
                                            <th>Status</th>
                                            <th style="width: 110px">Opções</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab_layouts" role="tabpanel">
                                    <table id="tabela_layouts" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <a id="adicionar_layout" class="btn btn-primary float-right"  data-toggle='modal' data-target='#modal_add' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar layout
                                        </a>
                                        <thead class="bg-blue-grey-100">
                                            <th>Descrição</th>
                                            <th>Logo</th>
                                            {{--  <th>Estilo</th>
                                            <th>Cor 1</th>
                                            <th>Cor 2</th>
                                            <th>Botões</th>  --}}
                                            <th style="width: 110px">Opções</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab_parceiros" role="tabpanel">
                                    <table id="tabela_parceiros" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                        <a id="adicionar_parceiro" class="btn btn-primary float-right"  data-toggle='modal' data-target='#modal_add' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar parceiro
                                        </a>
                                        <thead class="bg-blue-grey-100">
                                            <th>Nome</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                            <th style="width: 160px">Opções</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div> 
                                <div class="tab-pane" id="tab_configuracoes" role="tabpanel">
                                    <div id="configuracoes_projeto" style="padding: 30px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para ver detalhes de * no projeto -->
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

                    <!-- Modal padrão para adicionar produtos no projeto -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_produto" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div class="modal-dialog modal-simple">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" style="width: 100%; text-align:center">Selecione o produto</h4>
                                </div>
                                <div id="modal_detalhes_body" class="modal-body">
                                    <div class="row">
                                        <div class="form-group col-12" style="margin-top: 30px">
                                            <select id="select_produtos" class="form-control">
                                                <option value="">Selecione algo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button id="adicionar_produto" type="button" class="btn btn-success" data-dismiss="modal">Adicionar</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal padrão para adicionar * no projeto -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div id="modal_add_tamanho" class="modal-dialog modal-simple">
                            <div class="modal-content" id="conteudo_modal_add">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="row">
                                    <div id="modal_add_body" class="form-group col-12" style="margin-top: 30px">

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button id="cadastrar" type="button" class="btn btn-success" data-dismiss="modal">Salvar</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal padrão para editar * no projeto -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_editar" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div id="modal_editar_tipo" class="modal-dialog modal-simple">
                            <div class="modal-content" id="conteudo_modal_editar">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="row">
                                    <div id="modal_editar_body" class="form-group col-12" style="margin-top: 30px">

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button id="editar" type="button" class="btn btn-success" data-dismiss="modal">Salvar alterações</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal padrão para excluir * no plano -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_excluir" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div class="modal-dialog modal-simple">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <h4 id="modal_excluir_titulo" class="modal-title" style="width: 100%; text-align:center">Excluir ?</h4>
                                </div>
                                <div id="modal_excluir_body" class="modal-body">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                    <button id="bt_excluir" type="button" class="btn btn-success">Confirmar</button>
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

        $('#add_produto').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-simple');
            $('#modal_add_tamanho').removeClass('modal-lg');

            $('#select_produtos').html("<option value=''>Selecione</option>");

            $.ajax({
                method: "POST",
                url: "/produtos/getprodutos",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { projeto: id_projeto },
                error: function(){
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    var options = "<option value=''>Selecione</option>";
                    $.each(data, function(key, d){

                        options += "<option value='"+d.id+"'>"+d.nome+"</option>";

                    });

                    $('#select_produtos').html(options);
                }
            });
        });

        $('#adicionar_produto').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-simple');
            $('#modal_add_tamanho').removeClass('modal-lg');

            var id_produto = $('#select_produtos').val();

            if(id_produto == '')
                return;

            $.ajax({
                method: "POST",
                url: "/produtos/addprodutoprojeto",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { projeto: id_projeto, produto: id_produto },
                error: function(){
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_produto').hide();
                    $($.fn.dataTable.tables( true ) ).css('width', '100%');
                    $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                    alertPersonalizado('success','Produto adicionado!');
                }
            });

        });

        $('#adicionar_dominio').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-lg');
            $('#modal_add_tamanho').removeClass('modal-simple');

            $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

            $.ajax({
                method: "POST",
                url: "/dominios/getformadddominio",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { projeto: id_projeto },
                error: function(){
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#ip_dominio_cadastrar').mask('0ZZ.0ZZ.0ZZ.0ZZ', {translation: {'Z': {pattern: /[0-9]/, optional: true}}});

                    $('#cadastrar').unbind('click');

                    $('#cadastrar').on('click',function(){

                        if($('#ip_dominio').val() == '' || $('#dominio').val() == ''){
                            alertPersonalizado('error','Dados informados inválidos');
                            return false;
                        }

                        var form_data = new FormData(document.getElementById('cadastrar_dominio'));
                        form_data.append('projeto',id_projeto);

                        $.ajax({
                            method: "POST",
                            url: "/dominios/cadastrardominio",
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                if(data != 'sucesso'){
                                    alertPersonalizado('error',data);
                                }
                                else{
                                    alertPersonalizado('success','Domínio adicionado!');
                                }
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            },
                        });
                    });
                }
            });

        });

        $('#adicionar_pixel').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-simple');
            $('#modal_add_tamanho').removeClass('modal-lg');

            $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

            $.ajax({
                method: "GET",
                url: "/pixels/getformaddpixel",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    $('#modal_add').hide();
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#cadastrar').unbind('click');
    
                    $('#cadastrar').on('click',function(){

                        if($('#nome').val() == '' || $('#cod_pixel').val() == '' || $('#plataforma').val() == '' || $('#status_pixel').val() == ''){
                            alertPersonalizado('error','Dados informados inválidos');
                            return false;
                        }

                        var paramObj = {};
                        $.each($('#cadastrar_pixel').serializeArray(), function(_, kv) {
                            if (paramObj.hasOwnProperty(kv.name)) {
                                paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
                                paramObj[kv.name].push(kv.value);
                            }
                            else {
                                paramObj[kv.name] = kv.value;
                            }
                        });
                        paramObj['projeto'] = id_projeto;

                        $.ajax({
                            method: "POST",
                            url: "/pixels/cadastrarpixel",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { pixelData: paramObj },
                            error: function(){
                                $('#modal_add_produto').hide();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','Pixel adicionado!');
                                $('#modal_add_produto').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
                    });
                }
            });

        });

        $('#adicionar_cupom').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-simple');
            $('#modal_add_tamanho').removeClass('modal-lg');

            $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

            $.ajax({
                method: "POST",
                url: "/cuponsdesconto/getformaddcupom",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    $('#modal_add').hide();
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $("#valor_cupom_cadastrar").mask("0#");

                    $('#cadastrar').unbind('click');
    
                    $('#cadastrar').on('click',function(){

                        if($('#nome_cupom').val() == '' || $('#descricao_cupom').val() == '' || $('#tipo_cupom').val() == '' || $('#valor_cupom').val() == '' || $('#cod_cupom').val() == '' || $('#status_cupom').val() == ''){
                            alertPersonalizado('error','Dados informados inválidos');
                            return false;
                        }

                        var form_data = new FormData(document.getElementById('cadastrar_cupom'));
                        form_data.append('projeto',id_projeto);

                        $.ajax({
                            method: "POST",
                            url: "/cuponsdesconto/cadastrarcupom",
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','Cupom de desconto adicionado!');
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            },
                        });
                    });
                }
            });

        });

        $('#adicionar_sms').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-simple');
            $('#modal_add_tamanho').removeClass('modal-lg');

            $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

            $.ajax({
                method: "POST",
                url: "/sms/getformaddsms",
                data: {projeto: id_projeto},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#tempo_sms_cadastrar').mask('0#');

                    $('#cadastrar').unbind('click');
    
                    $('#cadastrar').on('click',function(){

                        if($('#plano_sms').val() == '' || $('#evento_sms').val() == '' || $('#tempo_sms').val() == '' || $('#periodo_sms').val() == '' || $('#status_sms').val() == '' || $('#mensagem_sms').val() == ''){
                            alertPersonalizado('error','Dados informados inválidos');
                            return false;
                        }

                        var form_data = new FormData(document.getElementById('cadastrar_sms'));
                        form_data.append('projeto',id_projeto);

                        $.ajax({
                            method: "POST",
                            url: "/sms/cadastrarsms",
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','SMS adicionado!');
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            },
                        });
                    });
                }
            });

        });

        $('#adicionar_brinde').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-lg');
            $('#modal_add_tamanho').removeClass('modal-simple');

            $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

            $.ajax({
                method: "GET",
                url: "/brindes/getformaddbrinde",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    $('#modal_add').hide();
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#cadastrar').unbind('click');

                    $('#cadastrar').on('click',function(){

                        if($('#titulo_brinde').val() == '' || $('#descricao_brinde').val() == '' || $('#foto_brinde').val() == '' || $('#tipo_brinde').val() == ''){
                            alertPersonalizado('error','Dados informados inválidos');
                            return false;
                        }

                        var form_data = new FormData(document.getElementById('cadastrar_brinde'));
                        form_data.append('projeto',id_projeto);

                        $.ajax({
                            method: "POST",
                            url: "/brindes/cadastrarbrinde",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            error: function(){
                                alertPersonalizado('error','Ocorreu algum erro');
                                $('#previewimage_brinde_cadastrar').imgAreaSelect({remove:true});
                            },
                            success: function(data){
                                alertPersonalizado('success','Brinde adicionado!');
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                $('#previewimage_brinde_cadastrar').imgAreaSelect({remove:true});
                            },
                        });
                    });

                    var p = $("#previewimage_brinde_cadastrar");
                    $("#foto_brinde_cadastrar").on("change", function(){

                        var imageReader = new FileReader();
                        imageReader.readAsDataURL(document.getElementById("foto_brinde_cadastrar").files[0]);

                        imageReader.onload = function (oFREvent) {
                            p.attr('src', oFREvent.target.result).fadeIn();

                            p.on('load', function(){
            
                                var img = document.getElementById('previewimage_brinde_cadastrar');
                                var x1, x2, y1, y2;

                                if (img.naturalWidth > img.naturalHeight) {
                                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                    x2 = x1 + (y2 - y1);
                                }
                                else {
                                    if (img.naturalWidth < img.naturalHeight) {
                                        x1 = Math.floor(img.naturalWidth / 100 * 10);;
                                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                        y2 = y1 + (x2 - x1);
                                    }
                                    else {
                                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    }
                                }

                                $('input[name="foto_brinde_cadastrar_x1"]').val(x1);
                                $('input[name="foto_brinde_cadastrar_y1"]').val(y1);
                                $('input[name="foto_brinde_cadastrar_w"]').val(x2 - x1);
                                $('input[name="foto_brinde_cadastrar_h"]').val(y2 - y1);

                                $('#modal_editar').on('hidden.bs.modal', function () {
                                    $('#previewimage_brinde_cadastrar').imgAreaSelect({remove:true});
                                });
                                $('#previewimage_brinde_cadastrar').imgAreaSelect({remove:true});

                                $('#previewimage_brinde_cadastrar').imgAreaSelect({
                                    x1: x1, y1: y1, x2: x2, y2: y2,
                                    aspectRatio: '1:1',
                                    handles: true,
                                    imageHeight: this.naturalHeight,
                                    imageWidth: this.naturalWidth,
                                    onSelectEnd: function (img, selection) {
                                        $('input[name="foto_brinde_cadastrar_x1"]').val(selection.x1);
                                        $('input[name="foto_brinde_cadastrar_y1"]').val(selection.y1);
                                        $('input[name="foto_brinde_cadastrar_w"]').val(selection.width);
                                        $('input[name="foto_brinde_cadastrar_h"]').val(selection.height);
                                    },
                                    parent: $('#conteudo_modal_add'),
                                });
                            })
                        };
            
                    });
            
                    $("#selecionar_foto_brinde_cadastrar").on("click", function(){
                        $("#foto_brinde_cadastrar").click();
                    });
            
                    $('#tipo_brinde').on('change', function(){
            
                        if($(this).val() == 1){
                            $('#div_input_arquivo').show();
                            $('#div_input_link').hide();
                        }
                        if($(this).val() == 2){
                            $('#div_input_arquivo').hide();
                            $('#div_input_link').show();
            
                        }
                    });

                }
            });

        });

        $('#adicionar_layout').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-lg');
            $('#modal_add_tamanho').removeClass('modal-simple');

            $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

            $.ajax({
                method: "POST",
                url: "/layouts/getformaddlayout",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    $('#modal_add').hide();
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){

                    $('#modal_add_body').html(data);

                    atualizarPreView();

                    function atualizarPreView(){

                        $('#form-preview').submit();
                    }

                    $("#atualizar_preview_cadastro").on("click", function(){
                        atualizarPreView();
                    });

                    $('#cadastrar').unbind('click');

                    $('#cadastrar').on('click',function(){

                        if($('#descricao').val() == '' || $('#logo').val() == ''){
                            alertPersonalizado('error','Dados informados inválidos');
                            return false;
                        }

                        var form_data = new FormData(document.getElementById('cadastrar_layout'));
                        form_data.append('projeto',id_projeto);

                        $.ajax({
                            method: "POST",
                            url: "/layouts/cadastrarlayout",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            error: function(){
                                alertPersonalizado('error','Ocorreu algum erro');
                                $('#previewimage_checkout_cadastrar').imgAreaSelect({remove:true});
                            },
                            success: function(data){
                                alertPersonalizado('success','Layout adicionado!');
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                $('#previewimage_checkout_cadastrar').imgAreaSelect({remove:true});
                            },
                        });
                    });

                    $("#formato_logo_cadastrar").on("change", function(){
                        $("#foto_checkout").val('');
                        $('#previewimage_checkout_cadastrar').imgAreaSelect({remove:true});
                        $('#previewimage_checkout_cadastrar').attr('src', '#');
                        $("#preview_logo_formato").val($(this).val());
                    });

                    var p = $("#previewimage_checkout_cadastrar");
                    $("#foto_checkout").on("change", function(){

                        var input = $(this).clone();
                        $('#form-preview').append(input);

                        var imageReader = new FileReader();
                        imageReader.readAsDataURL(document.getElementById("foto_checkout").files[0]);

                        imageReader.onload = function (oFREvent) {
                            p.attr('src', oFREvent.target.result).fadeIn();

                            p.on('load', function(){
            
                                var img = document.getElementById('previewimage_checkout_cadastrar');
                                var x1, x2, y1, y2;

                                if($("#formato_logo_cadastrar").val() == 'quadrado'){
                                    if (img.naturalWidth > img.naturalHeight) {
                                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                        x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                        x2 = x1 + (y2 - y1);
                                    }
                                    else {
                                        if (img.naturalWidth < img.naturalHeight) {
                                            x1 = Math.floor(img.naturalWidth / 100 * 10);;
                                            x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                            y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                            y2 = y1 + (x2 - x1);
                                        }
                                        else {
                                            x1 = Math.floor(img.naturalWidth / 100 * 10);
                                            x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                            y1 = Math.floor(img.naturalHeight / 100 * 10);
                                            y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                        }
                                    }
                                }
                                else{
                                    if (img.naturalWidth > img.naturalHeight) {
                                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                        x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1));
                                        if(x1 < 0)
                                            x1 = 2;
                                        x2 = x1 + ((y2 - y1) * 2);
                                        if(x2 > img.naturalWidth){
                                            x2 = img.naturalWidth - 2;
                                            y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 4);
                                            y2 = y1 + Math.floor(( x2 - x1) / 2);
                                        }
                                    }
                                    else {
                                        x1 = 2
                                        x2 = img.naturalWidth - 2;
                                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 4);
                                        y2 = y1 + Math.floor((x2 - x1) / 2 );
                                    }

                                }

                                $('input[name="foto_checkout_cadastrar_x1"]').val(x1);
                                $('input[name="foto_checkout_cadastrar_y1"]').val(y1);
                                $('input[name="foto_checkout_cadastrar_w"]').val(x2 - x1);
                                $('input[name="foto_checkout_cadastrar_h"]').val(y2 - y1);
                                $('input[name="preview_logo_x1"]').val(x1);
                                $('input[name="preview_logo_y1"]').val(y1);
                                $('input[name="preview_logo_w"]').val(x2 - x1);
                                $('input[name="preview_logo_h"]').val(y2 - y1);

                                var formato = '';
                                if($("#formato_logo_cadastrar").val() == 'quadrado'){
                                    formato = '1:1';
                                }
                                else{
                                    formato = '2:1';
                                }

                                $('#modal_editar').on('hidden.bs.modal', function () {
                                    $('#previewimage_checkout_cadastrar').imgAreaSelect({remove:true});
                                });
                                $('#previewimage_checkout_cadastrar').imgAreaSelect({remove:true});

                                $('#previewimage_checkout_cadastrar').imgAreaSelect({
                                    x1: x1, y1: y1, x2: x2, y2: y2,
                                    aspectRatio: formato,
                                    handles: true,
                                    imageHeight: this.naturalHeight,
                                    imageWidth: this.naturalWidth,
                                    onSelectEnd: function (img, selection) {
                                        $('input[name="foto_checkout_cadastrar_x1"]').val(selection.x1);
                                        $('input[name="foto_checkout_cadastrar_y1"]').val(selection.y1);
                                        $('input[name="foto_checkout_cadastrar_w"]').val(selection.width);
                                        $('input[name="foto_checkout_cadastrar_h"]').val(selection.height);
                                        $('input[name="preview_logo_x1"]').val(selection.x1);
                                        $('input[name="preview_logo_y1"]').val(selection.y1);
                                        $('input[name="preview_logo_w"]').val(selection.width);
                                        $('input[name="preview_logo_h"]').val(selection.height);
                                    },
                                    parent: $('#conteudo_modal_add'),
                                });
                            })
                        };
            
                    });
            
                    $("#selecionar_foto_checkout_cadastrar").on("click", function(){
                        $("#foto_checkout").click();
                    });

                }
            });

        });

        $('#adicionar_plano').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-lg');
            $('#modal_add_tamanho').removeClass('modal-simple');

            $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

            $.ajax({
                method: "POST",
                url: "/planos/getformaddplano",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {projeto: id_projeto},
                error: function(){
                    $('#modal_add').hide();
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $(".qtd-produtos").mask("0#");
                    $('.dinheiro').mask('#.###,#0', {reverse: true});

                    $("#frete_plano_cadastrar").on("change", function(){

                        if($(this).val() == '0'){
                            $("#div_frete_fixo_cadastrar").hide();
                            $("#div_valor_frete_fixo_cadastrar").hide();
                            $("#div_transportadora_cadastrar").hide();
                            $("#div_responsavel_frete_cadastrar").hide();
                            $("#div_id_plano_transportadora").hide();
                        }
                        else{
                            $("#div_frete_fixo_cadastrar").show();
                            if($("#frete_fixo_plano_cadastrar").val() == '1'){
                                $("#div_valor_frete_fixo_cadastrar").show();
                            }
                            $("#div_transportadora_cadastrar").show();
                            $("#div_responsavel_frete_cadastrar").show();
                            if($("#transportadora_plano_cadastrar").val() != '2'){
                                $("#div_id_plano_transportadora_cadastrar").show();
                            }
                        }
                    });

                    $("#frete_fixo_plano_cadastrar").on("change", function(){
                        if($(this).val() == '1'){
                            $("#div_valor_frete_fixo_cadastrar").show();
                        }
                        else{
                            $("#div_valor_frete_fixo_cadastrar").hide();
                        }
                    });

                    $("#transportadora_plano_cadastrar").on("change", function(){
                        if($(this).val() != '2'){
                            $("#div_id_plano_transportadora_cadastrar").show();
                            $("#responsavel_frete_cadastrar").append(new Option($(this).find("option:selected").text(), $(this).find("option:selected").text()));
                        }
                        else{
                            $("#div_id_plano_transportadora_cadastrar").hide();
                            $("#responsavel_frete_cadastrar option[value='Kapsula']").remove();
                            $("#responsavel_frete_cadastrar option[value='Lift Gold']").remove();
                        }
                    });

                    $('#cadastrar').unbind('click');

                    $('#cadastrar').on('click',function(){

                        if($('#nome_plano').val() == '' || $('#preco_plano').val() == '' || $('#descricao_plano').val() == '' || $('#status_plano').val() == '' || $('#frete_plano').val() == '' || $('#transportadora_plano').val() == '' || $('#frete_fixo_plano').val() == ''){
                            alertPersonalizado('error','Dados informados inválidos');
                            return false;
                        }

                        var form_data = new FormData(document.getElementById('cadastrar_plano'));
                        form_data.append('projeto',id_projeto);

                        $.ajax({
                            method: "POST",
                            url: "/planos/cadastrarplano",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            error: function(){
                                alertPersonalizado('error','Ocorreu algum erro');
                                $('#preview_image_plano_cadastrar').imgAreaSelect({remove:true});
                            },
                            success: function(data){
                                alertPersonalizado('success','Plano adicionado!');
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                $('#preview_image_plano_cadastrar').imgAreaSelect({remove:true});
                            },
                        });
                    });

                    var p = $("#preview_image_plano_cadastrar");
                    $("#foto_plano_cadastrar").on("change", function(){

                        var imageReader = new FileReader();
                        imageReader.readAsDataURL(document.getElementById("foto_plano_cadastrar").files[0]);

                        imageReader.onload = function (oFREvent) {

                            p.attr('src', oFREvent.target.result).fadeIn();

                            p.on('load', function(){
            
                                var img = document.getElementById('preview_image_plano_cadastrar');
                                var x1, x2, y1, y2;

                                if (img.naturalWidth > img.naturalHeight) {
                                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                    x2 = x1 + (y2 - y1);
                                }
                                else {
                                    if (img.naturalWidth < img.naturalHeight) {
                                        x1 = Math.floor(img.naturalWidth / 100 * 10);;
                                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                        y2 = y1 + (x2 - x1);
                                    }
                                    else {
                                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    }
                                }

                                $('input[name="foto_plano_cadastrar_x1"]').val(x1);
                                $('input[name="foto_plano_cadastrar_y1"]').val(y1);
                                $('input[name="foto_plano_cadastrar_w"]').val(x2 - x1);
                                $('input[name="foto_plano_cadastrar_h"]').val(y2 - y1);

                                $('#modal_editar').on('hidden.bs.modal', function () {
                                    $('#preview_image_plano_cadastrar').imgAreaSelect({remove:true});
                                });
                                $('#preview_image_plano_cadastrar').imgAreaSelect({remove:true});

                                $('#preview_image_plano_cadastrar').imgAreaSelect({
                                    x1: x1, y1: y1, x2: x2, y2: y2,
                                    aspectRatio: '1:1',
                                    handles: true,
                                    imageHeight: this.naturalHeight,
                                    imageWidth: this.naturalWidth,
                                    onSelectEnd: function (img, selection) {
                                        $('input[name="foto_plano_cadastrar_x1"]').val(selection.x1);
                                        $('input[name="foto_plano_cadastrar_y1"]').val(selection.y1);
                                        $('input[name="foto_plano_cadastrar_w"]').val(selection.width);
                                        $('input[name="foto_plano_cadastrar_h"]').val(selection.height);
                                    },
                                    parent: $('#conteudo_modal_add'),
                                });
                                
                            })
                        };
            
                    });
            
                    $("#selecionar_foto_plano_cadastrar").on("click", function(){
                        $("#foto_plano_cadastrar").click();
                    });

                    var qtd_produtos = 1;

                    var div_produtos = $('#produtos_div_1').parent().clone();

                    $('#add_produtoplano').on('click', function(){

                        qtd_produtos++;
        
                        var nova_div = div_produtos.clone();
        
                        var select = nova_div.find('select');
                        var input = nova_div.find('.qtd-produtos');
        
                        select.attr('id', 'produto_'+qtd_produtos);            
                        select.attr('name', 'produto_'+qtd_produtos);            
                        input.attr('name', 'produto_qtd_'+qtd_produtos);
                        input.addClass('qtd-produtos');       
        
                        div_produtos = nova_div;
        
                        $('#produtos').append(nova_div.html());
        
                        $(".qtd-produtos").mask("0#");

                    });
        
                    var qtd_pixels = 1;
        
                    var div_pixels = $('#pixels_div_1').parent().clone();
        
                    $('#add_pixel').on('click', function(){
        
                        qtd_pixels++;
        
                        var nova_div = div_pixels.clone();
        
                        var select = nova_div.find('select');
        
                        select.attr('id', 'pixel_'+qtd_pixels);            
                        select.attr('name', 'pixel_'+qtd_pixels);            
        
                        div_pixels = nova_div;
        
                        $('#pixels').append(nova_div.html());
                    });
        
                    var qtd_brindes = 1;
        
                    var div_brindes = $('#brindes_div_1').parent().clone();
        
                    $('#add_brinde').on('click', function(){
        
                        qtd_brindes++;
        
                        var nova_div = div_brindes.clone();
        
                        var select = nova_div.find('select');
        
                        select.attr('id', 'brinde_'+qtd_brindes);            
                        select.attr('name', 'brinde_'+qtd_brindes);            
        
                        div_brindes = nova_div;
        
                        $('#brindes').append(nova_div.html());
                    });
        
                    var qtd_cupons = 1;
        
                    var div_cupons = $('#cupons_div_1').parent().clone();
        
                    $('#add_cupom').on('click', function(){
        
                        qtd_cupons++;
        
                        var nova_div = div_cupons.clone();
        
                        var select = nova_div.find('select');
        
                        select.attr('id', 'cupom_'+qtd_cupons);            
                        select.attr('name', 'cupom_'+qtd_cupons);            
        
                        div_cupons = nova_div;
        
                        $('#cupons').append(nova_div.html());
                    });

                }
            });

        });

        $('#adicionar_parceiro').on('click', function(){

            $('#modal_add_tamanho').addClass('modal-lg');
            $('#modal_add_tamanho').removeClass('modal-simple');

            $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

            $.ajax({
                method: "GET",
                url: "/parceiros/getformaddparceiro",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    $('#modal_add').hide();
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#valor_remuneracao').mask('0#');

                    $('#cadastrar').unbind('click');
    
                    $('#cadastrar').on('click',function(){

                        if($('#email_parceiro').val() == '' || $('#valor_remuneracao').val() == ''){
                            alertPersonalizado('error','Dados informados inválidos');
                            return false;
                        }

                        var form_data = new FormData(document.getElementById('cadastrar_parceiro'));
                        form_data.append('projeto',id_projeto);

                        $.ajax({
                            method: "POST",
                            url: "/parceiros/cadastrarparceiro",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            error: function(){
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','Parceiro adicionado!');
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
                    });
                }
            });

        });

        $("#tabela_produtos").DataTable( {
            bLengthChange: false,
            ordering: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/produtos/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
                { data: 'quantidade', name: 'quantidade'},
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

                var id_produto = '';

                $('.excluir_produto').on('click', function(){

                    id_produto = $(this).attr('produto');
                    var name = $(this).closest("tr").find("td:first-child").text();
                    $('#modal_excluir_titulo').html('Remover do projeto o produto '+name+'?');        

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function(){

                        $.ajax({
                            method: "POST",
                            url: "/produtos/deletarprodutoplano",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { projeto: id_projeto, produto: id_produto },
                            error: function(){
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','Produto removido!');
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
            
                    });
                });

            }

        });

        $("#tabela_planos").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/planos/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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

                var id_cupom = '';

                $('.excluir_plano').on('click', function(){

                    id_plano = $(this).attr('plano');
                    var name = $(this).closest("tr").find("td:first-child").text();
                    $('#modal_excluir_titulo').html('Remover do projeto o plano '+name+' ?');

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function(){

                        $.ajax({
                            method: "POST",
                            url: "/planos/deletarplano",
                            data: { id: id_plano },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                if(data != 'sucesso'){
                                    alertPersonalizado('error',data);
                                }
                                else{
                                    alertPersonalizado('success','Plano removido!');
                                }
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
                    });
                });

                $('.editar_plano').on('click', function(){
                    $('#modal_editar_tipo').addClass('modal-lg');
                    $('#modal_editar_tipo').removeClass('modal-simple');
                    id_plano = $(this).attr('plano');

                    $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "POST",
                        url: "/planos/getformeditarplano",
                        data: {id: id_plano, projeto: id_projeto},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

                            $(".qtd-produtos").mask("0#");

                            $("#frete_plano_editar").on("change", function(){

                                if($(this).val() == '0'){
                                    $("#div_frete_fixo_editar").hide();
                                    $("#div_valor_frete_fixo_editar").hide();
                                    $("#div_transportadora_editar").hide();
                                    $("#div_responsavel_frete_editar").hide();
                                    $("#div_id_plano_transportadora_editar").hide();
                                }
                                else{
                                    $("#div_frete_fixo_editar").show();
                                    if($("#frete_fixo_plano_editar").val() == '1'){
                                        $("#div_valor_frete_fixo_editar").show();
                                    }
                                    $("#div_transportadora_editar").show();
                                    $("#div_responsavel_frete_editar").show();
                                    if($("#transportadora_plano_editar").val() != '2'){
                                        $("#div_id_plano_transportadora_editar_editar").show();
                                    }
                                }
                            });

                            $("#frete_fixo_plano_editar").on("change", function(){
                                if($(this).val() == '1'){
                                    $("#div_valor_frete_fixo_editar").show();
                                }
                                else{
                                    $("#div_valor_frete_fixo_editar").hide();
                                }
                            });

                            $("#transportadora_plano_editar").on("change", function(){
                                if($(this).val() != '2'){
                                    $("#div_id_plano_transportadora_editar").show();
                                    $("#responsavel_frete_editar").append(new Option($(this).find("option:selected").text(), $(this).find("option:selected").text()));
                                }
                                else{
                                    $("#div_id_plano_transportadora_editar").hide();
                                    $("#responsavel_frete_editar option[value='Kapsula']").remove();
                                    $("#responsavel_frete_editar option[value='Lift Gold']").remove();
                                }
                            });

                            $('#editar').unbind('click');

                            $('#editar').on('click',function(){

                                var form_data = new FormData(document.getElementById('editar_plano'));
                                form_data.append('projeto',id_projeto);

                                $.ajax({
                                    method: "POST",
                                    url: "/planos/editarplano",
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data: form_data,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    error: function(){
                                        alertPersonalizado('error','Ocorreu algum erro');
                                        $('#previewimage_plano_editar').imgAreaSelect({remove:true});
                                    },
                                    success: function(data){
                                        alertPersonalizado('success','Plano atualizado!');
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                        $('#previewimage_plano_editar').imgAreaSelect({remove:true});
                                    },
                                });
                            });

                            $('.dinheiro').mask('#.###,#0', {reverse: true});

                            var p = $("#previewimage_plano_editar");
                            $("#foto_plano_editar").on("change", function(){

                                var imageReader = new FileReader();
                                imageReader.readAsDataURL(document.getElementById("foto_plano_editar").files[0]);

                                imageReader.onload = function (oFREvent) {

                                    p.attr('src', oFREvent.target.result).fadeIn();

                                    p.on('load', function(){

                                        var img = document.getElementById('previewimage_plano_editar');
                                        var x1, x2, y1, y2;

                                        if (img.naturalWidth > img.naturalHeight) {
                                            y1 = Math.floor(img.naturalHeight / 100 * 10);
                                            y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                            x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                            x2 = x1 + (y2 - y1);
                                        }
                                        else {
                                            if (img.naturalWidth < img.naturalHeight) {
                                                x1 = Math.floor(img.naturalWidth / 100 * 10);;
                                                x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                                y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                                y2 = y1 + (x2 - x1);
                                            }
                                            else {
                                                x1 = Math.floor(img.naturalWidth / 100 * 10);
                                                x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                                y1 = Math.floor(img.naturalHeight / 100 * 10);
                                                y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                            }
                                        }

                                        $('input[name="foto_plano_editar_x1"]').val(x1);
                                        $('input[name="foto_plano_editar_y1"]').val(y1);
                                        $('input[name="foto_plano_editar_w"]').val(x2 - x1);
                                        $('input[name="foto_plano_editar_h"]').val(y2 - y1);

                                        $('#modal_editar').on('hidden.bs.modal', function () {
                                            $('#previewimage_plano_editar').imgAreaSelect({remove:true});
                                        });
                                        $('#previewimage_plano_editar').imgAreaSelect({remove:true});

                                        $('#previewimage_plano_editar').imgAreaSelect({
                                            x1: x1, y1: y1, x2: x2, y2: y2,
                                            aspectRatio: '1:1',
                                            handles: true,
                                            imageHeight: this.naturalHeight,
                                            imageWidth: this.naturalWidth,
                                            onSelectEnd: function (img, selection) {
                                                $('input[name="foto_plano_editar_x1"]').val(selection.x1);
                                                $('input[name="foto_plano_editar_y1"]').val(selection.y1);
                                                $('input[name="foto_plano_editar_w"]').val(selection.width);
                                                $('input[name="foto_plano_editar_h"]').val(selection.height);
                                            },
                                            parent: $('#conteudo_modal_editar'),
                                        });
                                    })
                                };

                            });

                            $("#selecionar_foto_plano_editar").on("click", function(){
                                $("#foto_plano_editar").click();
                            });

                            var qtd_produtos = '1';
                    
                            var div_produtos = $('#produtos_div_'+qtd_produtos).parent().clone();

                            $('#add_produto_plano').on('click', function(){

                                qtd_produtos++;
                    
                                var nova_div = div_produtos.clone();
                    
                                var select = nova_div.find('select');
                                var input = nova_div.find('.qtd-produtos');
                    
                                select.attr('id', 'produto_'+qtd_produtos);            
                                select.attr('name', 'produto_'+qtd_produtos);            
                                input.attr('name', 'produto_qtd_'+qtd_produtos);            
                                input.addClass('qtd-produtos');            

                                div_produtos = nova_div;

                                $('#produtos').append(nova_div.html());

                                $(".qtd-produtos").mask("0#");

                            });

                            var qtd_pixels = '1';

                            var div_pixels = $('#pixels_div_'+qtd_pixels).clone();

                            $('#add_pixel').on('click', function(){
                    
                                qtd_pixels++;
                    
                                var nova_div = div_pixels;
                    
                                var select = nova_div.find('select');
                    
                                select.attr('id', 'pixel_'+qtd_pixels);            
                                select.attr('name', 'pixel_'+qtd_pixels);         
                                select.val('');
                    
                                div_pixels = nova_div;
                    
                                $('#pixels').append('<div class="row">'+nova_div.html()+'</div>');
                            });

                            var qtd_brindes = '1';

                            var div_brindes = $('#brindes_div_'+qtd_brindes).clone();

                            $('#add_brinde').on('click', function(){
                    
                                qtd_brindes++;
                    
                                var nova_div = div_brindes;
                    
                                var select = nova_div.find('select');
                    
                                select.attr('id', 'brinde_'+qtd_brindes);            
                                select.attr('name', 'brinde_'+qtd_brindes);            
                    
                                div_brindes = nova_div;
                    
                                $('#brindes').append('<div class="row">'+nova_div.html()+'</div>');
                            });

                            var qtd_cupons = '1';

                            var div_cupons = $('#cupons_div_'+qtd_cupons).clone();

                            $('#add_cupom').on('click', function(){
                    
                                qtd_cupons++;
                    
                                var nova_div = div_cupons.clone();
                    
                                var select = nova_div.find('select');
                    
                                select.attr('id', 'cupom_'+qtd_cupons);            
                                select.attr('name', 'cupom_'+qtd_cupons);            
                    
                                div_cupons = nova_div;
                    
                                $('#cupons').append('<div class="row">'+nova_div.html()+'</div>');
                            });

                        }
                    });
                });

            }

        });

        $("#tabela_pixels").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/pixels/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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

                $('.detalhes_pixel').on('click', function() {
                    var pixel = $(this).attr('pixel');
                    $('#modal_detalhes_titulo').html('Detalhes do pixel');
                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    var data = { id_pixel : pixel };
                    $.post("/pixels/detalhe", data)
                    .then( function(response, status){
                        $('#modal_detalhes_body').html(response);
                    });
                });

                var id_pixel = '';

                $('.excluir_pixel').on('click', function(){

                    id_pixel = $(this).attr('pixel');
                    var name = $(this).closest("tr").find("td:first-child").text();
                    $('#modal_excluir_titulo').html('Remover do projeto o pixel '+name+' ?');        

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function(){

                        $.ajax({
                            method: "GET",
                            url: "/pixels/deletarpixel/"+id_pixel,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','Pixel removido!');
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });

                    });

                });

                $('.editar_pixel').on('click', function(){

                    $('#modal_editar_tipo').addClass('modal-simple');
                    $('#modal_editar_tipo').removeClass('modal-lg');

                    id_pixel = $(this).attr('pixel');

                    $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "POST",
                        url: "/pixels/getformeditarpixel",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {id: id_pixel},
                        error: function(){
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);
        
                            $('#editar').unbind('click');
            
                            $('#editar').on('click',function(){
        
                                var paramObj = {};
                                $.each($('#editar_pixel').serializeArray(), function(_, kv) {
                                    if (paramObj.hasOwnProperty(kv.name)) {
                                        paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
                                        paramObj[kv.name].push(kv.value);
                                    }
                                    else {
                                        paramObj[kv.name] = kv.value;
                                    }
                                });
                                paramObj['id'] = id_pixel;

                                $.ajax({
                                    method: "POST",
                                    url: "/pixels/editarpixel",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: { pixelData: paramObj },
                                    error: function(){
                                        alertPersonalizado('error','Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        alertPersonalizado('success','Pixel atualizado!');
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                    }
                                });
                            });
                        }
                    });

                });

            }

        });

        $("#tabela_sms").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/sms/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'plano', name: 'plano'},
                { data: function(data){
                    return data.evento.replace(new RegExp('_', 'g'), ' ');   
                }, name: 'evento'},
                { data: function(data){
                    return data.tempo + ' ' + data.periodo;
                }, name: 'tempo'},
                { data: 'mensagem', name: 'mensagem'},
                { data: function(data){
                    if(data.status)
                        return 'Ativo';
                    else
                        return 'Inativo';   
                }, name: 'status'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
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

                $('#modal_editar_tipo').addClass('modal-simple');
                $('#modal_editar_tipo').removeClass('modal-lg');

                var id_sms = '';

                $('.detalhes_sms').on('click', function() {
                    var sms = $(this).attr('sms');

                    $('#modal_detalhes_titulo').html('Detalhes do sms');

                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                    $.ajax({
                        method: "POST",
                        url: "/sms/detalhe",
                        data: { id_sms : sms },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_detalhes_body').html(data);
                        }
                    });

                });

                $('.excluir_sms').on('click', function(){

                    id_sms = $(this).attr('sms');
                    var name = $(this).closest("tr").find("td:first-child").text();
                    $('#modal_excluir_titulo').html('Remover do projeto o sms para o plano '+name+' ?');        

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function(){

                        $.ajax({
                            method: "GET",
                            url: "/sms/deletarsms/"+id_sms,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','SMS removido!');
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });

                    });

                });

                $('.editar_sms').on('click', function(){

                    id_sms = $(this).attr('sms');

                    $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "POST",
                        url: "/sms/getformeditarsms",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {id: id_sms, projeto : id_projeto},
                        error: function(){
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

                            $('#tempo_sms_editar').mask('0#');

                            $('#editar').unbind('click');

                            $('#editar').on('click',function(){

                                var form_data = new FormData(document.getElementById('editar_sms'));
                                form_data.append('projeto',id_projeto);
        
                                $.ajax({
                                    method: "POST",
                                    url: "/sms/editarsms",
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data: form_data,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    error: function(){
                                        alertPersonalizado('error','Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        alertPersonalizado('success','SMS atualizado!');
                                        $('#modal_add').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                    },
                                });
                            });
                        }
                    });
                });
            }
        });

        $("#tabela_brindes").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/brindes/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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

                var id_brinde = '';

                $('.excluir_brinde').on('click', function(){

                    id_brinde = $(this).attr('brinde');
                    var name = $(this).closest("tr").find("td:first-child").text();
                    $('#modal_excluir_titulo').html('Remover do projeto o brinde '+name+' ?');

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function(){

                        $.ajax({
                            method: "POST",
                            url: "/brindes/deletarbrinde",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { id: id_brinde },
                            error: function(){
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','Brinde removido!');
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
                    });
                });
            

                $('.editar_brinde').on('click', function(){

                    $('#modal_editar_tipo').addClass('modal-simple');
                    $('#modal_editar_tipo').removeClass('modal-lg');

                    id_brinde = $(this).attr('brinde');

                    $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "POST",
                        url: "/brindes/getformeditarbrinde",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {id: id_brinde},
                        error: function(){
                            $('#modal_editar').hide();
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);
        
                            $('#editar').unbind('click');
            
                            $('#editar').on('click',function(){
        
                                var form_data = new FormData(document.getElementById('editar_brinde'));
                                form_data.append('projeto',id_projeto);

                                $.ajax({
                                    method: "POST",
                                    url: "/brindes/editarbrinde",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data:  form_data,
                                    error: function(){
                                        $('#modal_editar').hide();
                                        alertPersonalizado('error','Ocorreu algum erro');
                                        $('#previewimage_brinde_editar').imgAreaSelect({remove:true});
                                    },
                                    success: function(data){
                                        alertPersonalizado('success','Brinde atualizado!');
                                        $('#modal_editar').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                        $('#previewimage_brinde_editar').imgAreaSelect({remove:true});
                                    }
                                });
                            });

                            var p = $("#previewimage_brinde_editar");
                            $("#foto_brinde_editar").on("change", function(){

                                var imageReader = new FileReader();
                                imageReader.readAsDataURL(document.getElementById("foto_brinde_editar").files[0]);
        
                                imageReader.onload = function (oFREvent) {
                                    p.attr('src', oFREvent.target.result).fadeIn();
        
                                    p.on('load', function(){
                    
                                        var img = document.getElementById('previewimage_brinde_editar');
                                        var x1, x2, y1, y2;

                                        if (img.naturalWidth > img.naturalHeight) {
                                            y1 = Math.floor(img.naturalHeight / 100 * 10);
                                            y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                            x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                            x2 = x1 + (y2 - y1);
                                        }
                                        else {
                                            if (img.naturalWidth < img.naturalHeight) {
                                                x1 = Math.floor(img.naturalWidth / 100 * 10);;
                                                x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                                y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                                y2 = y1 + (x2 - x1);
                                            }
                                            else {
                                                x1 = Math.floor(img.naturalWidth / 100 * 10);
                                                x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                                y1 = Math.floor(img.naturalHeight / 100 * 10);
                                                y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                            }
                                        }
 
                                        $('input[name="foto_brinde_editar_x1"]').val(x1);
                                        $('input[name="foto_brinde_editar_y1"]').val(y1);
                                        $('input[name="foto_brinde_editar_w"]').val(x2 - x1);
                                        $('input[name="foto_brinde_editar_h"]').val(y2 - y1);

                                        $('#modal_editar').on('hidden.bs.modal', function () {
                                            $('#previewimage_brinde_editar').imgAreaSelect({remove:true});
                                        });
                                        $('#previewimage_brinde_editar').imgAreaSelect({remove:true});
        
                                        $('#previewimage_brinde_editar').imgAreaSelect({
                                            x1: x1, y1: y1, x2: x2, y2: y2,
                                            aspectRatio: '1:1',
                                            handles: true,
                                            imageHeight: this.naturalHeight,
                                            imageWidth: this.naturalWidth,
                                            onSelectEnd: function (img, selection) {
                                                $('input[name="foto_brinde_editar_x1"]').val(selection.x1);
                                                $('input[name="foto_brinde_editar_y1"]').val(selection.y1);
                                                $('input[name="foto_brinde_editar_w"]').val(selection.width);
                                                $('input[name="foto_brinde_editar_h"]').val(selection.height);
                                            },
                                            parent: $('#conteudo_modal_editar'),
                                        });
                                    })
                                };
                    
                            });
                    
                            $("#selecionar_foto_brinde_editar").on("click", function(){
                                $("#foto_brinde_editar").click();
                            });
                            
                            $('#tipo_brinde').on('change', function(){
                    
                                if($(this).val() == 1){
                                    $('#div_input_arquivo').show();
                                    $('#div_input_link').hide();
                                }
                                if($(this).val() == 2){
                                    $('#div_input_arquivo').hide();
                                    $('#div_input_link').show();
                    
                                }
                            });
                    
                            var tipo_brinde = '1';
                    
                            if(tipo_brinde == '1'){
                                $('#div_input_arquivo').show();
                            }
                            if(tipo_brinde == '2'){
                                $('#div_input_link').show();
                            }

                        }
                    });
        
        
                });
            }

        });

        $("#tabela_cuponsdesconto").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/cuponsdesconto/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'nome', name: 'nome'},
                { data: 'tipo', name: 'tipo'},
                { data: 'valor', name: 'valor'},
                { data: 'cod_cupom', name: 'cod_cupom'},
                { data: 'status', name: 'status'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
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


                var id_cupom = '';

                $('.excluir_cupom').on('click', function(){

                    id_cupom = $(this).attr('cupom');
                    var name = $(this).closest("tr").find("td:first-child").text();
                    $('#modal_excluir_titulo').html('Remover do projeto o cupom '+name+' ?');

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function(){

                        $.ajax({
                            method: "POST",
                            url: "/cuponsdesconto/deletarcupom",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { id: id_cupom },
                            error: function(){
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','Cupom removido!');
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
                    });
                });

                $('.editar_cupom').on('click', function(){

                    $('#modal_editar_tipo').addClass('modal-simple');
                    $('#modal_editar_tipo').removeClass('modal-lg');

                    id_cupom = $(this).attr('cupom');

                    $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "POST",
                        url: "/cuponsdesconto/getformeditarcupom",
                        data: {id: id_cupom},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            $('#modal_editar').hide();
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

                            $("#valor_cupom_editar").mask("0#");

                            $('#editar').unbind('click');

                            $('#editar').on('click',function(){

                                var form_data = new FormData(document.getElementById('editar_cupom'));
                                form_data.append('projeto',id_projeto);

                                $.ajax({
                                    method: "POST",
                                    url: "/cuponsdesconto/editarcupom",
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data: form_data,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    error: function(){
                                        alertPersonalizado('error','Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        alertPersonalizado('success','Cupom atualizado!');
                                        $('#modal_add').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                    },
                                });
                            });
                        }
                    });
                });
            }
        });

        $("#tabela_dominios").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/dominios/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'dominio', name: 'dominio'},
                { data: 'ip_dominio', name: 'ip_dominio'},
                { data: 'status', name: 'status'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
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

                var id_dominio = '';

                $("#excluir_dominio").unbind("click");
                $('.excluir_dominio').on('click', function(){

                    id_dominio = $(this).attr('dominio');
                    var name = $(this).closest("tr").find("td:first-child").text();
                    $('#modal_excluir_titulo').html('Remover do projeto o dominio '+name+'?');        

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function(){

                        $.ajax({
                            method: "POST",
                            url: "/dominios/deletardominio",
                            data: { id: id_dominio, projeto: id_projeto },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                if(data != 'sucesso'){
                                    alertPersonalizado('error',data);
                                }
                                alertPersonalizado('success','Domínio removido!');
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
    
                    });

                });    

                $('#editar').unbind('click');

                $('.editar_dominio').on('click', function(){

                    $('#modal_editar_tipo').addClass('modal-lg');
                    $('#modal_editar_tipo').removeClass('modal-simple');

                    id_dominio = $(this).attr('dominio');

                    $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "POST",
                        url: "/dominios/getformeditardominio",
                        data: {id: id_dominio, projeto: id_projeto},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

                            var qtd_novos_registros = 1;

                            $('#ip_dominio_editar').mask('0ZZ.0ZZ.0ZZ.0ZZ', {translation: {'Z': {pattern: /[0-9]/, optional: true}}});

                            $("#bt_adicionar_entrada").on("click", function(){
                    
                                $("#novos_registros").after("<tr registro='"+qtd_novos_registros+"'><td>"+$("#tipo_registro").val()+"</td><td>"+$("#nome_registro").val()+"</td><td>"+$("#valor_registro").val()+"</td><td><button type='button' class='btn btn-danger remover_entrada'>Remover</button></td></tr>");
                    
                                $('#editar_dominio').append('<input type="hidden" name="tipo_registro_'+qtd_novos_registros+'" id="tipo_registro_'+qtd_novos_registros+'" value="'+$("#tipo_registro").val()+'" />');
                                $('#editar_dominio').append('<input type="hidden" name="nome_registro_'+qtd_novos_registros+'" id="nome_registro_'+qtd_novos_registros+'" value="'+$("#nome_registro").val()+'" />');
                                $('#editar_dominio').append('<input type="hidden" name="valor_registro_'+qtd_novos_registros+'" id="valor_registro_'+( qtd_novos_registros++) +'" value="'+$("#valor_registro").val()+'" />');
                    
                                $(".remover_entrada").unbind("click");
                    
                                $(".remover_entrada").on("click", function(){
                    
                                    var novo_registro = $(this).parent().parent();
                                    var id_registro = novo_registro.attr('registro');
                                    novo_registro.remove();
                                    alert(id_registro);
                                    $("#tipo_registro_"+id_registro).remove();
                                    $("#nome_registro_"+id_registro).remove();
                                    $("#valor_registro_"+id_registro).remove();
                                });
                    
                                $("#tipo_registro").val("A");
                                $("#nome_registro").val("");
                                $("#valor_registro").val("");
                            });
                    
                            $(".remover_registro").on("click", function(){
                    
                                var id_registro = $(this).attr('id-registro');
                    
                                var row = $(this).parent().parent();
                    
                                $.ajax({
                                    method: "POST",
                                    url: "/dominios/removerregistrodns",
                                    data: {
                                        id_registro: id_registro, 
                                        id_dominio: $("#id_dominio").val()
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    error: function(){
                                        alert('error','Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        if(data == 'sucesso'){
                                            row.remove();
                                            alertPersonalizado('success','Registro removido!');
                                        }
                                        else{
                                            alertPersonalizado('error',data);
                                        }
                                    },
                                });
                    
                            });

                            $('#editar').unbind('click');

                            $('#editar').on('click',function(){

                                var form_data = new FormData(document.getElementById('editar_dominio'));
                                form_data.append('projeto',id_projeto);

                                $.ajax({
                                    method: "POST",
                                    url: "/dominios/editardominio",
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data: form_data,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    error: function(){
                                        alertPersonalizado('error','Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        if(data == 'sucesso')
                                            alertPersonalizado('success','Domínio atualizado!');
                                        else
                                            alertPersonalizado('error',data);
                                        
                                        $('#modal_add').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                    },
                                });
                            });
                        }
                    });
                });

                $('.detalhes_dominio').unbind('click');

                $('.detalhes_dominio').on('click', function() {
                    var id_dominio = $(this).attr('dominio');

                    $('#modal_detalhes_titulo').html('Detalhes do domínio');
                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    $.ajax({
                        method: "POST",
                        url: "/dominios/detalhesdominio",
                        data: {dominio: id_dominio},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(response){
                            $('#modal_detalhes_body').html(response);
                        }
                    });
                });

            }

        });

        $("#tabela_layouts").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '/layouts/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: 'descricao', name: 'descricao'},
                { data: 'logo', name: 'logo'},
                {{--  { data: 'estilo', name: 'estilo'},
                { data: 'cor1', name: 'cor1'},
                { data: 'cor2', name: 'cor2'},
                { data: 'botao', name: 'botao'},  --}}
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
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

                $('.editar_layout').on('click', function(){

                    $('#modal_editar_tipo').addClass('modal-lg');
                    $('#modal_editar_tipo').removeClass('modal-simple');

                    id_layout = $(this).attr('layout');

                    $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "POST",
                        url: "/layouts/getformeditarlayout",
                        data: {id: id_layout, projeto: id_projeto},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            $('#modal_editar').hide();
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

                            atualizarPreView();

                            function atualizarPreView(){

                                $('#form_preview_editar').submit();
                            }

                            $("#formato_logo_editar").on("change", function(){
                                $("#foto_checkout").val('');
                                $('#previewimage_checkout_editar').imgAreaSelect({remove:true});
                                $('#previewimage_checkout_editar').attr('src', '#');
                                $("#preview_logo_formato").val($(this).val());
                            });
        
                            var p = $("#previewimage_checkout_editar");
                            $("#foto_checkout").on("change", function(){
        
                                var input = $(this).clone();
                                $('#form_preview_editar').append(input);
        
                                var imageReader = new FileReader();
                                imageReader.readAsDataURL(document.getElementById("foto_checkout").files[0]);
        
                                imageReader.onload = function (oFREvent) {
                                    p.attr('src', oFREvent.target.result).fadeIn();
        
                                    p.on('load', function(){
                    
                                        var img = document.getElementById('previewimage_checkout_editar');
                                        var x1, x2, y1, y2;
        
                                        if($("#formato_logo_editar").val() == 'quadrado'){
                                            if (img.naturalWidth > img.naturalHeight) {
                                                y1 = Math.floor(img.naturalHeight / 100 * 10);
                                                y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                                x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                                x2 = x1 + (y2 - y1);
                                            }
                                            else {
                                                if (img.naturalWidth < img.naturalHeight) {
                                                    x1 = Math.floor(img.naturalWidth / 100 * 10);;
                                                    x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                                    y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                                    y2 = y1 + (x2 - x1);
                                                }
                                                else {
                                                    x1 = Math.floor(img.naturalWidth / 100 * 10);
                                                    x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                                                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                                }
                                            }
                                        }
                                        else{
                                            if (img.naturalWidth > img.naturalHeight) {
                                                y1 = Math.floor(img.naturalHeight / 100 * 10);
                                                y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                                x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1));
                                                if(x1 < 0)
                                                    x1 = 2;
                                                x2 = x1 + ((y2 - y1) * 2);
                                                if(x2 > img.naturalWidth){
                                                    x2 = img.naturalWidth - 2;
                                                    y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 4);
                                                    y2 = y1 + Math.floor(( x2 - x1) / 2);
                                                }
                                            }
                                            else {
                                                x1 = 2
                                                x2 = img.naturalWidth - 2;
                                                y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 4);
                                                y2 = y1 + Math.floor((x2 - x1) / 2 );
                                            }
                                        }

                                        $('input[name="foto_checkout_editar_x1"]').val(x1);
                                        $('input[name="foto_checkout_editar_y1"]').val(y1);
                                        $('input[name="foto_checkout_editar_w"]').val(x2 - x1);
                                        $('input[name="foto_checkout_editar_h"]').val(y2 - y1);
                                        $('input[name="preview_logo_x1"]').val(x1);
                                        $('input[name="preview_logo_y1"]').val(y1);
                                        $('input[name="preview_logo_w"]').val(x2 - x1);
                                        $('input[name="preview_logo_h"]').val(y2 - y1);
        
                                        var formato = '';
                                        if($("#formato_logo_editar").val() == 'quadrado'){
                                            formato = '1:1';
                                        }
                                        else{
                                            formato = '2:1';
                                        }
        
                                        $('#previewimage_checkout_editar').imgAreaSelect({remove:true});
                                        $('#modal_editar').on('hidden.bs.modal', function () {
                                            $('#previewimage_checkout_editar').imgAreaSelect({remove:true});
                                        });
                                        $('#previewimage_checkout_editar').imgAreaSelect({
                                            x1: x1, y1: y1, x2: x2, y2: y2,
                                            aspectRatio: formato,
                                            handles: true,
                                            imageHeight: this.naturalHeight,
                                            imageWidth: this.naturalWidth,
                                            onSelectEnd: function (img, selection) {
                                                $('input[name="foto_checkout_editar_x1"]').val(selection.x1);
                                                $('input[name="foto_checkout_editar_y1"]').val(selection.y1);
                                                $('input[name="foto_checkout_editar_w"]').val(selection.width);
                                                $('input[name="foto_checkout_editar_h"]').val(selection.height);
                                                $('input[name="preview_logo_x1"]').val(selection.x1);
                                                $('input[name="preview_logo_y1"]').val(selection.y1);
                                                $('input[name="preview_logo_w"]').val(selection.width);
                                                $('input[name="preview_logo_h"]').val(selection.height);
                                            },
                                            parent: $('#conteudo_modal_editar'),
                                        });
                                    })
                                };
                    
                            });

                            $("#selecionar_foto_checkout_editar").on("click", function(){
                                $("#foto_checkout").click();
                            });

                            $("#atualizar_preview_editar").on("click", function(){
                                atualizarPreView();
                            });
        
                            $('#editar').unbind('click');

                            $('#editar').on('click',function(){

                                var form_data = new FormData(document.getElementById('editar_layout'));
                                form_data.append('projeto',id_projeto);

                                $.ajax({
                                    method: "POST",
                                    url: "/layouts/editarlayout",
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data: form_data,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    error: function(){
                                        alertPersonalizado('error','Ocorreu algum erro');
                                        $('#previewimage_checkout_editar').imgAreaSelect({remove:true});
                                    },
                                    success: function(data){
                                        alertPersonalizado('success','Layout atualizado!');
                                        $('#modal_add').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                        $('#previewimage_checkout_editar').imgAreaSelect({remove:true});
                                    },
                                });
                            });
                        }
                    });
                });

                $('.excluir_layout').on('click', function(){

                    id_layout = $(this).attr('layout');

                    $('#modal_excluir_titulo').html('Remover layout do projeto ?');

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function(){

                        $.ajax({
                            method: "POST",
                            url: "/layouts/removerlayout",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { id: id_layout },
                            error: function(){
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                alertPersonalizado('success','Layout removido!');
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
                    });
                });

            }

        });

        $("#tabela_parceiros").DataTable( {
            bLengthChange: false,
            ordering: false,
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: '/parceiros/data-source',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {projeto: id_projeto}
            },
            columns: [
                { data: function(data){
                    if(data.name == null)
                      return 'Pendente';
                    else
                      return data.name;
                }, name: 'name'},
                { data: 'tipo', name: 'tipo'},
                { data: 'status', name: 'status'},
                { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
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

                $('.detalhes_parceiro').unbind('click');

                $('.detalhes_parceiro').on('click', function() {
                    var id_parceiro = $(this).attr('parceiro');

                    $('#modal_detalhes_titulo').html('Detalhes da parceiro');
                    $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    $.ajax({
                        method: "POST",
                        url: "/parceiros/detalhesparceiro",
                        data: {parceiro: id_parceiro},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(){
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(response){
                            $('#modal_detalhes_body').html(response);
                        }
                    });
                });

                var id_parceiro = '';

                $('.excluir_parceiro').on('click', function(){

                    id_parceiro = $(this).attr('parceiro');

                    $('#modal_excluir_titulo').html('Remover parceiro do projeto ?');

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function(){

                        $.ajax({
                            method: "POST",
                            url: "/parceiros/removerparceiro",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { id: id_parceiro },
                            error: function(){
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error','Ocorreu algum erro');
                            },
                            success: function(data){
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
                    });
                });

                $('.editar_parceiro').on('click', function(){

                    $('#modal_editar_tipo').addClass('modal-simple');
                    $('#modal_editar_tipo').removeClass('modal-lg');

                    id_parceiro = $(this).attr('parceiro');

                    $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "POST",
                        url: "/parceiros/getformeditarparceiro",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {id_parceiro: id_parceiro},
                        error: function(){
                            alertPersonalizado('error','Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

                            $("#valor_parceiro_editar").mask("0#");

                            $('#editar').unbind('click');
            
                            $('#editar').on('click',function(){
        
                                var form_data = new FormData(document.getElementById('editar_parceiro'));
                                form_data.append('projeto',id_projeto);

                                $.ajax({
                                    method: "POST",
                                    url: "/parceiros/editarparceiro",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data:  form_data,
                                    error: function(){
                                        $('#modal_editar').hide();
                                        alertPersonalizado('error','Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        $('#modal_editar').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                    }
                                });
                            });
                        }
                    });
                });
            }
        });

        $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {

            $($.fn.dataTable.tables( true ) ).css('width', '100%');
            $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();

        });

        function updateConfiguracoes(){

            $.ajax({
                method: "GET",
                url: "/projetos/getconfiguracoesprojeto/"+id_projeto,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
    
                    $('#configuracoes_projeto').html(data);
    
                    $("#porcentagem_afiliados").mask("0#");

                    var p = $("#previewimage");
                    $("#foto_projeto").on("change", function(){

                        var imageReader = new FileReader();
                        imageReader.readAsDataURL(document.getElementById("foto_projeto").files[0]);
            
                        imageReader.onload = function (oFREvent) {
                            p.attr('src', oFREvent.target.result).fadeIn();

                            p.on('load', function(){
                    
                                var img = document.getElementById('previewimage');
                                var x1, x2, y1, y2;

                                if (img.naturalWidth > img.naturalHeight) {
                                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                    x2 = x1 + (y2 - y1);
                                }
                                else {
                                    if (img.naturalWidth < img.naturalHeight) {
                                        x1 = Math.floor(img.naturalWidth / 100 * 10);;
                                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                        y2 = y1 + (x2 - x1);
                                    }
                                    else {
                                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    }
                                }

                                $('input[name="foto_x1"]').val(x1);
                                $('input[name="foto_y1"]').val(y1);
                                $('input[name="foto_w"]').val(x2 - x1);
                                $('input[name="foto_h"]').val(y2 - y1);

                                $('#previewimage').imgAreaSelect({remove:true});

                                $('#previewimage').imgAreaSelect({
                                    x1: x1, y1: y1, x2: x2, y2: y2,
                                    aspectRatio: '1:1',
                                    handles: true,
                                    imageHeight: this.naturalHeight,
                                    imageWidth: this.naturalWidth,
                                    onSelectEnd: function (img, selection) {
                                        $('input[name="foto_x1"]').val(selection.x1);
                                        $('input[name="foto_y1"]').val(selection.y1);
                                        $('input[name="foto_w"]').val(selection.width);
                                        $('input[name="foto_h"]').val(selection.height);
                                    }
                                });
                            });
                        };
            
                    });
            
                    $("#selecionar_foto").on("click", function(){
                        $("#foto_projeto").click();
                    });

                    $("#visibilidade").on("change", function(){
                        if($(this).val() == 'publico'){
                            $("#div_dados_afiliados").show();
                        }
                        else{
                            $("#div_dados_afiliados").hide();
                        }
                    });
            
                    $('#bt_atualizar_configuracoes').on('click',function(){

                        var form_data = new FormData(document.getElementById('atualizar_configuracoes'));
                        form_data.append('projeto',id_projeto);

                        $.ajax({
                            method: "POST",
                            url: "/projetos/editarprojeto",
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(){
                                alertPersonalizado('error','Ocorreu algum erro');
                                $('#previewimage').imgAreaSelect({remove:true});
                            },
                            success: function(data){
                                alertPersonalizado('success','Dados do projeto alterados!');
                                $('#previewimage').imgAreaSelect({remove:true});
                                updateConfiguracoes();
                            },
                        });

                    });

                }
            });
        }

        updateConfiguracoes();

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

