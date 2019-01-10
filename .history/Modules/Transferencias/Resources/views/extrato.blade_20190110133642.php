@extends("layouts.master")

@section('content')

  <!-- Page --> 
  <div class="page">

    <div class="page-header">
        <div class="row">
            <div class="col-2">
                <h1 class="page-title">Extrato</h1>
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
                            <h1>Saldo futuro</h1>
                        </div>
                        <div class="tab-pane" id="tab_historico" role="tabpanel">
                            <h1>Histórico</h1>
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
                            <button id="confirmar_antecipacao" type="button" class="btn btn-success">Confirmar antecipação</button>
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

        var saldo_disponivel_antecipacao = "{!! $saldo_antecipavel !!}";
        saldo_disponivel_antecipacao = saldo_disponivel_antecipacao.replace(/[^0-9]/g,'');

        var saldo_disponivel_saque = "{!! $saldo_disponivel !!}";
        saldo_disponivel_saque = saldo_disponivel_saque.replace(/[^0-9]/g,'');
        
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
                    alert('Ocorreu algum erro');
                },
                success: function(data){

                    $('#tabela_taxa').html('R$ '+data.taxa);
                    $('#tabela_taxa_antecipacao').html('R$ '+data.taxa_antecipacao);
                    $('#tabela_valor_total').html('R$ '+data.valor_total);
                    $('#tabela_data_pagamento').html('R$ '+data.data_liberacao);

                    $("#carregando").html("");
                    $('#tabela_antecipacao').show();
        
                }
            });

        });
    });

  </script>


@endsection

