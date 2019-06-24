@extends("layouts.master")

@section('styles')

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

@endsection

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Projeto {{ $project->name }}</h1>
            <div class="page-header-actions">
                <a class="btn btn-success float-right" href="/projects">
                    Meus projetos
                </a>
            </div>
        </div>
        <div class="page-content container">
            <input type='hidden' id='project-id' value='{{Hashids::encode($project->id)}}'/>
            <div class="mb-30">
                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist" style="color: #ee535e">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-toggle="tab" href="#tab_info_geral"
                               aria-controls="tab_info_geral" role="tab">Informações gerais
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab-domains" class="nav-link" data-toggle="tab" href="#tab_domains"
                               aria-controls="tab_cupons" role="tab">Domínios
                            </a>
                        </li>
                        {{--<li class="nav-item" role="presentation">
                            <a class="nav-link" data-toggle="tab" href="#tab_layouts"
                               aria-controls="tab_cupons" role="tab">Layouts
                            </a>
                        </li>--}}
                        <li class="nav-item" role="presentation">
                            <a id="tab_pixels" class="nav-link" data-toggle="tab" href="#tab_pixels-panel"
                               aria-controls="tab_pixels" role="tab">Pixels
                            </a>
                        </li>
                        {{--@if($project->shopify_id == '')
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-toggle="tab" href="#tab_brindes"
                                   aria-controls="tab_brindes" role="tab">Brindes
                                </a>
                            </li>
                        @endif--}}
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-toggle="tab" href="#tab_cupons"
                               aria-controls="tab_cupons" role="tab">Cupons
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-toggle="tab" href="#tab_sms"
                               aria-controls="tab_cupons" role="tab">Notificações
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab-fretes" class="nav-link" data-toggle="tab" href="#tab-fretes-panel"
                               aria-controls="tab-fretes" role="tab">Frete
                            </a>
                        </li>
                        @if($project->shopify_id == '')
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-toggle="tab" href="#tab_planos" aria-controls="tab_planos" role="tab">
                                    Planos
                                </a>
                            </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <a id='tab-partners' class="nav-link" data-toggle="tab" href="#tab_partners"
                               aria-controls="tab_partners" role="tab">Parceiros
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-toggle="tab" href="#tab_configuracoes"
                               aria-controls="tab_cofiguracoes" role="tab">Configurações
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="panel pt-10 p-10" data-plugin="matchHeight">
                <div class="col-xl-12">
                    <div class="tab-content pt-20">
                        <div class="tab-content pt-20">
                            <!-- Painel de informações gerais -->
                            <div class="tab-pane active" id="tab_info_geral" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-3 col-xl-3">
                                        <img src="{{ $project->photo }}" alt="Imagem não encontrada" style="height: 200px; width: 200px"/>
                                    </div>
                                    <div class="col-lg-9 col-xl-9">
                                        <table class="table table-bordered table-hover table-striped">
                                            <tbody>
                                                <tr>
                                                    <td><b>Nome</b></td>
                                                    <td>{{ $project->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Descrição</b></td>
                                                    <td>{{ $project->description }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Visibilidade</b></td>
                                                    <td>{{ ($project->visibility == 'public') ? 'Projeto público' : 'Projeto privado' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Status</b></td>
                                                    <td>{{ $project->status == 1 ? 'Ativo' : 'Inativo' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Painel de Dominios -->
                            <div id="tab_domains" class="tab-pane" role="tabpanel">
                                @include('domains::index')
                            </div>
                            <!-- Painel de Pixels -->
                            <div class="tab-pane" id="tab_pixels-panel" role="tabpanel">
                                @include('pixels::index')
                            </div>
                            <!-- Painel de Cupons de Descontos -->

                            <div class="tab-pane" id="tab_cupons" role="tabpanel">
                                <table id="tabela_cuponsdesconto" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                    <a id="adicionar_cupom" class="btn btn-primary float-right" data-toggle='modal' data-target='#modal_add' style="color: white">
                                        <i class='icon wb-user-add' aria-hidden='true'></i> Adicionar cupom
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

                            <!-- Painel de Sms -->
                            <div class="tab-pane" id="tab_sms" role="tabpanel">
                                <table id="tabela_sms" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                    <a id="adicionar_sms" class="btn btn-primary float-right" data-toggle='modal' data-target='#modal_add' style="color: white">
                                        <i class='icon wb-user-add' aria-hidden='true'></i> Adicionar sms
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
                            <!-- Painel de Fretes -->
                            <div class="tab-pane" id="tab-fretes-panel" role="tabpanel">
                                @include('shipping::index')
                            </div>
                            <!--- Painel de Planos -->
                            <div class="tab-pane" id="tab_planos" role="tabpanel">
                                <table id="tabela_planos" class="table-bordered table-hover w-full" style="margin-top: 80px">
                                    <a id="adicionar_plano" class="btn btn-primary float-right" data-toggle='modal' data-target='#modal_add' style="color: white">
                                        <i class='icon wb-user-add' aria-hidden='true'></i> Adicionar plano
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
                            <!-- Painel de Parceiros -->
                            <div class="tab-pane" id="tab_partners" role="tabpanel">
                                @include('partners::index')
                            </div>
                            <!-- Painel de Configurações  Abre a tela edit-->
                            <div class="tab-pane" id="tab_configuracoes" role="tabpanel">
                                <div id="configuracoes_projeto" style="padding: 30px">
                                </div>
                            </div>
                        </div>

                        <!-- Modal padrão para adicionar Adicionar e Editar -->
                        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-content" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                            <div id="modal_add_size" class="modal-dialog modal-simple">
                                <div class="modal-content" id="conteudo_modal_add">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <h4 id="modal-title" class="modal-title" style="width: 100%; text-align:center"></h4>
                                    <div class="row">
                                        <div id="modal-add-body" class="form-group col-12">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="btn-modal" type="button" class="btn btn-success" data-dismiss="modal"></button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal padrão para excluir -->
                        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
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
        @push('scripts')
            <script src='{{asset('modules/partners/js/partners.js')}}'></script>
            <script src='{{asset('modules/Shipping/js/shipping.js')}}'></script>
            <script src='{{asset('modules/domain/js/domain.js')}}'></script>

            <script src='{{asset('modules/SmsMessage/js/smsMessage.js')}}'></script>
            <script src='{{asset('modules/Pixels/js/pixels.js')}}'></script>
            <script src='{{asset('modules/DiscountCoupons/js/discountCoupons.js')}}'></script>
            <script src='{{asset('modules/projects/js/projects.js')}}'></script>
            {{--@if(!$project->shopify_id)
                <script src='{{asset('modules/Gifts/js/gift.js')}}'></script>
            @endif--}}
        @endpush
        <script>
            $(document).ready(function () {

                var id_projeto = '{{Hashids::encode($project->id)}}';

                $('#adicionar_plano').on('click', function () {

                    $('#modal_add_size').addClass('modal-lg');
                    $('#modal_add_size').removeClass('modal-simple');

                    $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "POST",
                        url: "/planos/getformaddplano",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {projeto: id_projeto},
                        error: function () {
                            alertPersonalizado('error', 'Ocorreu algum erro');
                        },
                        success: function (data) {
                            $('#modal_add_body').html(data);

                            $(".qtd-produtos").mask("0#");
                            $('.dinheiro').mask('#.###,#0', {reverse: true});

                            $('#cadastrar').unbind('click');

                            $('#cadastrar').on('click', function () {

                                if ($('#nome_plano').val() == '' || $('#preco_plano').val() == '' || $('#descricao_plano').val() == '' || $('#status_plano').val() == '' || $('#frete_plano').val() == '' || $('#transportadora_plano').val() == '' || $('#frete_fixo_plano').val() == '') {
                                    alertPersonalizado('error', 'Dados informados inválidos');
                                    return false;
                                }

                                $('.loading').css("visibility", "visible");

                                var form_data = new FormData(document.getElementById('cadastrar_plano'));
                                form_data.append('projeto', id_projeto);

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
                                    error: function () {
                                        $('.loading').css("visibility", "hidden");
                                        alertPersonalizado('error', 'Ocorreu algum erro');
                                        $('#preview_image_plano_cadastrar').imgAreaSelect({remove: true});
                                    },
                                    success: function (data) {
                                        $('.loading').css("visibility", "hidden");
                                        alertPersonalizado('success', 'Plano adicionado!');
                                        $('#modal_add').hide();
                                        $($.fn.dataTable.tables(true)).css('width', '100%');
                                        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                                        $('#preview_image_plano_cadastrar').imgAreaSelect({remove: true});
                                    },
                                });
                            });

                            var p = $("#preview_image_plano_cadastrar");
                            $("#foto_plano_cadastrar").on("change", function () {

                                var imageReader = new FileReader();
                                imageReader.readAsDataURL(document.getElementById("foto_plano_cadastrar").files[0]);

                                imageReader.onload = function (oFREvent) {

                                    p.attr('src', oFREvent.target.result).fadeIn();

                                    p.on('load', function () {

                                        var img = document.getElementById('preview_image_plano_cadastrar');
                                        var x1, x2, y1, y2;

                                        if (img.naturalWidth > img.naturalHeight) {
                                            y1 = Math.floor(img.naturalHeight / 100 * 10);
                                            y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                            x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                            x2 = x1 + (y2 - y1);
                                        } else {
                                            if (img.naturalWidth < img.naturalHeight) {
                                                x1 = Math.floor(img.naturalWidth / 100 * 10);
                                                ;
                                                x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                                y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                                y2 = y1 + (x2 - x1);
                                            } else {
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
                                            $('#preview_image_plano_cadastrar').imgAreaSelect({remove: true});
                                        });
                                        $('#preview_image_plano_cadastrar').imgAreaSelect({remove: true});

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

                            $("#selecionar_foto_plano_cadastrar").on("click", function () {
                                $("#foto_plano_cadastrar").click();
                            });

                            var qtd_produtos = 1;

                            var div_produtos = $('#produtos_div_1').parent().clone();

                            $('#add_produtoplano').on('click', function () {

                                qtd_produtos++;

                                var nova_div = div_produtos.clone();

                                var select = nova_div.find('select');
                                var input = nova_div.find('.qtd-produtos');

                                select.attr('id', 'produto_' + qtd_produtos);
                                select.attr('name', 'produto_' + qtd_produtos);
                                input.attr('name', 'produto_qtd_' + qtd_produtos);
                                input.addClass('qtd-produtos');

                                div_produtos = nova_div;

                                $('#produtos').append(nova_div.html());

                                $(".qtd-produtos").mask("0#");

                            });

                            var qtd_brindes = 1;

                            var div_brindes = $('#brindes_div_1').parent().clone();

                            $('#add_brinde').on('click', function () {

                                qtd_brindes++;

                                var nova_div = div_brindes.clone();

                                var select = nova_div.find('select');

                                select.attr('id', 'brinde_' + qtd_brindes);
                                select.attr('name', 'brinde_' + qtd_brindes);

                                div_brindes = nova_div;

                                $('#brindes').append(nova_div.html());
                            });

                        }
                    });

                });

                $('#adicionar_parceiro').on('click', function () {

                    $('#modal_add_size').addClass('modal-lg');
                    $('#modal_add_size').removeClass('modal-simple');

                    $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

                    $.ajax({
                        method: "GET",
                        url: "/parceiros/getformaddparceiro",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            $('#modal_add').hide();
                            alertPersonalizado('error', 'Ocorreu algum erro');
                        },
                        success: function (data) {
                            $('#modal_add_body').html(data);

                            $('#valor_remuneracao').mask('0#');

                            $('#cadastrar').unbind('click');

                            $('#cadastrar').on('click', function () {

                                if ($('#email_parceiro').val() == '' || $('#valor_remuneracao').val() == '') {
                                    alertPersonalizado('error', 'Dados informados inválidos');
                                    return false;
                                }

                                $('.loading').css("visibility", "visible");

                                var form_data = new FormData(document.getElementById('cadastrar_parceiro'));
                                form_data.append('projeto', id_projeto);

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
                                    error: function () {
                                        $('.loading').css("visibility", "hidden");
                                        alertPersonalizado('error', 'Ocorreu algum erro');
                                    },
                                    success: function (data) {
                                        $('.loading').css("visibility", "hidden");
                                        alertPersonalizado('success', 'Parceiro adicionado!');
                                        $('#modal_add').hide();
                                        $($.fn.dataTable.tables(true)).css('width', '100%');
                                        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                                    }
                                });
                            });
                        }
                    });

                });

                $("#tabela_planos").DataTable({
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
                        {data: 'name', name: 'name'},
                        {
                            data: function (data) {
                                if (data.description == null)
                                    return '';
                                else
                                    return data.description.substr(0, 25);
                            }, name: 'description'
                        },
                        {data: 'code', name: 'code'},
                        {data: 'price', name: 'price'},
                        {data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
                    ],
                    "language": {
                        "sProcessing": "Carregando...",
                        "lengthMenu": "Apresentando _MENU_ registros por página",
                        "zeroRecords": "Nenhum registro encontrado",
                        "info": "Apresentando página _PAGE_ de _PAGES_",
                        "infoEmpty": "Nenhum registro encontrado",
                        "infoFiltered": "(filtrado por _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sSearch": "Procurar :",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Carregando...",
                        "oPaginate": {
                            "sFirst": "Primeiro",
                            "sLast": "Último",
                            "sNext": "Próximo",
                            "sPrevious": "Anterior",
                        },
                    },
                    "drawCallback": function () {

                        $('.detalhes_plano').on('click', function () {
                            var plano = $(this).attr('plano');
                            $('#modal_detalhes_titulo').html('Detalhes da plano');
                            $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                            var data = {id_plano: plano};
                            $.post("/planos/detalhe", data)
                                .then(function (response, status) {
                                    $('#modal_detalhes_body').html(response);
                                });
                        });

                        var id_cupom = '';

                        $('.excluir_plano').on('click', function () {

                            id_plano = $(this).attr('plano');
                            var name = $(this).closest("tr").find("td:first-child").text();
                            $('#modal_excluir_titulo').html('Remover do projeto o plano ' + name + ' ?');

                            $('#bt_excluir').unbind('click');

                            $('#bt_excluir').on('click', function () {

                                $('.loading').css("visibility", "visible");
                                $('#fechar_modal_excluir').click();

                                $.ajax({
                                    method: "POST",
                                    url: "/planos/deletarplano",
                                    data: {id: id_plano},
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    error: function () {
                                        $('.loading').css("visibility", "hidden");
                                        alertPersonalizado('error', 'Ocorreu algum erro');
                                    },
                                    success: function (data) {
                                        $('.loading').css("visibility", "hidden");
                                        if (data != 'sucesso') {
                                            alertPersonalizado('error', data);
                                        } else {
                                            alertPersonalizado('success', 'Plano removido!');
                                        }
                                        $($.fn.dataTable.tables(true)).css('width', '100%');
                                        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                                    }
                                });
                            });
                        });

                        $('.editar_plano').on('click', function () {
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
                                error: function () {
                                    alertPersonalizado('error', 'Ocorreu algum erro');
                                },
                                success: function (data) {
                                    $('#modal_editar_body').html(data);

                                    $(".qtd-produtos").mask("0#");

                                    $('#editar').unbind('click');

                                    $('#editar').on('click', function () {

                                        $('.loading').css("visibility", "visible");

                                        var form_data = new FormData(document.getElementById('editar_plano'));
                                        form_data.append('projeto', id_projeto);

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
                                            error: function () {
                                                $('.loading').css("visibility", "hidden");
                                                alertPersonalizado('error', 'Ocorreu algum erro');
                                                $('#previewimage_plano_editar').imgAreaSelect({remove: true});
                                            },
                                            success: function (data) {
                                                $('.loading').css("visibility", "hidden");
                                                alertPersonalizado('success', 'Plano atualizado!');
                                                $($.fn.dataTable.tables(true)).css('width', '100%');
                                                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                                                $('#previewimage_plano_editar').imgAreaSelect({remove: true});
                                            },
                                        });
                                    });

                                    $('.dinheiro').mask('#.###,#0', {reverse: true});

                                    var p = $("#previewimage_plano_editar");
                                    $("#foto_plano_editar").on("change", function () {

                                        var imageReader = new FileReader();
                                        imageReader.readAsDataURL(document.getElementById("foto_plano_editar").files[0]);

                                        imageReader.onload = function (oFREvent) {

                                            p.attr('src', oFREvent.target.result).fadeIn();

                                            p.on('load', function () {

                                                var img = document.getElementById('previewimage_plano_editar');
                                                var x1, x2, y1, y2;

                                                if (img.naturalWidth > img.naturalHeight) {
                                                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                                                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                                    x2 = x1 + (y2 - y1);
                                                } else {
                                                    if (img.naturalWidth < img.naturalHeight) {
                                                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                                                        ;
                                                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                                        y2 = y1 + (x2 - x1);
                                                    } else {
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
                                                    $('#previewimage_plano_editar').imgAreaSelect({remove: true});
                                                });
                                                $('#previewimage_plano_editar').imgAreaSelect({remove: true});

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

                                    $("#selecionar_foto_plano_editar").on("click", function () {
                                        $("#foto_plano_editar").click();
                                    });

                                    var qtd_produtos = '1';

                                    var div_produtos = $('#produtos_div_' + qtd_produtos).parent().clone();

                                    $('#add_produto_plano').on('click', function () {

                                        qtd_produtos++;

                                        var nova_div = div_produtos.clone();

                                        var select = nova_div.find('select');
                                        var input = nova_div.find('.qtd-produtos');

                                        select.attr('id', 'produto_' + qtd_produtos);
                                        select.attr('name', 'produto_' + qtd_produtos);
                                        input.attr('name', 'produto_qtd_' + qtd_produtos);
                                        input.addClass('qtd-produtos');

                                        div_produtos = nova_div;

                                        $('#produtos').append(nova_div.html());

                                        $(".qtd-produtos").mask("0#");

                                    });

                                    var qtd_brindes = '1';

                                    var div_brindes = $('#brindes_div_' + qtd_brindes).clone();

                                    $('#add_brinde').on('click', function () {

                                        qtd_brindes++;

                                        var nova_div = div_brindes;

                                        var select = nova_div.find('select');

                                        select.attr('id', 'brinde_' + qtd_brindes);
                                        select.attr('name', 'brinde_' + qtd_brindes);

                                        div_brindes = nova_div;

                                        $('#brindes').append('<div class="row">' + nova_div.html() + '</div>');
                                    });
                                }
                            });
                        });

                    }

                });

                $("#tabela_parceiros").DataTable({
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
                        {
                            data: function (data) {
                                if (data.name == null)
                                    return 'Pendente';
                                else
                                    return data.name;
                            }, name: 'name'
                        },
                        {data: 'type', name: 'type'},
                        {data: 'status', name: 'status'},
                        {data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
                    ],
                    "language": {
                        "sProcessing": "Carregando...",
                        "lengthMenu": "Apresentando _MENU_ registros por página",
                        "zeroRecords": "Nenhum registro encontrado",
                        "info": "Apresentando página _PAGE_ de _PAGES_",
                        "infoEmpty": "Nenhum registro encontrado",
                        "infoFiltered": "(filtrado por _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sSearch": "Procurar :",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Carregando...",
                        "oPaginate": {
                            "sFirst": "Primeiro",
                            "sLast": "Último",
                            "sNext": "Próximo",
                            "sPrevious": "Anterior",
                        },
                    },
                    "drawCallback": function () {

                        $('.detalhes_parceiro').unbind('click');

                        $('.detalhes_parceiro').on('click', function () {
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
                                error: function () {
                                    alertPersonalizado('error', 'Ocorreu algum erro');
                                },
                                success: function (response) {
                                    $('#modal_detalhes_body').html(response);
                                }
                            });
                        });

                        var id_parceiro = '';

                        $('.excluir_parceiro').on('click', function () {

                            id_parceiro = $(this).attr('parceiro');

                            $('#modal_excluir_titulo').html('Remover parceiro do projeto ?');

                            $('#bt_excluir').unbind('click');

                            $('#bt_excluir').on('click', function () {

                                $('.loading').css("visibility", "visible");
                                $('#fechar_modal_excluir').click();

                                $.ajax({
                                    method: "POST",
                                    url: "/parceiros/removerparceiro",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {id: id_parceiro},
                                    error: function () {
                                        $('.loading').css("visibility", "hidden");
                                        alertPersonalizado('error', 'Ocorreu algum erro');
                                    },
                                    success: function (data) {
                                        $('.loading').css("visibility", "hidden");
                                        $($.fn.dataTable.tables(true)).css('width', '100%');
                                        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                                    }
                                });
                            });
                        });

                        $('.editar_parceiro').on('click', function () {

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
                                error: function () {
                                    alertPersonalizado('error', 'Ocorreu algum erro');
                                },
                                success: function (data) {
                                    $('#modal_editar_body').html(data);

                                    $("#valor_parceiro_editar").mask("0#");

                                    $('#editar').unbind('click');

                                    $('#editar').on('click', function () {

                                        $('.loading').css("visibility", "visible");

                                        var form_data = new FormData(document.getElementById('editar_parceiro'));
                                        form_data.append('projeto', id_projeto);

                                        $.ajax({
                                            method: "POST",
                                            url: "/parceiros/editarparceiro",
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            processData: false,
                                            contentType: false,
                                            cache: false,
                                            data: form_data,
                                            error: function () {
                                                $('.loading').css("visibility", "hidden");
                                                $('#modal_editar').hide();
                                                alertPersonalizado('error', 'Ocorreu algum erro');
                                            },
                                            success: function (data) {
                                                $('.loading').css("visibility", "hidden");
                                                $('#modal_editar').hide();
                                                $($.fn.dataTable.tables(true)).css('width', '100%');
                                                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                                            }
                                        });
                                    });
                                }
                            });
                        });
                    }
                });

                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

                    $($.fn.dataTable.tables(true)).css('width', '100%');
                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();

                });

                $("#tipo_material_extra").on("change", function () {

                    $("#div_material_extra_imagem").css('display', 'none');
                    $("#div_material_extra_pdf").css('display', 'none');
                    $("#div_material_extra_video").css('display', 'none');

                    if ($(this).val() == 'imagem') {
                        $("#div_material_extra_imagem").css('display', 'block');
                    } else if ($(this).val() == 'pdf') {
                        $("#div_material_extra_pdf").css('display', 'block');
                    } else if ($(this).val() == 'video') {
                        $("#div_material_extra_video").css('display', 'block');
                    }
                });

                $("#selecionar_imagem_material_extra").on("click", function () {
                    $("#material_extra_imagem").click();
                });

                $("#selecionar_pdf_material_extra").on("click", function () {
                    $("#material_extra_pdf").click();
                });

                $("#material_extra_imagem").on("change", function () {

                    var imageReader = new FileReader();
                    imageReader.readAsDataURL(document.getElementById("material_extra_imagem").files[0]);

                    imageReader.onload = function (oFREvent) {
                        $("#previewimage_material_extra").attr('src', oFREvent.target.result).fadeIn();
                    };
                });

                $("#material_extra_pdf").on('change', function () {
                    $("#label_pdf_material_extra").html('Arquivo selecionado');
                });

                $("#bt_adicionar_material_extra").on("click", function () {

                    if ($("#descricao_material_extra").val() == '') {
                        $("#fechar_modal_material_extra").click();
                        alertPersonalizado('error', 'Descrição não informada');
                        return false;
                    }
                    if ($("#tipo_material_extra").val() == '') {
                        $("#fechar_modal_material_extra").click();
                        alertPersonalizado('error', 'Informe o tipo do material extra');
                        return false;
                    }
                    if ($("#tipo_material_extra").val() == 'imagem' && document.getElementById("material_extra_imagem").files.length == 0) {
                        $("#fechar_modal_material_extra").click();
                        alertPersonalizado('error', 'Imagem não selecionada');
                        return false;
                    }
                    if ($("#tipo_material_extra").val() == 'pdf' && document.getElementById("material_extra_pdf").files.length == 0) {
                        $("#fechar_modal_material_extra").click();
                        alertPersonalizado('error', 'Arquivo não selecionado');
                        return false;
                    }
                    if ($("#tipo_material_extra").val() == 'video' && $("#material_extra_video").val() == '') {
                        $("#fechar_modal_material_extra").click();
                        alertPersonalizado('error', 'Url do vídeo não informada');
                        return false;
                    }

                    $('.loading').css("visibility", "visible");

                    var form_data = new FormData(document.getElementById('add_material_extra'));
                    form_data.append('projeto', id_projeto);

                    $.ajax({
                        method: "POST",
                        url: "/projects/addmaterialextra",
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('error', 'Ocorreu algum erro');
                            $('#previewimage').imgAreaSelect({remove: true});
                        },
                        success: function (data) {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('success', 'Material extra adicionado!');
                            $('#previewimage').imgAreaSelect({remove: true});
                            updateConfiguracoes();
                        },
                    });

                });

            });
        </script>

@endsection

