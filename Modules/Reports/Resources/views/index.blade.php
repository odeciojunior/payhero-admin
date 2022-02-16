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
            <div class="row align-items-center justify-content-between" style="min-height: 50px;">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-4 col-sm-12 col-xs-12">
                            <h1 class="page-title new-title grey">
                                <span class="box-title ico-sell">Vendas</span>
                                Vendas
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

                <div class="row">
                    <div class="container d-flex box-chart-primary">
                        <!-- comission -->
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
                                    <div class="graph-reports">
                                        <div class="new-sell-graph"></div>
                                    </div>
                                </section>
                            </div>

                            <div class="">
                                <div class="d-flex justify-content-between sub-comission">
                                    <div class="inner-comission">

                                        <div class="card inner pad-bottom-0">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Meios de pagamento</strong>
                                                </h5>
                                            </header>
                                            <div id="payment-type-items" class="custom-table scrollbar pb-0 pt-0">
                                                <div class="row container-payment">
                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">

                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <i class="ms-Icon ms-Icon--PaymentCard grey" aria-hidden="true"></i>
                                                                    </div>Cartão
                                                                </div>

                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment grey" id='percent-credit-card'>
                                                                        0
                                                                    </div>

                                                                    <div class="col-payment col-graph">
                                                                        <div class="bar blue">barrinha</div>
                                                                    </div>

                                                                    <div class="col-payment"><span class="money-td green bold grey font-size-14" id='credit-card-value'></span></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">
                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="38.867"
                                                                            height="40.868"
                                                                            viewBox="0 0 38.867 40.868"
                                                                            style="width: 24px;"
                                                                        >
                                                                            <g id="Grupo_61" data-name="Grupo 61" transform="translate(-2948.5 213.743)">
                                                                                <g id="g992" transform="translate(2956.673 -190.882)">
                                                                                    <path id="path994" d="M-73.541-25.595a5.528,5.528,0,0,1-3.933-1.629l-5.68-5.68a1.079,1.079,0,0,0-1.492,0l-5.7,5.7a5.529,5.529,0,0,1-3.934,1.628H-95.4l7.193,7.194a5.753,5.753,0,0,0,8.136,0l7.214-7.214Z" transform="translate(95.4 34.202)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                                                </g>
                                                                                <g id="g996" transform="translate(2956.673 -212.243)">
                                                                                    <path id="path998" d="M-3.765-29.869A5.528,5.528,0,0,1,.169-28.24l5.7,5.7a1.056,1.056,0,0,0,1.493,0l5.68-5.68a5.529,5.529,0,0,1,3.934-1.629h.684l-7.214-7.214a5.753,5.753,0,0,0-8.136,0l-7.193,7.193Z" transform="translate(4.884 37.747)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                                                </g>
                                                                                <g id="g1000" transform="translate(2949 -201.753)">
                                                                                    <path id="path1002" d="M-121.731-14.725l-4.36-4.359a.83.83,0,0,1-.31.063h-1.982a3.917,3.917,0,0,0-2.752,1.14l-5.68,5.68a2.718,2.718,0,0,1-1.927.8,2.719,2.719,0,0,1-1.928-.8l-5.7-5.7a3.917,3.917,0,0,0-2.752-1.14h-2.437a.827.827,0,0,1-.293-.059l-4.377,4.377a5.753,5.753,0,0,0,0,8.136l4.377,4.377a.828.828,0,0,1,.293-.059h2.437a3.917,3.917,0,0,0,2.752-1.14l5.7-5.7a2.792,2.792,0,0,1,3.856,0l5.68,5.679a3.917,3.917,0,0,0,2.752,1.14h1.982a.83.83,0,0,1,.31.062l4.359-4.359a5.753,5.753,0,0,0,0-8.136" transform="translate(157.913 19.102)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                                                </g>
                                                                            </g>
                                                                        </svg>
                                                                    </div> Pix
                                                                </div>
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment grey" id='percent-values-pix'>
                                                                        0
                                                                    </div>
                                                                    <div class="col-payment col-graph">
                                                                        <div class="bar blue-2">barrinha</div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <span class="money-td green grey bold font-size-14" id='pix-value'></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">
                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <span class="">
                                                                            <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <g clip-path="url(#clip0_386_407)">
                                                                                    <rect x="-161.098" y="-1313.01" width="646" height="1962" rx="12" fill="white"/>
                                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4016 2.27981H2.40165C2.07013 2.27981 1.75218 2.41555 1.51776 2.65717C1.28333 2.89878 1.15163 3.22648 1.15163 3.56817V13.875C1.15172 14.2167 1.28346 14.5443 1.51787 14.7858C1.75229 15.0274 2.07019 15.1631 2.40165 15.1631H17.4019C17.7334 15.1631 18.0514 15.0273 18.2858 14.7857C18.5202 14.5441 18.6519 14.2164 18.6519 13.8747V3.56817C18.6519 3.39895 18.6196 3.23139 18.5567 3.07506C18.4939 2.91873 18.4018 2.77668 18.2857 2.65704C18.1696 2.5374 18.0317 2.44251 17.88 2.37779C17.7283 2.31306 17.5658 2.27977 17.4016 2.27981ZM2.40165 0.991455C1.7386 0.991455 1.10271 1.26293 0.633857 1.74616C0.165008 2.22939 -0.0983887 2.88479 -0.0983887 3.56817L-0.0983887 13.875C-0.0983887 14.5584 0.165008 15.2138 0.633857 15.6971C1.10271 16.1803 1.7386 16.4518 2.40165 16.4518H17.4019C17.7302 16.4518 18.0553 16.3851 18.3586 16.2556C18.6619 16.1261 18.9376 15.9363 19.1697 15.6971C19.4019 15.4578 19.586 15.1737 19.7116 14.8611C19.8373 14.5485 19.9019 14.2134 19.9019 13.875V3.56817C19.9019 3.22979 19.8373 2.89473 19.7116 2.58211C19.586 2.26948 19.4019 1.98543 19.1697 1.74616C18.9376 1.50689 18.6619 1.31709 18.3586 1.1876C18.0553 1.0581 17.7302 0.991455 17.4019 0.991455H2.40165Z" fill="#636363"/>
                                                                                    <path d="M4.34595 4.99976H6.27182V12.9399H4.34595V4.99976ZM7.23492 4.99976H8.19803V12.9399H7.23492V4.99976ZM14.9387 4.99976H15.9018V12.9399H14.9387V4.99976ZM11.087 4.99976H13.977V12.9399H11.087V4.99976ZM9.16113 4.99976H10.1242V12.9399H9.16113V4.99976Z" fill="#636363"/>
                                                                                </g>
                                                                                <defs>
                                                                                    <clipPath id="clip0_386_407">
                                                                                        <rect width="20.082" height="15.46" fill="white" transform="translate(0 0.991486)"/>
                                                                                    </clipPath>
                                                                                </defs>
                                                                            </svg>
                                                                        </span>
                                                                    </div> Boleto
                                                                </div>
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment grey" id='percent-values-boleto'>
                                                                        0
                                                                    </div>
                                                                    <div class="col-payment col-graph">
                                                                        <div class="bar">barrinha</div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <span class="money-td green bold grey font-size-14" id='boleto-value'></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="d-flex align-items cash-box seller">
                                                <div>
                                                    <span class="ico-coin seller">
                                                        <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M16.7647 0.164368C15.6137 0.164368 14.6814 1.09666 14.6814 2.2477V14.7477C14.6814 15.8987 15.6137 16.831 16.7647 16.831H18.848C19.9991 16.831 20.9314 15.8987 20.9314 14.7477V2.2477C20.9314 1.09666 19.9991 0.164368 18.848 0.164368H16.7647ZM16.7647 2.2477H18.848V14.7477H16.7647V2.2477ZM9.47303 4.33103C8.32199 4.33103 7.38969 5.26333 7.38969 6.41437V14.7477C7.38969 15.8987 8.32199 16.831 9.47303 16.831H11.5564C12.7074 16.831 13.6397 15.8987 13.6397 14.7477V6.41437C13.6397 5.26333 12.7074 4.33103 11.5564 4.33103H9.47303ZM9.47303 6.41437H11.5564V14.7477H9.47303V6.41437ZM2.18136 8.4977C1.03031 8.4977 0.0980225 9.42999 0.0980225 10.581V14.7477C0.0980225 15.8987 1.03031 16.831 2.18136 16.831H4.26469C5.41573 16.831 6.34803 15.8987 6.34803 14.7477V10.581C6.34803 9.42999 5.41573 8.4977 4.26469 8.4977H2.18136ZM2.18136 10.581H4.26469V14.7477H2.18136V10.581Z" fill="#2E85EC"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-size-12">
                                                        Cartão representa <strong>45%</strong> das vendas
                                                        aprovadas e tem um indíce de conversão de <strong>86%.</strong>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card inner pad-bottom-0">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Conversão</strong>
                                                </h5>
                                            </header>
                                            <div id="payment-type-items" class="custom-table scrollbar pb-0 pt-0">
                                                <div class="row container-payment">
                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">

                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <i class="ms-Icon ms-Icon--PaymentCard grey" aria-hidden="true"></i>
                                                                    </div>Cartão
                                                                </div>

                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment">
                                                                        <div class="box-payment center">
                                                                            <span>379</span>
                                                                            /<small>436</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <div class="box-payment right">
                                                                            <strong class="grey font-size-16">86.0%</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">
                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="38.867"
                                                                            height="40.868"
                                                                            viewBox="0 0 38.867 40.868"
                                                                            style="width: 24px;"
                                                                        >
                                                                            <g id="Grupo_61" data-name="Grupo 61" transform="translate(-2948.5 213.743)">
                                                                                <g id="g992" transform="translate(2956.673 -190.882)">
                                                                                    <path id="path994" d="M-73.541-25.595a5.528,5.528,0,0,1-3.933-1.629l-5.68-5.68a1.079,1.079,0,0,0-1.492,0l-5.7,5.7a5.529,5.529,0,0,1-3.934,1.628H-95.4l7.193,7.194a5.753,5.753,0,0,0,8.136,0l7.214-7.214Z" transform="translate(95.4 34.202)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                                                </g>
                                                                                <g id="g996" transform="translate(2956.673 -212.243)">
                                                                                    <path id="path998" d="M-3.765-29.869A5.528,5.528,0,0,1,.169-28.24l5.7,5.7a1.056,1.056,0,0,0,1.493,0l5.68-5.68a5.529,5.529,0,0,1,3.934-1.629h.684l-7.214-7.214a5.753,5.753,0,0,0-8.136,0l-7.193,7.193Z" transform="translate(4.884 37.747)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                                                </g>
                                                                                <g id="g1000" transform="translate(2949 -201.753)">
                                                                                    <path id="path1002" d="M-121.731-14.725l-4.36-4.359a.83.83,0,0,1-.31.063h-1.982a3.917,3.917,0,0,0-2.752,1.14l-5.68,5.68a2.718,2.718,0,0,1-1.927.8,2.719,2.719,0,0,1-1.928-.8l-5.7-5.7a3.917,3.917,0,0,0-2.752-1.14h-2.437a.827.827,0,0,1-.293-.059l-4.377,4.377a5.753,5.753,0,0,0,0,8.136l4.377,4.377a.828.828,0,0,1,.293-.059h2.437a3.917,3.917,0,0,0,2.752-1.14l5.7-5.7a2.792,2.792,0,0,1,3.856,0l5.68,5.679a3.917,3.917,0,0,0,2.752,1.14h1.982a.83.83,0,0,1,.31.062l4.359-4.359a5.753,5.753,0,0,0,0-8.136" transform="translate(157.913 19.102)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                                                </g>
                                                                            </g>
                                                                        </svg>
                                                                    </div> Pix
                                                                </div>
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment">
                                                                        <div class="box-payment center">
                                                                            <span>105</span>
                                                                            /<small>211</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <div class="box-payment right">
                                                                            <strong class="grey font-size-16">18.0%</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">
                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <span class="">
                                                                            <svg width="21" height="17" viewBox="0 0 21 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <g clip-path="url(#clip0_386_407)">
                                                                                    <rect x="-161.098" y="-1313.01" width="646" height="1962" rx="12" fill="white"/>
                                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4016 2.27981H2.40165C2.07013 2.27981 1.75218 2.41555 1.51776 2.65717C1.28333 2.89878 1.15163 3.22648 1.15163 3.56817V13.875C1.15172 14.2167 1.28346 14.5443 1.51787 14.7858C1.75229 15.0274 2.07019 15.1631 2.40165 15.1631H17.4019C17.7334 15.1631 18.0514 15.0273 18.2858 14.7857C18.5202 14.5441 18.6519 14.2164 18.6519 13.8747V3.56817C18.6519 3.39895 18.6196 3.23139 18.5567 3.07506C18.4939 2.91873 18.4018 2.77668 18.2857 2.65704C18.1696 2.5374 18.0317 2.44251 17.88 2.37779C17.7283 2.31306 17.5658 2.27977 17.4016 2.27981ZM2.40165 0.991455C1.7386 0.991455 1.10271 1.26293 0.633857 1.74616C0.165008 2.22939 -0.0983887 2.88479 -0.0983887 3.56817L-0.0983887 13.875C-0.0983887 14.5584 0.165008 15.2138 0.633857 15.6971C1.10271 16.1803 1.7386 16.4518 2.40165 16.4518H17.4019C17.7302 16.4518 18.0553 16.3851 18.3586 16.2556C18.6619 16.1261 18.9376 15.9363 19.1697 15.6971C19.4019 15.4578 19.586 15.1737 19.7116 14.8611C19.8373 14.5485 19.9019 14.2134 19.9019 13.875V3.56817C19.9019 3.22979 19.8373 2.89473 19.7116 2.58211C19.586 2.26948 19.4019 1.98543 19.1697 1.74616C18.9376 1.50689 18.6619 1.31709 18.3586 1.1876C18.0553 1.0581 17.7302 0.991455 17.4019 0.991455H2.40165Z" fill="#636363"/>
                                                                                    <path d="M4.34595 4.99976H6.27182V12.9399H4.34595V4.99976ZM7.23492 4.99976H8.19803V12.9399H7.23492V4.99976ZM14.9387 4.99976H15.9018V12.9399H14.9387V4.99976ZM11.087 4.99976H13.977V12.9399H11.087V4.99976ZM9.16113 4.99976H10.1242V12.9399H9.16113V4.99976Z" fill="#636363"/>
                                                                                </g>
                                                                                <defs>
                                                                                    <clipPath id="clip0_386_407">
                                                                                        <rect width="20.082" height="15.46" fill="white" transform="translate(0 0.991486)"/>
                                                                                    </clipPath>
                                                                                </defs>
                                                                            </svg>
                                                                        </span>
                                                                    </div> Boleto
                                                                </div>
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment">
                                                                        <div class="box-payment center">
                                                                            <span>105</span>
                                                                            /<small>211</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <div class="box-payment right">
                                                                            <strong class="grey font-size-16">49.0%</strong>
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
                                    <div class="inner-comission">

                                        <div class="card inner">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Dispositivos</strong>
                                                </h5>
                                            </header>
                                            <div id="payment-type-items" class="custom-table pb-0 pt-0">
                                                <div class="row container-payment">
                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">
                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <svg width="12" height="20" viewBox="0 0 12 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 15.7143C4.08579 15.7143 3.75 16.0341 3.75 16.4286C3.75 16.8231 4.08579 17.1429 4.5 17.1429H7.5C7.91421 17.1429 8.25 16.8231 8.25 16.4286C8.25 16.0341 7.91421 15.7143 7.5 15.7143H4.5ZM2.625 0C1.17525 0 0 1.11929 0 2.5V17.5C0 18.8807 1.17525 20 2.625 20H9.375C10.8247 20 12 18.8807 12 17.5V2.5C12 1.11929 10.8247 0 9.375 0H2.625ZM1.5 2.5C1.5 1.90827 2.00368 1.42857 2.625 1.42857H9.375C9.99632 1.42857 10.5 1.90827 10.5 2.5V17.5C10.5 18.0917 9.99632 18.5714 9.375 18.5714H2.625C2.00368 18.5714 1.5 18.0917 1.5 17.5V2.5Z" fill="#636363"/></svg>
                                                                    </div>Smartphones
                                                                </div>

                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment">
                                                                        <div class="box-payment center">
                                                                            <span>379</span>
                                                                            /<small>436</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <div class="box-payment right">
                                                                            <strong class="grey font-size-16">85%</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">
                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 2.83333C0 1.26853 1.26853 0 2.83333 0H14.1667C15.7315 0 17 1.26853 17 2.83333V11.3333C17 12.8981 15.7315 14.1667 14.1667 14.1667H11.3333V14.875C11.3333 15.2662 11.6505 15.5833 12.0417 15.5833H12.75C13.1412 15.5833 13.4583 15.9005 13.4583 16.2917C13.4583 16.6829 13.1412 17 12.75 17H4.25C3.8588 17 3.54167 16.6829 3.54167 16.2917C3.54167 15.9005 3.8588 15.5833 4.25 15.5833H4.95833C5.34953 15.5833 5.66667 15.2662 5.66667 14.875V14.1667H2.83333C1.26853 14.1667 0 12.8981 0 11.3333V2.83333ZM10.0376 15.5833C9.95928 15.3618 9.91667 15.1234 9.91667 14.875V14.1667H7.08333V14.875C7.08333 15.1234 7.04072 15.3618 6.96242 15.5833H10.0376ZM14.1667 12.75C14.9491 12.75 15.5833 12.1157 15.5833 11.3333H1.41667C1.41667 12.1157 2.05093 12.75 2.83333 12.75H14.1667ZM15.5833 2.83333C15.5833 2.05093 14.9491 1.41667 14.1667 1.41667H2.83333C2.05093 1.41667 1.41667 2.05093 1.41667 2.83333V9.91667H15.5833V2.83333Z" fill="#636363"/></svg>
                                                                    </div> Desktop
                                                                </div>
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment">
                                                                        <div class="box-payment center">
                                                                            <span>105</span>
                                                                            /<small>211</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <div class="box-payment right">
                                                                            <strong class="grey font-size-16">5%</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">
                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <span class="">
                                                                            <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M15.9749 0C17.0933 0 18 0.944265 18 2.10908V12.8909C18 14.0557 17.0933 15 15.9749 15H2.02512C0.906676 15 0 14.0557 0 12.8909V2.10908C0 0.944265 0.906676 0 2.02512 0H15.9749ZM15.9749 1.40605H2.02512C1.6523 1.40605 1.35008 1.72081 1.35008 2.10908V12.8909C1.35008 13.2792 1.6523 13.5939 2.02512 13.5939H15.9749C16.3477 13.5939 16.6499 13.2792 16.6499 12.8909V2.10908C16.6499 1.72081 16.3477 1.40605 15.9749 1.40605ZM7.42543 10.7801H10.5756C10.9484 10.7801 11.2507 11.0949 11.2507 11.4832C11.2507 11.8391 10.9967 12.1332 10.6672 12.1798L10.5756 12.1862H7.42543C7.05262 12.1862 6.75039 11.8714 6.75039 11.4832C6.75039 11.1272 7.00435 10.8331 7.33383 10.7865L7.42543 10.7801H10.5756H7.42543Z" fill="#636363"/>
                                                                            </svg>
                                                                        </span>
                                                                    </div> Tablet
                                                                </div>
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment">
                                                                        <div class="box-payment center">
                                                                            <span>105</span>
                                                                            /<small>211</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <div class="box-payment right">
                                                                            <strong class="grey font-size-16">1%</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card inner">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Recuperação</strong>
                                                </h5>
                                            </header>
                                            <div id="payment-type-items" class="custom-table pb-0 pt-0">
                                                <div class="row container-payment height-auto">
                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">
                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.11982 0.436309C0.64062 0.432235 0.255174 0.841681 0.251702 1.34176C0.248229 1.84184 0.64062 2.24314 1.11982 2.24722C1.7605 2.25265 2.27269 2.69985 2.66595 3.5488C3.03404 4.34243 3.14167 4.99554 3.1547 5.07676L4.18517 12.0091C4.50116 13.66 6.00126 14.9236 7.57691 14.9236H13.7614C15.3371 14.9236 16.8441 13.6483 17.1532 12.0374L18.2114 4.68062C18.4571 3.40049 17.5508 2.24722 16.2581 2.24722H3.96814C3.31531 1.12545 2.32044 0.446451 1.11982 0.436309ZM4.72774 4.05812H16.2581C16.4552 4.05812 16.5403 4.16705 16.5021 4.36942L15.4438 11.7262C15.3041 12.4479 14.5097 13.1127 13.7614 13.1127H7.57691C6.82945 13.1127 6.04121 12.4624 5.8945 11.6979L4.89094 4.7938C4.86056 4.60121 4.79025 4.29598 4.72774 4.05812ZM6.76262 15.829C6.04381 15.829 5.46043 16.4371 5.46043 17.1872C5.46043 17.9373 6.04381 18.5454 6.76262 18.5454C7.48142 18.5454 8.0648 17.9373 8.0648 17.1872C8.0648 16.4371 7.48142 15.829 6.76262 15.829ZM14.5757 15.829C13.8569 15.829 13.2735 16.4371 13.2735 17.1872C13.2735 17.9373 13.856 18.5454 14.5757 18.5454C15.2945 18.5454 15.8779 17.9373 15.8779 17.1872C15.8779 16.4371 15.2954 15.829 14.5757 15.829Z" fill="#636363"/></svg>
                                                                    </div>Carrinhos
                                                                </div>

                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment">
                                                                        <div class="box-payment center">
                                                                            <span>13%</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <div class="box-payment right">
                                                                            <strong class="grey font-size-16">R$12.560,44</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card inner">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Orderbump</strong>
                                                </h5>
                                            </header>
                                            <footer>
                                                <div class="d-flex align-items">
                                                    <!-- <div class="balance col-6">
                                                        <h6 class="grey font-size-14">
                                                            <span class="ico-coin">
                                                                <svg width="17" height="17" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                                                </svg>
                                                            </span>
                                                            Ganhos
                                                        </h6>
                                                        <small>R$</small>
                                                        <strong class="total grey">2.410,20</strong>
                                                    </div>
                                                    <div class="balance col-6">
                                                        <h6 class="grey font-size-14 qtd">Conversões</h6>
                                                        <strong class="total grey">56 vendas</strong>
                                                    </div> -->

                                                    <div class="balance col-4">    
                                                        <div class="box-ico-cash">
                                                            <span class="ico-cash">                                                    
                                                                <svg width="55" height="55" viewBox="0 0 55 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M28.4968 19.0015L36.0525 19.0029L36.1734 19.0168L36.2611 19.0364L36.365 19.0708L36.4541 19.1112L36.5179 19.1468L36.5805 19.1883L36.6445 19.2382L36.7076 19.2965L36.802 19.4062L36.8736 19.5174L36.9271 19.6302L36.9624 19.7355L36.9781 19.8007L36.9873 19.853L36.9983 20.0015V27.5054C36.9983 28.0576 36.5506 28.5054 35.9983 28.5054C35.4854 28.5054 35.0628 28.1193 35.005 27.622L34.9983 27.5054L34.998 22.4155L20.7061 36.7071C20.3456 37.0676 19.7784 37.0953 19.3861 36.7903L19.2919 36.7071C18.9314 36.3466 18.9037 35.7794 19.2087 35.3871L19.2919 35.2929L33.583 21.0015H28.4968C27.9839 21.0015 27.5612 20.6154 27.5035 20.1181L27.4968 20.0015C27.4968 19.4492 27.9445 19.0015 28.4968 19.0015Z" fill="#2E85EC"/>
                                                                    <circle cx="27.5" cy="27.5" r="26.5" stroke="#2E85EC" stroke-width="2"/>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="balance col-8">
                                                        <h6 class="no-orderbump">Sem vendas por orderbump</h6>
                                                        <p class="txt-no-orderbump">Ofereça mais um produto no checkout e aumente sua conversão</p>
                                                    </div>
                                                </div>
                                            </footer>
                                        </div>

                                        <div class="card inner">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Upsell</strong>
                                                </h5>
                                            </header>
                                            <footer>
                                                <div class="d-flex align-items">
                                                    <div class="balance col-6">
                                                        <h6 class="grey font-size-14">
                                                            <span class="ico-coin">
                                                                <svg width="17" height="17" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                                                </svg>
                                                            </span>
                                                            Ganhos
                                                        </h6>
                                                        <small>R$</small>
                                                        <strong class="total grey">1.202,55</strong>
                                                    </div>
                                                    <div class="balance col-6">
                                                        <h6 class="grey font-size-14 qtd">Conversões</h6>
                                                        <strong class="total grey">23 vendas</strong>
                                                    </div>

                                                    <!-- <div class="balance col-4">    
                                                        <div class="box-ico-cash">
                                                            <span class="ico-cash">                                                    
                                                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="55px" height="55px" viewBox="0 0 55 55" enable-background="new 0 0 55 55" xml:space="preserve">  <image id="image0" width="55" height="55" x="0" y="0"
                                                                    href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADcAAAA3CAYAAACo29JGAAAABGdBTUEAALGPC/xhBQAAACBjSFJN
                                                                AAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QA/wD/AP+gvaeTAAAA
                                                                CXBIWXMAAA7DAAAOwwHHb6hkAAAAB3RJTUUH5gIQDSkIZFXSSgAADaVJREFUaN7NmmtwVdd1gL+9
                                                                zzn36l69ESAJSWDeiIcx+IkNJhg9gDRO7NT+lWknbqfu40czndjGxnZnmkxTY5s4mXbaZurU40w7
                                                                yaSOYzvYGIQgftTGPMxTEg+noBcIgd5X995zz9m7P/a9QqArdPWI2/UD/lzts7691l57rbWX8DxP
                                                                MwUiBQgBSkF/XHO5X9Heq+jo13RFFAOuxldgS8gOCqaFJSV5gtI8ycxcSW5QICVoDWpKNAJ7sgtY
                                                                EjwF7b2Kkxd9jrR4NF3yaetV9EYVcd8Aj9gMCUEb8rMkZQWSymKL1RU2y2dZFOdKLAm+Gr8+w0VM
                                                                1HKWhGgCTrR57G5K8On5BC3dioQPOUHBjBzBrHxJca6kMCzIDoghhSOupmtQ09GnuNinuDygiMQh
                                                                YEFFoWTNXIfqJQ7LZ9mEnIlDjhtOCkj4cLjF45efx/nkfzx6o5qibMHyUos75zismGVRXiDJDwmC
                                                                thhy2ZRoDb4G19P0RjUt3YoTF30OXkhwst2na1CTHxLcO9fmkVVBVlfYONb43XVccFLA+auKnx2M
                                                                8V5Dgr6YZm6RpHpJgAcWOcyfLgkHDIXSBmJMBYRZF2DQ1ZzrVOw941LXlOB8lyI/JNiyNMC37gwy
                                                                Z5ocF2BGcEKA50PdaZd//TjO2cs+ZQWSh24N8AfLA5QXSiTGGpMVS5iNaelRvHPC5dfHXdp7FYuL
                                                                LR6/L4uNix0smeHGjQUnhYl+r30a5z8OxnF9zcbFDo+tyWJJsYVg6qLbjd/VGho7fF79JEb9mQRZ
                                                                tuBbdwb547uD5ATFmN+9KZwUcCWieWVflHdOuBSGBX96bxYPrwwQDoy9+FSIJWDA1bxx1OXVT2L0
                                                                RDXfWBHgrzeEmBa+uQ6jwkkBVwY0P9gzyPuNCeYVSZ6oCrF2ngMiM7eYKhEC0PDBFwlerItyoUvx
                                                                1eUBnqy6OaA92mJ9Mc2OfVHeb0ywpNjiuU1hbiuzzLn6EsHg2kauX+CQlyX43q4oO0+6OBKerA6R
                                                                ExRpN1uOAMOE+n/77xi/OemyYLrk+c1hbiu3piRgTEaUhtXlNs9vCjG3SPL2CZeffhLH89P/fiSc
                                                                gHdPufz8cJwZOYInq8OsLLMmnS1MlfgaVpXbPFEVYlq24D8PxdnV6A5dJ6PCSQGnL/v85OMYvobH
                                                                14ZYM9eeErDhl7gQE18nBbh2vsOf3ZeF52t+8nGMc53+CMAhOAHEEvDap3EudCm2LAvw4IrAlJwv
                                                                KeByv2JHfZRX9ke5MqDT7vS4RMM3bg1QuzTA764oXjsQJ+5d/5OhgCIlfPy7BHtPu8wtknz77iBZ
                                                                9uTvMCmgo1/x97uj1J9OIAS09yierglTlD3x60QDIUfw2D1BjrZ67GlKULU4wYZFzpCnSTBW64tp
                                                                fnEkTsyDR1cHmTfdmhKwzgHN9roo+84khtxxT1OC7XVRrkYmZ0GlYcEMi0dWBRlMGP37Y5rUkjJl
                                                                tYMXPI60eFSWWGxeGpi0N6YSgBf2DFLXlEAKqK10qFrsALCrweWlvVG6BicHqIEtyxwWz7Q41Oxx
                                                                uMVDymFwcc98LObB5qUBZuSISV3Sw8H2NCUA2LQ0wLbaMNtqw2xc7KCBdxtcXqybHKDWUJwr2bzU
                                                                IZqAXQ0J3OTZk1LAhS6fQ80es/Il6xc4U2ax3Y0GrKbS4bsbQxSEBEXZgq01IQOopwYQ4CsLHUry
                                                                JAebPVq6TeSUQsDhZo/OAc0ds20qCsdXVtwIdvUGsOolDk9Vh5meDB5Kw8wcydbqEA8sSgKeMi7a
                                                                PUFApWF2ocXtFRYdfYojrb4ppeKeKTyFgLvnmKJwMmDb66Kjgg1XpjhX8nRNEhDYecrlpfooPdGJ
                                                                AQZsuGuO8brDzR6uD/JqRHG2UzEtLKgssSZ01qSA7kHNy/VRdjW4Q2Bbq8PMyEkf7kcAath50uWH
                                                                +6L0TgBQa1hWalEYFpy57NMVUcjWbsXlfkVZgaQkT44bTgroiWpeqo+y85SLBqoWJy2Wc/N7bDjg
                                                                VxY6KA1vHXd5ZV+Uvtj4ADVQkieZlS/p6Fe09SjkhW7FoKupKLQIB8S4roAU2Mv1JkvXGjYudtha
                                                                ExrVYqMBPlMTYt18A/jmcWPB8QBqbRpTFYWSSFzT3K2Q7b0KX8GsPIktM1voOrC9ppBV2kSsp6pD
                                                                zMhJH5QsaYrPdIAleZJttaZe9DW8eWz8gLaE0jw51GqUVwZMrjItW2Sc0F4HdtKA3b/A4ZmaECW5
                                                                I8FSrYjDzR5HWj2Uhhs/pTTMyk8B2kOAKRfNRDchYFq2sdDViEJGXPOH2YHMyEQSbEf99WDbakOU
                                                                5I0O9sbRON95I8J33ojw9glzNtMBlhVInq0Ns3aebVz0mMuP9kcZiGsy0TDFMRAH6fnmI5m6pFLw
                                                                009jvHXcgK1PgpXeBOy/jsb58f4Y3YOarojZmDePuagxAO+bZ+MpeOOoy+sH4hnFA0smu3VKIy1p
                                                                Ik2mVbav4VKvuuaKGYL1xTSl+ZLiPElPVPPDfVF+fcxNq3AKcFsS0FfQ3qcyDlA6aSw7O9l/iLiZ
                                                                0TkS/nJdiHULHO6aY1M82hnDuOKP98fojWnmFEqe3RTGV5rvvx+ltUfxyr4oUsDXbw2kVbK8QPK3
                                                                W8IcavZYWWZn1K8cdA1dOCCwi8LGMboHdUZ3nAZuKZLMnR4YSqfS/eZXR11+9NvrwdbMNeXjs7UM
                                                                Ae7YF0VK+Nry9IAleZIHVwQyev3RGroiJkAWZUtkab5ESriYvBIyEaXN48Rom/HWcRMEeqMG7Lkk
                                                                WGoz1s53eLY2RHmBpHvQnMGdp9xRFfZVZkWzp+Bin8aSJvLKOdMswo6gpUcxmMgsIt1M3j7hsmOf
                                                                yRFnF0q2bQpzTxJs+OasnW+ujrJ8ydWIuVbea3An/H2BccnWHp9wwFzmsqJAMiNH0Nrtc7lfTap5
                                                                885Jlx31JrsvTwaEe28AGw54/wKHp2tMQLoS0bxYF+XdCQKKZDujrUcxM1dSXiCR03Mk82dYXI1o
                                                                mjr8CcO9c8Ll5WRlXZZv0qn75tlj5pbrFxrAkkkCCgGNl8zz18IZ0py5LAdWV5iM4LML3qgNzrEs
                                                                9nK9AZuVL3mmNsT9C5yMzonWsGGRw9Zqk92kAHc1jg8w4cOB8yb7WV1hE7STbYY7ZtsUZQsOXvBo
                                                                71PjysZ3njKuOARWE2J9hmDDAVMJd8qC2+ui7G5KZORJUkBbj+Jwi8eMHMHtFTZokL6CuUUWq8pt
                                                                WnsUH32R2YICeL/RuOLVyDWwVOkyXkkBPl0dojhXJrtmprk0lj4C+PCLBG09itUVNnOmmda/BAg5
                                                                UFsZwJbGElcjN09UDZhpz12JaErzrq/JJipawwOLjYsW5wo6+jX/sOdavzOtLgI6I5qdp1wCNmyq
                                                                DJCVbANJMAd7zVyblWU2J9t99p5OMFqqKQTsPZNge90gnQOakmSxuWGSYMMBq5Y4PFFlqviOfsUP
                                                                9kT57dn0gALY0+TScNFnVbnN3bfYQ9MTMrVgYVjwh6sCWBJ+fjhOS0/6s+d6JlNv71UU5wqeqg6x
                                                                YdHUgA0HrF3i8ERViOnZ5pp667g7IthJAc3dil8ccXEseGRVkPzQtYJ7qJ2ulCk21813qGtK8LPP
                                                                4nx3YwjrBhM6FmyqdBDAw7cF2JDsf0y1aIyLWULw9gmXmkoHW157uhCA68PrB+Kcu+yzaanDugX2
                                                                dTMv172sSgHH23z+5s0I/THN85tDfHVZIK1VEr7pOP2+X1iFMN5yY1dOCpMNfX/XIIVhyY6Hs1lW
                                                                ev0TwHV2URpWlFk8dk+QhK/5xw9iHG3zRlgvZcEv4+lY65FgloQjLR7/9KF5antsTZClpSPfNkaq
                                                                reGhlQEeWhmgtVvxwu4oZy/7aQH/L8SS0NTh88KeKO09im+uDPL1W4Npn9pGqJx6GvqLdSZQHG/3
                                                                +d6uKGf+HwCmwP7uvUFOXfKpWuLw5+uyyLLTPyOmVVdpmJ4teKoqxNr5NoeaPZ77zSBHWjwz6vQl
                                                                QwnMGTt0wehxrM3n/gXOmNMMY86htPUottdFqT+ToKxA8lfrsqhdGiAwgVmsiYhMBpR3G1z++cMY
                                                                7b2K6uQ1ka69kTFcavGrEc2/fBTjV8fiWBIeXBHkj+4KMrtQovn9BBaR9JDzXYrXD8R4J9n0/eZt
                                                                QR5fmzXmgE1GcCnAWALeOhHn1U/itPUoFs6UPLoqSM2SANNzrg2zTVZSiUPngOb9Rpdffh7nXKei
                                                                olDyJ2uy+NqKAMEMr6CMp/ZE8p+Giz7//mmM/WcTJHyoLLHYsizA/fMdygvl0BWhdWazAikLpe6z
                                                                1h7FB+cSvNfg0nDJJ2jDhoUO374niyUlFmS47rjghu/sYELz0Rdm3vLzFo+4Z3oWt1fY3HWLTWWx
                                                                RUmeJDsosGX60QytIaEgEtdc6lM0XvI5kHy6bu9VZNmwqsLm0dVB1s6zCTnjHw6Y0KSswLyj90U1
                                                                n13w2NXocrjFo7PfVBMFIUFZgSn1S/Ik08KS7IB5xlVKE3Gha1BxsVfR2mNmobujxiQzcwV3zHao
                                                                rXS4c45NXpZAqYlNjEx4DHg4ZNyD5i4z33ykxedsp09HvyLi6ptW9rZl2t/FuZJFM82M8+oKi4pC
                                                                i6DNhKGmBG64pEZ9Xc9Ypb1X0dJt/u+KaAZcjac0thTkBAVFYUFpvqSiUFKWLykMy6FcdaqumP8F
                                                                CHotCRcObH4AAAAldEVYdGRhdGU6Y3JlYXRlADIwMjItMDItMTZUMTY6NDI6MTIrMDM6MDBCCShp
                                                                AAAAJXRFWHRkYXRlOm1vZGlmeQAyMDIyLTAyLTE2VDE2OjQyOjEyKzAzOjAwM1SQ1QAAAABJRU5E
                                                                rkJggg==" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="balance col-8">
                                                        <h6 class="no-orderbump">Sem vendas por upsell</h6>
                                                        <p class="txt-no-orderbump">Ofereça mais um produto no checkout e aumente sua conversão</p>
                                                    </div> -->
                                                </div>
                                            </footer>
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
                                    <h6 class="font-size-14">Hoje</h6>
                                </header>
                                <div class="d-flex box-graph-dist">
                                    <div class="distribution-graph-seller"></div>
                                    <div class="info-graph">
                                        <h6 class="font-size-14 grey">Saldo Total</h6>
                                        <em>
                                            <small class="font-size-14">R$</small>
                                            <strong class="grey">2.654.202,00</strong>
                                        </em>
                                    </div>
                                </div>
                                <div class="d-flex box-distribution secondary">
                                    <div class="distribution-area">
                                        <div class="item">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#1BE4A8" stroke-width="3"/>
                                                </svg>
                                            </span>
                                            <small class="font-size-14">Aprovadas</small>
                                        </div>
                                        <div class="item right">
                                            <strong class="grey font-size-14">R$162.445,22</strong>
                                        </div>
                                        <div class="item right"><small class="grey font-size-14">46%</small></div>
                                    </div>

                                    <div class="distribution-area">
                                        <div class="item">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#FFBA06" stroke-width="3"/>
                                                </svg>
                                            </span>
                                            <small class="font-size-14">Pendentes</small>
                                        </div>
                                        <div class="item right">
                                            <strong class="grey font-size-14">R$148.254,63</strong>
                                        </div>
                                        <div class="item right">
                                            <small class="grey font-size-14">24%</small>
                                        </div>
                                    </div>

                                    <div class="distribution-area">
                                        <div class="item">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#665FE8" stroke-width="3"/>
                                                </svg>
                                            </span>
                                            <small class="font-size-14">Canceladas</small>
                                        </div>
                                        <div class="item right">
                                            <strong class="grey font-size-14">R$8.648,75</strong>
                                        </div>
                                        <div class="item right">
                                            <small class="grey font-size-14">12%</small>
                                        </div>
                                    </div>

                                    <div class="distribution-area">
                                        <div class="item">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#FF2F2F" stroke-width="3"/>
                                                </svg>
                                            </span>
                                            <small class="font-size-14">Recusadas</small>
                                        </div>
                                        <div class="item right">
                                            <strong class="grey font-size-14">R$8.648,75</strong>
                                        </div>
                                        <div class="item right">
                                            <small class="grey font-size-14">4%</small>
                                        </div>
                                    </div>

                                    <div class="distribution-area">
                                        <div class="item">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#00C2FF" stroke-width="3"/>
                                                </svg>
                                            </span>
                                            <small class="font-size-14">Reembolsos</small>
                                        </div>
                                        <div class="item right">
                                            <strong class="grey font-size-14">R$8.648,75</strong>
                                        </div>
                                        <div class="item right">
                                            <small class="grey font-size-14">3.5%</small>
                                        </div>
                                    </div>

                                    <div class="distribution-area">
                                        <div class="item">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#D10000" stroke-width="3"/>
                                                </svg>
                                            </span>
                                            <small class="font-size-14">Chargebacks</small>
                                        </div>
                                        <div class="item right">
                                            <strong class="grey font-size-14">R$8.648,75</strong>
                                        </div>
                                        <div class="item right">
                                            <small class="grey font-size-14">1.2%</small>
                                        </div>
                                    </div>

                                    <div class="distribution-area">
                                        <div class="item">
                                            <span>
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#767676" stroke-width="3"/>
                                                </svg>
                                            </span>
                                            <small class="font-size-14">Outros</small>
                                        </div>
                                        <div class="item right">
                                            <strong class="grey font-size-14">R$8.648,75</strong>
                                        </div>
                                        <div class="item right">
                                            <small class="grey font-size-14">1.2%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div class="d-flex sub-distribution">
                                    <div class="card inner">
                                        <header class="d-flex title-graph">
                                            <h5 class="grey font-size-16">
                                                <strong>Vendas mais frequentes</strong>
                                            </h5>
                                        </header>
                                        <div id="payment-type-items" class="custom-table scrollbar pb-0 pt-0">
                                            <div class="row">
                                                <div class="container">
                                                    <div class="data-holder b-bottom scroll-212">
                                                        <div class="box-payment-option pad-0">
                                                            <div class="d-flex align-items list-sales">
                                                                <div class="d-flex align-items">
                                                                    <div>
                                                                        <figure class="box-ico">
                                                                            <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                        </figure>
                                                                    </div>
                                                                    <div>
                                                                        <span>Casaco Tiles Neon F...</span>
                                                                    </div>
                                                                </div>
                                                                <div class="grey font-size-14">000</div>
                                                                <div class="grey font-size-14"><strong>9.5K</strong></div>
                                                            </div>
                                                        </div>

                                                        <div class="box-payment-option pad-0">
                                                            <div class="d-flex align-items list-sales">
                                                                <div class="d-flex align-items">
                                                                    <div>
                                                                        <figure class="box-ico">
                                                                            <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                        </figure>
                                                                    </div>
                                                                    <div>
                                                                        <span>Casaco Tiles Neon F...</span>
                                                                    </div>
                                                                </div>
                                                                <div class="grey font-size-14">000</div>
                                                                <div class="grey font-size-14"><strong>9.5K</strong></div>
                                                            </div>
                                                        </div>

                                                        <div class="box-payment-option pad-0">
                                                            <div class="d-flex align-items list-sales">
                                                                <div class="d-flex align-items">
                                                                    <div>
                                                                        <figure class="box-ico">
                                                                            <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                        </figure>
                                                                    </div>
                                                                    <div>
                                                                        <span>Casaco Tiles Neon F...</span>
                                                                    </div>
                                                                </div>
                                                                <div class="grey font-size-14">000</div>
                                                                <div class="grey font-size-14"><strong>9.5K</strong></div>
                                                            </div>
                                                        </div>

                                                        <div class="box-payment-option pad-0">
                                                            <div class="d-flex align-items list-sales">
                                                                <div class="d-flex align-items">
                                                                    <div>
                                                                        <figure class="box-ico">
                                                                            <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                        </figure>
                                                                    </div>
                                                                    <div>
                                                                        <span>Casaco Tiles Neon F...</span>
                                                                    </div>
                                                                </div>
                                                                <div class="grey font-size-14">000</div>
                                                                <div class="grey font-size-14"><strong>9.5K</strong></div>
                                                            </div>
                                                        </div>

                                                        <div class="box-payment-option pad-0">
                                                            <div class="d-flex align-items list-sales">
                                                                <div class="d-flex align-items">
                                                                    <div>
                                                                        <figure class="box-ico">
                                                                            <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                        </figure>
                                                                    </div>
                                                                    <div>
                                                                        <span>Casaco Tiles Neon F...</span>
                                                                    </div>
                                                                </div>
                                                                <div class="grey font-size-14">000</div>
                                                                <div class="grey font-size-14"><strong>9.5K</strong></div>
                                                            </div>
                                                        </div>

                                                        <div class="box-payment-option pad-0">
                                                            <div class="d-flex align-items list-sales">
                                                                <div class="d-flex align-items">
                                                                    <div>
                                                                        <figure class="box-ico">
                                                                            <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                        </figure>
                                                                    </div>
                                                                    <div>
                                                                        <span>Casaco Tiles Neon F...</span>
                                                                    </div>
                                                                </div>
                                                                <div class="grey font-size-14">000</div>
                                                                <div class="grey font-size-14"><strong>9.5K</strong></div>
                                                            </div>
                                                        </div>

                                                        <div class="box-payment-option pad-0">
                                                            <div class="d-flex align-items list-sales">
                                                                <div class="d-flex align-items">
                                                                    <div>
                                                                        <figure class="box-ico">
                                                                            <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                        </figure>
                                                                    </div>
                                                                    <div>
                                                                        <span>Casaco Tiles Neon F...</span>
                                                                    </div>
                                                                </div>
                                                                <div class="grey font-size-14">000</div>
                                                                <div class="grey font-size-14"><strong>9.5K</strong></div>
                                                            </div>
                                                        </div>

                                                        <div class="box-payment-option pad-0">
                                                            <div class="d-flex align-items list-sales">
                                                                <div class="d-flex align-items">
                                                                    <div>
                                                                        <figure class="box-ico">
                                                                            <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                        </figure>
                                                                    </div>
                                                                    <div>
                                                                        <span>Casaco Tiles Neon F...</span>
                                                                    </div>
                                                                </div>
                                                                <div class="grey font-size-14">000</div>
                                                                <div class="grey font-size-14"><strong>9.5K</strong></div>
                                                            </div>
                                                        </div>

                                                        <div class="box-payment-option pad-0">
                                                            <div class="d-flex align-items list-sales">
                                                                <div class="d-flex align-items">
                                                                    <div>
                                                                        <figure class="box-ico">
                                                                            <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                        </figure>
                                                                    </div>
                                                                    <div>
                                                                        <span>Casaco Tiles Neon F...</span>
                                                                    </div>
                                                                </div>
                                                                <div class="grey font-size-14">000</div>
                                                                <div class="grey font-size-14"><strong>9.5K</strong></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card inner">
                                        <header class="d-flex title-graph recurrence">
                                            <h5 class="grey font-size-16">
                                                <strong>Recorrência</strong>
                                            </h5>
                                        </header>
                                        <canvas id="salesChart"></canvas>
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
    <script type='text/javascript' src='{{asset('modules/reports/js/chart-js/Chartjs-3.7-min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/moment.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/global/js/daterangepicker.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-tooltip.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-legend.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/reports.js?v=' . uniqid())}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/reports-sales.js?v=' . uniqid())}}'></script>
@endpush

