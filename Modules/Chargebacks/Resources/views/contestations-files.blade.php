@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css?v=04') }}">
@endpush
<div class="modal fade example-modal-lg" id="modal_contestation_files" aria-hidden="true"
     aria-labelledby="exampleModalTitle"
     role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple modal-sidebar modal-lg">
        <div id='modal-contestationFiles' class="modal-content p-20 " style="width: 500px;">
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
            <div class="modal-body">

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

                <div style="background:#fef3d6; border: 1px solid #fa941a; border-radius:15px; padding:10px;">
                    Contestação realizada em <strong id="request_date"></strong><br>
                    Razão: <span id="reason"></span>
                </div>

                <br>
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
                            <label for="pdf">Enviar os arquivos</label>
                            <input type="file" name="files[]" id="multiplefiles"
                                   class="form-control" multiple/>
                        </div>
                        <div class="row mt-10 mb-0">
                            <div class="col-12 text-left">
                                <input type="hidden" value="" name="contestation" id="contestation">
                                <button id="update-contestation-observation" contestation="" type="submit"
                                        class="btn btn-primary">Enviar
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
        <div class="clearfix"></div>
    </div>
</div>
</div>
@push('scripts')
    <script src="{{ asset('/modules/chargebacks/js/contestations-detail.js?v=' . random_int(100, 10000)) }}"></script>
@endpush
