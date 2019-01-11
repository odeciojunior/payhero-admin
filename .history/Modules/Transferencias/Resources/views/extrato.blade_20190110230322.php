@extends("layouts.master")

@section('content')

  <!-- Page --> 
  <div class="page">

    <div class="page-header">
        <div class="row">
            <div class="col-3">
                <h1 class="page-title" style="margin-top: 20px">Extrato</h1>
            </div>
            <div class="col-3">
                <div style="border: 1px solid green">
                    <div class="card-header bg-green-600 white px-30 py-10">
                        <span>Saldo disponível</span>
                    </div>
                    <div class="card-block px-30 py-10">
                        <div class="row">
                            <div class="col-12">
                                <div id="label_saldo_disponivel" class="blue-grey-700" style="font-size: 20px">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div style="border: 1px solid blue">
                    <div class="card-header bg-blue-600 white px-30 py-10">
                        <span>Aguardando liberação</span>
                    </div>
                    <div class="card-block px-30 py-10">
                        <div class="row">
                            <div class="col-12">
                                <div id="label_saldo_futuro" class="blue-grey-700" style="font-size: 20px">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div style="border: 1px solid orange">
                    <div class="card-header bg-orange-600 white px-30 py-10" style="font-size: 12px">
                        <span>Disponível para antecipação</span>
                    </div>
                    <div class="card-block px-30 py-10">
                        <div class="row">
                            <div class="col-12">
                                <div id="label_saldo_antecipavel" class="blue-grey-700" style="font-size: 20px">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

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
                        <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_saldo_futuro"
                            aria-controls="tab_saldo_futuro" role="tab">Próximos lançamentos</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_historico"
                            aria-controls="tab_historico" role="tab">Histórico</a></li>
                    </ul>
                    <div class="tab-content pt-20">
                        <div class="tab-pane active" id="tab_saldo_futuro" role="tabpanel">
                            <div class="row">
                                <div class="col-2">
                                    <h5>Filtros por data :</h5>
                                </div>
                                <div class="form-group col-3">
                                    <label for="data_inicio_futuros_lancamentos">Data inicial</label>
                                    <input id="data_inicio_futuros_lancamentos" type="date" class="form-control">
                                </div>
                                <div class="form-group col-3">
                                    <label for="data_fim_futuros_lancamentos">Data final</label>
                                    <input id="data_fim_futuros_lancamentos" type="date" class="form-control">
                                </div>
                            </div>
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Método de pagamento</th>
                                </thead>
                                <tbody id="tabela_saldo_futuro">
                                    <!-- Carregado dinamicamente -->
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_historico" role="tabpanel">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Método de pagamento</th>
                                </thead>
                                <tbody id="tabela_historico">
                                    <!-- Carregado dinamicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
                
        </div>
    @endif
</div>


  <script>

    $(document).ready(function(){

        var date = new Date();
        var currentDate = date.toISOString().slice(0,10);
alert(currentDate);
        $("#data_inicio_futuros_lancamentos").val(currentDate);

        function atualizarSaldos(id_empresa){

            $.ajax({
                method: "POST",
                url: "/extrato/getsaldos",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { empresa: id_empresa},
                error: function(){
                    alert('Ocorreu algum erro');
                },
                success: function(data){

                    $('#label_saldo_disponivel').html('R$ '+data.saldo_disponivel);
                    $('#label_saldo_futuro').html('R$ '+data.saldo_futuro);
                    $('#label_saldo_antecipavel').html('R$ '+data.saldo_antecipavel);
                }

            });
        }

        function atualizarTabelaSaldoFuturo(id_empresa){

            $.ajax({
                method: "POST",
                url: "/extrato/detalhessaldofuturo",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { empresa: id_empresa},
                error: function(){
                    alert('Ocorreu algum erro');
                },
                success: function(data){

                    var dados_tabela = "";
                    $.each(data, function(i, item) {
                        dados_tabela += "<tr>";
                        dados_tabela += "<td>"+data[i].data_pagamento+"</td>";
                        if(data[i].status == "Pago"){
                            dados_tabela += "<td><span class='badge  badge-info'>"+data[i].status+"</span></td>";
                        }
                        else if(data[i].status == "Aguardando pagamento"){
                            dados_tabela += "<td><span class='badge badge-success'>"+data[i].status+"</span></td>";
                        }
                        else if(data[i].status == "Pagamento estornado"){
                            dados_tabela += "<td><span class='badge badge-warning'>"+data[i].status+"</span></td>";
                        }
                        else{
                            dados_tabela += "<td><span class='badge badge-default'>"+data[i].status+"</span></td>";
                        }
                        dados_tabela += "<td>"+data[i].valor+"</td>";
                        dados_tabela += "<td>"+data[i].metodo+"</td>";
                        dados_tabela += "</tr>";
                    });
                    $('#tabela_saldo_futuro').html(dados_tabela);
                }

            });
        }

        function atualizarTabelaHistorico(id_empresa){

            $.ajax({
                method: "POST",
                url: "/extrato/historico",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { empresa: id_empresa},
                error: function(){
                    alert('Ocorreu algum erro');
                },
                success: function(data){

                    var dados_tabela = "";
                    $.each(data, function(i, item) {
                        dados_tabela += "<tr>";
                        dados_tabela += "<td>"+data[i].data_pagamento+"</td>";
                        if(data[i].status == "Pago"){
                            dados_tabela += "<td><span class='badge  badge-info'>"+data[i].status+"</span></td>";
                        }
                        else if(data[i].status == "Aguardando pagamento"){
                            dados_tabela += "<td><span class='badge badge-success'>"+data[i].status+"</span></td>";
                        }
                        else if(data[i].status == "Pagamento estornado"){
                            dados_tabela += "<td><span class='badge badge-warning'>"+data[i].status+"</span></td>";
                        }
                        else{
                            dados_tabela += "<td><span class='badge badge-default'>"+data[i].status+"</span></td>";
                        }
                        dados_tabela += "<td>"+data[i].valor+"</td>";
                        dados_tabela += "<td>"+data[i].metodo+"</td>";
                        dados_tabela += "</tr>";
                    });
                    $('#tabela_historico').html(dados_tabela);
                }

            });
        }

        atualizarSaldos($('#select_empresas').val());
        atualizarTabelaSaldoFuturo($('#select_empresas').val());
        atualizarTabelaHistorico($('#select_empresas').val());

        $("#select_empresas").on("change", function(){

            $('#label_saldo_disponivel').html("");
            $('#label_saldo_futuro').html("");
            $('#label_saldo_antecipavel').html("");

            $('#tabela_historico').html("");
            $('#tabela_saldo_futuro').html("");

            atualizarSaldos($(this).val());
            atualizarTabelaSaldoFuturo($(this).val());
            atualizarTabelaHistorico($(this).val());
        });
    });

  </script>


@endsection

