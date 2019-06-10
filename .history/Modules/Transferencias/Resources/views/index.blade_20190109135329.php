@extends("layouts.master")

@section('content')

  <!-- Page --> 
  <div class="page">
    <div class="page-header">
        <h1 class="page-title">Transferências</h1>
    </div>

    <div class="page-content container-fluid">
        <div class="panel pt-30 p-30" data-plugin="matchHeight">
            <div style="float: right; padding: 5px">
                <select id="select_empresas" class="form-control">
                    <option value="">Informações da empresa Empresa 1</option>
                </select>
            </div>
            <hr>
            <div class="row">
            </div>
            <div class="row" style="margin-top: 30px">
                <div class="col-4">
                    <div style="border: 1px solid green">
                        <div class="card-header bg-green-600 white px-30 py-10">
                            <span>Disponível para saque</span>
                        </div>
                        <div class="card-block px-30 py-10">
                            <div class="row">
                                <div class="col-9">
                                    <div class="blue-grey-700" style="font-size: 30px">
                                        R$ 2.899,44
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-8 text-center">
                    <h3> Nova Transferência </h3>
                </div>
            </div>
            <div class="row" style="margin-top: 2px">
                <div class="col-4">
                    <div style="border: 1px solid blue">
                        <div class="card-header bg-blue-600 white px-30 py-10">
                            <span>Disponível para antecipação</span>
                        </div>
                        <div class="card-block px-30 py-10">
                            <div class="row">
                                <div class="col-9">
                                    <div class="blue-grey-700" style="font-size: 30px">
                                        R$ 2.899,44
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <label for="valor_saque">Valor do saque</label>
                    <input class="form-control dinheiro" type="text" id="valor_saque" placeholder="Valor">
                </div>
                <div class="col-4 text-center">
                    <button class="btn btn-success" id="sacar_dinheiro" style="margin-top: 25px">Sacar dinheiro</button>
                </div>
            </div>
            <div class="row" style="margin-top: 2px">
                <div class="col-4">
                    <div style="border: 1px solid grey">
                        <div class="card-header bg-grey-600 white px-30 py-10">
                            <span>Aguardando liberação</span>
                        </div>
                        <div class="card-block px-30 py-10">
                            <div class="row">
                                <div class="col-9">
                                    <div class="blue-grey-700" style="font-size: 30px">
                                        R$ 2.899,44
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-8 text-center">
                </div>
            </div>
        </div>
    </div>
</div>


  <script>

    $(document).ready(function(){

        $('.dinheiro').mask('#.###,#0', {reverse: true});
    });

  </script>


@endsection

