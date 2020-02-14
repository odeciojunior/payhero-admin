@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=1') }}">
    @endpush
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">
                <i class="material-icons turn-back" style='color:grey;cursor:pointer;' title='Voltar'>arrow_back</i> Chamado: #490019
            </h1>
            <hr class='mb-0 mt-10'>
        </div>
        <div class="page-content container">
            <div class="card card-shadow card-top">
                <div class="card-body bg-white p-40">
                    <div>
                        <span class='font-size-14 font-weight-bold ticket-subject'>Título do chamado</span>
                    </div>
                    <div>
                        <span class='font-size-14 customer-name'></span>
                    </div>
                    <div>
                          <span class='font-size-14 ticket-informations'>
                           </span>
                    </div>
                    <div class='row my-20 font-size-12'>
                        <div class='col-6 col-lg-3'>
                            <span>Código da Transação</span>
                            <br>
                            <span id='sale_code' class='font-weight-bold sale-code'></span>
                        </div>
                        <div class='col-6 col-lg-3'>
                            <span>Empresa</span>
                            <br>
                            <span class='font-weight-bold company-name'>Health Lab</span>
                        </div>
                        <div class='col-6 col-lg-3'>
                            <span>Produtos</span>
                            <br>
                            <span class='font-weight-bold ticket-products'></span>
                        </div>
                        <div class='col-6 col-lg-3'>
                            <span>Valor total</span>
                            <br>
                            <span class='font-weight-bold total-value'>R$ 360,00</span>
                        </div>
                    </div>
                    <div class='my-20'>
                        <span class='font-size-16 mt-20 ticket-status'></span>
                        <hr class='mb-0 mt-10'>
                    </div>
                    <div id='div-ticket-comments'>
                        {{--                        <div class="d-flex flex-row mb-3">--}}
                        {{--                            <div class="p-2 bd-highlight">--}}
                        {{--                                <img src="https://i.pinimg.com/736x/27/72/43/277243ecf332f1c672a861a2c536a690.jpg" style='height:50px;width:50px;' class="img-fluid rounded-circle">--}}
                        {{--                            </div>--}}
                        {{--                            <div class="p-2">--}}
                        {{--                                <span class='font-weight-bold'>Matheus Silva</span>--}}
                        {{--                                <br>--}}
                        {{--                                <span>Postado em: 17/09/2019 12:00</span>--}}
                        {{--                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                    </div>
                    <div class='text-right mt-10 div-buttons'>
                        <button id='btn-solve' class='btn btn-outline-danger mr-20'>Resolver chamado</button>
                        <button id='btn-answer' class='btn btn-primary'>Responder</button>
                    </div>
                    <div class='row div-message' style='display:none;'>
                        <div class='col-lg-12'>
                            <div class='form-group'>
                                <label>Mensagem</label>
                                <textarea class='form-control user-message' placeholder='Digite sua resposta' rows='6'></textarea>
                            </div>
                        </div>
                        <div class='col-lg-12 text-right'>
                            <button id='btn-cancel' class='btn mr-20'>Cancelar</button>
                            <button id='btn-send' class='btn btn-primary'>Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src='{{asset('/modules/tickets/js/show.js')}}'></script>
    @endpush
@endsection
