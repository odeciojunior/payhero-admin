@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">

        <div class="page-header">
            <h1 class="page-title">SMS</h1>
        </div>

        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">

                <div class="row">

                    <div class="col-8">
                        <div class="card card-shadow" style="border: 1px solid green">
                            <div class="card-header bg-green-600 white px-30 py-10">
                                <span>Disponíveis</span>
                            </div>
                            <div class="card-block px-30 py-10">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="blue-grey-700" style="margin-top: 20px; font-size: 35px">
                                            {!! $sms_disponiveis !!} sms disponíveis
                                        </div>
                                    </div>
                                    <div class="col-3 text-right">
                                        <i class="icon wb-envelope-open font-size-50" style="margin-top: 20px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-4">

                        <div style="text-align: right">
                            <button type="button" class="btn btn-success" data-toggle='modal' data-target='#modal_comprar_sms'>
                                <i class="icon wb-plus" aria-hidden="true"></i>
                                <br>
                                <span class="text-uppercase hidden-sm-down">Comprar</span>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-6">
                        <div class="card card-shadow" style="border: 1px solid blue">
                            <div class="card-header bg-blue-600 white px-30 py-10">
                                <span>Enviados</span>
                            </div>
                            <div class="card-block px-30 py-10">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="blue-grey-700" style="margin-top: 20px; font-size: 25px">
                                            {!! $sms_enviados !!} sms enviados
                                        </div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <i class="icon wb-envelope font-size-50" style="margin-top: 20px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card card-shadow" style="border: 1px solid blue">
                            <div class="card-header bg-blue-600 white px-30 py-10">
                                <span>Recebidos</span>
                            </div>
                            <div class="card-block px-30 py-10">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="blue-grey-700" style="margin-top: 20px; font-size: 25px">
                                            {!! $sms_recebidos !!} sms recebidos
                                        </div>
                                    </div>
                                    <div class="col-4 text-right">
                                        <i class="icon wb-envelope font-size-50" style="margin-top: 20px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <h3 style="margin: 30px 0 20px 0">Histórico de compras</h3>

                    <table id="tabela_compras" class="table table-hover table-stripped table-bordered">
                        <thead>
                            <th>Quantidade</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Forma de pagamento</th>
                            <th>Status</th>
                            <th>Detalhes</th>
                        </thead>
                        <tbody>
                            @if(count($compras) > 0)
                                @foreach($compras as $compra)
                                    <tr>
                                        <td>{!! $compra['quantidade'] !!}</td>
                                        <td>{!! $compra['data_inicio'] !!}</td>
                                        <td>{!! $compra['valor_total_pago'] !!}</td>
                                        <td>{!! $compra['forma_pagamento'] !!}</td>
                                        <td>{!! $compra['status'] !!}</td>
                                        <td><button class="btn btn-success detalhes" data-toggle='modal' data-target='#modal_detalhes_historico' compra-id="{!! $compra['id'] !!}">Detalhes</button></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr> <td colspan="6">Nenhuma compra encontrada</td></tr>
                            @endif
                        </tbody>
                    </table>
                    <div id="nav-tabela_compras"></div>

                </div>

                <div id="modal_comprar_sms" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" style="width: 100%; text-align:center">Adicionar sms</h4>
                            </div>
                            <div class="modal-body">
                                <table class="table table-hover table-stripped table-bordered" style="margin: 60px 0 40px 0">
                                    <tbody>
                                        <tr>
                                            <td><input id="quantidade" class="form-control" value="0"></td>
                                            <td><b>Quantidade</b></td>
                                        </tr>
                                        <tr>
                                            <td>x 0,09</td>
                                            <td><b>Custo</b></td>
                                        </tr>
                                        <tr>
                                            <td id="total">R$ 0.00</td>
                                            <td><b>TOTAL</b></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="modal-footer text-center">
                                <button id="comprar" type="button" class="btn btn-success" style="width: 30%; margin: auto" disabled>Comprar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="modal_detalhes_historico" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" style="width: 100%; text-align:center">Detalhes da compra</h4>
                            </div>
                            <div class="modal-body">
                                <table class="table table-hover table-stripped table-bordered" style="margin: 60px 0 40px 0">
                                    <tbody id="detalhes_body">
                                    </tbody>
                                </table>

                            </div>
                            <div class="modal-footer text-center">
                                <button type="button" class="btn btn-danger" style="width: 30%; margin: auto" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script>

        $(document).ready( function(){

            $('#quantidade').mask('0#');

            paginarTabela("tabela_compras");

            var quantidade = 0;
            var valor_total = 0;

            $('#quantidade').on('keyup', function(){

                quantidade = $(this).val();

                if( quantidade == '' || (! $.isNumeric(quantidade)) ){
                    $('#total').html('0');
                    return;
                }

                var resultado = parseFloat(quantidade) * 0.09;

                resultado = resultado.toFixed(2).toLocaleString('pt-BR');

                $('#total').html('R$ '+resultado);

                valor_total = resultado;

                if(resultado.replace(/[^0-9]/g,'') > 2000){
                    $("#comprar").attr('disabled', false);
                }
                else{
                    $("#comprar").attr('disabled', true);
                }
            });

            $('#comprar').on('click', function(){

                var form = "<input type='hidden' name='valor' value='"+valor_total+"'>";
                form += "<input type='hidden' name='quantidade' value='"+quantidade+"'>";
                form += "<input type='hidden' name='servico' value='SMS'>";
                $("<form action='/checkout' method='POST'>" + form + "</form>").appendTo($(document.body)).submit();

            });

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

            $(".detalhes").on("click", function(){

                $("#detalhes_body").html("<div class='text-center'>Carregando...</div>");

                var id = $(this).attr('compra-id');

                $.ajax({
                    method: "POST",
                    url: "/sms/detalhescompra",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { id_compra: id },
                    error: function(){
                        alert('Ocorreu algum erro');
                    },
                    success: function(data){

                        $("#detalhes_body").html(data);
                    }
                });

            });

        });

    </script>


@endsection

