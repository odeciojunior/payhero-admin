@extends("layouts.master")
@section('title', '- Utilização de Cupons')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! mix('build/layouts/reports/coupons.min.css') !!}">
    @endpush

        <div class="page">

            @include('layouts.company-select',['version'=>'mobile'])

            <div style="display: none" class="page-header container inner-header">
                @can('report_sales')
                <header class="top-system">
                    <a href="{!! route('reports.marketing') !!}" class="back">
                        <svg style="margin-right: 10px;" width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26 9C26.5523 9 27 8.55228 27 8C27 7.44772 26.5523 7 26 7V9ZM0.292892 7.29289C-0.0976315 7.68342 -0.0976315 8.31658 0.292892 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928932C7.68054 0.538408 7.04738 0.538408 6.65685 0.928932L0.292892 7.29289ZM26 7L1 7V9L26 9V7Z" fill="#636363"/>
                        </svg>
                        Voltar para Marketing
                    </a>
                </header>
                @endcan

                <div class="row align-items-center justify-content-between top-inner-reports">
                    <div class="col-8">
                        <h1 class="d-flex title-system">
                            <span class="box-title ico-cupons">Cupons</span>
                            Cupons
                        </h1>
                        <!-- <h1 class="page-title">Cupons</h1>
                        <span type="hidden" class="error-data"></span> -->
                    </div>
                </div>
            </div>
            <div id="project-not-empty" style="display: none">

                <section class="container box-inner-reports" id="reports-content">
                    <div class="row" style="display: none;">
                        <div class="col-12 box-items-finance pending">
                            <div class="row mb-20 pending-blocked">
                            @if(!auth()->user()->hasRole('attendance'))
                                <div class="fianance-items box-inner-items col-md-3 col-6 pr-5 pr-md-15">
                                    <div class="finance-card border orange mb-10 block-result">
                                        <span class="title">Total pendente</span>
                                        <div class="d-flex">
                                            <span class="detail"></span>
                                            <strong class="number" id='total-pending'>0</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="fianance-items box-inner-items col-md-3 col-6 pr-5 pr-md-15">
                                    <div class="finance-card border blue mb-10 block-result">
                                        <span class="title">Quantidade de vendas</span>
                                        <div class="d-flex">
                                            <strong class="number" id="total_sales">0</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            </div>
                        </div>
                    </div>
                </section>

                <div id="reports-content" class="page-content container inner-reports-content">
                    <div class="row justify-content-between">
                        <div class="col-lg-12">
                            <form id='filter_form'>
                                <div id="" class="card shadow p-20">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                            <label for="projeto">Loja</label>
                                            <select name='select_project' id="projeto" class="sirius-select">
                                            </select>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                            <label for="status">Status</label>
                                            <select name='sale_status' id="status" class="sirius-select">
                                                <option value="">Todos status</option>
                                                <option value="1">Aprovado</option>
                                                <option value="2">Aguardando pagamento</option>
                                                <option value="4">Chargeback</option>
                                                <option value="7">Estornado</option>
                                                <option value="6">Em análise</option>
                                                <option value="8">Parcialmente estornado</option>
                                                <option value="20">Análise Antifraude</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                            <div class="form-icons">
                                                <label for="date_range" >Data</label>
                                                <div class="col-12 mb-10 date-report">
                                                    <div class="row align-items-center form-icons box-select coupons">
                                                        <input id="date-filter" type="text" name="daterange" class="font-size-14" value="" readonly>
                                                        <i style="right:16px;" class="form-control-icon form-control-icon-right o-agenda-1 font-size-18"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                            <button id="bt_filtro" class="btn btn-primary w-full coupons-btn">
                                                <img style="height: 12px; margin-right: 4px; visibility: hidden;" src=" {{ mix('build/global/img/svg/check-all.svg') }} ">
                                                Aplicar filtros
                                                <img style="height: 12px; margin-right: 4px; visibility: hidden;" src=" {{ mix('build/global/img/svg/check-all.svg') }} ">
                                            </button>
                                        </div>
                                    </div>
                                    <div class="collapse" id="bt_collapse">
                                        <div class="row"></div>
                                    </div>
                                    <div class="row" style="height: 30px">
                                        {{-- <div class="col-6 col-xl-3 mt-20 offset-xl-6 pr-0">
                                            <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center"
                                                data-toggle="collapse"
                                                data-target="#bt_collapse"
                                                aria-expanded="false"
                                                aria-controls="bt_collapse">
                                                <img id="icon-filtro" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/filter-2-line.svg') }} "/>
                                                <span id="text-filtro">Filtros avançados</span>
                                            </div>
                                        </div> --}}
                                        {{-- <div class="col-6 col-xl-3 mt-20 offset-xl-9">
                                            <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                                <img style="height: 12px; margin-right: 4px" src=" {{ mix('build/global/img/svg/check-all.svg') }} "/>
                                                Aplicar filtros
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="fixhalf"></div>

                        <div class="col-lg-12">
                            <div class="card shadow" style="min-height: 300px;">
                                <div class="data-holder">
                                    <table class="table-coupons table table-striped" style="width:100%;margin: auto;">
                                        <thead>
                                        <tr>
                                            <td class="table-title">Cupom</td>
                                            <td class="table-title">Loja</td>
                                            <td class="table-title">Utilizados</td>
                                        </tr>
                                        </thead>
                                        <tbody id="body-table-coupons"  img-empty="{!! mix('build/global/img/geral-1.svg')!!}">
                                        {{-- js carrega... --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center justify-content-md-end">
                                <ul id="pagination-coupons" class="pagination-sm text-right margin-chat-pagination" style="position:relative;float:right">
                                    {{-- js carrega... --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Quando não tem loja cadastrado  --}}
                @include('projects::empty')
            {{-- FIM loja nao existem lojas--}}
        </div>


@endsection

@push('scripts')
    <script src='{{ mix('build/layouts/reports/coupons.min.js') }}'></script>
@endpush
