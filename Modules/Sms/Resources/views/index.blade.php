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
                                    482 SMS disponíveis
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
                                    27 sms enviados
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
                                    19 sms recebidos
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

            <table class="table table-hover table-stripped table-bordered">
                <thead>
                    <th>Quantidade</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Valor</th>
                </thead>
                <tbody>
                    <tr>
                        <td>2.000</td>
                        <td>20/11/2018</td>
                        <td>14:40</td>
                        <td>R$ 180,00</td>
                    </tr>
                    <tr>
                        <td>4.000</td>
                        <td>28/11/2018</td>
                        <td>10:04</td>
                        <td>R$ 360,00</td>
                    </tr>
                </tbody>
            </table>

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

                        <div class="example-wrap" style="margin: 20px 0 20px 0">
                            <div class="text-center">
                                <h4 class="example-title">Forma de pagamento</h4>
                            </div>
                            <div class="radio-custom radio-primary">
                                <input type="radio" id="saldo" name="inputRadios">
                                <label for="saldo">Saldo cloudfox</label>
                            </div>
                            <div class="radio-custom radio-primary">
                                <input type="radio" id="boleto_cartao" name="inputRadios" checked="">
                                <label for="boleto_cartao">Cartão / Boleto</label>
                            </div>
                        </div>                    
                    </div>
                    <div class="modal-footer text-center">
                        <button id="comprar" type="button" class="btn btn-success" style="width: 30%; margin: auto">Comprar</button>
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

        $('#quantidade').on('keyup', function(){

            var quantidade = $(this).val();

            if( quantidade == '' || (! $.isNumeric(quantidade)) ){
                $('#total').html('0');
                return;
            }

            var resultado = parseFloat(quantidade) * 0.09;

            resultado = resultado.toLocaleString('pt-BR');

            $('#total').html('R$ '+resultado);

        });

        $('#comprar').on('click', function(){

            if($('#saldo').is(':checked')){

                alert('aqui vai ser descontado do saldo.. (aguardando integração com pagar.me)');
            }
            else if($('#boleto_cartao').is(':checked')){
                
                alert('aqui vai ser redirecionado para o checkout em outra aba.. (aguardando integração com pagar.me)');
            }
        });

    });

  </script>


@endsection

