@extends("layouts.master")

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
    <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css') }}">
@endpush

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Perfil</h1>
        </div>
        <div class="page-content container">
            <div class="card shadow">
                <div class="nav-tabs-horizontal mt-15" data-plugin="tabs">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                        <li class="nav-item" role="presentation" id='nav_users'>
                            <a class="nav-link active" data-toggle="tab" href="#tab_user" aria-controls="tab_user" role="tab">Meus dados
                            </a>
                        </li>
                        @if(!auth()->user()->hasRole('attendance'))
                            <li class="nav-item" role="presentation" id="nav_documents">
                                <a class="nav-link" data-toggle="tab" href="#tab_documentos" aria-controls="tab_documentos" role="tab">
                                    Documentos
                                </a>
                            </li>
                        @endif
                        @if(!auth()->user()->hasRole('attendance'))
                            <li class="nav-item" role="presentation" id="nav_taxs">
                                <a class="nav-link" data-toggle="tab" href="#tab_taxs" aria-controls="tab_taxs" role="tab">
                                    Tarifas e Prazos
                                </a>
                            </li>
                        @endif
                        @if(!auth()->user()->hasRole('attendance'))
                            <li class="nav-item" role="presentation" id="nav_notifications">
                                <a class="nav-link" data-toggle="tab" href="#tab_notifications" aria-controls="tab_notifications" role="tab">
                                    Notificações
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="p-30 pt-20">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tab_user" role="tabpanel">
                                <form method="POST" enctype="multipart/form-data" id='profile_update_form'>
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h5 class="title-pad"> Dados Pessoais </h5>
                                            <p class="sub-pad"> Precisamos saber um pouco sobre você </p>
                                        </div>
                                        <div class="col">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="form-group col-xl-6">
                                                    <label for="name">Nome Completo</label>
                                                    <input name="name" value="" type="text" class="input-pad" id="name" placeholder="Nome">
                                                </div>
                                                <div class="form-group col-xl-6">
                                                    <label for="email">Email</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="input_group_email" id="addon-email">
                                                            </span>
                                                        </div>
                                                        <input name="email" value="" type="text" class="input-pad form-control" id="email" placeholder="Email" aria-describedby="addon-email">
                                                    </div>
                                                    <small id="message_not_verified_email" style='color:red; display:none;'>Email não verificado, clique
                                                        <a href='#' id='btn_verify_email' onclick='event.preventDefault();' data-toggle='modal' data-target='#modal_verify_email'>aqui</a>
                                                        para verificá-lo!
                                                    </small>
                                                </div>
                                                <div class="form-group col-xl-6">
                                                    {{--carrega label no js--}}
                                                    <label for="cpf" class='label-document'></label>
                                                    <input name="document" value="" type="text" class="input-pad" id="document">
                                                </div>
                                                <div class="form-group col-xl-6">
                                                    <label for="celular">Celular (WhatsApp)</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="input_group_cellphone" id="addon-cellphone">
                                                            </span>
                                                        </div>
                                                        <input name="cellphone" value="" type="text" class="input-pad form-control" id="cellphone" placeholder="Celular" aria-describedby="addon-cellphone">
                                                    </div>
                                                    <small id="message_not_verified_cellphone" style='color:red; display:none;'>Celular não verificado, clique
                                                        <a href='#' id='btn_verify_cellphone' onclick='event.preventDefault();' data-toggle='modal' data-target='#modal_verify_cellphone'>aqui</a>
                                                        para verificá-lo!
                                                    </small>
                                                </div>
                                                <div class="form-group col-xl-4">
                                                    <label for="date_birth">Data de nascimento</label>
                                                    <input name="date_birth" value="" type="date" class="form-control input-pad" id="date_birth">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group col-6">
                                                <label for="select_profile_photo">Foto de perfil</label>
                                                <br>
                                                <input name="profile_photo" type="file" class="form-control input-pad" id="profile_photo" style="display:none">
                                                <div style="margin: 20px 0 0 30px;">
                                                    <img src="" id="previewimage" alt="Nenhuma foto cadastrada" accept="image/*" style="max-height: 250px; max-width: 350px; cursor:pointer;"/>
                                                </div>
                                                <input type="hidden" name="photo_x1"/>
                                                <input type="hidden" name="photo_y1"/>
                                                <input type="hidden" name="photo_w"/>
                                                <input type="hidden" name="photo_h"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-15">
                                        <div class="col-lg-6">
                                            <h5 class="title-pad"> Dados Residenciais </h5>
                                            <p class="sub-pad"> Não esqueça de enviar os comprovantes.</p>
                                        </div>
                                        <div class="col">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-3">
                                            <label for="zip_code">CEP</label>
                                            <input name="zip_code" value="" type="text" class="input-pad dados-residenciais" id="zip_code" placeholder="digite seu CEP">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="street">Rua</label>
                                            <input name="street" value="" type="text" class="input-pad dados-residenciais" id="street" placeholder="Rua">
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <label for="number">Número</label>
                                            <input name="number" value="" type="text" data-mask="0#" class="input-pad dados-residenciais" id="number" placeholder="Número">
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="neighborhood">Bairro</label>
                                            <input name="neighborhood" value="" type="text" class="input-pad dados-residenciais" id="neighborhood" placeholder="Bairro">
                                        </div>
                                        <div class="form-group col">
                                            <label for="complement">Complemento</label>
                                            <input name="complement" value="" type="text" class="input-pad dados-residenciais" id="complement" placeholder="Complemento">
                                        </div>
                                        <div class="form-group col">
                                            <label for="city">Cidade</label>
                                            <input name="city" value="" type="text" class="input-pad dados-residenciais" id="city" placeholder="Cidade">
                                        </div>
                                        <div class="form-group col div-state" style='display:none;'>
                                            <label for="state">Estado</label>
                                            <input name="state" value="" type="text" class="input-pad dados-residenciais" id="state" placeholder="Estado">
                                        </div>
                                        <div class="form-group col">
                                            <label for="country">País</label>
                                            <select id="country" name='country' class="form-control select-pad">
                                                <option value="brazil">Brasil</option>
                                                <option value="usa">Estados Unidos</option>
                                                <option value="chile">Chile</option>
                                                <option value="germany">Alemanha</option>
                                                <option value="spain">Espanha</option>
                                                <option value="france">França</option>
                                                <option value="italy">Itália</option>
                                                <option value="portugal">Portugal</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-12 text-right" style="margin-top: 30px">
                                            <a href="#" data-toggle='modal' data-target='#modal_change_password' class="mr-10">
                                                <i class="icon fa-lock" aria-hidden="true"></i> Alterar senha
                                            </a>
                                            <button id="update_profile" type="submit" class="btn btn-success">Atualizar Dados</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="tab_documentos" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h5 class="title-pad"> Documentos </h5>
                                        <p class="sub-pad"> Para movimentar sua conta externamente, precisamos de algumas comprovações. </p>
                                        <div class="alert alert-info alert-dismissible fade show text-center" id='text-alert-documents-cpf' role="alert" style='display:none;'>
                                            <strong>Atenção!</strong> Os documentos somente serão analisados após todos serem enviados.
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col">
                                    </div>
                                </div>
                                <div class="row mt-15" id='row_dropzone_documents' style='display:none;'>
                                    <div class="col-lg-12">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Documento</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="custom-t-body">
                                                <tr>
                                                    <td>
                                                        Documento com foto
                                                    </td>
                                                    <td id="td_personal_status"></td>
                                                    <td>
                                                        <i id='personal-document-id' title='Enviar documento' class='icon wb-upload gradient details-document' data-document='personal_document' aria-hidden="true" style="cursor:pointer; font-size: 20px"></i>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Comprovante de residência
                                                    </td>
                                                    <td id="td_address_status"></td>
                                                    <td>
                                                        <i id='address-document-id' title='Enviar Documento' class='icon wb-upload gradient details-document' data-document='address_document' aria-hidden="true" style="cursor:pointer; font-size: 20px"></i>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-md-12'>
                                        <div id='div_address_pending' class='alert alert-info text-center my-20' style='display:none;'>
                                            <p>Antes de enviar os documentos é necessário completar todos os seus dados residenciais na aba MEUS DADOS.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-md-12'>
                                        <div id='div_documents_refused'></div>
                                    </div>
                                </div>
                            </div>
                            <div class='tab-pane fade' id='tab_taxs' role='tabpanel'>
                                <div class='row' style='padding:0 30px 0 30px'>
                                    {{--CARTAO DE CRÉDITO--}}
                                    <div class='col-lg-12'>
                                        <h6 class='title-pad'>Cartão de crédito:</h6>
                                    </div>
                                    <div class='col'></div>
                                    <div class='row mt-15 col-xl-12'>
                                        <div class='form-group col-xl-5'>
                                            <label for='credit-card-tax'>Por venda (porcentagem):</label>
                                            <input id='credit-card-tax' disabled='disabled' class="form-control">
                                        </div>
                                        <div class='form-group col-xl-5'>
                                            <div class='form-group'>
                                                <label for='credit-card-release'>Dias para liberação:</label>
                                                <select id="credit-card-release" class="form-control" disabled>
                                                    <option value="plan-30">30 dias (taxa de 5.9%)</option>
                                                    <option value="plan-15">15 dias (taxa de 6.5%)</option>
                                                    <option value="plan-tracking-code" disabled>Ao informar o código de rastreio (em breve)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    {{--CARTAO DE CRÉDITO--}}
                                    {{--CARTAO DE DEBITO--}}
                                    <div class='col-lg-12'>
                                        <h6 class='title-pad'>Cartão de débito:</h6>
                                    </div>
                                    <div class='col'></div>
                                    <div class='row mt-15 col-xl-12'>
                                        <div class='form-group col-xl-5'>
                                            <label for='debit-card-tax'>Por venda (porcentagem):</label>
                                            <input id='debit-card-tax' disabled='disabled' class="form-control">
                                        </div>
                                        <div class='form-group col-xl-5'>
                                            <div class='form-group'>
                                                <label for='debit-card-release'>Dias para liberação:</label>
                                                <input id='debit-card-release' disabled='disabled' class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    {{--CARTAO DE DEBITO--}}
                                    {{--BOLETO--}}
                                    <div class='col-lg-12'>
                                        <h6 class='title-pad'>Boleto:</h6>
                                    </div>
                                    <div class='col'></div>
                                    <div class='row mt-15 col-xl-12'>
                                        <div class='form-group col-xl-5'>
                                            <label for='boleto-tax'>Por venda (porcentagem):</label>
                                            <input id='boleto-tax' disabled='disabled' class="form-control">
                                        </div>
                                        <div class='form-group col-xl-5'>
                                            <div class='form-group'>
                                                <label for='boleto-release'>Dias para liberação:</label>
                                                <input id='boleto-release' disabled='disabled' class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <p class='info' style='font-size: 10px; margin-top: -10px'>
                                                <i class='icon wb-info-circle' aria-hidden='true'></i> Taxa de parcelamento no cartão de crédito de R$
                                                <label id="installment-tax" style="color: gray"></label>
                                                % ao mês.
                                            </p>
                                            <p class='info' style='font-size: 10px; margin-top: -13px'>
                                                <i class='icon wb-info-circle' aria-hidden='true'></i> Taxa fixa de R$
                                                <label style="color: gray" id="transaction-tax"></label>
                                                por transação.
                                            </p>
                                            <p class='info' style='font-size: 10px; margin-top: -13px'>
                                                <i class='icon wb-info-circle' aria-hidden='true'></i> Em boletos com o valor menor de R$ 40,00 a taxa cobrada será de R$ 3,00.
                                            </p>
                                        </div>
                                        <div class="col-lg-12 text-right" style="margin-top: 30px">
                                            <button id="update_taxes" type="button" class="btn btn-success mr-100">Atualizar taxas</button>
                                        </div>
                                    </div>
                                    {{--BOLETO--}}
                                </div>
                            </div>
                            <div class='tab-pane fade' id='tab_notifications' role='tabpanel'>
                                <div class='row' style='padding:0 30px 0 30px'>
                                    <div class='col-12'>
                                        <h6 class='title-pad'>Notificações a receber</h6>
                                        <p class="sub-pad"> Defina quais notificações deseja receber </p>
                                    </div>
                                    <div class='row mt-15 col-12'>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="billet_generated" class="mb-10">Boleto gerado</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="billet_generated_switch" name="billet_generated" class="check notification_switch" value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="boleto_compensated" class="mb-10">Boleto compensado</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="boleto_compensated_switch" name="boleto_compensated" class="check notification_switch" value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="credit_card_in_proccess" class="mb-10">Em Processo (Cartão)</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="credit_card_in_proccess_switch" name="credit_card_in_proccess" class="check notification_switch" value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="sale_approved" class="mb-10">Venda aprovada</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="sale_approved_switch" name="sale_approved" class="check notification_switch" value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="notazz" class="mb-10">Notificação de Nota Fiscal</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="notazz_switch" name="notazz" class="check notification_switch" value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="released_balance" class="mb-10">Saldo liberado</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="released_balance_switch" name="released_balance" class="check notification_switch" value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="domain_approved" class="mb-10">Domínio Aprovado</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="domain_approved_switch" name="domain_approved" class="check notification_switch" value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="shopify" class="mb-10">Shopify</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="shopify_switch" name="shopify" class="check notification_switch" value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_change_password" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="width: 100%; text-align:center">Alterar senha</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 50px">
                            <label for="new_password">Nova senha (mínimo 6 caracteres)</label>
                            <input id="new_password" type="password" class="form-control input-pad" placeholder="Nova senha">
                            <label for="new_password_confirm" style="margin-top: 20px">Nova senha (confirmação)</label>
                            <input id="new_password_confirm" type="password" class="form-control input-pad" placeholder="Nova senha (confirmação)">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            <button id="password_update" type="button" class="btn btn-success" data-dismiss="modal" disabled>Alterar</button>
                        </div>
                    </div>
                </div>
            </div>
            {{--Modal Verificação Celular--}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_verify_cellphone" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="width: 100%; text-align:center">Verificar celular</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 10px">
                            <span>Um código de verificação foi enviado para o seu celular, digite o código recebido no campo abaixo</span>
                            <br>
                            <form method="POST" enctype="multipart/form-data" id='match_cellphone_verifycode_form'>
                                @csrf
                                <label for="cellphone_verify_code" style="margin-top: 20px">Código de verificação</label>
                                <input id="cellphone_verify_code" type="number" min='0' max='9999999' minlength='6' maxlength='7' class="form-control input-pad" placeholder="Insira o código aqui">
                                <button type='submit' class='btn btn-success mt-1'>
                                    <i class='fas fa-check'></i> Verificar
                                </button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            {{--Modal Verificação Email--}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_verify_email" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="width: 100%; text-align:center">Verificar email</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 10px">
                            <span>Um código de verificação foi enviado para o seu email, digite o código recebido no campo abaixo</span>
                            <br>
                            <form method="POST" enctype="multipart/form-data" id='match_email_verifycode_form'>
                                @csrf
                                <label for="email_verify_code" style="margin-top: 20px">Código de verificação</label>
                                <input id="email_verify_code" type="number" min='0' max='9999999' minlength='6' maxlength='7' class="form-control input-pad" placeholder="Insira o código aqui">
                                <button type='submit' class='btn btn-success mt-1'>
                                    <i class='fas fa-check'></i> Verificar
                                </button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal detalhes --}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-details-document" aria-hidden="true"
                 aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="fechar_modal_documents">
                                <span aria-hidden="true">×</span>
                            </button>
                            <div style="width: 100%; text-align:center">
                                <h4 id='modal-title-documents' class="modal-title"></h4>
                                <div id='modal-title-documents-info'>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body" style="margin-top: 10px">
                            <div class='row'>
                                <div class='col-lg-12' id='table-documents' style='min-height:100px;max-height:150px; overflow-x:hidden; overflow-y:scroll;margin-bottom: 20px;'>
                                    <table class="table table-striped table-hover table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th class='text-center' scope="col">Data Envio</th>
                                                <th class='text-center' scope="col">Status</th>
                                                <th class='text-center' scope="col"></th>
                                                <th class='text-center' scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody id='profile-documents-modal' class="custom-t-body">
                                        </tbody>
                                    </table>
                                </div>
                                <div class='col-lg-12' id='document-refused-motived' style='display:none;'>
                                </div>
                                <div class="col-lg-12">
                                    <div id="dropzone">
                                        <form method="POST" enctype="multipart/form-data" class="dropzone" id='dropzoneDocuments'>
                                            @csrf
                                            <div class="dz-message needsclick text-dropzone dropzone-previews" id='dropzone-text-document'>
                                                Arraste ou clique para fazer upload.<br/>
                                            </div>
                                            <input id="document_type" name="document_type" value="" type="hidden" class="input-pad">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('/modules/global/js/dropzone.js')}}"></script>
        <script src="{{asset('/modules/profile/js/profile.js?v=1')}}"></script>
    @endpush

@endsection


