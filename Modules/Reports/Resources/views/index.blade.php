@extends("layouts.master")
@section('title', '- Relatório de Vendas')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ mix('build/layouts/reports/index.min.css') }}">
    @endpush

    <div class="page mb-0">
        <div style="display: none" class="page-header container">

            <div class="filter-container row justify-content-between">

                <div class="col-sm-12 col-lg-3 col-xl-3">
                    <h1 class="page-title new-title grey">
                        <span class="box-title ico-sell">Vendas</span>
                        Vendas
                    </h1>
                    <span type="hidden" class="error-data"></span>
                </div>

                <div class=" col-sm-12 col-lg-9 col-xl-7">

                    <div class="row justify-content-end align-items-center">

                        <div class="col-12 col-sm-6 col-lg-4 mb-10 select-projects">
                            <select id='select_projects' class="sirius-select">
                                {{-- JS carrega.. --}}
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4 mb-10 date-report">
                            <div class="row align-items-center form-icons box-select">
                                <input id="date-filter" type="text" name="daterange" class="font-size-14" value="" readonly>
                                <i style="right:16px;" class="form-control-icon form-control-icon-right o-agenda-1 font-size-18"></i>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-lg-4 mb-10 pr-0">
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
                                <div class="finance-card border blue mb-10 onPreLoad" id="sales-transactions"></div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border orange mb-10 onPreLoad" id="sales-average-ticket"></div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border green mb-10 onPreLoad" id="sales-comission"></div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border pink mb-10 onPreLoad" id="sales-number-chargeback"></div>
                            </div>
                        </div>
					</div>
				</div>

                <div class="row">
                    <div class="container d-flex box-chart-primary">
                        <!-- comission -->
                        <div class="primary">
                            <div class="card" id="card-status">
                                <section class="container pb-20">
                                    <header class="d-flex title-graph title-graph-sales">
                                        <h5 class="grey font-size-16">
                                            <strong>Quantidade</strong>
                                        </h5>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-10 select-projects">
                                            <div class="sirius-select-container">
                                                <select name="status" id="status-graph" class="sirius-select1">
                                                    <option value="">Todos os status</option>
                                                    <option value="approved">Aprovadas</option>
                                                    <option value="pending">Pendentes</option>
                                                    <option value="canceled">Canceladas</option>
                                                    <option value="refused">Recusadas</option>
                                                    <option value="blacklist">Reembolsos</option>
                                                    <option value="charge_back">Chargebacks</option>
                                                    <option value="">Outros</option>
                                                </select>
                                            </div>
                                        </div>
                                    </header>
                                    <div class="onPreLoad" id="block-status"></div>
                                </section>
                            </div>

                            <div class="">
                                <div class="d-flex justify-content-between sub-comission">
                                    <div class="inner-comission">
                                        <div class="card inner" id="card-payments">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Meios de pagamento</strong>
                                                </h5>
                                            </header>
                                            <div class="onPreLoad" id="block-payments"></div>
                                        </div>
                                        <div class="card" id="card-info">
                                            <div class="d-flex align-items cash-box seller onPreLoad" id="block-info-card">
                                            </div>
                                        </div>
                                        <div class="card inner pad-bottom-0" id="card-conversion">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Conversão</strong>
                                                </h5>
                                            </header>
                                            <div class="custom-table scrollbar pb-0 pt-0 onPreLoad" id="block-conversion"></div>
                                        </div>
                                    </div>
                                    <div class="inner-comission">

                                        <div class="card inner" id="card-devices">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Dispositivos</strong>
                                                </h5>
                                            </header>
                                            <div class="custom-table pb-0 pt-0 onPreLoad" id="block-devices"></div>
                                        </div>

                                        <div class="card inner" id="card-abandoned">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Recuperação</strong>
                                                </h5>
                                            </header>
                                            <div id="block-abandoned" class="custom-table pb-0 pt-0 onPreLoad"></div>
                                        </div>

                                        <div class="card inner" id="card-orderbump">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Orderbump</strong>
                                                </h5>
                                            </header>
                                            <footer id="block-orderbump" class="onPreLoad">
                                                
                                            </footer>
                                        </div>

                                        <div class="card inner" id="card-upsell">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Upsell</strong>
                                                </h5>
                                            </header>
                                            <footer id="block-upsell" class="onPreLoad"></footer>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /comission -->

                        <!-- distribtion -->
                        <div class="distribution">
                            <div class="card inner" id="card-distribution">
                                <header class="d-flex title-graph title-distribution">
                                    <h5 class="grey font-size-16">
                                        <strong>Distribuição</strong>
                                    </h5>
                                    <h6 class="font-size-14">Hoje</h6>
                                </header>
                                <div class="onPreLoad" id="block-distribution"></div>
                            </div>
                            <div class="">
                                <div class="d-flex sub-distribution">
                                    <div class="card inner sales-card" id="card-most-sales">
                                        <header class="d-flex title-graph">
                                            <h5 class="grey font-size-16">
                                                <strong>Vendas mais frequentes</strong>
                                            </h5>
                                        </header>
                                        <div class="custom-table scrollbar pb-0 pt-0">
                                            <div class="row">
                                                <div class="container">
                                                    <div class="data-holder b-bottom scroll-212 onPreLoad" id="block-sales"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card inner" id="card-recurrence">
                                        <header class="d-flex title-graph recurrence">
                                            <h5 class="grey font-size-16">
                                                <strong>Recorrência</strong>
                                            </h5>
                                        </header>
                                        <div class="onPreLoad" id="block-recurrence"></div>
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
    <script type='text/javascript' src='{{ mix('build/layouts/reports/sales.min.js') }}'></script>
@endpush
