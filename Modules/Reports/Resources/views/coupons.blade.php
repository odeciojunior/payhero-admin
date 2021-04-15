@extends("layouts.master")
@section('title', '- Utilização de Cupons')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/reports/css/reports.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=02') !!}">
    @endpush

        <div class="page">
            <div style="display: none" class="page-header container">
                <div class="row">
                    <div class="col-8">
                        <h1 class="page-title">Utilização de Cupons</h1>
                        <span type="hidden" class="error-data"></span>
                    </div>
                </div>
            </div>
            <div id="project-not-empty" style="display: none">
                <div id="reports-content" class="page-content container">
                    <div class="row justify-content-between mt-20">
                        <div class="col-lg-12">
                            <form id='filter_form'>
                                <div id="" class="card shadow p-20">
                                    <div class="row align-items-baseline">
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                            <label for="projeto">Projeto</label>
                                            <select name='select_project' id="projeto" class="form-control select-pad">
                                                <option value="">Todos projetos</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                            <label for="status">Status</label>
                                            <select name='sale_status' id="status" class="form-control select-pad">
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
                                            <div class="form-group form-icons">
                                                <label for="date_range" >Data</label>
                                                <i style="right: 20px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-18"></i>
                                                <input name='date_range' id="date_range" class="select-pad pr-30" placeholder="Clique para editar..." readonly >
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                            <label> </label>
                                            <button id="bt_filtro" class="btn btn-primary col-sm-12" style="margin-top: .5rem">
                                                <img style="height: 12px; margin-right: 4px" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} ">Aplicar
                                            </button>
                                        </div>
                                        <div class="col-2">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-lg-12">
                            <div class="card shadow" style="min-height: 300px;">
                                <div class="data-holder">
                                    <table class="table-coupons table table-striped" style="width:100%;margin: auto;">
                                        <thead>
                                        <tr>
                                            <th>Código cupom</th>
                                            <th>Projeto</th>
                                            <th>Quantidade utilizada</th>
                                        </tr>
                                        </thead>
                                        <tbody id="body-table-coupons">
                                        {{-- js carrega... --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <ul id="pagination-coupons" class="pagination-sm margin-chat-pagination" style="position:relative;float:right">
                                {{-- js carrega... --}}
                            </ul>
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
