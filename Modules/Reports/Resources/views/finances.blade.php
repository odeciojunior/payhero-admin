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
                                        <div class="new-finance-graph"></div>
                                    </div>
                                </section>
                            </div>

                            <div class="">
                                <div class="d-flex justify-content-between sub-comission">
                                    <div class="inner-comission">
                                        <div class="card inner cash">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Cashback</strong>
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
                                                            Recebido
                                                        </h6>
                                                        <small>R$</small>
                                                        <strong class="total grey">1.655,00</strong>
                                                    </div>
                                                    <div class="balance col-6">
                                                        <h6 class="grey font-size-14 qtd">Quantidade</h6>
                                                        <strong class="total grey">240 vendas</strong>
                                                    </div> -->

                                                    <div class="balance col-4">
                                                        <div class="box-ico-cash">
                                                            <span class="ico-cash">
                                                                <svg width="47" height="47" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 17.5C14.366 17.5 17.5 14.366 17.5 10.5C17.5 6.63401 14.366 3.5 10.5 3.5C6.63401 3.5 3.5 6.63401 3.5 10.5C3.5 14.366 6.63401 17.5 10.5 17.5ZM10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" fill="#1BE4A8"/>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.625 6.125C9.625 5.64175 10.0168 5.25 10.5 5.25C10.9832 5.25 11.375 5.64175 11.375 6.125C12.8247 6.125 14 7.30025 14 8.75C14 9.23325 13.6082 9.625 13.125 9.625C12.6418 9.625 12.25 9.23325 12.25 8.75C12.25 8.26675 11.8582 7.875 11.375 7.875H10.5H9.40049C9.04123 7.875 8.75 8.16623 8.75 8.52549C8.75 8.80548 8.92916 9.05406 9.19479 9.1426L12.3586 10.1972C13.3388 10.5239 14 11.4413 14 12.4745C14 13.8003 12.9253 14.875 11.5995 14.875H11.375C11.375 15.3582 10.9832 15.75 10.5 15.75C10.0168 15.75 9.625 15.3582 9.625 14.875C8.17525 14.875 7 13.6997 7 12.25C7 11.7668 7.39175 11.375 7.875 11.375C8.35825 11.375 8.75 11.7668 8.75 12.25C8.75 12.7332 9.14175 13.125 9.625 13.125H10.5H11.5995C11.9588 13.125 12.25 12.8338 12.25 12.4745C12.25 12.1945 12.0708 11.9459 11.8052 11.8574L8.64139 10.8028C7.66117 10.4761 7 9.55873 7 8.52549C7 7.19974 8.07474 6.125 9.40049 6.125L9.625 6.125Z" fill="#1BE4A8"/>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="balance col-8">
                                                        <h6 class="no-cashback">Ainda sem cashback :(</h6>
                                                        <p class="txt-no-cashback">Suba de nível e mantenha a saúde da conta boa para receber cashback</p>
                                                    </div>
                                                </div>
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
                                    <div class="inner-comission">
                                        <!-- <div class="card inner relative">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Saques</strong>
                                                </h5>
                                            </header>
                                            <canvas id="financesChart" height="285"></canvas>
                                        </div> -->
                                        <div class="card inner relative">
                                            <header class="d-flex title-graph no-withdrawals">
                                                <h5 class="grey font-size-16">
                                                    <strong>Saques</strong>
                                                </h5>
                                                <small>Últimos 6 meses</small>
                                            </header>
                                            <footer class="d-flex footer-withdrawals">
                                                <div>
                                                    <svg width="122" height="151" viewBox="0 0 122 151" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M60.994 144.494C94.68 144.494 121.988 117.186 121.988 83.5C121.988 49.8139 94.68 22.506 60.994 22.506C27.3079 22.506 0 49.8139 0 83.5C0 117.186 27.3079 144.494 60.994 144.494Z" fill="url(#paint0_linear_1185_948)"/>
                                                        <path d="M95.9638 57.4758H26.024C23.7783 57.4758 21.9578 59.2964 21.9578 61.5421V146.934C21.9578 149.179 23.7783 151 26.024 151H95.9638C98.2095 151 100.03 149.179 100.03 146.934V61.5421C100.03 59.2964 98.2095 57.4758 95.9638 57.4758Z" fill="white"/>
                                                        <path d="M52.8614 69.6746H31.7169C30.3694 69.6746 29.2771 70.7669 29.2771 72.1143C29.2771 73.4618 30.3694 74.5541 31.7169 74.5541H52.8614C54.2089 74.5541 55.3012 73.4618 55.3012 72.1143C55.3012 70.7669 54.2089 69.6746 52.8614 69.6746Z" fill="#B4DAFF"/>
                                                        <path d="M67.5 80.2467H31.7169C30.3694 80.2467 29.2771 81.339 29.2771 82.6865C29.2771 84.0339 30.3694 85.1262 31.7169 85.1262H67.5C68.8474 85.1262 69.9398 84.0339 69.9398 82.6865C69.9398 81.339 68.8474 80.2467 67.5 80.2467Z" fill="#DFEAFB"/>
                                                        <path d="M52.8614 91.6324H31.7169C30.3694 91.6324 29.2771 92.7248 29.2771 94.0722C29.2771 95.4196 30.3694 96.512 31.7169 96.512H52.8614C54.2089 96.512 55.3012 95.4196 55.3012 94.0722C55.3012 92.7248 54.2089 91.6324 52.8614 91.6324Z" fill="#B4DAFF"/>
                                                        <path d="M67.5 102.205H31.7169C30.3694 102.205 29.2771 103.297 29.2771 104.644C29.2771 105.992 30.3694 107.084 31.7169 107.084H67.5C68.8474 107.084 69.9398 105.992 69.9398 104.644C69.9398 103.297 68.8474 102.205 67.5 102.205Z" fill="#DFEAFB"/>
                                                        <path d="M52.8614 113.59H31.7169C30.3694 113.59 29.2771 114.683 29.2771 116.03C29.2771 117.378 30.3694 118.47 31.7169 118.47H52.8614C54.2089 118.47 55.3012 117.378 55.3012 116.03C55.3012 114.683 54.2089 113.59 52.8614 113.59Z" fill="#B4DAFF"/>
                                                        <path d="M67.5 124.163H31.7169C30.3694 124.163 29.2771 125.255 29.2771 126.602C29.2771 127.95 30.3694 129.042 31.7169 129.042H67.5C68.8474 129.042 69.9398 127.95 69.9398 126.602C69.9398 125.255 68.8474 124.163 67.5 124.163Z" fill="#DFEAFB"/>
                                                        <g filter="url(#filter0_d_1185_948)">
                                                            <path d="M95.9638 16H26.024C23.7783 16 21.9578 17.8205 21.9578 20.0663V44.4639C21.9578 46.7096 23.7783 48.5301 26.024 48.5301H95.9638C98.2095 48.5301 100.03 46.7096 100.03 44.4639V20.0663C100.03 17.8205 98.2095 16 95.9638 16Z" fill="#1485FD"/>
                                                        </g>
                                                        <path d="M52.8614 24.9458H31.7169C30.3694 24.9458 29.2771 26.0381 29.2771 27.3856C29.2771 28.733 30.3694 29.8253 31.7169 29.8253H52.8614C54.2089 29.8253 55.3012 28.733 55.3012 27.3856C55.3012 26.0381 54.2089 24.9458 52.8614 24.9458Z" fill="#B4DAFF"/>
                                                        <path d="M67.5 35.5181H31.7169C30.3694 35.5181 29.2771 36.6104 29.2771 37.9578C29.2771 39.3053 30.3694 40.3976 31.7169 40.3976H67.5C68.8474 40.3976 69.9398 39.3053 69.9398 37.9578C69.9398 36.6104 68.8474 35.5181 67.5 35.5181Z" fill="white"/>
                                                        <defs>
                                                            <filter id="filter0_d_1185_948" x="1.95776" y="0" width="118.072" height="72.5302" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                                            <feOffset dy="4"/>
                                                            <feGaussianBlur stdDeviation="10"/>
                                                            <feComposite in2="hardAlpha" operator="out"/>
                                                            <feColorMatrix type="matrix" values="0 0 0 0 0.180392 0 0 0 0 0.521569 0 0 0 0 0.92549 0 0 0 0.17 0"/>
                                                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1185_948"/>
                                                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1185_948" result="shape"/>
                                                            </filter>
                                                            <linearGradient id="paint0_linear_1185_948" x1="60.994" y1="22.506" x2="60.994" y2="144.494" gradientUnits="userSpaceOnUse">
                                                                <stop stop-color="#E3ECFA"/>
                                                                <stop offset="1" stop-color="#DAE7FF"/>
                                                            </linearGradient>
                                                        </defs>
                                                    </svg>
                                                </div>

                                                <div class="data-withdrawals">
                                                    <h6>Sem dados, por enquanto...</h6>
                                                    <p>
                                                    Ainda faltam dados suficientes para a comparação, continue rodando!
                                                    </p>
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
                            <div class="">
                                <div class="d-flex sub-distribution">
                                    <div class="card inner">
                                        <header class="d-flex title-graph">
                                            <h5 class="grey font-size-16">
                                                <strong>Pendente</strong>
                                            </h5>
                                            <a href="{{ route('reports.pending') }}" class="box-link silver">
                                                <span>Acessar&nbsp&nbsp</span>
                                                <i class="o-arrow-right-1 redirect"></i>
                                            </a>
                                        </header>
                                        <footer class="">
                                            <div class="d-flex">
                                                <div class="balance col-3">
                                                    <h6 class="grey font-size-14">Total</h6>
                                                    <strong class="grey total">1.2K</strong>
                                                </div>
                                                <div class="balance col-9">
                                                    <h6 class="font-size-14">Saldo</h6>
                                                    <small>R$</small>
                                                    <strong class="total orange">24.588,52</strong>
                                                </div>
                                            </div>
                                        </footer>
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
                                                    <strong class="total red">4.588,52</strong>
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
    <!--script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script-->
    <script type='text/javascript' src='{{asset('modules/reports/js/chart-js/Chartjs-3.7-min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/moment.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/global/js/daterangepicker.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-tooltip.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-legend.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/reports.js?v=' . uniqid())}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/reports-finance.js?v=' . uniqid())}}'></script>
@endpush

