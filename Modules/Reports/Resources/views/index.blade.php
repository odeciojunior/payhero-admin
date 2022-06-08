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
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.75504 11.37C1.47004 11.37 1.18504 11.265 0.960039 11.04C0.525039 10.605 0.525039 9.88501 0.960039 9.45001L9.45004 0.960009C9.88504 0.525009 10.605 0.525009 11.04 0.960009C11.475 1.39501 11.475 2.11501 11.04 2.55001L2.55004 11.04C2.34004 11.265 2.04004 11.37 1.75504 11.37Z" fill="#636363"/>
                                <path d="M10.245 11.37C9.96004 11.37 9.67504 11.265 9.45004 11.04L0.960039 2.55001C0.525039 2.11501 0.525039 1.39501 0.960039 0.960009C1.39504 0.525009 2.11504 0.525009 2.55004 0.960009L11.04 9.45001C11.475 9.88501 11.475 10.605 11.04 11.04C10.815 11.265 10.53 11.37 10.245 11.37Z" fill="#636363"/>
                            </svg>
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
                                    <div class="align-middle-input"><input type="checkbox"><label for="">Vendas</label></div>
                                    <div class="align-middle-input"><input type="checkbox"><label for="">Financeiro</label></div>
                                    <div class="align-middle-input"><input type="checkbox"><label for="">Marketing</label></div>
                                </div>

                                <h6>Escolha o formato que irá receber</h6>
                                <div class="d-flex">
                                    <div class="align-middle-input"><input type="radio" name="format" value="csv"><label for="">.csv</label></div>
                                    <div class="align-middle-input"><input type="radio" name="format" value="xls"><label for="">.xls</label></div>
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
                                <div class="finance-card fc no-ske border blue mb-10 onPreLoad" id="sales-transactions"></div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card fc no-ske border orange mb-10 onPreLoad" id="sales-average-ticket"></div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card fc no-ske border green mb-10 onPreLoad" id="sales-comission"></div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card fc no-ske border pink mb-10 onPreLoad" id="sales-number-chargeback"></div>
                            </div>
                        </div>
					</div>
				</div>

                <div class="row">
                    <div class="col-12 d-flex box-chart-primary">
                        <!-- comission -->
                        <div class="primary">
                            <div class="card" id="card-status">
                                <section class="container pb-20 graph-principal">
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
                                        <div class="card inner card-payments" id="card-payments">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Meios de pagamento</strong>
                                                </h5>
                                            </header>
                                            <div class="onPreLoad" id="block-payments"></div>
                                        </div>
                                        <div class="card" id="card-info">
                                            <div class="d-flex align-items cash-box seller onPreLoad no-ske" id="block-info-card">
                                            </div>
                                        </div>
                                        <div class="card inner pad-bottom-0 card-conversion" id="card-conversion">
                                            <header class="d-flex title-graph mt-0">
                                                <h5 class="grey font-size-16">
                                                    <strong>Conversão</strong>
                                                </h5>
                                            </header>
                                            <div class="custom-table onPreLoad" id="block-conversion"></div>
                                        </div>
                                    </div>
                                    <div class="inner-comission">

                                        <div class="card inner card-devices" id="card-devices">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Dispositivos</strong>
                                                </h5>
                                            </header>
                                            <div class="custom-table pb-0 pt-0 onPreLoad" id="block-devices"></div>
                                        </div>

                                        <div class="card inner card-abandoned" id="card-abandoned">
                                            <header class="d-flex title-graph mt-0">
                                                <h5 class="grey font-size-16">
                                                    <strong>Carrinhos</strong>
                                                </h5>
                                            </header>
                                            <div id="block-abandoned" class="no-ske block-abandoned onPreLoad"></div>
                                        </div>

                                        <div class="card inner card-orderbump" id="card-orderbump">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Orderbump</strong>
                                                </h5>
                                            </header>
                                            <footer id="block-orderbump" class="no-ske onPreLoad">
                                                
                                            </footer>
                                        </div>

                                        <div class="card inner card-upsell no-ske" id="card-upsell">
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
                            <div class="card inner distribution-content sales" id="card-distribution">
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
                                    <div class="card inner sales-card card-most-sales" id="card-most-sales">
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
