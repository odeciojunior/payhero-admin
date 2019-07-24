<!-- Page -->
<div class='row no-gutters mb-10'>
    <div style='position:absolute; width:50%' class="d-flex align-items-center">
        <a class="ml-8 rounded-add pointer" data-toggle="modal" data-target="#modal-detalhes-dominio" style='background-color: #4c6aff;'>
            <i class="icon wb-info"></i></a>
        <span class="link-button-dependent blue-50 pointer" data-toggle="modal" data-target="#modal-detalhes-dominio" style='margin-left:5px'>Como configurar o dominio?</span>
    </div>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div id="add-domain" class="d-flex align-items-center justify-content-end">
            <span class="link-button-dependent red pointer" data-toggle="modal" data-target="#modal-content"> Adicionar domínio </span>
            <a class="ml-10 rounded-add pointer" data-toggle="modal" data-target="#modal-content">
                <i class="icon wb-plus" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='tabela-dominios' class='table text-left table-dominios table-striped' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title' width='400px'>Nome</td>
                        <td class='table-title' width='400px'>Ip do domínio</td>
                        <td class='table-title' width='400px'>Status</td>
                        <td class='table-title' width='200px'>Opções</td>
                    </tr>
                </thead>
                <tbody id='domain-table-body'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal detalhes do domínio -->
<!--<div class='modal fade example-modal-lg modal-3d-flip-vertical' id='modal-detalhes-dominio' aria-hidden='true' aria-labelledby='exampleModalTitle' role='dialog' tabindex='-1'>
    <div class='modal-dialog modal-simple'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'></span>
                </button>
                <h4 id='modal-dominio-titulo' class='modal-title' style='width:100%; text-align:center;'></h4>
            </div>
            <div class='modal-body pr-30 pl-30' id='modal-dominio-body'>
                <label for='information'>Descrição</label>
                <input name='information' class='form-control' id='information' placeholder='descricao'>
            </div>
            <div class='modal-footer'>
                <button type='button' id='btn-save-updated' class='btn btn-success' data-dismiss='modal'>Salvar</button>
                <button type='button' class='btn btn-danger' data-dismiss='modal'>Fechar</button>
            </div>
        </div>
    </div>
</div>-->
<!-- End Modal -->
<!--MODAL INFORMAÇÃO-->
<div class="modal fade modal-3d-flip-vertical" id="modal-detalhes-dominio" aria-hidden='true' aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="panel-group panel-group-continuous m-0" id="exampleAccrodion1" aria-multiselectable="true" role="tablist">
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingFirst" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse" href="#exampleCollapseFirst" aria-controls="exampleCollapseFirst" aria-expanded="false">
                            <strong>Primeiro passo</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="exampleCollapseFirst" aria-labelledby="exampleHeadingFirst" role="tabpanel" style="">
                        <div class="panel-body justify-content-center">
                            <div class="d-flex align-items-center">
                                <span> Clique em <strong>adicionar domínio</strong></span>
                                <a class="ml-10 rounded-add pointer">
                                    <i class="icon wb-plus" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingSecond" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse" href="#exampleCollapseSecond" aria-controls="exampleCollapseSecond" aria-expanded="false">
                            <strong>Segundo passo</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="exampleCollapseSecond" aria-labelledby="exampleHeadingSecond" role="tabpanel" style="">
                        <div id='segundaInfo' class="panel-body">
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingThird" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse" href="#exampleCollapseThird" aria-controls="exampleCollapseThird" aria-expanded="false">
                            <strong>Terceiro passo</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="exampleCollapseThird" aria-labelledby="exampleHeadingThird" role="tabpanel" style="">
                        <div class="panel-body">
                            Clique no botão de
                            <strong>vizualizar nas opções</strong>, lá você vai encontrar as novas configurações de dns para seu domínio
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingFourth" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse" href="#exampleCollapseFourth" aria-controls="exampleCollapseFourth" aria-expanded="false">
                            <strong>Quarto passo</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="exampleCollapseFourth" aria-labelledby="exampleHeadingFourth" role="tabpanel" style="">
                        <div class="panel-body">
                            Logo após, vá até onde você registrou seu domínio e
                            <strong>atualize as configurações</strong> com as novas entradas DNS
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingFifth" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse" href="#exampleCollapseFifth" aria-controls="exampleCollapseFifth" aria-expanded="false">
                            <strong>Quinto passo</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="exampleCollapseFifth" aria-labelledby="exampleHeadingFifth" role="tabpanel" style="">
                        <div class="panel-body">
                            Pronto, agora é só
                            <strong>esperar</strong> que assim que seu domínio estiver aprovado nós te avisaremos
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingSixth" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse" href="#exampleCollapseSixth" aria-controls="exampleCollapseFifth" aria-expanded="false">
                            <strong>Porque preciso configurar meu domínio?</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="exampleCollapseSixth" aria-labelledby="exampleHeadingSixth" role="tabpanel" style="">
                        <div class="panel-body">
                            <ul>
                                <li>Checkout transparente - assim que seu domínio estiver configurado o cliente final não precisa sair do seu domínio para finalizar o pagamento. (Ex: https://checkout.minhaloja.com</li>
                                <li>Servidor de email - seus clientes receberão notificações de email em nome da sua loja.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODAL INFORMAÇÃO -->
