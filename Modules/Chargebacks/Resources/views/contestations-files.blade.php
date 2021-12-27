@push('css')
    <link rel="stylesheet" href='{{asset('/modules/sales/css/index.css?v=' . uniqid())}}'>

    <style>
        input[type="file"] {
            position: absolute;
            left: 0;
            opacity: 0;
            top: 0;
            bottom: 0;
            width: 100%;
        }

        .input-div {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #3788ea;
            border: 3px solid #4792ec;
            border-radius: 10px;
        }

        .input-div label {
            text-align:center;
            padding:20px 0;
            color:white
        }

        .input-div.dragover {
            background-color: #aaa;
        }

    </style>
@endpush
<div class="modal fade example-modal-lg" id="modal_contestation_files" aria-hidden="true"
     aria-labelledby="exampleModalTitle"
     role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple modal-sidebar modal-lg">
        <div id='modal-contestationFiles' class="modal-content p-20 " style="min-width: 300px;">
            <div class="header-modal">
                <div class="row justify-content-between align-items-center" style="width: 100%;">
                    <div class="col-lg-10 text-left"><h4 class="font-weight-bold">Arquivos para contestação</h4>
                        <strong id="sale_hash"></strong>
                    </div>

                    <div class="col-lg-2 text-right">
                        <a role="button" data-dismiss="modal">
                            <i class="material-icons pointer">close</i></a>
                    </div>

                    <div class="col-12">
                        <hr>
                    </div>
                </div>

            </div>
            <div class="modal-body row">

                    <div class="col-lg-12 col-sm-8 col-md-8">

                        <div style="padding:0px 10px;" class="switch-holder row mb-10" title='Ativar/Desativar'>
                            <label class="switch" style="margin-right:10px">
                                <input type="checkbox" name="status-file" id="status-file"
                                       class='check-status'>
                                <span class="slider round"></span>
                            </label>
                            <span id="check-status-text">Não concluido</span>
                        </div>


                        {{--                <p>Faturado por <strong id="company"></strong><br>--}}
                        {{--                    Pagamento via <strong id="payment-type"></strong> em <strong id="payday"></strong><br>--}}
                        {{--                    Liberação em <strong id="liberation"></strong></p>--}}
                        {{--                <hr>--}}

                        <div style="background:#fef3d6; border: 1px solid #fa941a; border-radius:15px; padding:10px">
                            Contestação realizada em <strong id="request_date"></strong><br>
                            Razão: <span id="reason"></span>
                        </div>
                        <br>
                        <p class="text-warning"><i class="fa fa-info-circle"></i> Permitido no máximo 10 arquivos por contestação</p>


                        {{--                <p>--}}
                        {{--                    Para evitar chargeback, sugerimos que você envie estes arquivos antes do prazo de expiração:--}}
                        {{--                </p>--}}
                        {{--                <ul>--}}
                        {{--                    <li>Nota fiscal</li>--}}
                        {{--                    <li>Comprovante de acordo com o titular do cartão</li>--}}
                        {{--                    <li>Comprovação de ciência do portador sobre condições de pagamento e cancelamento</li>--}}
                        {{--                    <li>Print das Políticas e Termos da sua loja virtual</li>--}}
                        {{--                    <li>Nota fiscal</li>--}}
                        {{--                </ul>--}}

                        {{--                <hr>--}}

                        <form method="post" id="sendfilesform" name="sendfilesform"
                              action="{{ route('contestations.sendContestationFiles') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="observation">Escolha uma categoria</label>
                                <select name="type" class="form-control" id="type">
                                    <option value="NOTA_FISCAL">Nota fiscal</option>
                                    <option value="POLITICA_VENDA">Politica de venda</option>
                                    <option value="ENTREGA">Entrega</option>
                                    <option value="INFO_ACORDO">Informação do acordo</option>
                                    <option value="OUTROS">Outros</option>
                                </select>
                            </div>
                            <div class="form-group">

                                {{--                            <div class="col-sm-6 col-xl-3 text-right mt-20">--}}
                                {{--                                <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">--}}
                                {{--                                    <input type="file" id="test">--}}
                                {{--                                    <img style="height: 12px; margin-right: 4px" src=" http://dev.admin.com/modules/global/img/svg/check-all.svg ">--}}
                                {{--                                    Aplicar filtros--}}
                                {{--                                </div>--}}
                                {{--                            </div>--}}

                                {{--                            <label for="pdf">Enviar os arquivos</label>--}}
                                {{--                            <input type="file" name="files[]" id="multiplefiles"--}}
                                {{--                                   class="form-control" multiple/>--}}
                                <div class="row mt-10 mb-0">
                                    <div class="col-8 text-left text-white">
                                        <div class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center" style="padding:0">
                                            <label for="test" class="text-white text-left ">
                                                <span class="o-upload-to-cloud-1 mt-2 text-white"></span>
                                                Selecionar o arquivo
                                                <input type="file" name="files[]" id="multiplefiles" multiple>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-4 text-left text-black-50">
                                        <p id="filename"></p>
                                    </div>

                                </div>

                            </div>
                            <div class="row mt-10 mb-0">
                                <div class="col-12 text-left">
                                    <input type="hidden" value="" name="contestation" id="contestation">
                                    <button id="btn-send-file" contestation="" type="submit"
                                            class="btn btn-primary">Enviar arquivo
                                    </button>
                                </div>
                            </div>


                        </form>
                        <hr>
                        <p>Últimos arquivos enviados</p>
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered" style="width:100%">
                                <thead style="width:100%">
                                <tr>
                                    <td>Tipo</td>
                                    <td>Arquivo</td>
                                    <td>Enviado em:</td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody id="latest_files">
                                </tbody>
                            </table>
                        </div>
                    </div>

            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
</div>
@push('scripts')
    <script src="{{ asset('/modules/chargebacks/js/contestations-detail.js?v=' . random_int(100, 10000)) }}"></script>
@endpush
