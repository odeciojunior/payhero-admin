@extends("layouts.master")
@section('title', '- Relatórios')
@section('content')
@section('styles')
@endsection
   

<div class="page">

    <div class="page-header container">
        <div class="row">

        <div class="col-12">
        <h1 class="page-title">Relatórios</h1>

        </div>

        <div class="col-lg-12 mt-30">
            <div class="row justify-content-between align-items-start">

                    <div class="col-3">
                        <div class="input-holder">
                            <select class="form-control">
                                <option> Empresa 1</option>
                                <option> Empresa 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6 align-items-baseline">
                        

                    <div class="row justify-content-end align-items-center">

                    <div class="col-lg-3 text-right">
                        <div class="btn-group" data-toggle="buttons" role="group" style="margin-top: 2px;">
                            <label class="btn btn-outline-primary fix-m">
                                <input type="radio" name="radio-filtro" value="semana" checked=""> Semana
                            </label>
                            <label class="btn btn-outline-primary fix-m">
                                <input type="radio" name="radio-filtro" value="mes"> Mês
                            </label>
                            <label class="btn btn-outline-primary fix-m active">
                                <input type="radio" name="radio-filtro" value="ano"> Ano
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-5 text-right">

                        <a id="personalizado" class="text-filtros">
                            <svg class="icon-filtro" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M24 2v22h-24v-22h3v1c0 1.103.897 2 2 2s2-.897 2-2v-1h10v1c0 1.103.897 2 2 2s2-.897 2-2v-1h3zm-2 6h-20v14h20v-14zm-2-7c0-.552-.447-1-1-1s-1 .448-1 1v2c0 .552.447 1 1 1s1-.448 1-1v-2zm-14 2c0 .552-.447 1-1 1s-1-.448-1-1v-2c0-.552.447-1 1-1s1 .448 1 1v2zm6.687 13.482c0-.802-.418-1.429-1.109-1.695.528-.264.836-.807.836-1.503 0-1.346-1.312-2.149-2.581-2.149-1.477 0-2.591.925-2.659 2.763h1.645c-.014-.761.271-1.315 1.025-1.315.449 0 .933.272.933.869 0 .754-.816.862-1.567.797v1.28c1.067 0 1.704.067 1.704.985 0 .724-.548 1.048-1.091 1.048-.822 0-1.159-.614-1.188-1.452h-1.634c-.032 1.892 1.114 2.89 2.842 2.89 1.543 0 2.844-.943 2.844-2.518zm4.313 2.518v-7.718h-1.392c-.173 1.154-.995 1.491-2.171 1.459v1.346h1.852v4.913h1.711z"></path>
                            </svg>
                            Personalizado
                        </a>
                    </div>
                    </div>
                    </div>
            </div>
         </div>

    </div>
</div>


    <div class="page-content container">

                    <div class="nav-tabs-line">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="nav-vendas-tab" data-toggle="tab" href="#nav-vendas"
                                        role="tab" aria-controls="nav-vendas" aria-selected="true">Vendas</a>
                                    <a class="nav-item nav-link" id="nav-visitas-tab" data-toggle="tab" href="#nav-visitas"
                                        role="tab" aria-controls="nav-visitas" aria-selected="false">Visitas</a>
                                </div>
                    </div>
                                       
                <div class="tab-content gutter_top mt-15 gutter_bottom mb-30" id="nav-tabContent">

                        <!-- VENDAS -->
                        <div class="tab-pane fade show active" id="nav-vendas" role="tabpanel">

                                <div class="row justify-content-between">
                                    

                                        <div class="col-lg-12">
                                            <div class="card shadow">
                
                                                <div class="wrap">
                
                                                    <div class="row justify-content-between gutter_top">
                                                        <div class="col-lg-2">
                                                            <h6 class="label-price relatorios"> Receita gerada </h6>
                                                            <h4 class="number green">R$ 2.500,00</h4>
                
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <h6 class="label-price relatorios"> Aprovadas </h6>
                                                            <h4 class="number green"><i class="fas fa-check"></i>20</h4>
                
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <h6 class="label-price relatorios"> Boletos </h6>
                                                            <h4 class="number gray">62</h4>
                
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <h6 class="label-price relatorios"> Recusadas </h6>
                                                            <h4 class="number red">3</h4>
                
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <h6 class="label-price relatorios"> Reembolsos </h6>
                                                            <h4 class="number purple">2</h4>
                
                                                        </div>
                
                                                        <div class="col-lg-12">
                                                            <div class="grafico">
                                                                <div class="text">
                                                                    <h1 class="text-muted op5"> Graph here </h1>
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                    </div>
                
                
                
                                                </div>
                
                
                                            </div>
                                        </div>
                
                                        <div class="col-lg-12 gutter_top">
                                            <div class="card shadow">
                
                                                <div class="card-header">
                                                    <h4> Origens </h4>
                                                </div>
                
                                                <div class="custom-table min-250">
                                                    <div class="row">
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        origem.html
                                                                    </div>
                
                                                                    <div class="col">
                                                                        2
                                                                    </div>
                
                                                                    <div class="col">
                                                                        2
                                                                    </div>
                
                                                                    <div class="col">
                                                                        2
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <span class="money-td green">R$500,00</span>
                
                                                                    </div>
                
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        origem.html
                                                                    </div>
                
                                                                    <div class="col">
                                                                        2
                                                                    </div>
                
                                                                    <div class="col">
                                                                        2
                                                                    </div>
                
                                                                    <div class="col">
                                                                        2
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <span class="money-td green">R$500,00</span>
                
                                                                    </div>
                
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                
                                        <div class="col-lg-6 gutter_top">
                                            <div class="card shadow">
                                                <div class="card-header">
                                                    <h4> Dispositivos </h4>
                                                </div>
                
                                                <div class="custom-table min-250">
                                                    <div class="row">
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        Desktop
                                                                    </div>
                
                                                                    <div class="col">
                                                                        0%
                                                                    </div>
                
                                                                    <div class="col-lg-3">
                                                                        <span class="money-td green">R$500,00</span>
                                                                    </div>
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        Mobile
                                                                    </div>
                
                                                                    <div class="col">
                                                                        30%
                                                                    </div>
                
                                                                    <div class="col-lg-3">
                                                                        <span class="money-td green">R$1.200,00</span>
                                                                    </div>
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        Tablet
                                                                    </div>
                
                                                                    <div class="col">
                                                                        60%
                                                                    </div>
                
                                                                    <div class="col-lg-3">
                                                                        <span class="money-td green">R$500,00</span>
                                                                    </div>
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                
                
                
                                            </div>
                                            <div class="card shadow gutter_top">
                                                    <div class="card-header">
                                                        <h4> Meios de Pagamento </h4>
                                                    </div>
                
                                                    <div class="custom-table">
                                                        <div class="row">
                                                            <div class="col-lg-12 ">
                                                                <div class="data-holder b-bottom">
                
                                                                    <div class="row wrap justify-content-between">
                                                                        <div class="col-lg-4">
                                                                            Cartão
                                                                        </div>
                
                                                                        <div class="col-lg-2">
                                                                            30%
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            16%
                                                                        </div>
                
                                                                        <div class="col-lg-4">
                                                                            <span class="money-td green">R$2.500,00</span>
                                                                        </div>
                
                
                                                                    </div>
                                                                </div>
                                                            </div>
                
                                                            <div class="col-lg-12 ">
                                                                <div class="data-holder b-bottom">
                
                                                                    <div class="row wrap justify-content-between">
                                                                        <div class="col-lg-4">
                                                                            Boleto
                                                                        </div>
                
                                                                        <div class="col-lg-2">
                                                                            30%
                                                                        </div>
                                                                        <div class="col-lg-2">
                                                                            16%
                                                                        </div>
                
                                                                        <div class="col-lg-4">
                                                                            <span class="money-td green">R$2.500,00</span>
                                                                        </div>
                
                
                                                                    </div>
                                                                </div>
                                                            </div>
                
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                
                                        <div class="col-lg-6 gutter_top ">
                                            <div class="card shadow">
                                                <div class="card-header">
                                                    <h4> Páginas </h4>
                                                </div>
                
                                                <div class="data-holder empty-400"></div>
                                            </div>
                
                
                                        </div>
                
                
                
                
                                    </div>
                            
                        </div>      

                        <!-- VISITAS -->
                        <div class="tab-pane fade" id="nav-visitas" role="tabpanel">

                                <div class="row justify-content-between">

                                        <div class="col-lg-12">
                                            <div class="card shadow">
                
                                                <div class="wrap">
                
                                                    <div class="row justify-content-between">
                
                                                        <div class="col-lg-12">
                                                            <div class="grafico">
                                                                <div class="text">
                                                                    <h1 class="text-muted"> Graph here </h1>
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                    </div>
                                                </div>
                
                
                                            </div>
                                        </div>
                
                                        <div class="col-lg-6 gutter_top">
                                            <div class="card shadow">
                                                <div class="card-header">
                                                    <h4> Origens </h4>
                                                </div>
                
                                                <div class="custom-table">
                                                    <div class="row">
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        origem.html
                                                                    </div>
                
                                                                    <div class="col text-right">
                                                                        200
                                                                    </div>
                
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        origem.html
                                                                    </div>
                
                                                                    <div class="col text-right">
                                                                        200
                                                                    </div>
                
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                
                
                                                    </div>
                                                </div>
                
                
                
                                            </div>
                                        </div>
                
                                        <div class="col-lg-6 gutter_top">
                                            <div class="card shadow">
                                                <div class="card-header">
                                                    <h4> Páginas </h4>
                                                </div>
                
                                                <div class="custom-table ">
                                                    <div class="row">
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        Páginas
                                                                    </div>
                
                                                                    <div class="col text-right">
                                                                        200
                                                                    </div>
                
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        Checkout
                                                                    </div>
                
                                                                    <div class="col text-right">
                                                                        200
                                                                    </div>
                
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                
                
                                                    </div>
                                                </div>
                
                
                
                                            </div>
                                        </div>
                
                                        <div class="col-lg-6 gutter_top">
                                            <div class="card shadow">
                                                <div class="card-header">
                                                    <h4> Referências </h4>
                                                </div>
                
                                                <div class="custom-table empty-200">
                
                                                    <div class="empty-card d-flex flex-column text-center">
                                                        <h2 class="op-5"> X </h2>
                                                        <h5 class="op-5"> Não encontramos nenhuma referência </h5>
                                                    </div>
                
                                                </div>
                
                
                
                                            </div>
                                        </div>
                
                                        <div class="col-lg-6 gutter_top">
                                            <div class="card shadow">
                                                <div class="card-header">
                                                    <h4> Dispositivos </h4>
                                                </div>
                
                                                <div class="custom-table">
                                                    <div class="row">
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        Desktop
                                                                    </div>
                
                                                                    <div class="col text-right">
                                                                        0%
                                                                    </div>
                
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        Mobile
                                                                    </div>
                
                                                                    <div class="col text-right">
                                                                        0%
                                                                    </div>
                
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                        <div class="col-lg-12 ">
                                                            <div class="data-holder b-bottom">
                
                                                                <div class="row wrap justify-content-between">
                                                                    <div class="col-lg-6">
                                                                        Tablet
                                                                    </div>
                
                                                                    <div class="col text-right">
                                                                        0%
                                                                    </div>
                
                
                
                                                                </div>
                                                            </div>
                                                        </div>
                
                
                                                    </div>
                                                </div>
                
                
                
                                            </div>
                                        </div>
                
                                    </div>
                         

                        </div>
                </div>


    </div>

</div>


@endsection

