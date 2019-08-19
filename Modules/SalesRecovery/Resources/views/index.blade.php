@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Recuperação de vendas</h1>
        </div>
        <div class="page-content container">
            @if(count($projects) > 0 )
                <div id="" class="card shadow p-20">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3">
                            <label for="project">Projeto</label>
                            <select name='select_project' id="project" class="form-control select-pad">
                                {{--                            <option value="">Todos projetos</option>--}}
                                @foreach($projects as $project)
                                    <option value="{{Hashids::encode($project['id'])}}">{{$project['nome']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3">
                            <label for="type_recovery">Tipo de Recuperação</label>
                            <select name='select_type_recovery' id="type_recovery" class="form-control select-pad">
                                <option value="1" selected>Carrinho Abandonado</option>
                                <option value="2">Boleto Vencido</option>
                                <option value="3">Cartão Recusado</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                            <label for="start_date">Data inicial</label>
                            <input name='start_date' id="start_date" class="form-control input-pad" type="date">
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                            <label for="end_date">Data final</label>
                            <input name='end_date' id="end_date" class="form-control input-pad" type="date">
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 mt-30 text-right float-right">
                            <button id="bt_filtro" class="btn btn-primary col-12">
                                <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card shadow" style="min-height: 300px">
                    <div class="page-invoice-table table-responsive">
                        <table id='carrinhoAbandonado' class="table table-striped unify">
                            <thead>
                                <tr>
                                    <td class="table-title display-sm-none display-m-none display-lg-none">Data</td>
                                    <td class="table-title">Projeto</td>
                                    <td class="table-title display-sm-none display-m-none">Cliente</td>
                                    <td class="table-title">Email</td>
                                    <td class="table-title">Sms</td>
                                    <td class="table-title">Status</td>
                                    <td class="table-title">Valor</td>
                                    <td class="table-title display-sm-none"></td>
                                    <td class="table-title display-sm-none">Link</td>
                                    <td class="table-title display-sm-none">Detalhes</td>
                                </tr>
                            </thead>
                            <tbody id="table_data" class='min-row-height'>
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal detalhes da venda-->
                    <div class="modal fade example-modal-lg" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div class="modal-dialog modal-simple modal-sidebar modal-lg">
                            <div class="modal-content p-20 " style="">
                                <div class="header-modal">
                                    <div class="row justify-content-between align-items-center" style="width: 100%;">
                                        <div class="col-lg-2"> &nbsp;</div>
                                        <div class="col-lg-8 text-center"><h4 id='modal-title'> Detalhes da venda </h4>
                                        </div>
                                        <div class="col-lg-2 text-right">
                                            <a role="button" data-dismiss="modal">
                                                <i class="material-icons pointer">close</i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-body">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->
                </div>
                <div class="row">
                    <div class="col-12">
                        <ul id="pagination" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                            {{-- js carrega... --}}
                        </ul>
                    </div>
                </div>
            @else
                @push('css')
                    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
                @endpush

                <div class="content-error d-flex text-center">
                    <img src="{!! asset('modules/global/img/emptyprojetos.svg') !!}" width="250px">
                    <h1 class="big gray">Você ainda não tem nenhum projeto!</h1>
                    <p class="desc gray">Que tal criar um primeiro projeto para começar a vender? </p>
                    <a href="/projects/create" class="btn btn-primary gradient">Cadastrar primeiro projeto</a>
                </div>
            @endif
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('modules/salesrecovery/js/salesrecovery.js') }}"></script>

    @endpush

@endsection

