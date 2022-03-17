@extends("layouts.master")

@push('css')
    {{-- <link rel="stylesheet" type="text/css" href="{{ mix('modules/profile/css/basic.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ mix('modules/profile/css/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ mix('modules/global/css/switch.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/> --}}
    <link rel="stylesheet" href="{{ mix('build/layouts/profile/index.min.css') }}">

@endpush

@section('content')

    <style>
        select[readonly] {
            background: #eee;
            pointer-events: none;
            touch-action: none;
        }
    </style>
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
                            <a class="nav-link active" id='user-nav' data-toggle="tab" href="#tab_user" aria-controls="tab_user"
                               role="tab">Meus dados
                            </a>
                        </li>
                        @if(!auth()->user()->hasRole('attendance'))
                            <li class="nav-item" role="presentation" id="nav_documents">
                                <a class="nav-link" id='documents-nav' data-toggle="tab" href="#tab_documentos"
                                   aria-controls="tab_documentos" role="tab">
                                    Documentos
                                </a>
                            </li>
                        @endif
                        @if(!auth()->user()->hasRole('attendance'))
                            <li class="nav-item" role="presentation" id="nav_notifications">
                                <a class="nav-link" data-toggle="tab" href="#tab_notifications"
                                   aria-controls="tab_notifications" role="tab">
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
                                    {{--                                    new div--}}
                                    <div class='row'>
                                        <div class='col-lg-8'>
                                            <div class='row'>
                                                <div class="form-group col-lg-8">
                                                    <label for="name">Nome Completo</label>
                                                    <input name="name" value="" type="text" class="input-pad" id="name"
                                                           placeholder="Nome">
                                                </div>
                                                <div class="form-group col-lg-4">
                                                   {{-- <label for="date_birth">Data de nascimento</label>
                                                    <input name="date_birth" value="" type="date"
                                                           class="form-control input-pad" id="date_birth"
                                                           onkeydown="return false">--}}
                                                    <label for="document" class='label-document'></label>
                                                    <input name="document" value="" type="text" class="input-pad"
                                                           id="document">
                                                </div>
                                                {{--<div class="form-group col-lg-4">
                                                    --}}{{--carrega label no js--}}{{--

                                                </div>--}}
                                                <div class="form-group col-lg-6">
                                                    <label for="email">Email</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="input_group_email"
                                                                  id="addon-email">
                                                            </span>
                                                        </div>
                                                        <input name="email" value="" type="text"
                                                               class="input-pad form-control" id="email"
                                                               placeholder="Email" aria-describedby="addon-email">
                                                    </div>
                                                    <small id="message_not_verified_email"
                                                           style='color:red; display:none;'>Email não verificado, clique
                                                        <a href='#' id='btn_verify_email'
                                                           onclick='event.preventDefault();' data-toggle='modal'
                                                           data-target='#modal_verify_email'>aqui
                                                        </a>
                                                        para verificá-lo!
                                                    </small>
                                                </div>
                                                <div class="form-group col-xl-6">
                                                    <label for="celular">Celular (WhatsApp)</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="input_group_cellphone"
                                                                  id="addon-cellphone">
                                                            </span>
                                                        </div>
                                                        <input name="cellphone" value="" type="text"
                                                               class="input-pad form-control" id="cellphone"
                                                               placeholder="Celular" aria-describedby="addon-cellphone">
                                                    </div>
                                                    <small id="message_not_verified_cellphone"
                                                           style='color:red; display:none;'>Celular não verificado,
                                                        clique
                                                        <a href='#' id='btn_verify_cellphone'
                                                           onclick='event.preventDefault();' data-toggle='modal'
                                                           data-target='#modal_verify_cellphone'>aqui
                                                        </a>
                                                        para verificá-lo!
                                                    </small>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group col-6">
                                                <label for="select_profile_photo">Foto de perfil</label>
                                                <br>
                                                <input name="profile_photo" type="file" class="form-control input-pad"
                                                       id="profile_photo" style="display:none">
                                                <div style="margin: 20px 0 0 30px;">
                                                    <img src="" id="previewimage" alt="Nenhuma foto cadastrada"
                                                         accept="image/*"
                                                         style="max-height: 250px; max-width: 350px; cursor:pointer;"/>
                                                </div>
                                                <input type="hidden" name="photo_x1"/>
                                                <input type="hidden" name="photo_y1"/>
                                                <input type="hidden" name="photo_w"/>
                                                <input type="hidden" name="photo_h"/>
                                            </div>
                                        </div>
                                    </div>
                                    {{--                                    end new div--}}
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
                                            <input name="zip_code" value="" type="text"
                                                   class="input-pad dados-residenciais" id="zip_code"
                                                   placeholder="digite seu CEP">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="street">Rua</label>
                                            <input name="street" value="" type="text"
                                                   class="input-pad dados-residenciais" id="street" placeholder="Rua">
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <label for="number">Número</label>
                                            <input name="number" value="" type="text" data-mask="0#"
                                                   class="input-pad dados-residenciais" id="number"
                                                   placeholder="Número">
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="neighborhood">Bairro</label>
                                            <input name="neighborhood" value="" type="text"
                                                   class="input-pad dados-residenciais" id="neighborhood"
                                                   placeholder="Bairro">
                                        </div>
                                        <div class="form-group col">
                                            <label for="complement">Complemento</label>
                                            <input name="complement" value="" type="text"
                                                   class="input-pad dados-residenciais" id="complement"
                                                   placeholder="Complemento">
                                        </div>
                                        <div class="form-group col">
                                            <label for="city">Cidade</label>
                                            <input name="city" value="" type="text" class="input-pad dados-residenciais"
                                                   id="city" placeholder="Cidade">
                                        </div>
                                        <div class="form-group col div-state" style='display:none;'>
                                            <label for="state">Estado</label>
                                            <input name="state" value="" type="text"
                                                   class="input-pad dados-residenciais" id="state" placeholder="Estado">
                                        </div>
                                        <div class="form-group col">
                                            <label for="country">País</label>
                                            <select id="country" name='country' class="form-control select-pad" tabindex="-1" aria-disabled="true">
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
                                            <a href="https://sirius.cloudfox.net/termos" target='_blank' class="mr-10 float-left">
                                                Termos de uso
                                            </a>
                                            <a href="#" data-toggle='modal' data-target='#modal_change_password'
                                               class="mr-10">
                                                <i class="icon fa-lock" aria-hidden="true"></i> Alterar senha
                                            </a>
                                            <button id="update_profile" type="submit" class="btn btn-success">Atualizar
                                                Dados
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="tab_documentos" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h5 class="title-pad"> Documentos </h5>
                                        <p class="sub-pad"> Para manter a nossa comunidade segura, precisamos comprovar alguns dados através dos seus documentos pessoais. </p>
                                        <div class="alert alert-info alert-dismissible fade show text-center"
                                             id='text-alert-documents-cpf' role="alert" style='display:none;'>
                                            <strong>Atenção!</strong> Os documentos somente serão analisados após todos
                                            serem enviados.
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
                                                    <i id='personal-document-id' title='Enviar documento'
                                                       class='icon wb-upload gradient details-document'
                                                       data-document='personal_document' aria-hidden="true"
                                                       style="cursor:pointer; font-size: 20px"></i>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Comprovante de residência
                                                </td>
                                                <td id="td_address_status"></td>
                                                <td>
                                                    <i id='address-document-id' title='Enviar Documento'
                                                       class='icon wb-upload gradient details-document'
                                                       data-document='address_document' aria-hidden="true"
                                                       style="cursor:pointer; font-size: 20px"></i>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-md-12'>
                                        <div id='div_address_pending' class='alert alert-info text-center my-20'
                                             style='display:none;'>
                                            <p>Antes de enviar os documentos é necessário completar todos os seus dados
                                                residenciais na aba MEUS DADOS.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-md-12'>
                                        <div id='div_documents_refused'></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tab Notifications -->
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
                                                    <input type="checkbox" id="billet_generated_switch"
                                                           name="billet_generated" class="check notification_switch"
                                                           value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="boleto_compensated" class="mb-10">Boleto compensado</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="boleto_compensated_switch"
                                                           name="boleto_compensated" class="check notification_switch"
                                                           value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="sale_approved" class="mb-10">Venda aprovada</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="sale_approved_switch"
                                                           name="sale_approved" class="check notification_switch"
                                                           value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="domain_approved" class="mb-10">Domínio Aprovado</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="domain_approved_switch"
                                                           name="domain_approved" class="check notification_switch"
                                                           value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="shopify" class="mb-10">Shopify</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="shopify_switch" name="shopify"
                                                           class="check notification_switch" value='1'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-4">
                                            <div class="switch-holder">
                                                <label for="affiliation_switch" class="mb-10">Afiliação</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" id="affiliation_switch" name="affiliation"
                                                           class="check notification_switch" value='1'>
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
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_change_password"
                 aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    id="fechar_modal_excluir">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="width: 100%; text-align:center">Alterar senha</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 50px">
                            <label for="new_password">Nova senha (mínimo 6 caracteres)</label>
                            <input id="new_password" type="password" class="form-control input-pad"
                                   placeholder="Nova senha">
                            <label for="new_password_confirm" style="margin-top: 20px">Nova senha (confirmação)</label>
                            <input id="new_password_confirm" type="password" class="form-control input-pad"
                                   placeholder="Nova senha (confirmação)">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                            <button id="password_update" type="button" class="btn btn-success" data-dismiss="modal"
                                    disabled>Alterar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{--Modal Verificação Celular--}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_verify_cellphone"
                 aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    id="fechar_modal_excluir">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="width: 100%; text-align:center">Verificar celular</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 10px">
                            <span>Um código de verificação foi enviado para o seu celular, digite o código recebido no campo abaixo</span>
                            <br>
                            <form method="POST" enctype="multipart/form-data" id='match_cellphone_verifycode_form'>
                                @csrf
                                <label for="cellphone_verify_code" style="margin-top: 20px">Código de
                                    verificação</label>
                                <input id="cellphone_verify_code" type="number" min='0' max='9999999' minlength='6'
                                       maxlength='7' class="form-control input-pad" placeholder="Insira o código aqui">
                                <button type='submit' class='btn btn-success mt-1'>
                                    <i class='fas fa-check'></i> Verificar
                                </button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            {{--Modal Verificação Email--}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_verify_email" aria-hidden="true"
                 aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    id="fechar_modal_excluir">
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
                                <input id="email_verify_code" type="number" min='0' max='9999999' minlength='6'
                                       maxlength='7' class="form-control input-pad" placeholder="Insira o código aqui">
                                <button type='submit' class='btn btn-success mt-1'>
                                    <i class='fas fa-check'></i> Verificar
                                </button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal detalhes --}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-details-document"
                 aria-hidden="true"
                 aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    id="fechar_modal_documents">
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
                                <div class='col-lg-12' id='table-documents'
                                     style='min-height:100px;max-height:150px; overflow-x:hidden; overflow-y:scroll;margin-bottom: 20px;'>
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
                                        <form method="POST" enctype="multipart/form-data" class="dropzone"
                                              id='dropzoneDocuments'>
                                            @csrf
                                            <div class="dz-message needsclick text-dropzone dropzone-previews"
                                                 id='dropzone-text-document'>
                                                Arraste ou clique para fazer upload.<br/>
                                            </div>
                                            <input id="document_type" name="document_type" value="" type="hidden"
                                                   class="input-pad">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .select2-selection--single {
            /*border: 1px solid #dddddd !important;*/
            border-radius: .215rem !important;
            height: 43px !important;
        }

        .select2-selection__rendered {
            color: #707070 !important;
            font-size: 16px !important;
            font-family: 'Muli', sans-serif;
            line-height: 43px !important;
            padding-left: 14px !important;
            padding-right: 38px !important;
        }

        .select2-selection__arrow {
            height: 43px !important;
            right: 10px !important;
        }

        .select2-selection__arrow b {
            border-color: #8f9ca2 transparent transparent transparent !important;
        }

        .select2-container--open .select2-selection__arrow b {
            border-color: transparent transparent #8f9ca2 transparent !important;
        }
    </style>
    @push('scripts')
        {{-- <script src="{{ mix('modules/global/js/dropzone.min.js') }}"></script>
        <script src="{{ mix('modules/profile/js/profile.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script> --}}
        <script src="{{ mix('build/layouts/profile/index.min.js') }}"></script>

    @endpush

@endsection


