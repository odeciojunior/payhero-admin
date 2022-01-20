@extends("layouts.master")
@section('title', '- Relatório de Vendas')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist-plugin-tooltip.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/reports.css?v=' . uniqid()) !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=' . uniqid()) !!}">
    @endpush

    <div class="page mb-0">
        <div style="display: none" class="page-header container">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-4 col-sm-12 col-xs-12">
                            <h1 class="page-title new-title grey">
                                <span class="box-title ico-finance">financeiro</span>
                                Financeiro
                            </h1>
                            <span type="hidden" class="error-data"></span>
                        </div>
                        <div class="col-lg-8 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-sm-6 col-m-3 col-lg-4">
                                    <div>
                                        <select id='select_projects' class="form-control input-pad">
                                            {{-- JS carrega.. --}}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-m-3 col-lg-5">
                                    <div class="row align-items-center form-icons box-select">
                                        <i style="right:10%;" class="form-control-icon form-control-icon-right o-agenda-1 font-size-18"></i>
                                        <input id="date-filter" type="text" name="daterange" class="input-pad text-center pr-30 font-size-14 ml-5" style="width: 92%" value="" readonly>
                                    </div>
                                </div>
                                <div class="box-export col-lg-3">
                                    <div class="inner-reports">
                                        <a href="" class="grey lk-export">
                                            <i class="o-download-cloud-1 mr-2"></i>
                                            Exportar relatórios
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="line-reports row">
                <div class="modal-reports">
                    <header class="head-modal">
                        <h3>Exportar relatórios</h3>
                        <a href="" title="Fechar" class="reports-remove">
                            <i class="icon wb-close"></i>
                        </a>
                    </header>
                    <section class="modal-report-content">
                        <h6>E-mail</h6>
                        <form action="">
                            <fieldset>
                                <div class="form-group form-icons">
                                    <i class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-18"></i>
                                    <input 
                                        type="text" 
                                        placeholder="E-mail para enviar os relatórios"
                                        class="modal-email"
                                    >
                                </div>

                                <h6>Selecione quais:</h6>
                                <div class="d-flex">
                                    <div class=""><input type="checkbox"><label for="">Vendas</label></div>
                                    <div class=""><input type="checkbox"><label for="">Financeiro</label></div>
                                    <div class=""><input type="checkbox"><label for="">Marketing</label></div>
                                </div>

                                <h6>Escolha o formato que irá receber</h6>
                                <div class="d-flex">
                                    <div><input type="radio" name="format" value="csv"><label for="">.csv</label></div>
                                    <div><input type="radio" name="format" value="xls"><label for="">.xls</label></div>
                                </div>

                                <div class="d-flex modal-buttons">
                                    <input type="reset" value="Cancelar" class="reset">
                                    <input type="submit" value="Enviar para o e-mail" class="send">
                                </div>
                            </fieldset>
                        </form>
                    </section>
                </div>
            </div>
        </div>
        
        <div style="overflow: hidden;" id="project-not-empty" style="display: none">

            <section class="container box-reports" id="reports-content">
				<div class="row">
					<div class="col-12 box-items-finance">
                        <div class="row mb-20">
                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border blue mb-10">
                                    <span class="title">N de transações</span>
                                    <div class="d-flex">
                                        <strong class="number">74.860</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border orange mb-10">
                                    <span class="title">Ticket Médio</span>
                                    <div class="d-flex">
                                        <span class="detail">R$</span>
                                        <strong class="number">1.440,20</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border green mb-10">
                                    <span class="title">Comissão total</span>
                                    <div class="d-flex">
                                        <span class="detail">R$</span>
                                        <strong class="number">1.457.080,55</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border pink mb-10">
                                    <span class="title">Total em Chargebacks</span>
                                    <div class="d-flex">
                                        <span class="detail">R$</span>
                                        <strong class="number">24.120,50</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
				</div>

                <div class="row" style="border: 1px solid red">
                    <div class="container d-flex box-chart-primary">
                        <!-- /comission -->    
                        <div class="commission">
                            <div class="card">
                                <section class="container">
                                    <header class="d-flex title-graph">
                                        <h5 class="grey font-size-16">
                                            <strong>Comissão</strong>
                                        </h5>
                                    </header>

                                    <div class="d-flex justify-content-between box-finances-values">
                                        <div class="finances-values">
                                            <span>R$</span>
                                            <strong>26.567,33</strong>
                                        </div>
                                        <div class="finances-values">
                                            <svg class="green" width="18" height="14" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.1237 0L16.9451 0.00216293L17.1065 0.023901L17.2763 0.0736642L17.4287 0.145306L17.4865 0.18052L17.5596 0.23218L17.6737 0.332676L17.8001 0.484464L17.8876 0.634047L17.9499 0.792176L17.9845 0.938213L18 1.125V7.88084C18 8.50216 17.4964 9.00583 16.8751 9.00583C16.3057 9.00583 15.835 8.58261 15.7606 8.03349L15.7503 7.88084L15.7495 3.8415L9.41947 10.1762C9.01995 10.5759 8.39457 10.6121 7.95414 10.2849L7.82797 10.1758L5.62211 7.96668L1.92041 11.6703C1.48121 12.1098 0.768994 12.1099 0.329622 11.6707C-0.069807 11.2713 -0.106236 10.6463 0.220416 10.2059L0.329304 10.0797L4.82693 5.57966C5.22645 5.17994 5.85182 5.14374 6.29225 5.47097L6.41841 5.58004L8.62427 7.78914L14.1597 2.25H10.1237C9.55424 2.25 9.08361 1.82677 9.00912 1.27766L8.99885 1.125C8.99885 0.50368 9.50247 0 10.1237 0Z" fill="#1BE4A8"/>
                                            </svg>
                                            <em class="green">23%</em>
                                        </div>
                                    </div>
                                </section>

                                <section class="container">
                                    <div class="graph-finance">
                                        <div class="new-finance-graph"></div>
                                    </div>
                                </section>
                            </div>

                            <div class="" style="border: 1px solid red">
                                <div class="d-flex justify-content-between sub-comission">
                                    <div class="inner-comission" style="border: 1px solid">
                                        <div class="card inner">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Cashback</strong>
                                                </h5>
                                            </header>
                                        </div>
                                        <div class="card inner">
                                            <div class="d-flex">
                                                <div class=""></div>
                                                <div class="">
                                                    <p>
                                                        O cashback por venda varia de acordo com o número de parcelas escolhidas pelo cliente.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="inner-comission" style="border: 1px solid">
                                        <div class="card inner">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Saques</strong>
                                                </h5>
                                                <ul class="d-flex">
                                                    <li><a href="">Receitas</a></li>
                                                    <li><a href="">Saques</a></li>
                                                    <li><a href="">Últimos 6 meses</a></li>
                                                </ul>
                                            </header>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /comission -->
                        
                        <!-- distribtion -->
                        <div class="distribution">
                            <div class="card inner">
                                <header class="d-flex title-graph">
                                    <h5 class="grey font-size-16">
                                        <strong>Distribuição</strong>
                                    </h5>
                                    <h6 class="font-size-14">Atual</h6>
                                </header>
                                <div class="d-flex box-graph-dist">
                                    <div class="distribution-graph"></div>
                                    <div class="info-graph">
                                        <h6 class="font-size-14 grey">Saldo Total</h6>
                                        <em>
                                            <small class="font-size-14">R$</small>
                                            <strong class="grey">2.654.202,00</strong>
                                        </em>
                                    </div>
                                </div>
                                <div class="d-flex box-distribution">
                                    <div class="distribution-area">
                                        <header class="grey font-size-14">
                                            <span class="green cube"></span>
                                            Disponível
                                        </header>
                                        <footer class="footer-distribution">
                                            <small>R$</small>
                                            <strong>1.655.200,00</strong>
                                        </footer>
                                    </div>
                                    <div class="distribution-area">
                                        <header class="grey font-size-14">
                                            <span class="cube yellow">
                                                <i></i>
                                            </span>
                                            Pendente
                                        </header>
                                        <footer class="footer-distribution">
                                            <small>R$</small>
                                            <strong>830.800,00</strong>
                                        </footer>
                                    </div>
                                    <div class="distribution-area">
                                        <header class="grey font-size-14">
                                            <span class="cube red">
                                                <i></i>
                                            </span>
                                            Bloqueado
                                        </header>
                                        <footer class="footer-distribution">
                                            <small>R$</small>
                                            <strong>26.540,22</strong>
                                        </footer>
                                    </div>
                                    <div class="distribution-area">
                                        <header class="grey font-size-14">
                                            <span class="cube strong">
                                                <i></i>
                                            </span>
                                            Débitos
                                        </header>
                                        <footer class="footer-distribution">
                                            <small>R$</small>
                                            <strong>8.654,20</strong>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                            <div class="" style="border: 1px solid red">
                                <div class="d-flex sub-distribution">
                                    <div class="card inner">
                                        <header class="d-flex title-graph">
                                            <h5 class="grey font-size-16">
                                                <strong>Pendente</strong>
                                            </h5>
                                            <a href="#" class="box-link silver">
                                                <span>Acessar&nbsp&nbsp</span>
                                                <i class="o-arrow-right-1 redirect"></i>
                                            </a>
                                        </header>
                                    </div>
                                    <div class="card inner">
                                        <header class="d-flex title-graph">
                                            <h5 class="grey font-size-16">
                                                <strong>Bloqueado</strong>
                                            </h5>
                                            <a href="#" class="box-link silver">
                                                <span>Acessar&nbsp&nbsp</span>
                                                <i class="o-arrow-right-1 redirect"></i>
                                            </a>
                                        </header>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /distribtion -->
                    </div>
                </div>
            </section>
        </div>
        @include('projects::empty')
    </div>
@endsection

@push('scripts')
    <!--script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script-->
    <script type='text/javascript' src='{{asset('modules/reports/js/moment.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/global/js/daterangepicker.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-tooltip.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-legend.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/reports.js?v=' . uniqid())}}'></script>
@endpush

