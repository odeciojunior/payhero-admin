@extends("layouts.master")
@section('title', '- Utilização de Cupons')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/reports/css/v6/css/all.min.css') !!}">
        <!-- <link rel="stylesheet" href="{{ asset('/modules/global/css/table.css?v='. versionsFile()) }}"> -->
        <link rel="stylesheet" href="{!! asset('modules/reports/css/coupons.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/reports.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=123') !!}">
    @endpush

        <div class="page">
            <div style="display: none" class="page-header container">
                <header class="top-system">
                    <a href="{!! route('reports.marketing') !!}" class="back">
                        <i class="fa-solid fa-arrow-left-long"></i>
                        Voltar para Marketing
                    </a>
                </header>

                <div class="row align-items-center justify-content-between" style="min-height: 50px;">
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
                
                <section class="container box-reports" id="reports-content">
                    <div class="row">
                        <div class="col-12 box-items-finance pending">
                            <div class="row mb-20">
                            @if(!auth()->user()->hasRole('attendance'))
                                <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                    <div class="finance-card border orange mb-10">
                                        <span class="title">Total bloqueado</span>
                                        <div class="d-flex">
                                            <span class="detail">R$</span>
                                            <strong class="number" id="">356.568,22</strong>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                                    <div class="finance-card border blue mb-10">
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

                <div id="reports-content" class="page-content container" style="padding-top: 0">
                    <div class="row justify-content-between">
                        <div class="col-lg-12">
                            <form id='filter_form'>
                                <div id="" class="card shadow p-20">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                            <label for="projeto">Projeto</label>
                                            <select name='select_project' id="projeto" class="sirius-select">
                                                <option value="">Todos projetos</option>
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
                                                <i style="right: 25px;bottom: 21px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-20"></i>
                                                <input name='date_range' id="date_range" class="input-pad pr-30" placeholder="Clique para editar..." readonly style="height: 49px;">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                            <button id="bt_filtro" class="btn btn-primary w-full" style="padding: 14px 15px;margin-top: 27px !important;">
                                                <img style="height: 12px; margin-right: 4px" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} ">Aplicar filtros
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
                                                <img id="icon-filtro" class="hidden-xs-down" src=" {{ asset('/modules/global/img/svg/filter-2-line.svg') }} "/>
                                                <span id="text-filtro">Filtros avançados</span>
                                            </div>
                                        </div> --}}
                                        {{-- <div class="col-6 col-xl-3 mt-20 offset-xl-9">
                                            <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                                <img style="height: 12px; margin-right: 4px" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} "/>
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
                                            <td class="table-title">Projeto</td>
                                            <td class="table-title">Utilizados</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tbody id="body-table-coupons"  img-empty="{!! asset('modules/global/img/geral-1.svg')!!}">
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
            {{-- Quando não tem projeto cadastrado  --}}
                @include('projects::empty')
            {{-- FIM projeto nao existem projetos--}}
        </div>


@endsection

@push('scripts')
    <script src='{{asset('modules/reports/js/report-coupons.js?v=' . random_int(100, 10000))}}'></script>
    <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
@endpush
