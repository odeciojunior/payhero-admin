@extends("layouts.master")

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
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
                        <li class="nav-item" role="presentation" id="nav_documents">
                            <a class="nav-link" data-toggle="tab" href="#tab_documentos" aria-controls="tab_documentos" role="tab">
                                Documentos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation" id="nav_taxs">
                            <a class="nav-link" data-toggle="tab" href="#tab_taxs" aria-controls="tab_taxs" role="tab">
                                Tarifas e Prazos
                            </a>
                        </li>
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
                                                    <input name="email" value="" type="text" class="input-pad" id="email" placeholder="Email">
                                                </div>
                                                <div class="form-group col-xl-6">
                                                    <label for="cpf">Documento</label>
                                                    <input name="document" value="" type="text" class="input-pad" id="document" placeholder="Documento">
                                                </div>
                                                <div class="form-group col-xl-6">
                                                    <label for="celular">Celular</label>
                                                    <input name="cellphone" value="" type="text" data-mask="(00) 00000-0000" class="input-pad" id="cellphone" placeholder="Celular">
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
                                            <input name="zip_code" value="" type="text" data-mask="00000-000" class="input-pad" id="zip_code" placeholder="digite seu CEP">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="street">Rua</label>
                                            <input name="street" value="" type="text" class="input-pad" id="street" placeholder="Rua">
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <label for="number">Número</label>
                                            <input name="number" value="" type="text" data-mask="0#" class="input-pad" id="number" placeholder="Número">
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="neighborhood">Bairro</label>
                                            <input name="neighborhood" value="" type="text" class="input-pad" id="neighborhood" placeholder="Bairro">
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="complement">Complemento</label>
                                            <input name="complement" value="" type="text" class="input-pad" id="complement" placeholder="Complemento">
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="city">Cidade</label>
                                            <input name="city" value="" type="text" class="input-pad" id="city" placeholder="Cidade">
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="state">Estado</label>
                                            <input name="state" value="" type="text" class="input-pad" id="state" placeholder="Estado">
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
                                    <div class="col-lg-6">
                                        <h5 class="title-pad"> Documentos </h5>
                                        <p class="sub-pad"> Para movimentar sua conta externamente, precisamos de algumas comprovações. </p>
                                    </div>
                                    <div class="col">
                                    </div>
                                </div>
                                <div class="row mt-15">
                                    <div class="col-lg-6">
                                        <div id="dropzone">
                                            <form method="POST" enctype="multipart/form-data" class="dropzone" id='dropzoneDocuments'>
                                                @csrf
                                                <div class="dz-message needsclick">
                                                    Arraste ou clique para fazer upload.<br/>
                                                </div>
                                                <input id="document_type" name="document_type" value="" type="hidden" class="input-pad">
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Documento</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="custom-t-body">
                                                <tr>
                                                    <td>Identidade</td>
                                                    <td id="td_personal_status"></td>
                                                </tr>
                                                <tr>
                                                    <td>Residência</td>
                                                    <td id="td_address_status"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-12  mt-10">
                                        <small class="text-muted" style="line-height: 1.5;"> Doc. de Identidade aceitos: RG ou CNH (oficial e com foto)
                                            <br> Comp. de Residência aceitos: conta de energia, água ou de serviços públicos.
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class='tab-pane fade' id='tab_taxs' role='tabpanel'>
                                <div class='row' style='padding:0 30px 0 30px'>
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
                                                <select id="credit-card-release" class="form-control">
                                                    <option value="plan-30">30 dias (taxa de 5.9%)</option>
                                                    <option value="plan-15">15 dias (taxa de 6.5%)</option>
                                                    <option value="plan-tracking-code" disabled>Ao informar o código de rastreio (em breve)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
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
                                                <i class='icon wb-info-circle' aria-hidden='true'></i> Taxa de parcelamento no cartão de crédito de R$ <label id="installment-tax" style="color: gray"></label>%.
                                            </p>
                                            <p class='info' style='font-size: 10px; margin-top: -13px'>
                                                <i class='icon wb-info-circle' aria-hidden='true'></i> Taxa fixa de R$ <label style="color: gray" id="transaction-tax"></label> por transação.
                                            </p>
                                            <p class='info' style='font-size: 10px; margin-top: -13px'>
                                                <i class='icon wb-info-circle' aria-hidden='true'></i> Em boletos com o valor menor de R$ 40,00 a taxa cobrada será de R$ 3,00.
                                            </p>
                                        </div>
                                        <div class="col-lg-12 text-right" style="margin-top: 30px">
                                            <button id="update_taxes" type="button" class="btn btn-success mr-100">Atualizar taxas</button>
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
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('/modules/global/js/dropzone.js')}}"></script>
        <script src="{{asset('/modules/profile/js/profile.js?v=1')}}"></script>
    @endpush

@endsection


