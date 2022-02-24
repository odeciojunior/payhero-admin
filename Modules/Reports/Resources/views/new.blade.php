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
                            <h1 class="page-title new-title grey">Relatórios</h1>
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
					<div class="col-12">
						<!-- /FINANCE -->	
						<div class="row">
							<header class="header-reports container">
								<h3 class="title-reports">
									<a href="{!! route('reports.finances') !!}" class="lk-reports grey">
										<span class="box-title ico-finance">financeiro</span>
										Financeiro
									</a>
								</h3>
								<a href="{!! route('reports.finances') !!}" class="box-link">
                                    <span>Acessar&nbsp&nbsp</span>
                                    <i class="o-arrow-right-1 redirect"></i>
                                </a>
							</header>
                            <div class="container container-reports">
                                <div id="reports-content" class="">
                                    <div class='container col-sm-12 mt-20 d-lg-block'>
										<div class="row cards-reports">
											<div class="card">
												<div class="card-body">
                                                    <header class="container">
													    <h6 class="font-size-16 gray-700 grey"> Comissão </h6>
                                                        <div class="d-flex value-price">
													        <h4 id='comission' class="font-size-24 bold grey"></h4>
                                                            <em class="pink">
                                                                <i class="ms-Icon ms-Icon--SkypeArrow x-hidden-focus"></i>
                                                                23%
                                                            </em>
                                                        </div>
                                                    </header>
													<div class="new-graph"></div>
												</div>
											</div>
											<div class="card ">
												<div class="card-body">
                                                    <header class="container">
													    <h6 class="font-size-16 gray-700 grey"> Pendentes </h6>
                                                        <div class="d-flex value-price">
                                                            <h4 id='pending' class="font-size-24 bold grey">0</h4>
                                                        </div>
                                                    </header>
                                                    <div class="new-graph-pending"></div>
												</div>
											</div>
                                            <div class="card">
												<div class="card-body">
                                                    <header class="container">
                                                        <h6 class="font-size-16 gray-700 grey"> Cashback </h6>
                                                        <div class="d-flex value-price">
                                                            <h4 id='cashback' class="font-size-24 bold grey">0</h4>
                                                        </div>
                                                    </header>
													<div class="new-graph-cashback"></div>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
						</div>
						<!-- /FINANCE -->
						
						<!-- SELL -->
						<div class="row">
							<header class="header-reports container">
								<h3 class="title-reports">
									<a href="{!! route('reports.index') !!}" class="lk-reports grey">
										<span class="box-title ico-sell">vendas</span>
										Vendas
									</a>
								</h3>
                                <a href="{!! route('reports.index') !!}" class="box-link">
                                    <span>Acessar&nbsp&nbsp</span>
                                    <i class="o-arrow-right-1 redirect"></i>
                                </a>
							</header>
							<div class="container container-reports">
                                <div id="reports-content" class="">
                                    <div class='container col-sm-12 mt-20 d-lg-block'>
										<div class="row cards-reports">
											<div class="card">
												<div class="card-body">
                                                    <header class="container">
                                                        <h6 class="font-size-16 gray-700 grey"> Vendas realizadas </h6>
                                                        <div class="d-flex value-price">
                                                            <h4 id='sales' class=" font-size-24 bold">0</h4>
                                                        </div>
                                                    </header>
                                                    <div class="new-graph-sell"></div>
												</div>
											</div>
											<div class="card ">
												<div class="card-body">
                                                    <header class="container">
													    <h6 class="font-size-16 gray-700 grey"> Meios de pagamento </h6>
                                                    </header>
                                                    <div id="payment-type-items" class="custom-table scrollbar pb-0 pt-0">
                                                        <div class="row container-payment">
															<div class="container">
																<div class="data-holder b-bottom">
																	<div class="box-payment-option">
																		
																		<div class="col-payment grey box-image-payment">
																			<div class="box-ico">
																				<svg width="21" height="16" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                    <path d="M13.806 10.415C13.3901 10.415 13.053 10.7814 13.053 11.2334C13.053 11.6855 13.3901 12.0519 13.806 12.0519H16.3163C16.7322 12.0519 17.0693 11.6855 17.0693 11.2334C17.0693 10.7814 16.7322 10.415 16.3163 10.415H13.806ZM2.30106 0.047699C1.03022 0.047699 0 1.16738 0 2.54858V13.0068C0 14.388 1.03022 15.5077 2.30106 15.5077H17.7809C19.0517 15.5077 20.082 14.388 20.082 13.0068V2.54858C20.082 1.16738 19.0517 0.047699 17.7809 0.047699H2.30106ZM1.25512 13.0068V5.95886H18.8268V13.0068C18.8268 13.6346 18.3586 14.1435 17.7809 14.1435H2.30106C1.7234 14.1435 1.25512 13.6346 1.25512 13.0068ZM1.25512 4.59475V2.54858C1.25512 1.92076 1.7234 1.41181 2.30106 1.41181H17.7809C18.3586 1.41181 18.8268 1.92076 18.8268 2.54858V4.59475H1.25512Z" fill="#636363"/>
                                                                                </svg>
																			</div>Cartão
																		</div>
																		
																		<div class="box-payment-option option">
																			<div class="col-payment grey" id='percent-credit-card'>0</div>
																			<div class="col-payment col-graph"><div class="bar">barrinha</div></div>
																			<div class="col-payment end"><span class="money-td green bold grey" id='credit-card-value'></span></div>
																		</div>
																	</div>
																</div>
															</div>
                                                            <div class="container">
																<div class="data-holder b-bottom">
																	<div class="box-payment-option">
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
																				<div class="bar purple">barrinha</div>
																			</div>
																			<div class="col-payment end">
																				<span class="money-td green grey bold" id='pix-value'></span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="container">
																<div class="data-holder b-bottom">
																	<div class="box-payment-option">
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
																				<div class="bar pink">barrinha</div>
																			</div>
																			<div class="col-payment end">
																				<span class="money-td green bold grey" id='boleto-value'></span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															
														</div>
                                                        <div class="no-payment">
                                                            Não há meios de pagamento
                                                        </div>
                                                    </div>
												</div>
											</div>
											<div class="card">
												<div class="card-body">
                                                    <header class="container">
													    <h6 class="font-size-16 gray-700 grey"> Produtos </h6>
													    <h4 id='qtd-reembolso' class=" font-size-24 bold">0</h4>
                                                    </header>
                                                    <footer>
                                                        <ul class="list-products container">
                                                            <li>
                                                                <div class="box-list-products">
                                                                    <figure>
                                                                        <img width="24px" height="24px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                    </figure>
                                                                    <div class="bars blue" style="min-width: 102px"><span>51</span></div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="box-list-products">
                                                                    <figure>
                                                                        <img width="24px" height="24px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                    </figure>
                                                                    <div class="bars purple" style="min-width: 80px"><span>40</span></div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="box-list-products">
                                                                    <figure>
                                                                        <img width="24px" height="24px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                    </figure>
                                                                    <div class="bars pink" style="min-width: 65px"><span>32</span></div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="box-list-products">
                                                                    <figure>
                                                                        <img width="24px" height="24px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                    </figure>
                                                                    <div class="bars orange" style="min-width: 42px"><span>21</span></div>
                                                                </div>
                                                            </li>

                                                            <li>
                                                                <div class="box-list-products">
                                                                    <figure>
                                                                        <img width="24px" height="24px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                    </figure>
                                                                    <div class="bars yellow" style="min-width: 106px"><span>19</span></div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="box-list-products">
                                                                    <figure>
                                                                        <img width="24px" height="24px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                    </figure>
                                                                    <div class="bars light-blue" style="min-width: 20px"><span>4</span></div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="box-list-products">
                                                                    <figure>
                                                                        <img width="24px" height="24px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                    </figure>
                                                                    <div class="bars light-green" style="min-width: 19px"><span>1</span></div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="box-list-products">
                                                                    <figure>
                                                                        <img width="24px" height="24px" src="{!! asset('modules/global/img/reports/img-casaco.png') !!}" alt="">
                                                                    </figure>
                                                                    <div class="bars grey" style="min-width: 19px"><span>1</span></div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </footer>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
						</div>
						<!-- /SELL -->

						<!-- MARKETING -->
						<div class="row">
							<header class="header-reports container">
								<h3 class="title-reports">
									<a href="" class="lk-reports grey">
										<span class="box-title ico-mkt">Marketing</span>
										Marketing
									</a>
								</h3>
                                <a href="{!! route('reports.marketing') !!}" class="box-link">
                                    <span>Acessar&nbsp&nbsp</span>
                                    <i class="o-arrow-right-1 redirect"></i>
                                </a>
							</header>
							<div class="container container-reports">
                                <div id="reports-content" class="">
                                    <div class='container col-sm-12 mt-20 d-lg-block'>
										<div class="row cards-reports">
											<div class="card">
												<div class="card-body">
                                                    <header class="container">
													    <h6 class="font-size-16 gray-700 grey"> Cupons </h6>
													    <h4 id='qtd-dispute' class=" font-size-24 bold">0</h4>
                                                    </header>
                                                    <div class="container d-flex justify-content-between box-donut">
														<div class="new-graph-pie"></div>
														<div class="data-pie">
															<ul>
																<li>
																	<div class="donut-pie blue">
																		<figure>
                                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#2E85EC" stroke-width="3"/>
                                                                            </svg>
																		</figure>
																		<div>Mariana</div>
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
																		<div>Cassio</div>
																	</div>
																	<div class="grey bold">16</div>
																</li>
																<li>
																	<div class="donut-pie purple">
                                                                        <figure>
                                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#665FE8" stroke-width="3"/>
                                                                            </svg>																			
																		</figure>
																		<div>Agosto</div>
																	</div>
																	<div class="grey bold">12</div>
																</li>
																<li>
																	<div class="donut-pie orange">
                                                                        <figure>
                                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke="#FF7900" stroke-width="3"/>
                                                                            </svg>																			
																		</figure>
																		<div>meuaniversario</div>
																	</div>
																	<div class="grey bold">06</div>
																</li>
															</ul>
														</div>
                                                    </div>
												</div>
											</div>
											<div class="card">
												<div class="card-body">
                                                    <header class="container">
                                                        <h6 class="font-size-16 gray-700 grey"> Regiões </h6>
                                                        <!-- <h4 id='qtd-chargeback' class=" font-size-24 bold">0</h4> -->
                                                    </header>
                                                    <footer class="container footer-regions">
                                                        <section class="">
                                                            <canvas id="regionsChart" width="180" height="200"></canvas>
                                                        </section>
                                                        <section>
                                                            <ul class="conversion-colors">
                                                                <li class="blue">60%</li>
                                                                <li class="purple">42%</li>
                                                                <li class="pink">48%</li>
                                                                <li class="orange">35%</li>
                                                            </ul>
                                                        </section>
                                                        <section class="">
                                                            <ul class="regions-legend">
                                                                <li class="access"><span></span>Acessos</li>
                                                                <li class="conversion"><span></span>Conversões</li>
                                                            </ul>
                                                        </section>
                                                    </footer>
												</div>
											</div>
											<div class="card ">
												<div class="card-body card-origin">
													<div class="row">
														<header class="col-8">
															<h6 class="font-size-16 gray-700"> Origens </h6>
														</header>
														<div class="col-4">
															<select class="form-control float-right" id='origin'>
																<option selected value="src">SRC</option>
																<option value="utm_source">UTM Source</option>
																<option value="utm_medium">UTM Medium</option>
																<option value="utm_campaign">UTM Campaign</option>
																<option value="utm_term">UTM Term</option>
																<option value="utm_content">UTM Content</option>
															</select>
														</div>
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
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <img src="{!! asset('modules/global/img/reports/img-nodata.png') !!}" alt="">
                                                                                </td>
                                                                                <td>
                                                                                    <p class="no-data-origin">
                                                                                        <strong>Nada por aqui...</strong>
                                                                                        Não há dados suficientes para gerar este gráfico.
                                                                                    </p>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
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
										</div>
                                    </div>
                                </div>
                            </div>
						</div>
						<!-- /MARKETING -->
					</div>
                    <div class="col-12"  style="display: none;">
                        <div class="d-flex no-data">
                            <figure>
                                <svg width="214" height="214" viewBox="0 0 214 214" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M107 214C166.094 214 214 166.094 214 107C214 47.9055 166.094 0 107 0C47.9055 0 0 47.9055 0 107C0 166.094 47.9055 214 107 214Z" fill="url(#paint0_linear_517_989)"/>
                                <path d="M171.2 214H42.8V75.6135C48.852 75.6067 54.6541 73.1996 58.9334 68.9203C63.2128 64.6409 65.6199 58.8388 65.6267 52.7869H148.373C148.367 55.7848 148.955 58.7543 150.104 61.5235C151.253 64.2926 152.939 66.8065 155.066 68.9196C157.179 71.0469 159.693 72.7339 162.462 73.883C165.232 75.032 168.202 75.6202 171.2 75.6135V214Z" fill="white"/>
                                <path d="M107 145.52C125.91 145.52 141.24 130.19 141.24 111.28C141.24 92.3698 125.91 77.04 107 77.04C88.0898 77.04 72.76 92.3698 72.76 111.28C72.76 130.19 88.0898 145.52 107 145.52Z" fill="#4285F4"/>
                                <path d="M119.106 127.421L107 115.315L94.8943 127.421L90.8591 123.386L102.965 111.28L90.8591 99.1746L94.8943 95.1394L107 107.245L119.106 95.1394L123.141 99.1746L111.035 111.28L123.141 123.386L119.106 127.421Z" fill="white"/>
                                <path d="M125.547 154.08H88.4535C86.0897 154.08 84.1735 155.996 84.1735 158.36C84.1735 160.724 86.0897 162.64 88.4535 162.64H125.547C127.911 162.64 129.827 160.724 129.827 158.36C129.827 155.996 127.911 154.08 125.547 154.08Z" fill="#DFEAFB"/>
                                <path d="M138.387 171.2H75.6134C73.2496 171.2 71.3334 173.116 71.3334 175.48C71.3334 177.844 73.2496 179.76 75.6134 179.76H138.387C140.75 179.76 142.667 177.844 142.667 175.48C142.667 173.116 140.75 171.2 138.387 171.2Z" fill="#DFEAFB"/>
                                <defs>
                                <linearGradient id="paint0_linear_517_989" x1="107" y1="0" x2="107" y2="214" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#E3ECFA"/>
                                <stop offset="1" stop-color="#DAE7FF"/>
                                </linearGradient>
                                </defs>
                                </svg>
                            </figure>

                            <div>
                                <h6>Ainda não tem nada pra mostrar aqui! :(</h6>
                                <p>
                                    Faça a sua primeira venda para começar a usar a sua central de relatórios.
                                </p>
                                <a href="/dashboard" class="lk-dashboard" title="Ir para Dashboard">Ir para Dashboard</a>
                            </div>
                        </div>
                    </div>
				</div>
            </section>

            <div id="reports-content" class="page-content container" style="display: none">
                <div class='container col-sm-12 mt-20 d-lg-block'>
                    <div class='row'>
                        <!-- <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Receita gerada </h6>
                                <h4 id='revenue-generated'>0</h4>
                            </div>
                            <div class="s-border-right yellow"></div>
                        </div> -->
                        <!-- <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Aprovadas </h6>
                                <h4 id='qtd-aproved' class=" font-size-30 bold">0</h4>
                            </div>
                        </div> -->
                        <!-- <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Pendentes </h6>
                                <h4 id='qtd-pending' class=" font-size-30 bold">0</h4>
                            </div>
                        </div> -->
                        <!-- <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Canceladas </h6>
                                <h4 id='qtd-canceled' class=" font-size-30 bold">0</h4>
                            </div>
                        </div> -->
                        <!-- <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Recusadas </h6>
                                <h4 id='qtd-recusadas' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Reembolsos </h6>
                                <h4 id='qtd-reembolso' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Chargeback </h6>
                                <h4 id='qtd-chargeback' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Em disputa </h6>
                                <h4 id='qtd-dispute' class=" font-size-30 bold">0</h4>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="row justify-content-between mt-20">
                    {{-- <div class="col-lg-12">
                        <div class="card shadow">
                            <div class="wrap">
                                <div class="row justify-content-between gutter_top">
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Receita gerada </h6>
                                        <h4 id='revenue-generated' class="number green" style='color:green'>0</h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Aprovadas </h6>
                                        <h4 id='qtd-aproved' class="number green" style='color:green'>0<i
                                                class="fas fa-check"></i>
                                        </h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Pendentes </h6>
                                        <h4 id='qtd-pending' class="number blue-800" style='color:blue'>0<i
                                                class="fas fa-check"></i>
                                        </h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Canceladas </h6>
                                        <h4 id='qtd-canceled' class="number red" style='color:red'>0<i
                                                class="fas fa-check"></i>
                                        </h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Recusadas </h6>
                                        <h4 id='qtd-recusadas' class="number red" style='color:red'>0</h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Reembolsos </h6>
                                        <h4 id='qtd-reembolso' class="number purple" style='color:purple'>0</h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> ChargeBack </h6>
                                        <h4 id='qtd-chargeback' class="number purple" style='color:purple'>0</h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Em disputa </h6>
                                        <h4 id='qtd-dispute' class="number purple" style='color:blue'>0</h4>
                                    </div>
                                    <!--div class="col-lg-12">
                                        <div class="grafico">
                                            <div class="text">
                                                <h1 class="text-muted op5"> Graph here </h1>
                                            </div>
                                        </div>
                                    </div-->
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-lg-12 gutter_top display-xsm-none display-sm-none" class="ct-chart"
                         id="ecommerceChartView">
                        <div class="card">
                            <div class="card-header card-header-transparent py-20">
                                <!--div class="btn-group dropdown"-->
                                <!--a href="#" class="text-body dropdown-toggle blue-grey-700" data-toggle="dropdown">PRODUCTS SALES</a-->
                                <!--div class="dropdown-menu animate" role="menu">
                                    <a class="dropdown-item" href="#" role="menuitem">Sales</a>
                                    <a class="dropdown-item" href="#" role="menuitem">Total sales</a>
                                    <a class="dropdown-item" href="#" role="menuitem">profit</a>
                                </div-->
                                <!--/div-->
                                <ul class="nav nav-pills nav-pills-rounded chart-action" style="display: none">
                                    <li class="nav-item">
                                        <a class="active nav-link" data-toggle="tab" href="#scoreLineToDay">Day</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#scoreLineToWeek">Week</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#scoreLineToMonth">Month</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="widget-content tab-content bg-white p-20">
                                <div id="empty-graph" class="row justify-content-center align-items-center d-flex" style="vertical-align: middle">
                                    <img src="{!! asset('modules/global/img/sem-dados.svg') !!}" alt="">
                                    <p style="font-size: 23px" class="gray">Nenhuma venda encontrada</p>
                                </div>
                                <div class="ct-chart tab-pane active" id="scoreLineToDay"></div>
                                <div class="ct-chart tab-pane" id="scoreLineToWeek"></div>
                                <div class="ct-chart tab-pane" id="scoreLineToMonth"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 gutter_top" style="display: none">
                        <div class="card shadow">
                            <div class="card-header s-card-header">
                                <h4> Dispositivos </h4>
                            </div>
                            <div class="custom-table">
                                <div class="row">
                                    <div class="col-6 col-md-12 col-lg-12 ">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-6 d-flex align-items-center">
                                                    <span class="mr-10 o-imac-screen-1"></span> Desktop
                                                </div>
                                                <div class="col-lg-6">
                                                    <span class="money-td green" id='percent-desktop'>0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-lg-12 ">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-6 d-flex align-items-center">
                                                    <span class="ml-5 mr-15 o-iphone-1"></span> Mobile
                                                </div>
                                                <div class="col-lg-6">
                                                    <span class="money-td green" id='percent-mobile'>0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 gutter_top" style="display: none">
                        <div class="card shadow">
                            <div class="card-header s-card-header">
                                <h4> Conversão </h4>
                            </div>
                            <div class="list-linear-gradient-top"></div>
                            <div id="conversion-items" class="custom-table scrollbar pb-0 pt-0">
                                <div class="row">
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <span class="mr-10 o-bank-cards-1"></span> Cartão
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="" id='qtd-cartao-convert'>0</span>
                                                </div>
                                                <div class="col-lg-4" id='percent-credit-card-convert'>
                                                    0
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <span class="mr-10 o-cash-dispenser-1"></span> Boleto
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="" id='qtd-boleto-convert'>0</span>
                                                </div>
                                                <div class="col-lg-4" id='percent-boleto-convert'>
                                                    0
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="38.867" height="40.868" viewBox="0 0 38.867 40.868" style="width: 24px;" class="mr-10">
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
                                                    </svg> PIX
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="" id='qtd-pix-convert'>0</span>
                                                </div>
                                                <div class="col-lg-4" id='percent-pix-convert'>
                                                    0
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-linear-gradient-bottom"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 gutter_top" style="display: none">
                        <div class="card shadow">
                            <div class="card-header s-card-header">
                                <h4> Meios de Pagamento </h4>
                            </div>
                            <div class="list-linear-gradient-top"></div>
                            <div id="payment-type-items" class="custom-table scrollbar pb-0 pt-0">
                                <div class="row">
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <span class="mr-10 o-bank-cards-1"></span> Cartão
                                                </div>
                                                <div class="col-lg-3" id='percent-credit-card'>
                                                    0
                                                </div>
                                                <div class="col-lg-5">
                                                    <span class="money-td green" id='credit-card-value'></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <span class="mr-10 o-cash-dispenser-1"></span> Boleto
                                                </div>
                                                <div class="col-lg-3" id='percent-values-boleto'>
                                                    0
                                                </div>
                                                <div class="col-lg-5">
                                                    <span class="money-td green" id='boleto-value'></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="38.867" height="40.868" viewBox="0 0 38.867 40.868" style="width: 24px;" class="mr-10">
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
                                                    </svg> Pix
                                                </div>
                                                <div class="col-lg-3" id='percent-values-pix'>
                                                    0
                                                </div>
                                                <div class="col-lg-5">
                                                    <span class="money-td green" id='pix-value'></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-linear-gradient-bottom"></div>
                        </div>
                    </div>
                    <div class='col-lg-8' style="display: none">
                        <div class="card shadow ">
                            <div class="card-header s-card-header">
                                <h4> Mais Vendidos </h4>
                            </div>
                            <div style=' max-height: 150px; overflow-y: auto; height: 150px;'>
                                <div style="padding: 0 20px;" class=" card-body data-holder">
                                    <table class="table-vendas-itens table table-striped" style="width:100%;margin: auto; margin-top:15px">
                                        <tbody id="origins-table-itens" img-empty="{!! asset('modules/global/img/vendas.svg')!!}">
                                            {{-- js carrega... --}}
                                        </tbody>
                                    </table>
                                    <br/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-lg-4' style="display: none">
                        <div class='card shadow'>
                            <div class='card-header s-card-header'>
                                <h4>Ticket Médio</h4>
                            </div>
                            <div style='height: 150px; '>
                                <div class='card-body custom-table min-250'>
                                    <div class='row align-items-center h-100'>
                                        <div class='col-lg-12 text-center'>
                                            <div class='data-holder text-center'>
                                                <div class='row wrap justify-content-between text-center'>
                                                    <div class='col-lg-12 text-center'>
                                                        <span class='money-td green h3' id='ticket-medio'>0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-10" style="display: none">
                        <div class="card shadow">
                            <div class="card-header s-card-header">
                                <div class="row">
                                    <div class='col-8'>
                                        <h4 class='float-left'> Origens</h4>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-control float-right" id='origin'>
                                            <option selected value="src">SRC</option>
                                            <option value="utm_source">UTM Source</option>
                                            <option value="utm_medium">UTM Medium</option>
                                            <option value="utm_campaign">UTM Campaign</option>
                                            <option value="utm_term">UTM Term</option>
                                            <option value="utm_content">UTM Content</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="data-holder">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table-vendas table table-striped "
                                               style="width:100%;margin: auto; margin-top:15px">
                                            <tbody id="origins-table">
                                                {{-- js carrega... --}}
                                            </tbody>
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
            </div>
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
    <script type='text/javascript' src='{{asset('modules/reports/js/regions.js?v=' . uniqid())}}'></script>
@endpush

