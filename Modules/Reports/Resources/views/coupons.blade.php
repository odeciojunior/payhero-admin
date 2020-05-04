@extends("layouts.master")
@section('title', '- Utilização de Cupons')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/reports/css/reports.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
    @endpush

    <div class="page">
        <div class="page-header container">
            <div class="row">
                <div class="col-8">
                    <h1 class="page-title">Utilização de Cupons</h1>
                    <span type="hidden" class="error-data"></span>
                </div>
            </div>
        </div>
        <div id="reports-content" class="page-content container" style="display:none">
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
                                    <label for="date_type" >Data</label>
                                    <input name='date_range' id="date_range" class="select-pad" placeholder="Clique para editar..." readonly >
                                </div>
                                <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                    <label> </label>
                                    <button id="bt_filtro" class="btn btn-primary col-sm-12" style="margin-top: .5rem">
                                        <i class="icon wb-check" aria-hidden="true"></i>Aplicar
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
                        <div class=" card-body data-holder">
                            <table class="table-coupons table table-striped" style="width:100%;margin: auto; margin-top:15px;">
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
                    <ul id="pagination-coupons" class="pagination-sm" style="position:relative;float:right">
                        {{-- js carrega... --}}
                    </ul>
                </div>
            </div>
        </div>
        @include('projects::empty')
    </div>
    </div>
@endsection

@push('scripts')
    <script src='{{asset('modules/reports/js/report-coupons.js?v=1')}}'></script>
    <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
@endpush
