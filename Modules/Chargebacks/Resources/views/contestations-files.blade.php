@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css?v=04') }}">
@endpush
<div class="modal fade example-modal-lg" id="modal_contestation_files" aria-hidden="true" aria-labelledby="exampleModalTitle"
     role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple modal-sidebar modal-lg">
        <div id='modal-contestationFiles' class="modal-content p-20 " style="width: 500px;">
            <div class="header-modal">
                <div class="row justify-content-between align-items-center" style="width: 100%;">
                    <div class="col-lg-2"> &nbsp;</div>
                    <div class="col-lg-8 text-center"><h4>Arquivos para contestação</h4></div>
                    <div class="col-lg-2 text-right">
                        <a role="button" data-dismiss="modal">
                            <i class="material-icons pointer">close</i></a>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <p>Envie arquivos para contestação xxxxxxx xxxxxxxxxx xxxxxxxxxxxxxx xxxxxxxxx</p>
                <hr>
                <form action="">

                    <div class="row"></div>
                    <div class="form-group">
                        <label for="observation">Escolha uma categoria</label>
                        <select name="" class="form-control" id="">
                            <option value="NOTA_FISCAL">Nota fiscal</option>
                            <option value="POLITICA_VENDA">Politica de venda</option>
                            <option value="ENTREGA">Entrega</option>
                            <option value="INFO_ACORDO">Informação do acordo</option>
                            <option value="OUTROS">Nota fiscal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pdf">Enviar os arquivos</label>
                        <input type="file" id="file_contestation" name="file_contestation"
                               class="form-control" multiple/>
                    </div>
                    <div class="row mt-40 mb-0">
                        <div class="col-12 text-right">
                            <button id="update-contestation-observation" contestation="" type="button"
                                    class="btn btn-success">Enviar
                            </button>
                        </div>
                    </div>

                </form>

                <hr>
                <p>Últimos arquivos enviados</p>
                <hr>
                <table class="table table-responsive table-bordered" style="width:100%">
                    <thead style="width:100%">
                    <tr>
                        <td>Tipo</td>
                        <td>Arquivo</td>
                        <td>Enviado em:</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            Politica de venda
                        </td>
                        <td>
                            <a href="#">Imagem</a>
                        </td>
                        <td>
                            17/03/2021
                        </td>
                    </tr>
                    </tbody>

                </table>

            </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('/modules/chargebacks/js/contestations-detail.js?v=' . random_int(100, 10000)) }}"></script>
@endpush
