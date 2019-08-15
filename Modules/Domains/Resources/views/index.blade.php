<!-- Page -->
<div class='row no-gutters mb-10'>
    <div style='position:absolute; width:50%' class="d-flex align-items-center">
        <a class="ml-8 rounded-add pointer" data-toggle="modal" data-target="#modal-detalhes-dominio" style='background-color: #4c6aff;'>
            <i class="icon wb-info"></i></a>
        <span class="link-button-dependent blue-50 pointer" data-toggle="modal" data-target="#modal-detalhes-dominio" style='margin-left:5px'>Como configurar o domínio?</span>
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
<ul id="pagination-domain" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
    {{-- js carrega... --}}
</ul>

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
                                <span> Clique em <strong>Adicionar domínio</strong></span>
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
                        <div class="panel-body justify-content-center">
                            <div class="d-flex align-items-center">
                                <span> Digite o nome do seu <strong>domínio</strong> onde seu site esta hospedado e clique em  <i class="material-icons btn-fix" style='color:green'> save </i><strong style='color:green'>Salvar</strong></span>
                            </div>
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
                            Se você estiver utilizando esse domínio em algum site,
                            adicione uma entrada do tipo <strong>A</strong> para 
                            seu site continuar funcionando normalmente.
                            <ul>
                                <li>Digite o nome do domínio no campo <strong>nome</strong></li>
                                <li>Digite o ip do servidor onde o site está hospedado no campo <strong>valor</strong></li>
                                <li>Clique em <strong>Adicionar</strong></li>
                            </ul>
                            Após clique em <strong>Contiuar</strong>
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
                            <strong>atualize as configurações</strong> com as novas entradas DNS geradas. Por fim clique em
                            <strong>Verificar</strong>
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
                            Obtendo sucesso o sistema estará pronto para uso. Caso contrário aguarde alguns minutos e utilize o botão
                            <i class="material-icons gradient">remove_red_eye</i> para verificar novamente.
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
