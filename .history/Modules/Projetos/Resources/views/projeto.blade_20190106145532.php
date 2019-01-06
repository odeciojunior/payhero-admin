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
                                </div>
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
                                        <a id="adicionar_dominio" class="btn btn-primary float-right"  data-toggle='modal' data-target='#modal_add' style="color: white">
                                            <i class='icon wb-user-add' aria-hidden='true'></i>
                                            Adicionar dominio
                                        </a>
                                        <thead class="bg-blue-grey-100">
                                            <th>Domínio</th>
                                            <th>Ip do domínio</th>
                                            <th style="width: 100px">Opções</th>
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
                            <div class="modal-content">
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
                                    <button id="cadastrar" type="button" class="btn btn-success" data-dismiss="modal">Adicionar</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal padrão para editar * no projeto -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_editar" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div id="modal_editar_tipo" class="modal-dialog modal-simple">
                            <div class="modal-content">
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
                                    <button id="editar" type="button" class="btn btn-success" data-dismiss="modal">Editar</button>
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
                    alert('Ocorreu algum erro');
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
                    $('#modal_add_produto').hide();
                    alert('Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_produto').hide();
                    $($.fn.dataTable.tables( true ) ).css('width', '100%');
                    $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
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
                    $('#modal_add').hide();
                    alert('Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#ip_dominio').mask('0ZZ.0ZZ.0ZZ.0ZZ', {translation: {'Z': {pattern: /[0-9]/, optional: true}}});

                    $('#cadastrar').unbind('click');

                    $('#cadastrar').on('click',function(){

                        if($('#ip_dominio').val() == '' || $('#dominio').val() == ''){
                            alert('Dados informados inválidos');
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
                                if(data != 'sucesso'){
                                    alert(data);
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
                    alert('Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#cadastrar').unbind('click');
    
                    $('#cadastrar').on('click',function(){

                        if($('#nome').val() == '' || $('#cod_pixel').val() == '' || $('#plataforma').val() == '' || $('#status_pixel').val() == ''){
                            alert('Dados informados inválidos');
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
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
                    alert('Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#cadastrar').unbind('click');
    
                    $('#cadastrar').on('click',function(){

                        if($('#titulo_brinde').val() == '' || $('#descricao_brinde').val() == '' || $('#foto_brinde').val() == '' || $('#tipo_brinde').val() == '' || $('#descricao_brinde').val() == '' || $('#foto_brinde').val() == ''){
                            alert('Dados informados inválidos');
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
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
                    alert('Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#numero_tempo').mask('0#');

                    $('#cadastrar').unbind('click');
    
                    $('#cadastrar').on('click',function(){

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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
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
                    alert('Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#cadastrar').unbind('click');

                    $('#cadastrar').on('click',function(){

                        if($('#titulo_brinde').val() == '' || $('#descricao_brinde').val() == '' || $('#foto_brinde').val() == '' || $('#tipo_brinde').val() == ''){
                            alert('Dados informados inválidos');
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            },
                        });
                    });

                    $("#foto_brinde").change(function(e) {

                        for (var i = 0; i < e.originalEvent.srcElement.files.length; i++) {
            
                            var file = e.originalEvent.srcElement.files[i];
            
                            if($('img').length != 0){
                                $('img').remove();
                            }
            
                            var img = document.createElement("img");
                            var reader = new FileReader();
            
                            reader.onloadend = function() {
                    
                                img.src = reader.result;
                    
                                $(img).on('load', function (){

                                    var width = img.width, height = img.height;

                                    if (img.width > img.height) {
                                        if (width > 400) {
                                          height *= 400 / img.width;
                                          width = 400;
                                        }
                                    } else {
                                        if (img.height > 200) {
                                          width *= 200 / img.height;
                                          height = 200;
                                        }
                                    }

                                    $(img).css({
                                        'width' : width+'px',
                                        'height' : height+'px',
                                        'margin-top' : '30px',
                                    });
                                })    
                            }
                            reader.readAsDataURL(file);

                            $(this).after(img);
                        }

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
                    alert('Ocorreu algum erro');
                },
                success: function(data){

                    $('#modal_add_body').html(data);

                    atualizarPreView();

                    function atualizarPreView(){

                        $('#form-preview').submit();
                    }

                    $('#estilo').on('change',function(){

                        $('#cores_multi_camada').hide();
                        $('#cores_padrao').hide();

                        if($(this).val() == 'Backgoud Multi Camada'){
                            $('#cor1-padrao').prop('required', false);
                            $('#cor1').prop('required', true);
                            $('#cor2').prop('required', true);
                            $('#cores_multi_camada').show();
                        }
                        else if($(this).val() == 'Padrao'){
                            $('#cor1-padrao').prop('required', true);
                            $('#cor1').prop('required', false);
                            $('#cor2').prop('required', false);
                            $('#cores_padrao').show();
                        }
            
                        $('#preview_estilo').val($(this).val());
            
                        atualizarPreView();
                    });

                    $('#botoes').on('change',function(){
                        $('#preview_botoes').val($(this).val());
                        atualizarPreView();
                    });

                    $('#cor1').on('blur',function(){
                        $('#preview_cor1').val($(this).val());
                        atualizarPreView();
                    });

                    $('#cor1-padrao').on('blur', function(){
                        $('#preview_cor1').val($(this).val());
                        atualizarPreView();
                    });

                    $('#cor2').on('blur',function(){
                        $('#preview_cor2').val($(this).val());
                        atualizarPreView();
                    });

                    $('#logo').on('change', function(){
                        var input = $(this).clone();
                        $('#form-preview').append(input);
                        atualizarPreView();
                    });

                    $('#cadastrar').unbind('click');

                    $('#cadastrar').on('click',function(){

                        if($('#descricao').val() == '' || $('#logo').val() == ''){
                            alert('Dados informados inválidos');
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            },
                        });
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
                    alert('Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#cadastrar').unbind('click');

                    $('#cadastrar').on('click',function(){

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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
                                $('#modal_add').hide();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();

                            },
                        });
                    });

                    $('.dinheiro').mask('#.###,#0', {reverse: true});

                    $("#plano_foto").change(function(e) {
        
                        for (var i = 0; i < e.originalEvent.srcElement.files.length; i++) {
        
                            var file = e.originalEvent.srcElement.files[i];
        
                            if($('img').length != 0){
                                $('img').remove();
                            }
        
                            var img = document.createElement("img");
                            var reader = new FileReader();
        
                            reader.onloadend = function() {
                    
                                img.src = reader.result;
                    
                                $(img).on('load', function (){
                    
                                    var width = img.width, height = img.height;
                    
                                    if (img.width > img.height) {
                                        if (width > 400) {
                                        height *= 400 / img.width;
                                        width = 400;
                                        }
                                    } else {
                                        if (img.height > 200) {
                                        width *= 200 / img.height;
                                        height = 200;
                                        }
                                    }
                        
                                    $(img).css({
                                        'width' : width+'px',
                                        'height' : height+'px',
                                        'margin-top' : '30px',
                                    });
                    
                                })    
                            }
                            reader.readAsDataURL(file);
                    
                            $(this).after(img);
                        }
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
        
                        div_produtos = nova_div;
        
                        $('#produtos').append(nova_div.html());
        
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
                    alert('Ocorreu algum erro');
                },
                success: function(data){
                    $('#modal_add_body').html(data);

                    $('#cadastrar').unbind('click');
    
                    $('#cadastrar').on('click',function(){

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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
                                if(data != 'sucesso'){
                                    alert(data);
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
                            $('#modal_editar').hide();
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

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
                                        alert('Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        $('#modal_add').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                    },
                                });
                            });

                            $('.dinheiro').mask('#.###,#0', {reverse: true});

                            $("#foto_plano").change(function(e) {
                    
                                for (var i = 0; i < e.originalEvent.srcElement.files.length; i++) {

                                    var file = e.originalEvent.srcElement.files[i];
                    
                                    if($('img').length != 0){
                                        $('img').remove();
                                    }
                    
                                    var img = document.createElement("img");
                                    var reader = new FileReader();
                    
                                    reader.onloadend = function() {
                    
                                        img.src = reader.result;
                    
                                        $(img).on('load', function (){
                    
                                            var width = img.width, height = img.height;
                    
                                            if (img.width > img.height) {
                                                if (width > 400) {
                                                  height *= 400 / img.width;
                                                  width = 400;
                                                }
                                            } else {
                                                if (img.height > 200) {
                                                  width *= 200 / img.height;
                                                  height = 200;
                                                }
                                            }
                    
                                            $(img).css({
                                                'width' : width+'px',
                                                'height' : height+'px',
                                                'margin-top' : '30px',
                                            });
                    
                                        })    
                                    }
                                    reader.readAsDataURL(file);
                    
                                    $(this).after(img);
                                }
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
                    
                                div_produtos = nova_div;
                    
                                $('#produtos').append(nova_div.html());
                    
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
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
                            $('#modal_editar').hide();
                            alert('Ocorreu algum erro');
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
                                        $('#modal_editar').hide();
                                        alert('Ocorreu algum erro');
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

        $("#tabela_sms").DataTable( {
            bLengthChange: false,
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
                            alert('Ocorreu algum erro');
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
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
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

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
                                        alert('Ocorreu algum erro');
                                    },
                                    success: function(data){
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
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
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);
        
                            $('#editar').unbind('click');
            
                            $('#editar').on('click',function(){
        
                                var paramObj = {};
                                $.each($('#editar_brinde').serializeArray(), function(_, kv) {
                                    if (paramObj.hasOwnProperty(kv.name)) {
                                        paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
                                        paramObj[kv.name].push(kv.value);
                                    }
                                    else {
                                        paramObj[kv.name] = kv.value;
                                    }
                                });
                                paramObj['id'] = id_brinde;
        
                                $.ajax({
                                    method: "POST",
                                    url: "/brindes/editarbrinde",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:  paramObj,
                                    error: function(){
                                        $('#modal_editar').hide();
                                        alert('Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        $('#modal_editar').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                    }
                                });
                            });

                            $("#foto_editar_brinde").change(function(e) {

                                for (var i = 0; i < e.originalEvent.srcElement.files.length; i++) {
                    
                                    var file = e.originalEvent.srcElement.files[i];
                    
                                    if($('img').length != 0){
                                        $('img').remove();
                                    }
                    
                                    var img = document.createElement("img");
                                    var reader = new FileReader();
                    
                                    reader.onloadend = function() {
                            
                                        img.src = reader.result;
                            
                                        $(img).on('load', function (){
                            
                                            var width = img.width, height = img.height;
                            
                                            if (img.width > img.height) {
                                                if (width > 400) {
                                                  height *= 400 / img.width;
                                                  width = 400;
                                                }
                                            } else {
                                                if (img.height > 200) {
                                                  width *= 200 / img.height;
                                                  height = 200;
                                                }
                                            }
                                
                                            $(img).css({
                                                'width' : width+'px',
                                                'height' : height+'px',
                                                'margin-top' : '30px',
                                            });
                    
                                        })    
                                    }
                                    reader.readAsDataURL(file);
                    
                                    $(this).after(img);
                                }
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
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
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

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
                                        alert('Ocorreu algum erro');
                                    },
                                    success: function(data){
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

                var id_dominio = '';

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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
                                if(data != 'sucesso'){
                                    alert(data);
                                }
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
    
                    });

                });    

                $('#editar').unbind('click');

                {{--  $('.editar_dominio').on('click', function(){

                    $('#modal_editar_tipo').addClass('modal-simple');
                    $('#modal_editar_tipo').removeClass('modal-lg');

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
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

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
                                        alert('Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        $('#modal_add').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                                    },
                                });
                            });
                        }
                    });
                });  --}}


            }

        });

        $("#tabela_layouts").DataTable( {
            bLengthChange: false,
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
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

                            atualizarPreView();

                            function atualizarPreView(){

                                $('#form-preview').submit();
                            }

                            $('#estilo').on('change',function(){

                                $('#cores_multi_camada').hide();
                                $('#cores_padrao').hide();

                                if($(this).val() == 'Backgoud Multi Camada'){
                                    $('#cor1-padrao').prop('required', false);
                                    $('#cor1-multi-camadas').prop('required', true);
                                    $('#cor2-multi-camadas').prop('required', true);
                                    $('#cores_multi_camada').show();
                                }
                                else if($(this).val() == 'Padrao'){
                                    $('#cor1-padrao').prop('required', true);
                                    $('#cor1-multi-camadas').prop('required', false);
                                    $('#cor2-multi-camadas').prop('required', false);
                                    $('#cores_padrao').show();
                                }
                    
                                $('#preview_estilo').val($(this).val());
                    
                                atualizarPreView();
                            });

                            $('#botoes').on('change',function(){
                                $('#preview_botoes').val($(this).val());
                                atualizarPreView();
                            });

                            $('#cor1-multi-camadas').on('blur',function(){
                                $('#preview_cor1').val($(this).val());
                                atualizarPreView();
                            });

                            $('#cor1-padrao').on('blur', function(){
                                $('#preview_cor1').val($(this).val());
                                atualizarPreView();
                            });

                            $('#cor2-multi-camadas').on('blur',function(){
                                $('#preview_cor2').val($(this).val());
                                atualizarPreView();
                            });

                            $('#logo').on('change', function(){
                                var input = $(this).clone();
                                $('#form-preview').append(input);
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
                                        alert('Ocorreu algum erro');
                                    },
                                    success: function(data){
                                        $('#modal_add').hide();
                                        $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
                                $('#fechar_modal_excluir').click();
                                $($.fn.dataTable.tables( true ) ).css('width', '100%');
                                $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
                            }
                        });
                    });
                });

            }

        });

        $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {

            $($.fn.dataTable.tables( true ) ).css('width', '100%');
            $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();

        });

        $("#tabela_parceiros").DataTable( {
            bLengthChange: false,
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
                { data: 'name', name: 'name'},
                { data: 'tipo', name: 'tipo'},
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
                            alert('Ocorreu algum erro');
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
                                alert('Ocorreu algum erro');
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
                            alert('Ocorreu algum erro');
                        },
                        success: function(data){
                            $('#modal_editar_body').html(data);

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
                                        alert('Ocorreu algum erro');
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

        function updateConfiguracoes(){

            $.ajax({
                method: "GET",
                url: "/projetos/getconfiguracoesprojeto/"+id_projeto,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(){
                    alert('Ocorreu algum erro');
                },
                success: function(data){
    
                    $('#configuracoes_projeto').html(data);
    
                    $("input:file").change(function(e) {

                        for (var i = 0; i < e.originalEvent.srcElement.files.length; i++) {

                            var file = e.originalEvent.srcElement.files[i];

                            if($('img').length != 0){
                                $('img').remove();
                            }

                            var img = document.createElement("img");
                            var reader = new FileReader();

                            reader.onloadend = function() {

                                img.src = reader.result;

                                $(img).on('load', function (){

                                    var width = img.width, height = img.height;

                                    if (img.width > img.height) {
                                        if (width > 400) {
                                            height *= 400 / img.width;
                                            width = 400;
                                        }
                                    } else {
                                        if (img.height > 200) {
                                            width *= 200 / img.height;
                                            height = 200;
                                        }
                                    }

                                    $(img).css({
                                        'width' : width+'px',
                                        'height' : height+'px',
                                        'margin-top' : '30px',
                                    });

                                })
                            }
                            reader.readAsDataURL(file);

                            $(this).after(img);
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
                                alert('Ocorreu algum erro');
                            },
                            success: function(data){
                                alert('Dados do projeto alterados!');
                                updateConfiguracoes();
                            },
                        });

                    });

                }
            });
        }

        updateConfiguracoes();

    });

  </script>

@endsection

