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
                        <li class="nav-item" role="presentation">
                            <a id="tab_pixels" class="nav-link" data-toggle="tab" href="#tab_pixels-panel"
                               aria-controls="tab_pixels" role="tab">Pixels
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id='tab_coupons' class="nav-link" data-toggle="tab" href="#tab_coupons-panel"
                               aria-controls="tab_coupons" role="tab">Cupons
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id='tab_sms' class="nav-link" data-toggle="tab" href="#tab_sms-panel"
                               aria-controls="tab_coupons" role="tab">Notificações
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab-fretes" class="nav-link" data-toggle="tab" href="#tab-fretes-panel"
                               aria-controls="tab-fretes" role="tab">Frete
                            </a>
                        </li>
                        @if($project->shopify_id == '')
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-toggle="tab" href="#tab_plans-panel" aria-controls="tab_plans" role="tab">
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
                            <a id="tab_configuration" class="nav-link" data-toggle="tab" href="#tab_configuration_project"
                               aria-controls="tab_configuration_project" role="tab">Configurações
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
                            <div class="tab-pane" id="tab_coupons-panel" role="tabpanel">
                                @include('discountcoupons::index')
                            </div>
                            <!-- Painel de Sms -->
                            <div class="tab-pane" id="tab_sms-panel" role="tabpanel">
                                @include('sms::index')
                            </div>
                            <!-- Painel de Fretes -->
                            <div class="tab-pane" id="tab-fretes-panel" role="tabpanel">
                                @include('shipping::index')
                            </div>
                            <!--- Painel de Planos -->
                            <div class="tab-pane" id="tab_plans-panel" role="tabpanel">
                                @include('plans::index')
                            </div>
                            <!-- Painel de Parceiros -->
                            <div class="tab-pane" id="tab_partners" role="tabpanel">
                                @include('partners::index')
                            </div>
                            <!-- Painel de Configurações  Abre a tela edit-->
                            <div class="tab-pane" id="tab_configuration_project" role="tabpanel">
                                @include('projects::edit')
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
            <script src='{{asset('modules/plans/js/plans.js')}}'></script>
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


                                }
                            });
                        });

                    }

                });



            });
        </script>

@endsection

