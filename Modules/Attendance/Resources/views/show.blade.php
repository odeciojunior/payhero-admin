@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=2') }}">
    @endpush
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">
                <i class="material-icons turn-back" style='color:grey;cursor:pointer;' title='Voltar'>arrow_back</i> Chamado: #
                <span id="ticket-id"></span>
            </h1>
            <hr class='mb-0 mt-10'>
        </div>
        <div class="page-content container">
            <div class="card card-shadow card-top">
                <div class="card-body bg-white p-40">
                    <div>
                        <h4 class='font-weight-bold ticket-subject'></h4>
                    </div>
                    <div>
                        <span class='font-size-14 ticket-description'></span>
                    </div>
                    <div>
                        <span class='font-size-14 customer-name'></span>
                    </div>
                    <div>
                          <span class='font-size-14 ticket-informations'></span>
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
                            <span class='font-weight-bold company-name'></span>
                        </div>
                        <div class='col-6 col-lg-3'>
                            <span>Planos</span>
                            <br>
                            <span class='font-weight-bold ticket-products'></span>
                        </div>
                        <div class='col-6 col-lg-3'>
                            <span>Valor total</span>
                            <br>
                            <span class='font-weight-bold total-value'></span>
                        </div>
                    </div>
                    <div class='my-20'>
                        <span class='font-size-16 mt-20 ticket-status'></span>
                        <hr class='mb-0 mt-10'>
                    </div>
                    <div style="display:none">
                        <span class="font-weight-bold d-block mb-10">Anexos:</span>
                        <div id='div-ticket-attachments'>
                            {{-- js carrega... --}}
                        </div>
                        <hr class="mt-30">
                    </div>
                    <div id='div-ticket-comments'>
                        {{-- js carrega... --}}
                    </div>
                    <div class='text-right mt-10 div-buttons'>
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
        <script src='{{asset('/modules/tickets/js/show.js?v=2')}}'></script>
    @endpush
@endsection
