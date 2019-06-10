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
                                                    <div class="blue-grey-700" style="font-size: 30px">
                                                        R$ {!! $saldo_disponivel !!}
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
                                                    <div class="blue-grey-700" style="font-size: 30px">
                                                        R$ {!! $saldo_futuro !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="row">
                                        <div class="col-7">
                                            <label for="valor_saque">Valor do saque</label>
                                            <input class="form-control dinheiro" type="text" id="valor_saque" placeholder="Valor">
                                        </div>
                                        <div class="col-5 text-center">
                                            <button class="btn btn-success" id="sacar_dinheiro" style="margin-top: 25px">Sacar dinheiro</button>
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
                                                    <div class="blue-grey-700" style="font-size: 30px">
                                                        R$ {!! $saldo_transferido !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
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
                                                    <div class="blue-grey-700" style="font-size: 30px">
                                                        R$ {!! $saldo_antecipavel !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="text-center">
                                        <h3> Simulação de antecipação </h3>
                                    </div>
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="valor_saque">Valor do saque</label>
                                            <input class="form-control dinheiro" type="text" id="valor_saque" placeholder="Valor">
                                        </div>
                                        <div class="col-4 text-center">
                                            <button class="btn btn-success" id="sacar_dinheiro" style="margin-top: 25px">Sacar dinheiro</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>


  <script>

    $(document).ready(function(){

        $('.dinheiro').mask('#.###,#0', {reverse: true});
    });

  </script>


@endsection

