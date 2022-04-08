@extends("layouts.master")
@section('title', '- Relatório de Vendas')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ mix('build/layouts/reports/index.min.css') }}">
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
                                <div class="finance-card border blue mb-10 onPreLoad" id="finance-transactions"></div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border orange mb-10 onPreLoad" id="finance-ticket"></div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card commission border green mb-10 onPreLoad" id="finance-commission"></div>
                            </div>

                            <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border pink mb-10 onPreLoad" id="finance-chargebacks"></div>
                            </div>
                        </div>
					</div>
				</div>

                <div class="row">
                    <div class="container d-flex box-chart-primary">
                        <!-- comission -->    
                        <div class="commission">
                            <div class="card onPreLoad" id="info-commission"></div>

                            <div class="">
                                <div class="d-flex justify-content-between sub-comission">
                                    <div class="inner-comission">
                                        <div class="card inner cash" id="card-cashback">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Cashback</strong>
                                                </h5>
                                            </header>
                                            <footer>
                                                <div class="d-flex align-items onPreLoad" id="block-cash"></div>
                                            </footer>
                                        </div>
                                        <div class="card">
                                            <div class="d-flex align-items cash-box">
                                                <div>
                                                    <span class="ico-coin">
                                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span>
                                                        O cashback por venda varia de acordo com o número de parcelas escolhidas pelo cliente.
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="inner-comission" id="card-draw">
                                        <div class="card inner relative" id="card-withdraw">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Saques</strong>
                                                </h5>
                                            </header>
                                            <div class="graph">
                                                <div class="onPreLoad" id="draw"></div>
                                            </div>
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
                                            <strong class="value-pending"></strong>
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
                            <div class="">
                                <div class="d-flex sub-distribution">
                                    <div class="card inner" id="card-pending">
                                        <header class="d-flex title-graph">
                                            <h5 class="grey font-size-16">
                                                <strong>Pendente</strong>
                                            </h5>
                                            <a href="{{ route('reports.pending') }} " class="box-link silver">
                                                <span>Acessar&nbsp&nbsp</span>
                                                <i class="o-arrow-right-1 redirect"></i>
                                            </a>
                                        </header>
                                        <div class="onPreLoad" id="block-pending"></div>
                                    </div>
                                    <div class="card inner">
                                        <header class="d-flex title-graph">
                                            <h5 class="grey font-size-16">
                                                <strong>Bloqueado</strong>
                                            </h5>
                                            <a href="{{ route('reports.blockedbalance') }}" class="box-link silver">
                                                <span>Acessar&nbsp&nbsp</span>
                                                <i class="o-arrow-right-1 redirect"></i>
                                            </a>
                                        </header>
                                        <footer class="">
                                            <div class="d-flex">
                                                <div class="balance col-3">
                                                    <h6 class="grey font-size-14">Total</h6>
                                                    <strong class="grey total">748</strong>
                                                </div>
                                                <div class="balance col-9">
                                                    <h6 class="font-size-14">Saldo</h6>
                                                    <small>R$</small>
                                                    <strong class="total red ">4.588,52</strong>
                                                </div>
                                            </div>
                                        </footer>
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
    <script type='text/javascript' src='{{ mix('build/layouts/reports/finances.min.js') }}'></script>
@endpush

