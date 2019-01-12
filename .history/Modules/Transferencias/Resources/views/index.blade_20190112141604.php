@extends("layouts.master")

@section('content')

  <!-- Page --> 
  <div class="page">

    @if(1 != 1)

    @else
        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                <div style="float: right; padding: 5px">
                    <label for="select_empresas">Empresa</label>
                    <select id="select_empresas" class="form-control">
                        @foreach($empresas as $empresa)
                            <option value="{!! $empresa['id'] !!}">Dados financeiros da empresa {!! $empresa['nome'] !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                </div>

                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_transferencias"
                            aria-controls="tab_transferencias" role="tab">Transferências</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_antecipacoes"
                            aria-controls="tab_antecipacoes" role="tab">Antecipações</a></li>
                    </ul>
                    <div class="tab-content pt-20">
                        <div class="tab-pane active" id="tab_transferencias" role="tabpanel">

                            <div class="row" style="margin-top: 30px">
                                <div class="col-5">
                                    <div style="border: 1px solid green">
                                        <div class="card-header bg-green-600 white px-30 py-10">
                                            <span>Disponível para saque</span>
                                        </div>
                                        <div class="card-block px-30 py-10">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div id="label_saldo_disponivel" class="blue-grey-700" style="font-size: 30px">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7 text-center">
                                    <h3 style="margin-top: 40px"> Nova Transferência </h3>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 2px">
                                <div class="col-5">
                                    <div style="border: 1px solid blue">
                                        <div class="card-header bg-blue-600 white px-30 py-10">
                                            <span>Aguardando liberação</span>
                                        </div>
                                        <div class="card-block px-30 py-10">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div id="label_saldo_futuro" class="blue-grey-700" style="font-size: 30px">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="row">
                                        <div class="col-7">
                                            <label for="valor_saque">Valor do saque (taxa de R$ 3.67)</label>
                                            <input class="form-control dinheiro" type="text" id="valor_saque" placeholder="R$ 0.00">
                                        </div>
                                        <div class="col-5 text-center">
                                            <button class="btn btn-success" id="sacar_dinheiro" style="margin-top: 25px" disabled>Sacar dinheiro</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 2px">
                                <div class="col-5">
                                    <div style="border: 1px solid grey">
                                        <div class="card-header bg-grey-600 white px-30 py-10">
                                            <span>Saldo transferido</span>
                                        </div>
                                        <div class="card-block px-30 py-10">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div id="label_saldo_transferido" class="blue-grey-700" style="font-size: 30px">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                </div>
                            </div>

                            <hr style="margin-top:30px">

                            <h3 style="margin-top: 30px"> Histórico de transferências </h3>

                            <div class="row" style="margin-top: 30px">
                                <div class="col-12">
                                    <table id="tabela_transferencias" class="table table-hover table-bordered">
                                        <thead>
                                            <th>Data de solicitação</th>
                                            <th>Data de liberação</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                            <th></th>
                                        </thead>
                                        <tbody id="dados_tabela_transferencias">
                                            <!-- Carregado dinamicamente -->
                                        </tbody>
                                    </table>
                                    <div id="nav-tabela_transferencias"></div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_antecipacoes" role="tabpanel">
                            <div class="row" style="margin-top: 30px">
                                <div class="col-5">
                                    <div style="border: 1px solid green">
                                        <div class="card-header bg-green-600 white px-30 py-10">
                                            <span>Disponível para antecipação</span>
                                        </div>
                                        <div class="card-block px-30 py-10">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div id="label_saldo_antecipavel" class="blue-grey-700" style="font-size: 30px;padding: 10px 0 10px 0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="text-center">
                                        <h4> Simular antecipação </h4>
                                    </div>
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="valor_saque">Valor da simulação</label>
                                            <input id="valor_simulacao" class="form-control dinheiro" type="text" id="valor_saque" placeholder="R$ 0.00">
                                        </div>
                                        <div class="col-4 text-center">
                                            <button class="btn btn-success" id="visualizar_simulacao" style="margin-top: 25px" data-toggle='modal' data-target='#detalhes_simulacao' disabled>Realizar simulação</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin-top:30px">

                            <h3 style="margin-top: 30px"> Histórico de antecipações </h3>

                            <div class="row" style="margin-top: 30px">
                                <div class="col-12">
                                    <table id="tabela_antecipacoes" class="table table-hover table-bordered">
                                        <thead>
                                            <th>Data de solicitação</th>
                                            <th>Data de liberação</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                            <th></th>
                                        </thead>
                                        <tbody id="dados_tabela_antecipacoes">
                                            <!-- Carregado dinamicamente -->
                                        </tbody>
                                    </table>
                                    <div id="nav-tabela_antecipacoes"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para ver detalhes de * no projeto -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="detalhes_simulacao" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                            </button>
                            <h4 id="modal_detalhes_titulo" class="modal-title" style="width: 100%; text-align:center">Simulação de antecipação de </h4>
                        </div>
                        <div id="carregando">
                        </div>
                        <div id="modal_detalhes_body" class="modal-body">
                            <table id="tabela_antecipacao" class='table table-bordered table-hover'>
                                <tbody>
                                    <tr>
                                        <td><b>Valor total</b></td>
                                        <td id="tabela_valor_total"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Taxa de antecipação</b></td>
                                        <td id="tabela_taxa_antecipacao"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Taxa</b></td>
                                        <td id="tabela_taxa"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Data do pagamento</b></td>
                                        <td id="tabela_data_pagamento"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button id="confirmar_antecipacao" type="button" class="btn btn-success" data-dismiss="modal">Confirmar antecipação</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
                
        </div>
    @endif
</div>


  <script>

    $(document).ready(function(){

        var saldo_disponivel_antecipacao = "0";

        var saldo_disponivel_saque = "0";

        $('.dinheiro').mask('###,###,###.#0', {reverse: true});

        $('#valor_simulacao').on('input', function(){
            var valor_input = $(this).val().replace(/[^0-9]/g,'');
            if(valor_input < 100 || valor_input > parseInt(saldo_disponivel_antecipacao)){
                $('#visualizar_simulacao').attr('disabled',true);
            }
            else{
                $('#visualizar_simulacao').attr('disabled',false);
            }
        });

        $('#valor_saque').on('input', function(){
            var valor_input = $(this).val().replace(/[^0-9]/g,'');
            if(valor_input < 100 || valor_input > parseInt(saldo_disponivel_saque)){
                $('#sacar_dinheiro').attr('disabled',true);
            }
            else{
                $('#sacar_dinheiro').attr('disabled',false);
            }
        });

        $('#sacar_dinheiro').on('click',function(){

            $.ajax({
                method: "POST",
                url: "/transferencias/saque",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { empresa: $("#select_empresas").val(), valor: $("#valor_saque").val().replace(/[^0-9]/g,'')},
                error: function(){
                    //
                },
                success: function(data){

                    $('#valor_saque').val('');
                    atualizarSaldos($("#select_empresas").val());
                    atualizarHistoricoTransferencias();
                    $('#sacar_dinheiro').attr('disabled',true);

                }

            });

        });

        $('#visualizar_simulacao').on('click', function(){

            $("#modal_detalhes_titulo").html("Simulação da antecipação de um valor de "+$('#valor_simulacao').val());

            $("#carregando").html("<div class='text-center'>Carregando...</div>");
            $('#tabela_antecipacao').hide();

            $.ajax({
                method: "POST",
                url: "/transferencias/detalhesantecipacao",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { empresa: $("#select_empresas").val(), valor: $("#valor_simulacao").val().replace(/[^0-9]/g,'') },
                error: function(){
                    //
                },
                success: function(data){

                    $.ajax({
                        method: "POST",
                        url: "/transferencias/detalhesantecipacao",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: { empresa: $("#select_empresas").val(), valor: $("#valor_simulacao").val().replace(/[^0-9]/g,'') },
                        error: function(){
                            //
                        },
                        success: function(data){

                            $('#tabela_taxa').html('R$ '+data.taxa);
                            $('#tabela_taxa_antecipacao').html('R$ '+data.taxa_antecipacao);
                            $('#tabela_valor_total').html('R$ '+data.valor_total);
                            $('#tabela_data_pagamento').html('R$ '+data.data_liberacao);

                            $("#carregando").html("");
                            $('#tabela_antecipacao').show();

                            $("#confirmar_antecipacao").unbind("click");

                            $("#confirmar_antecipacao").on("click", function(){

                                $.ajax({
                                    method: "POST",
                                    url: "/transferencias/confirmarantecipacao",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: { empresa: $("#select_empresas").val(), valor: $("#valor_simulacao").val().replace(/[^0-9]/g,'') },
                                    error: function(){
                                        //
                                    },
                                    success: function(data){
                    
                                        $("#valor_simulacao").val("R$ 0.00");
                                        atualizarSaldos($("#select_empresas").val());
                                        atualizarHistoricoAntecipacoes();
                                        $('#visualizar_simulacao').attr('disabled',true);

                                    }
                                });
                                    
                            });
                
                        }
                    });
        
                }
            });

        });

        function atualizarSaldos(id_empresa){

            $.ajax({
                method: "POST",
                url: "/extrato/getsaldos",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { empresa: id_empresa},
                error: function(){
                    //
                },
                success: function(data){

                    $('#label_saldo_disponivel').html('R$ '+data.saldo_disponivel);
                    $('#label_saldo_futuro').html('R$ '+data.saldo_futuro);
                    $('#label_saldo_transferido').html('R$ '+data.saldo_transferido);
                    $('#label_saldo_antecipavel').html('R$ '+data.saldo_antecipavel);

                    saldo_disponivel_antecipacao = data.saldo_antecipavel.replace(/[^0-9]/g,'');
                    saldo_disponivel_saque = data.saldo_disponivel.replace(/[^0-9]/g,'');

                }

            });
        }

        function atualizarHistoricoTransferencias(){

            $.ajax({
                method: "POST",
                url: "/transferencias/historicotransferencias",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { empresa: $('#select_empresas').val() },
                error: function(){
                    //
                },
                success: function(data){

                    var dados_tabela = "";
                    $.each(data, function(i, item) {
                        dados_tabela += "<tr>";
                        dados_tabela += "<td>"+data[i].data_solicitacao+"</td>";
                        dados_tabela += "<td>"+data[i].data_liberacao+"</td>";
                        dados_tabela += "<td>"+data[i].valor+"</td>";
                        if(data[i].status == "Transferência pendente"){
                            dados_tabela += "<td><span class='badge  badge-info'>"+data[i].status+"</span></td>";
                        }
                        else{
                            dados_tabela += "<td><span class='badge badge-default'>"+data[i].status+"</span></td>";
                        }
                        dados_tabela += "</tr>";
                        dados_tabela += "<td></td>";
                    });
                    $('#dados_tabela_transferencias').html(dados_tabela);
                    paginarTabela("tabela_transferencias");
                }

            });
        }

        function atualizarHistoricoAntecipacoes(){

            $.ajax({
                method: "POST",
                url: "/transferencias/historicoantecipacoes",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { empresa: $('#select_empresas').val() },
                error: function(){
                    //
                },
                success: function(data){

                    var dados_tabela = "";
                    $.each(data, function(i, item) {
                        dados_tabela += "<tr>";
                        dados_tabela += "<td>"+data[i].data_solicitacao+"</td>";
                        dados_tabela += "<td>"+data[i].data_liberacao+"</td>";
                        dados_tabela += "<td>"+data[i].valor+"</td>";
                        if(data[i].status == "Transferência pendente"){
                            dados_tabela += "<td><span class='badge  badge-info'>"+data[i].status+"</span></td>";
                        }
                        else{
                            dados_tabela += "<td><span class='badge badge-default'>"+data[i].status+"</span></td>";
                        }
                        dados_tabela += "<td></td>";
                        dados_tabela += "</tr>";
                    });
                    $('#dados_tabela_antecipacoes').html(dados_tabela);
                    paginarTabela("tabela_antecipacoes");
                }

            });
        }
        
        function paginarTabela(id_tabela){

            var rowsShown = 8;
            var rowsTotal = $('#'+id_tabela+' tbody tr').length;
            var numPages = rowsTotal/rowsShown;
            $('#nav-'+id_tabela).html('');
            for(i = 0;i < numPages;i++) {
                var pageNum = i + 1;
                $('#nav-'+id_tabela).append('<a href="#" class="btn" rel="'+i+'">'+pageNum+'</a> ');
            }
            $('#'+id_tabela+' tbody tr').hide();
            $('#'+id_tabela+' tbody tr').slice(0, rowsShown).show();
            $('#nav-'+id_tabela+' a:first').addClass('active');
            $('#nav-'+id_tabela+' a:first').addClass('btn-primary');
            $('#nav-'+id_tabela+' a').bind('click', function(){

                $('#nav-'+id_tabela+' a').removeClass('active');
                $('#nav-'+id_tabela+' a').removeClass('btn-primary');
                $('#nav-'+id_tabela+' a').addClass('btn');
                $(this).addClass('active');
                $(this).addClass('btn-primary');
                var currPage = $(this).attr('rel');
                var startItem = currPage * rowsShown;
                var endItem = startItem + rowsShown;
                $('#'+id_tabela+' tbody tr').css('opacity','0.0').hide().slice(startItem, endItem).
                        css('display','table-row').animate({opacity:1}, 300);

            });
        }

        atualizarSaldos($("#select_empresas").val());
        atualizarHistoricoTransferencias();
        atualizarHistoricoAntecipacoes();

        $("#select_empresas").on("change", function(){

            $('#label_saldo_disponivel').html("");
            $('#label_saldo_futuro').html("");
            $('#label_saldo_transferido').html("");
            $('#label_saldo_antecipavel').html("");

            atualizarSaldos($(this).val());
        });
    });

  </script>


@endsection

