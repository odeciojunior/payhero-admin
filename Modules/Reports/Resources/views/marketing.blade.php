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
                                <span class="box-title ico-mkt">Marketing</span>
                                Marketing
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
					<div class="col-12 box-items-finance mkt">
                        <div class="row mb-20">
                            <div class="fianance-items col-md-4 col-6 pr-5 pr-md-15">
                                <div class="finance-card border blue mb-10">
                                    <span class="title">Acessos</span>
                                    <div class="d-flex">
                                        <strong class="number">74.860</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="fianance-items col-md-4 col-6 pr-5 pr-md-15">
                                <div class="finance-card border purple mb-10">
                                    <span class="title">Vendas</span>
                                    <div class="d-flex">
                                        <span class="detail">R$</span>
                                        <strong class="number">1.440,20</strong>
                                        <small class="percent">(52%)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="fianance-items col-md-4 col-6 pr-5 pr-md-15">
                                <div class="finance-card border green mb-10">
                                    <span class="title">Receita</span>
                                    <div class="d-flex">
                                        <span class="detail">R$</span>
                                        <strong class="number">1.457.080,55</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
				</div>

                <div class="row">
                    <div class="container d-flex box-chart-primary">
                        <!-- comission -->    
                        <div class="commission mkt">
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
                                        <div class="card inner">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Resumo</strong>
                                                </h5>
                                                <a href="#" class="box-link silver" style="visibility: hidden;">
                                                    <span>Acessar&nbsp;&nbsp;</span>
                                                    <i class="o-arrow-right-1 redirect"></i>
                                                </a>
                                            </header>
                                            <footer>
                                                <div class="d-flex align-items box-resume">
                                                    <div class="balance col-6">
                                                        <header class="grey font-size-14 header-resume">
                                                            <span class="ico-coin">
                                                                <svg width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M7.25 0C7.66421 0 8 0.335786 8 0.75V3.25C8 3.66421 7.66421 4 7.25 4C6.83579 4 6.5 3.66421 6.5 3.25V0.75C6.5 0.335786 6.83579 0 7.25 0ZM2.46965 1.96981C2.76253 1.67691 3.23741 1.67689 3.53031 1.96977L5.28031 3.71965C5.57322 4.01253 5.57323 4.48741 5.28035 4.78031C4.98747 5.07322 4.51259 5.07323 4.21969 4.78035L2.46969 3.03047C2.17678 2.73759 2.17677 2.26271 2.46965 1.96981ZM12.0303 1.96979C12.3232 2.26269 12.3232 2.73756 12.0303 3.03045L10.2803 4.78045C9.98744 5.07335 9.51256 5.07335 9.21967 4.78045C8.92678 4.48756 8.92678 4.01269 9.21967 3.71979L10.9697 1.96979C11.2626 1.6769 11.7374 1.6769 12.0303 1.96979ZM0.5 6.75C0.5 6.33579 0.835786 6 1.25 6H3.75C4.16421 6 4.5 6.33579 4.5 6.75C4.5 7.16421 4.16421 7.5 3.75 7.5H1.25C0.835786 7.5 0.5 7.16421 0.5 6.75ZM8.67734 6.48562C7.82176 5.75227 6.5 6.36019 6.5 7.48706V18.7608C6.5 19.9715 7.99462 20.5426 8.80191 19.6403L11.4165 16.7181C11.6918 16.4104 12.0801 16.2274 12.4927 16.2109L16.3327 16.0573C17.5304 16.0094 18.0484 14.518 17.1384 13.7379L8.67734 6.48562ZM8 18.287V7.88067L15.8123 14.5769L12.4327 14.7121C11.6146 14.7448 10.8446 15.1077 10.2986 15.7179L8 18.287Z" fill="#2E85EC"/>
                                                                </svg>
                                                            </span>
                                                            <h6>
                                                                <small>Acessos</small>
                                                                <strong>20758</strong>
                                                            </h6>
                                                        </header>
                                                    </div>
                                                    <div class="balance col-6">
                                                        <header class="grey font-size-14 header-resume">
                                                            <span class="ico-coin">
                                                                <img width="34px" height="34px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                            </span>
                                                            <h6>
                                                                <small>Produto + vendido</small>
                                                                <strong>324</strong>
                                                            </h6>
                                                        </header>                                                        
                                                    </div>
                                                    <div class="balance col-6">
                                                        <header class="grey font-size-14 header-resume">
                                                            <span class="ico-coin">
                                                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M1.11982 0.436309C0.64062 0.432235 0.255174 0.841681 0.251702 1.34176C0.248229 1.84184 0.64062 2.24314 1.11982 2.24722C1.7605 2.25265 2.27269 2.69985 2.66595 3.5488C3.03404 4.34243 3.14167 4.99554 3.1547 5.07676L4.18517 12.0091C4.50116 13.66 6.00126 14.9236 7.57691 14.9236H13.7614C15.3371 14.9236 16.8441 13.6483 17.1532 12.0374L18.2114 4.68062C18.4571 3.40049 17.5508 2.24722 16.2581 2.24722H3.96814C3.31531 1.12545 2.32044 0.446451 1.11982 0.436309ZM4.72774 4.05812H16.2581C16.4552 4.05812 16.5403 4.16705 16.5021 4.36942L15.4438 11.7262C15.3041 12.4479 14.5097 13.1127 13.7614 13.1127H7.57691C6.82945 13.1127 6.04121 12.4624 5.8945 11.6979L4.89094 4.7938C4.86056 4.60121 4.79025 4.29598 4.72774 4.05812ZM6.76262 15.829C6.04381 15.829 5.46043 16.4371 5.46043 17.1872C5.46043 17.9373 6.04381 18.5454 6.76262 18.5454C7.48142 18.5454 8.0648 17.9373 8.0648 17.1872C8.0648 16.4371 7.48142 15.829 6.76262 15.829ZM14.5757 15.829C13.8569 15.829 13.2735 16.4371 13.2735 17.1872C13.2735 17.9373 13.856 18.5454 14.5757 18.5454C15.2945 18.5454 15.8779 17.9373 15.8779 17.1872C15.8779 16.4371 15.2954 15.829 14.5757 15.829Z" fill="#2E85EC"/>
                                                                </svg>
                                                            </span>
                                                            <h6>
                                                                <small>Conversão</small>
                                                                <strong>78%</strong>
                                                            </h6>
                                                        </header>                                                        
                                                    </div>
                                                    <div class="balance col-6">
                                                        <header class="grey font-size-14 header-resume">
                                                            <span class="ico-coin">
                                                                <svg width="17" height="21" viewBox="0 0 17 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M8.5 0C8.91421 0 9.25 0.335786 9.25 0.75V2.0033H11.25C12.2165 2.0033 13 2.7868 13 3.7533V9.0033H15.25C16.2165 9.0033 17 9.7868 17 10.7533V18.2533C17 19.2198 16.2165 20.0033 15.25 20.0033H6.25452L6.25 20.0033H1.75C0.783502 20.0033 0 19.2198 0 18.2533V9.82392C0 9.20503 0.326894 8.63216 0.859717 8.3173L4 6.46166V3.7533C4 2.7868 4.7835 2.0033 5.75 2.0033H7.75V0.75C7.75 0.335786 8.08579 0 8.5 0ZM5.5 6.02174C6.3097 6.14174 7 6.82833 7 7.75572V18.5033H10V10.7533C10 9.87168 10.6519 9.14233 11.5 9.02102V3.7533C11.5 3.61522 11.3881 3.5033 11.25 3.5033H8.50942L8.5 3.50335L8.49058 3.5033H5.75C5.61193 3.5033 5.5 3.61522 5.5 3.7533V6.02174ZM11.75 10.5033C11.6119 10.5033 11.5 10.6152 11.5 10.7533V18.5033H15.25C15.3881 18.5033 15.5 18.3914 15.5 18.2533V10.7533C15.5 10.6152 15.3881 10.5033 15.25 10.5033H11.75ZM5.12282 7.54049L1.62282 9.60869C1.5467 9.65367 1.5 9.73551 1.5 9.82392V18.2533C1.5 18.3914 1.61193 18.5033 1.75 18.5033H5.5V7.75572C5.5 7.56214 5.28947 7.44201 5.12282 7.54049Z" fill="#2E85EC"/>
                                                                </svg>
                                                            </span>
                                                            <h6>
                                                                <small>Maior comprador</small>
                                                                <strong>SP</strong>
                                                            </h6>
                                                        </header>
                                                    </div>
                                                </div>
                                            </footer>
                                        </div>
                                        <div class="card inner pad-bottom-0">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Dispositivos</strong>
                                                </h5>
                                            </header>
                                            <div class="custom-table pb-0 pt-0">
                                                <div class="row container-devices">
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
                                                                            <span class="silver">83%</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <div class="box-payment right">
                                                                            <strong class="grey font-size-14">R$265.210,55</strong>
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
                                                                            <span class="silver">5%</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <div class="box-payment right">
                                                                            <strong class="grey font-size-14">R$265.210,55</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="d-flex align-items cash-box">
                                                <div>
                                                    <span class="ico-coin mkt">
                                                        <svg width="12" height="20" viewBox="0 0 12 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M4.5 15.7143C4.08579 15.7143 3.75 16.0341 3.75 16.4286C3.75 16.8231 4.08579 17.1429 4.5 17.1429H7.5C7.91421 17.1429 8.25 16.8231 8.25 16.4286C8.25 16.0341 7.91421 15.7143 7.5 15.7143H4.5ZM2.625 0C1.17525 0 0 1.11929 0 2.5V17.5C0 18.8807 1.17525 20 2.625 20H9.375C10.8247 20 12 18.8807 12 17.5V2.5C12 1.11929 10.8247 0 9.375 0H2.625ZM1.5 2.5C1.5 1.90827 2.00368 1.42857 2.625 1.42857H9.375C9.99632 1.42857 10.5 1.90827 10.5 2.5V17.5C10.5 18.0917 9.99632 18.5714 9.375 18.5714H2.625C2.00368 18.5714 1.5 18.0917 1.5 17.5V2.5Z" fill="#2E85EC"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="mkt-msg-conversion">
                                                        Smartphones com Android são os dispositivos mais usados e representam <strong>72% das suas conversões.</strong>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="inner-comission">
                                        <div class="card inner pad-bottom-0">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Cupons</strong>
                                                </h5>
                                                <a href="#" class="box-link silver">
                                                    <span>Acessar&nbsp;&nbsp;</span>
                                                    <i class="o-arrow-right-1 redirect"></i>
                                                </a>
                                            </header>
                                            <div class="container d-flex justify-content-between box-donut">
                                                <div class="new-graph-pie-mkt"></div>
                                                <div class="data-pie">
                                                    <ul>
                                                        <li>
                                                            <div class="donut-pie blue">
                                                                <figure>
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#2E85EC" stroke-width="3"/>
                                                                    </svg>
                                                                </figure>
                                                                <div>Mariana20</div>
                                                            </div>
                                                            <div class="grey bold">18</div>
                                                        </li>
                                                        <li>
                                                            <div class="donut-pie pink">
                                                                <figure>
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#F43F5E" stroke-width="3"/>
                                                                    </svg>
                                                                </figure>
                                                                <div>Cassio15</div>
                                                            </div>
                                                            <div class="grey bold">16</div>
                                                        </li>
                                                        <li>
                                                            <div class="donut-pie orange">
                                                                <figure>
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#FF7900" stroke-width="3"/>
                                                                    </svg>																			
                                                                </figure>
                                                                <div>Agosto20</div>
                                                            </div>
                                                            <div class="grey bold">10</div>
                                                        </li>
                                                        <li>
                                                            <div class="donut-pie purple">
                                                                <figure>
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#665FE8" stroke-width="3"/>
                                                                    </svg>																			
                                                                </figure>
                                                                <div>meular15</div>
                                                            </div>
                                                            <div class="grey bold">6</div>
                                                        </li>
                                                        <li>
                                                            <div class="donut-pie">
                                                                <figure>
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#9E9E9E" stroke-width="3"/>
                                                                    </svg>																			
                                                                </figure>
                                                                <div>Outros</div>
                                                            </div>
                                                            <div class="grey bold">32</div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card inner pad-bottom-0">
                                            <header class="d-flex title-graph">
                                                <h5 class="grey font-size-16">
                                                    <strong>Sistemas</strong>
                                                </h5>
                                            </header>
                                            <div class="custom-table pb-0 pt-0">
                                                <div class="row container-devices">
                                                    <div class="container">
                                                        <div class="data-holder b-bottom">
                                                            <div class="box-payment-option pad-0">
                                                                
                                                                <div class="col-payment grey box-image-payment">
                                                                    <div class="box-ico">
                                                                        <svg width="19" height="11" viewBox="0 0 19 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M13.8798 8.22017C13.7228 8.22017 13.5693 8.1723 13.4387 8.08263C13.3081 7.99296 13.2063 7.8655 13.1462 7.71638C13.0861 7.56725 13.0704 7.40316 13.101 7.24486C13.1317 7.08655 13.2073 6.94114 13.3183 6.827C13.4294 6.71287 13.5709 6.63514 13.7249 6.60365C13.879 6.57216 14.0386 6.58833 14.1837 6.65009C14.3288 6.71186 14.4528 6.81646 14.5401 6.95067C14.6274 7.08488 14.6739 7.24266 14.6739 7.40407C14.6739 7.62051 14.5903 7.82809 14.4413 7.98114C14.2924 8.13419 14.0904 8.22017 13.8798 8.22017ZM5.1367 8.22017C4.97965 8.22017 4.82612 8.1723 4.69553 8.08263C4.56494 7.99296 4.46317 7.8655 4.40306 7.71638C4.34296 7.56725 4.32724 7.40316 4.35788 7.24486C4.38852 7.08655 4.46415 6.94114 4.5752 6.827C4.68626 6.71287 4.82775 6.63514 4.98178 6.60365C5.13582 6.57216 5.29548 6.58833 5.44058 6.65009C5.58568 6.71186 5.7097 6.81646 5.79695 6.95067C5.88421 7.08488 5.93078 7.24266 5.93078 7.40407C5.93078 7.62051 5.84712 7.82809 5.6982 7.98114C5.54928 8.13419 5.3473 8.22017 5.1367 8.22017ZM14.1611 3.32358L15.7492 0.509745C15.7707 0.471217 15.7845 0.428716 15.7899 0.384686C15.7953 0.340655 15.7922 0.295962 15.7807 0.253173C15.7693 0.210384 15.7497 0.170343 15.7231 0.13535C15.6966 0.100358 15.6635 0.0711024 15.6259 0.0492653C15.5884 0.0274282 15.5469 0.0134396 15.5041 0.00810309C15.4612 0.00276656 15.4177 0.00618727 15.3762 0.0181687C15.3346 0.0301501 15.2957 0.0504561 15.2618 0.0779202C15.2279 0.105384 15.1996 0.139465 15.1785 0.178206L13.5738 3.02605C12.2993 2.42749 10.9137 2.12006 9.51241 2.12494C8.1095 2.12141 6.72203 2.42573 5.44275 3.01755L3.83805 0.169705C3.79504 0.0923793 3.72399 0.0357041 3.64043 0.0120738C3.55687 -0.0115564 3.46761 -0.000218677 3.39216 0.0436075C3.31671 0.0874337 3.26122 0.160182 3.23783 0.245942C3.21443 0.331702 3.22503 0.423494 3.26731 0.501244L4.84719 3.31508C3.48116 4.08075 2.32251 5.18448 1.47615 6.52635C0.629778 7.86821 0.12242 9.40583 0 11H19C18.8813 9.40696 18.3763 7.86983 17.5311 6.52892C16.6858 5.18802 15.5272 4.08604 14.1611 3.32358Z" fill="#636363"/>
                                                                        </svg>
                                                                    </div>Android
                                                                </div>
                                                                
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment col-graph">
                                                                        <div class="bar blue" style="width: 85%">barrinha</div>
                                                                    </div>

                                                                    <div class="col-payment">
                                                                        <!-- <span class="money-td green bold grey font-size-14" id='credit-card-value'></span> -->
                                                                        <span class="money-td green bold grey font-size-14  value-percent">85%</span>
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
                                                                        <svg width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M12.5275 9.56283C12.5534 12.2868 14.9732 13.1933 15 13.2048C14.9795 13.2688 14.6134 14.4966 13.7252 15.765C12.9573 16.8615 12.1604 17.954 10.9051 17.9766C9.6716 17.9988 9.27497 17.2619 7.86473 17.2619C6.45492 17.2619 6.01423 17.954 4.84659 17.9988C3.63487 18.0436 2.71215 16.8131 1.93795 15.7206C0.355974 13.4858 -0.852985 9.4057 0.770342 6.65155C1.57678 5.28383 3.01794 4.41773 4.58218 4.39552C5.77207 4.37334 6.89517 5.1777 7.62256 5.1777C8.34949 5.1777 9.7143 4.21039 11.1491 4.35245C11.7497 4.37688 13.4358 4.58952 14.5184 6.13794C14.4312 6.19078 12.5066 7.2855 12.5275 9.56284V9.56283ZM10.2093 2.87397C10.8526 2.1131 11.2856 1.0539 11.1675 0C10.2402 0.0364151 9.11892 0.603766 8.4538 1.36422C7.85772 2.03763 7.33569 3.11548 7.47654 4.14852C8.51011 4.22665 9.56598 3.63532 10.2093 2.87397" fill="#636363"/>
                                                                        </svg>
                                                                    </div> IOS
                                                                </div>
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment col-graph">
                                                                        <div class="bar blue-2" style="width: 60%">barrinha</div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <!-- <span class="money-td green grey bold font-size-14" id='pix-value'></span> -->
                                                                        <span class="money-td green grey bold font-size-14 value-percent">85%</span>
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
                                                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M6.63593 0H0.000244141V6.63569H6.63593V0Z" fill="#636363"/>
                                                                                <path d="M13.9997 -6.10352e-05H7.36401V6.63563H13.9997V-6.10352e-05Z" fill="#636363"/>
                                                                                <path d="M6.63593 7.36432H0.000244141V14H6.63593V7.36432Z" fill="#636363"/>
                                                                                <path d="M13.9997 7.36432H7.36401V14H13.9997V7.36432Z" fill="#636363"/>
                                                                            </svg>
                                                                        </span>
                                                                    </div> Windows
                                                                </div>
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment col-graph">
                                                                        <div class="bar" style="width:43%">barrinha</div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <span class="money-td green bold grey font-size-14  value-percent">5%</span>
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
                                                                            <svg width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M6.28817 4.06562C6.28817 4.1169 6.2393 4.1169 6.2393 4.1169H6.19042C6.14155 4.1169 6.14155 4.06562 6.09268 4.01434C6.09268 4.01434 6.04381 3.96306 6.04381 3.91177C6.04381 3.86049 6.04381 3.86049 6.09268 3.86049L6.19042 3.91177C6.2393 3.96306 6.28817 4.01434 6.28817 4.06562ZM5.40847 3.5528C5.40847 3.29639 5.31072 3.14254 5.16411 3.14254C5.16411 3.14254 5.16411 3.19383 5.11523 3.19383V3.29639H5.26185C5.26185 3.39895 5.31072 3.45024 5.31072 3.5528H5.40847ZM7.11899 3.29639C7.21674 3.29639 7.26561 3.39895 7.31448 3.5528H7.41223C7.36335 3.50152 7.36335 3.45024 7.36335 3.39895C7.36335 3.34767 7.36335 3.29639 7.31448 3.24511C7.26561 3.19383 7.21674 3.14254 7.16787 3.14254C7.16787 3.14254 7.11899 3.19383 7.07012 3.19383C7.07012 3.24511 7.11899 3.24511 7.11899 3.29639ZM5.65283 4.1169C5.60396 4.1169 5.60396 4.1169 5.60396 4.06562C5.60396 4.01434 5.60396 3.96306 5.65283 3.91177C5.75057 3.91177 5.79945 3.86049 5.79945 3.86049C5.84832 3.86049 5.84832 3.91177 5.84832 3.91177C5.84832 3.96306 5.79945 4.01434 5.7017 4.1169H5.65283ZM5.11523 4.06562C4.91975 3.96306 4.87087 3.80921 4.87087 3.5528C4.87087 3.39895 4.87087 3.29639 4.96862 3.19383C5.01749 3.09126 5.11523 3.03998 5.21298 3.03998C5.31072 3.03998 5.3596 3.09126 5.45734 3.19383C5.50621 3.34767 5.55508 3.50152 5.55508 3.65536V3.70665V3.75793H5.60396V3.70665C5.65283 3.70665 5.65283 3.60408 5.65283 3.39895C5.65283 3.24511 5.65283 3.09126 5.55508 2.93742C5.45734 2.78357 5.3596 2.68101 5.16411 2.68101C5.01749 2.68101 4.87087 2.78357 4.822 2.93742C4.72426 3.14254 4.70471 3.29639 4.70471 3.5528C4.70471 3.75793 4.77313 3.96306 4.96862 4.16818C5.01749 4.1169 5.06636 4.1169 5.11523 4.06562ZM11.2243 11.2964C11.2731 11.2964 11.2731 11.2759 11.2731 11.2297C11.2731 11.1169 11.2243 10.9836 11.0776 10.8349C10.931 10.681 10.6867 10.5836 10.3934 10.5425C10.3446 10.5374 10.2957 10.5374 10.2957 10.5374C10.2468 10.5272 10.2468 10.5272 10.1979 10.5272C10.1491 10.522 10.0513 10.5118 10.0025 10.5015C10.1491 10.0246 10.1979 9.60408 10.1979 9.23485C10.1979 8.72203 10.1002 8.36306 9.90471 8.05536C9.70922 7.74767 9.51373 7.59383 9.26937 7.54254C9.2205 7.59383 9.2205 7.59383 9.2205 7.64511C9.46486 7.74767 9.70922 7.9528 9.85584 8.26049C10.0025 8.61947 10.0513 8.92716 10.0513 9.28613C10.0513 9.57331 10.0025 9.99895 9.80696 10.5425C9.61147 10.6246 9.41599 10.8143 9.26937 11.1118C9.26937 11.1579 9.26937 11.1836 9.31824 11.1836C9.31824 11.1836 9.36711 11.1374 9.41599 11.0502C9.51373 10.9631 9.5626 10.8759 9.66035 10.7887C9.80696 10.7015 9.90471 10.6554 10.0513 10.6554C10.2957 10.6554 10.54 10.6913 10.6867 10.7631C10.8822 10.8297 10.9799 10.9015 11.0288 10.9836C11.0776 11.0605 11.1265 11.1323 11.1754 11.199C11.1754 11.2656 11.2243 11.2964 11.2243 11.2964ZM6.72802 3.86049C6.67914 3.80921 6.67914 3.70665 6.67914 3.60408C6.67914 3.39895 6.67914 3.29639 6.77689 3.14254C6.87463 3.03998 6.97238 2.9887 7.07012 2.9887C7.21674 2.9887 7.31448 3.09126 7.41223 3.19383C7.4611 3.34767 7.50997 3.45024 7.50997 3.60408C7.50997 3.86049 7.41223 4.01434 7.21674 4.06562C7.21674 4.06562 7.26561 4.1169 7.31448 4.1169C7.41223 4.1169 7.4611 4.16818 7.55884 4.21947C7.60772 3.91177 7.65659 3.70665 7.65659 3.45024C7.65659 3.14254 7.60772 2.93742 7.50997 2.78357C7.36335 2.62972 7.21674 2.57844 7.02125 2.57844C6.87463 2.57844 6.72802 2.62972 6.5814 2.73229C6.48366 2.88613 6.43478 2.9887 6.43478 3.14254C6.43478 3.39895 6.48366 3.60408 6.5814 3.80921C6.63027 3.80921 6.67914 3.86049 6.72802 3.86049ZM7.31448 4.68101C6.67914 5.14254 6.19042 5.34767 5.79945 5.34767C5.45734 5.34767 5.11523 5.19383 4.822 4.93742C4.87087 5.03998 4.91975 5.14254 4.96862 5.19383L5.26185 5.50152C5.45734 5.70665 5.7017 5.80921 5.94606 5.80921C6.28817 5.80921 6.67914 5.60408 7.16787 5.24511L7.60772 4.93742C7.70546 4.83485 7.8032 4.73229 7.8032 4.57844C7.8032 4.52716 7.8032 4.47588 7.75433 4.47588C7.70546 4.37331 7.4611 4.21947 6.97238 4.06562C6.53253 3.86049 6.19042 3.75793 5.99493 3.75793C5.84832 3.75793 5.60396 3.86049 5.26185 4.06562C4.96862 4.27075 4.77313 4.47588 4.77313 4.68101C4.77313 4.68101 4.822 4.73229 4.87087 4.83485C5.16411 5.09126 5.45734 5.24511 5.75057 5.24511C6.14155 5.24511 6.63027 5.03998 7.26561 4.52716V4.62972C7.31448 4.62972 7.31448 4.68101 7.31448 4.68101ZM8.43854 15.04C8.63403 15.4256 8.97614 15.6195 9.36711 15.6195C9.46486 15.6195 9.5626 15.6041 9.66035 15.5733C9.75809 15.5528 9.85584 15.5169 9.90471 15.4759C9.95358 15.44 10.0025 15.4041 10.0513 15.3631C10.1491 15.3272 10.1491 15.3015 10.1979 15.2759L11.0288 14.522C11.2243 14.3584 11.4197 14.2154 11.6641 14.0913C11.8596 13.9682 12.0551 13.8861 12.1528 13.84C12.2994 13.799 12.3972 13.7374 12.4949 13.6554C12.5438 13.5784 12.5927 13.481 12.5927 13.3579C12.5927 13.2092 12.4949 13.0964 12.3972 13.0143C12.2994 12.9323 12.2017 12.8759 12.104 12.84C12.0062 12.8041 11.9085 12.722 11.7619 12.5836C11.6641 12.4502 11.5664 12.2656 11.5175 12.0246L11.4686 11.7272C11.4197 11.5887 11.4197 11.4861 11.3709 11.4297C11.3709 11.4143 11.3709 11.4092 11.322 11.4092C11.2731 11.4092 11.1754 11.4554 11.1265 11.5425C11.0288 11.6297 10.931 11.7272 10.8333 11.8297C10.7844 11.9323 10.6378 12.0246 10.54 12.1118C10.3934 12.199 10.2468 12.2451 10.1491 12.2451C9.75809 12.2451 9.5626 12.1323 9.41599 11.9118C9.31824 11.7477 9.26937 11.5579 9.2205 11.3425C9.12275 11.2554 9.07388 11.2092 8.97614 11.2092C8.73178 11.2092 8.63403 11.4759 8.63403 12.0143V12.1836V12.7784V13.2349V13.4554V13.6092C8.63403 13.6554 8.58516 13.7579 8.58516 13.9169C8.53629 14.0759 8.53629 14.2564 8.53629 14.4605L8.43854 15.0297V15.0384V15.04ZM1.35208 14.7672C1.80659 14.8369 2.32952 14.9861 2.92087 15.2138C3.51223 15.4395 3.87388 15.5574 4.00584 15.5574C4.34794 15.5574 4.6314 15.3984 4.86599 15.0913C4.91486 14.9918 4.91486 14.8749 4.91486 14.7405C4.91486 14.2559 4.63629 13.6431 4.07914 12.8995L3.74681 12.4328C3.67839 12.3354 3.59531 12.1866 3.48779 11.9866C3.38516 11.7866 3.2923 11.6328 3.21899 11.5251C3.15546 11.4072 3.05283 11.2892 2.92087 11.1713C2.79381 11.0533 2.64719 10.9764 2.48591 10.9354C2.28065 10.9764 2.13892 11.0482 2.0705 11.1456C2.00208 11.2431 1.96298 11.3507 1.9532 11.4636C1.93854 11.5713 1.90922 11.6431 1.86035 11.679C1.81148 11.7097 1.72839 11.7354 1.61599 11.761C1.59155 11.761 1.54757 11.761 1.48403 11.7661H1.35208C1.09305 11.7661 0.917115 11.7969 0.824257 11.8482C0.702077 11.9969 0.638543 12.1661 0.638543 12.3456C0.638543 12.4277 0.658092 12.5661 0.69719 12.761C0.736287 12.9507 0.755836 13.1046 0.755836 13.2123C0.755836 13.4225 0.69719 13.6328 0.575009 13.8431C0.452829 14.0636 0.389295 14.2277 0.389295 14.3446C0.438167 14.5436 0.760724 14.6836 1.35208 14.7656V14.7672ZM2.97952 10.1041C2.97952 9.75024 3.06749 9.36049 3.24832 8.89895C3.42426 8.43742 3.6002 8.12972 3.77125 7.92459C3.76148 7.87331 3.73704 7.87331 3.69794 7.87331L3.64907 7.82203C3.50734 7.97588 3.33629 8.33485 3.13102 8.84767C2.92576 9.30921 2.81824 9.73485 2.81824 10.0477C2.81824 10.2784 2.872 10.4784 2.96975 10.6528C3.07727 10.822 3.33629 11.0682 3.74681 11.381L4.26486 11.7349C4.81711 12.2374 5.11035 12.5861 5.11035 12.7913C5.11035 12.899 5.06148 13.0066 4.91486 13.1246C4.81711 13.2477 4.68516 13.3092 4.57275 13.3092C4.56298 13.3092 4.55809 13.3195 4.55809 13.3451C4.55809 13.3502 4.60696 13.4528 4.7096 13.6528C4.91486 13.9451 5.35471 14.0887 5.94117 14.0887C7.01636 14.0887 7.84719 13.6272 8.48253 12.7041C8.48253 12.4477 8.48253 12.2887 8.43366 12.222V12.0323C8.43366 11.699 8.48253 11.4477 8.58027 11.2836C8.67802 11.1195 8.77576 11.0425 8.92238 11.0425C9.02012 11.0425 9.11787 11.0784 9.21561 11.1554C9.26448 10.7605 9.26448 10.4169 9.26448 10.1092C9.26448 9.64254 9.26448 9.25793 9.16674 8.89895C9.11787 8.59126 9.02012 8.33485 8.92238 8.12972C8.82463 7.97588 8.72689 7.82203 8.62914 7.66818C8.5314 7.51434 8.48253 7.36049 8.38478 7.20665C8.33591 7.00152 8.28704 6.84767 8.28704 6.59126C8.14042 6.33485 8.04268 6.07844 7.89606 5.82203C7.79832 5.56562 7.70057 5.30921 7.60283 5.10408L7.16298 5.46306C6.67426 5.82203 6.28328 5.97588 5.94117 5.97588C5.64794 5.97588 5.40358 5.92459 5.25696 5.71947L4.96373 5.46306C4.96373 5.6169 4.91486 5.82203 4.81711 6.02716L4.50922 6.64254C4.37238 7.00152 4.29907 7.20665 4.28441 7.36049C4.26486 7.46306 4.2502 7.56562 4.24042 7.56562L3.87388 8.33485C3.47802 9.10408 3.27764 9.8169 3.27764 10.4066C3.27764 10.5246 3.28742 10.6477 3.30696 10.7707C3.08704 10.6118 2.97952 10.3913 2.97952 10.1041ZM6.47877 14.9554C5.84343 14.9554 5.35471 15.0456 5.0126 15.2246V15.2092C4.76824 15.5169 4.49456 15.6759 4.11336 15.6759C3.87388 15.6759 3.49757 15.5784 2.9893 15.3836C2.47614 15.199 2.02163 15.0574 1.62576 14.9641C1.58666 14.9523 1.49869 14.9348 1.35696 14.9113C1.22012 14.8882 1.09305 14.8646 0.980648 14.841C0.878017 14.8179 0.760724 14.7831 0.633656 14.7359C0.511475 14.6954 0.413731 14.6425 0.340423 14.5784C0.272979 14.5138 0.239746 14.441 0.239746 14.3595C0.239746 14.2774 0.256363 14.1897 0.289596 14.0964C0.320874 14.04 0.355084 13.9836 0.389295 13.9323C0.423506 13.8759 0.452829 13.8246 0.472378 13.7733C0.501701 13.7272 0.52125 13.681 0.540799 13.6297C0.560348 13.5836 0.579896 13.5374 0.589671 13.481C0.599445 13.4297 0.60922 13.3784 0.60922 13.3272C0.60922 13.2759 0.589671 13.122 0.550573 12.8502C0.511475 12.5836 0.491927 12.4143 0.491927 12.3425C0.491927 12.1169 0.540799 11.9374 0.648318 11.8092C0.755836 11.681 0.858468 11.6143 0.965987 11.6143H1.52802C1.572 11.6143 1.64042 11.5887 1.74305 11.5272C1.77726 11.4451 1.80659 11.3784 1.82614 11.3169C1.85057 11.2554 1.86035 11.2092 1.87012 11.1887C1.8799 11.1579 1.88967 11.1272 1.89945 11.1015C1.91899 11.0656 1.94343 11.0246 1.97764 10.9836C1.93854 10.9323 1.91899 10.8656 1.91899 10.7836C1.91899 10.7272 1.91899 10.6759 1.92877 10.6451C1.92877 10.4605 2.01185 10.199 2.18779 9.85536L2.35884 9.53229C2.50057 9.25536 2.60809 9.05024 2.68629 8.84511C2.76937 8.63998 2.85734 8.33229 2.95508 7.92203C3.03328 7.56306 3.21899 7.20408 3.51223 6.84511L3.87877 6.38357C4.1329 6.07588 4.29907 5.81947 4.39193 5.61434C4.48478 5.40921 4.53366 5.1528 4.53366 4.94767C4.53366 4.84511 4.50922 4.53742 4.45546 4.02459C4.40659 3.51177 4.38215 2.99895 4.38215 2.53742C4.38215 2.17844 4.41148 1.92203 4.47501 1.66562C4.53854 1.40921 4.65095 1.1528 4.81711 0.947672C4.96373 0.742544 5.15922 0.537416 5.45245 0.434852C5.74569 0.332288 6.08779 0.281006 6.47877 0.281006C6.62538 0.281006 6.772 0.281006 6.91862 0.332288C7.06523 0.332288 7.26072 0.38357 7.50508 0.486134C7.70057 0.588698 7.89606 0.691262 8.04268 0.845108C8.23817 0.998955 8.38478 1.25536 8.5314 1.51177C8.62914 1.81947 8.72689 2.12716 8.77576 2.53742C8.82463 2.79383 8.82463 3.05024 8.8735 3.40921C8.8735 3.7169 8.92238 3.92203 8.92238 4.07588C8.97125 4.22972 8.97125 4.43485 9.02012 4.69126C9.06899 4.89639 9.11787 5.10152 9.21561 5.25536C9.31335 5.46049 9.4111 5.66562 9.55772 5.87075C9.70433 6.12716 9.89982 6.38357 10.0953 6.69126C10.5352 7.20408 10.8773 7.76818 11.0728 8.33229C11.3171 8.84511 11.4637 9.51177 11.4637 10.2246C11.4637 10.5784 11.4149 10.922 11.3171 11.2554C11.4149 11.2554 11.4637 11.2964 11.5126 11.3682C11.5615 11.44 11.6103 11.5938 11.6592 11.8349L11.7081 12.2143C11.757 12.3272 11.8058 12.4349 11.9525 12.5272C12.0502 12.6195 12.1479 12.6964 12.2946 12.7579C12.3923 12.8092 12.5389 12.881 12.6367 12.9733C12.7344 13.0759 12.7833 13.1836 12.7833 13.2964C12.7833 13.4707 12.7344 13.599 12.6367 13.6913C12.5389 13.7938 12.4412 13.8656 12.2946 13.9118C12.1968 13.9631 12.0013 14.0656 11.7081 14.2102C11.4637 14.362 11.2194 14.5461 10.975 14.7641L10.4863 15.2005C10.2908 15.4005 10.0953 15.5441 9.94869 15.6313C9.80208 15.7236 9.60659 15.7697 9.4111 15.7697L9.06899 15.7287C8.67802 15.621 8.43366 15.4159 8.28704 15.1031C7.50508 15.0036 6.86975 14.9543 6.47877 14.9543" fill="#636363"/>
                                                                            </svg>
                                                                        </span>
                                                                    </div> Linux
                                                                </div>
                                                                <div class="box-payment-option option">
                                                                    <div class="col-payment col-graph">
                                                                        <div class="bar" style="width: 20%">barrinha</div>
                                                                    </div>
                                                                    <div class="col-payment">
                                                                        <span class="money-td green bold grey font-size-14 value-percent">1%</span>
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
                        <!-- /comission -->
                        
                        <!-- distribtion -->
                        <div class="distribution mkt">
                            <div class="card inner">
                                <header class="d-flex title-graph">
                                    <h5 class="grey font-size-16">
                                        <strong>Origem</strong>
                                    </h5>
                                    <select class="form-control float-right origin-sel" id='origin'>
                                        <option selected value="src">SRC</option>
                                        <option value="utm_source">UTM Source</option>
                                        <option value="utm_medium">UTM Medium</option>
                                        <option value="utm_campaign">UTM Campaign</option>
                                        <option value="utm_term">UTM Term</option>
                                        <option value="utm_content">UTM Content</option>
                                    </select>
                                </header>

                                <div class="card-body card-origin">
                                    <div class="row">
                                        <div>
                                            
                                        </div>

                                        <!-- <div class="col-4">
                                            
                                        </div> -->
                                    </div>
                                    <div class="row">
                                        <div class="data-holder">
                                            <div class="row">
                                                <div class="col-12">
                                                    <table class="table-vendas table table-striped "
                                                        style="width:100%;margin: auto; margin-top:15px">
                                                        <!-- <tbody id="origins-table">
                                                            {{-- js carrega... --}}
                                                        </tbody> -->
                                                    </table>
                                                </div>
                                            </div>
                                            <br/>
                                        </div>
                                        <div class="row">
                                            <div class="col-11">
                                                <ul id="pagination-origins" class="pagination-sm float-right margin-chat-pagination"
                                                    style="margin-top:10px; margin-left: 5%">
                                                    {{-- js carrega... --}}
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                <strong>Vendas mais frequentes</strong>
                                            </h5>
                                        </header>
                                        <div id="" class="custom-table scrollbar pb-0 pt-0">
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
    <script type='text/javascript' src='{{asset('modules/reports/js/reports-marketing.js?v=' . uniqid())}}'></script>
@endpush

