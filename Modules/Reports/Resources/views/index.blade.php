@extends("layouts.master")
@section('title', '- Relatório de Vendas')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ mix('build/layouts/reports/index.min.css') }}">
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
                                        <div class="card inner" id="card-payments">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Meios de pagamento</strong>
                                                </h5>
                                            </header>
                                            <div class="onPreLoad" id="block-payments"></div>
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
                                            <div class="custom-table scrollbar pb-0 pt-0">
                                                <div class="row container-payment">
                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">

                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <svg width="21" height="16" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M13.806 10.415C13.3901 10.415 13.053 10.7814 13.053 11.2334C13.053 11.6855 13.3901 12.0519 13.806 12.0519H16.3163C16.7322 12.0519 17.0693 11.6855 17.0693 11.2334C17.0693 10.7814 16.7322 10.415 16.3163 10.415H13.806ZM2.30106 0.047699C1.03022 0.047699 0 1.16738 0 2.54858V13.0068C0 14.388 1.03022 15.5077 2.30106 15.5077H17.7809C19.0517 15.5077 20.082 14.388 20.082 13.0068V2.54858C20.082 1.16738 19.0517 0.047699 17.7809 0.047699H2.30106ZM1.25512 13.0068V5.95886H18.8268V13.0068C18.8268 13.6346 18.3586 14.1435 17.7809 14.1435H2.30106C1.7234 14.1435 1.25512 13.6346 1.25512 13.0068ZM1.25512 4.59475V2.54858C1.25512 1.92076 1.7234 1.41181 2.30106 1.41181H17.7809C18.3586 1.41181 18.8268 1.92076 18.8268 2.54858V4.59475H1.25512Z" fill="#636363"></path>
                                                                        </svg>
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
                                <header class="d-flex title-graph">
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
    <script type='text/javascript' src='{{ mix('build/layouts/reports/sales.min.js') }}'></script>
@endpush
