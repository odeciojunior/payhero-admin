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
                        <span>Disponível para saque</span>
                    </div>
                    <div class="card-block px-30 py-10">
                        <div class="row">
                            <div class="col-12">
                                <div class="blue-grey-700" style="font-size: 20px">
                                    R$ {!! $saldo_disponivel !!}
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
                                <div class="blue-grey-700" style="font-size: 20px">
                                    R$ {!! $saldo_futuro !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div style="border: 1px solid orange">
                    <div class="card-header bg-orange-600 white px-30 py-10">
                        <span>Disponível para antecipação</span>
                    </div>
                    <div class="card-block px-30 py-10">
                        <div class="row">
                            <div class="col-12">
                                <div class="blue-grey-700" style="font-size: 20px">
                                    R$ {!! $saldo_antecipavel !!}
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
                            aria-controls="tab_saldo_futuro" role="tab">Saldo futuro</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_historico"
                            aria-controls="tab_historico" role="tab">Histórico</a></li>
                    </ul>
                    <div class="tab-content pt-20">
                        <div class="tab-pane active" id="tab_saldo_futuro" role="tabpanel">
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
                            <h1>Histórico</h1>
                        </div>
                    </div>
                </div>
            </div>
                
        </div>
    @endif
</div>


  <script>

    $(document).ready(function(){

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
                        dados_tabela += "<td><span class='badge badge-outline badge-md badge-warning'>"+data[i].status+"</span></td>";
                        dados_tabela += "<td>"+data[i].valor+"</td>";
                        dados_tabela += "<td>"+data[i].metodo+"</td>";
                        dados_tabela += "</tr>";
                    });
                    $('#tabela_saldo_futuro').html(dados_tabela);
                }

            });
        }

        atualizarTabelaSaldoFuturo($('#select_empresas').val());
    });

  </script>


@endsection

