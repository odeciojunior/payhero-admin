<!-- Page -->
<div class='row no-gutters mb-10'>
    <div style='position:absolute; width:50%' class="d-flex align-items-center">
        <a class="ml-8 rounded-add pointer" data-toggle="modal" data-target="#modal-detalhes-dominio" style='background-color: #4c6aff;'>
            <i class="icon wb-info"></i></a>
        <span class="link-button-dependent blue-50 pointer" data-toggle="modal" data-target="#modal-detalhes-dominio" style='margin-left:5px'>Como configurar o domínio?</span>
    </div>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div id="add-domain" class="d-flex align-items-center justify-content-end pointer">
            <span class="link-button-dependent red"> Adicionar domínio </span>
            <a class="ml-10 rounded-add" id='btn-add-domain'>
                <i class="icon wb-plus" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='tabela-dominios' class='table table-striped text-left table-dominios table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title text-center'>Nome</td>
                        <td class='table-title text-center'>Status</td>
                        <td class='table-title options-column-width text-center'>Opções</td>
                    </tr>
                </thead>
                <tbody id='domain-table-body' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-domain" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
    {{-- js carrega... --}}
</ul>
{{-- Modal Create Domain--}}
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-add-domain" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title-add-domain"></h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class='modal-content-add-domain'>
                <div id="modal-body-add-domain" class="modal-body" style='min-height: 100px;'>
                    <form id='form-add-domain'>
                        <div class='row'>
                            <div class='form-group'>
                                <label for='name'>Domínio</label>
                                <input name='name' type='text' class='input-pad name-domain' id='name' placeholder='seudominio.com'>
                                <span class='info-domain'></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer-add-domain">
                <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                    Fechar
                </a>
                <button id="btn-modal-add-domain" type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success float-right" style='display:none;'>
                    <i class="material-icons btn-fix"> save </i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Modal Create Domain--}}

<!-- Modal Editar Dominio -->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-content-domain" role="dialog" tabindex="-1" style='padding-right: 15px;'>
    <div class="modal-dialog modal-dialog-centered modal-simple modal-lg">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title-edit-domain">Gerenciador de registros DNS </h4>
                <a id="modal-button-close-edit-domain-record" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal-body-content-domain" class="modal-body" style='min-height: 100px'>
                <a id='domain' hidden></a>
                <form id='form-modal-add-domain-record' class=''>
                    <div class="row">
                        <div class='form-group col-lg-2'>
                            <select id='type-register' name='type-register' class='form-control input-pad'>
                                <option value='A'>A</option>
                                <option value='AAA'>AAA</option>
                                <option value='CNAME'>CNAME</option>
                                <option value='TXT'>TXT</option>
                                <option value='MX'>MX</option>
                            </select>
                        </div>
                        <div class='form-group col-lg-10'>
                            <input id='name-register' name='name-register' class='input-pad' placeholder='Nome' required>
                            <p id='error-name-register-dns' class='text-danger' style='display:none;'>O campo nome é obrigatório</p>
                        </div>
                        <div class='form-group col-lg-3'>
                            <select id='proxy-select' name='proxy' class='input-pad'>
                                <option id='proxy-active' value='1'>Proxy Ativado</option>
                                <option id='proxy-inactive' value='0'>Proxy Desativado</option>
                            </select>
                        </div>
                        <div class='col-lg-8'>
                            <input id='value-record' name='value-record' class='input-pad' placeholder='Valor' required>
                            <p id='error-value-record' class='text-danger' style='display:none;'>O campo valor é obrigatório</p>
                        </div>
                        <div class='col-lg-1'>
                            <button class='btn btn-primary' id='bt-add-record' title='Adicionar novo registro dns'>
                                <i class='fa fa-plus'></i>
                            </button>
                        </div>
                    </div>
                </form>
                <div class='row mx-2 col-sm-12'>
                    <h4 class='text-sm-center col-sm-12'>Listas de registros DNS</h4>
                </div>
                <div id='divCustomDomain' class='table-responsive' style='overflow-y:scroll; height: 300px;'>
                    <table id='new-registers-table' class='table table-hover table-bordered table-striped'>
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Nome</th>
                                <th>Conteúdo</th>
                                <th>Proxy</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id='table-body-new-records'>
                            {{-- JS carrega tabela  --}}
                        </tbody>
                    </table>
                    <div id='empty-info' class='alert alert-info' align='center' style='display:none;'>
                        <h4>Você ainda não possui nenhuma entrada personalizada!</h4>
                        <h4>Fique a vontade para adicionar acima!</h4>
                    </div>
                </div>
                <div class='bg-info'></div>
            </div>
            <div class="modal-footer-domain-content">
                <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                    Fechar
                </a>
                <button id="btn-modal-continue-domain" type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success float-right btn-continue-domain" style='display:none;'>
                    Continuar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal detalhes do domínio -->
{{--   --}}


<!-- Modal Excluir Modal -->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-domain" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1" style='padding-right: 15px;'>
    <div class="modal-dialog  modal-dialog-centered  modal-simple">
        <div class="modal-content" id='modal-content-domain-delete' style="height: 294px;">
            <div class="modal-header text-center">
                <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="close-modal-delete-domain">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal-delete-domain-body" class="modal-body text-center p-20">
                <div class="d-flex justify-content-center not-domain-none">
                    <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                </div>
                <h3 class="black not-domain-none" id='title-delete-domain'> Você tem certeza? </h3>
                <p class="gray not-domain-none" id='description-delete-domain'> Se você excluir esse registro, não será possível recuperá-lo! </p>
            </div>
            <div class="modal-delete-footer modal-footer d-flex align-items-center justify-content-center">
                <button id='btn-cancel-delete-domain' type="button" class="col-4 btn btn-gray btn-delete-modal-domain" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                <button id="btn-delete-domain" type="button" class="col-4 btn btn-danger btn-delete-modal-domain" style="width: 20%;">Excluir</button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->
<!--Modal Informações de dominios-->
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
                            Se você estiver utilizando esse domínio em algum site, adicione uma entrada do tipo
                            <strong>A</strong> para seu site continuar funcionando normalmente.
                            <ul>
                                <li>Digite o nome do domínio no campo <strong>nome</strong></li>
                                <li>Digite o ip do servidor onde o site está hospedado no campo <strong>valor</strong>
                                </li>
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
<!--Modal Informações de dominios-->
{{-- Modal Informações dns e recheck --}}
<div class='modal fade example-modal-lg modal-3d-flip-vertical' id='modal-info-dns' role='dialog' tabindex='-1' style='padding-right: 15px;'>
    <div class='modal-dialog modal-dialog-centered modal-simple'>
        <div class='modal-content p-10'>
            {{-- Modal verificação --}}
            <div id='content-modal-recheck-dns'>
                <div class='modal-header' id='modal-dns-header'>
                    <h4 class='modal-title' id='modal-title-dns-recheck'>Verificação</h4>
                    <a id='modal-button-close-dns-rechek' class='close-card pointer close' role='button' data-dismiss='modal' aria-label='Close'>
                        <i class='material-icons md-16'>close</i>
                    </a>
                </div>
                <div class='content-dns'>
                    <div class='modal-body' id='modal-info-dsn-body' style='min-height: 100px;'>
                        <div class='swal2-icon swal2-info swal2-animate-info-icon' style='display: flex;'>i</div>
                        <h3 align='center'>
                            <strong>Domínio cadastrado</strong>
                        </h3>
                        <h4 align='center'>Agora falta pouco</h4>
                        <h4 align='center'>
                            Entre onde você registrou seu dominio
                            <span id='nameHost'></span>
                            e remova os nameservers atuais, logo após você só precisa adicionar esses novos nameservers. Depois clique em
                            <strong style='color: green;'>Verificar</strong>
                        </h4>
                        <div id="table-info-dns-check" style="width:100%">
                            <table class="table table-striped">
                                <thead></thead>
                                <tbody id='table-zones-add'>
                                </tbody>
                            </table>
                        </div>
                        <span id='domain-hash'></span>
                        <div style='width: 100%; text-align: center; padding-top: 3%;' id='div-recheck-dns'>
{{--                            <button class='btn btn-success btn-verify-domain' domain='' style='font-size: 25px;'>Verificar</button>--}}
                        </div>
                    </div>
                </div>
                <div class='modal-footer' id='modal-dns-footer'>
                </div>
            </div>
            {{-- Modal erro verificação --}}
            <div id='content-modal-recheck-dns-error' style='display:none;'>
                <div class='modal-header' id='modal-dns-header'>
                    <h4 class='modal-title' id='modal-title-dns-recheck'>Oppsssss...</h4>
                    <a id='modal-button-close-dns-rechek' class='close-card pointer close' role='button' data-dismiss='modal' aria-label='Close'>
                        <i class='material-icons md-16'>close</i>
                    </a>
                </div>
                <div class='modal-body' id='modal-info-dsn-body' style='min-height: 100px;'>
                    <div id="modal-add-body-domain-erro-verificacao" class="modal-body" style="min-height: 100px">
                        <div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;">
                            <span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span>
                        </div>
                        <h3 align="center"><strong>Domínio ainda não registrado</strong></h3>
                        <h4 align="center">Parece que o seu dominio ainda não foi liberado</h4>
                        <h4 align="center">Seria bom conferir as configurações no seu provedor de dominio, caso tenha alguma duvida em como realizar a configuração
                            <span class="red pointer" data-dismiss="modal" data-toggle="modal" data-target="#modal-detalhes-dominio">clique aqui</span>
                        </h4>
                        <div style="width:100%;text-align:center;padding-top:3%">
                            <span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span>
                        </div>
                    </div>
                </div>
                <div class='modal-footer' id='modal-dns-footer'>
                </div>
            </div>
            {{-- Modal Sucesso --}}
            <div id='content-modal-recheck-dns-success' style='display:none;'>
                <div class='modal-header' id='modal-dns-success-header'>
                    <h4 class='modal-title' id='modal-title-dns-success-recheck'>Verificação</h4>
                    <a id='modal-button-close-dns-success-rechek' class='close-card pointer close' role='button' data-dismiss='modal' aria-label='Close'>
                        <i class='material-icons md-16'>close</i>
                    </a>
                </div>
                <div class='content-dns'>
                    <div class='modal-body' id='modal-info-dsn-success-body' style='min-height: 100px;'>
                        <div class='' style='display: flex;'>
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                            </svg>
                        </div>
                        <h3 align='center'>
                            <h4 align="center">Tudo pronto já podemos começar</h4>
                        </h3>
                        <h4 align="center">O checkout transparente e o servidor de email já estão configurados apenas aguardando suas vendas.</h4>
                        <div style="width:100%;text-align:center;padding-top:3%">
                            <span class="btn btn-success" data-dismiss="modal" style="font-size: 25px">Começar</span>
                        </div>
                    </div>
                </div>
                <div class='modal-footer' id='modal-dns-footer'>
                </div>
            </div>
        </div>
    </div>
</div>

