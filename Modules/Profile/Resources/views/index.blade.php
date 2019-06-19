@extends("layouts.master")
@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.scss')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
@endpush
@section('content')
    <!-- Page -->
    <div class="page">
        <div class="page-header">
            <h1 class="page-title">Configuração do perfil</h1>
        </div>
        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                <div class="col-xl-12">
                    <div class="example-wrap">
                        <div class="nav-tabs-horizontal" data-plugin="tabs">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-toggle="tab" href="#tab_usuario"
                                       aria-controls="tab_usuario" role="tab">Usuário
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-toggle="tab" href="#tab_documentos"
                                       aria-controls="tab_documentos" role="tab">Documentos
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content pt-20">
                                <div class="tab-pane active" id="tab_usuario" role="tabpanel">
                                    <form method="POST" action="{!! route('profile.update', ['id' => 'self']) !!}" enctype="multipart/form-data" id='profile_update_form'>
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="panel-heading col-10">
                                                <h3 class="panel-title">Informações básicas</h3>
                                            </div>
                                            <div class="col-2">
                                                <button type="button" class="btn btn-success" data-toggle='modal' data-target='#modal_change_password'>
                                                    Aterar senha
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="name">Nome</label>
                                                <input name="name" value="{!! $user->name !!}" type="text" class="form-control" id="name">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="email">Email</label>
                                                <input name="email" value="{!! $user->email !!}" type="text" class="form-control" id="email">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="cpf">Documento</label>
                                                <input name="document" value="{!! $user->document !!}" type="text" class="form-control" id="document">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="celular">Celular</label>
                                                <input name="cellphone" value="{!! $user->cellphone !!}" type="text" class="form-control" id="cellphone">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="date_birth">Data de nascimento</label>
                                                <input name="date_birth" value="{!! $user->date_birth !!}" type="date" class="form-control" id="date_birth">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-12">
                                                <label for="select_profile_photo">Foto de perfil</label>
                                                <br>
                                                <input type="button" id="select_profile_photo" class="btn btn-default" value="Selecionar foto do perfil">
                                                <input name="profile_photo" type="file" class="form-control" id="profile_photo" style="display:none">
                                                <div style="margin: 20px 0 0 30px;">
                                                    <img src="{!!$user->photo!!}" id="previewimage" alt="Nenhuma foto cadastrada" accept="image/*" style="max-height: 250px; max-width: 350px;"/>
                                                </div>
                                                <input type="hidden" name="photo_x1"/>
                                                <input type="hidden" name="photo_y1"/>
                                                <input type="hidden" name="photo_w"/>
                                                <input type="hidden" name="photo_h"/>
                                            </div>
                                        </div>
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Endereço</h3>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="zip_code">CEP</label>
                                                <input name="zip_code" value="{!! $user->zip_code !!}" type="text" class="form-control" id="zip_code">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="country">País</label>
                                                <input name="country" value="{!! $user->country !!}" type="text" class="form-control" id="country">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="state">Estado</label>
                                                <input name="state" value="{!! $user->state !!}" type="text" class="form-control" id="state">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="city">Cidade</label>
                                                <input name="city" value="{!! $user->city !!}" type="text" class="form-control" id="city">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="neighborhood">Bairro</label>
                                                <input name="neighborhood" value="{!! $user->neighborhood !!}" type="text" class="form-control" id="neighborhood">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="street">Rua</label>
                                                <input name="street" value="{!! $user->street !!}" type="text" class="form-control" id="street">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="number">Número</label>
                                                <input name="number" value="{!! $user->number !!}" type="text" class="form-control" id="number">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="complement">Complemento</label>
                                                <input name="complement" value="{!! $user->complement !!}" type="text" class="form-control" id="complement">
                                            </div>
                                        </div>
                                        <div class="form-group" style="margin-top: 30px">
                                            <input id="update_profile" type="submit" class="form-control btn btn-success" value="Atualizar" style="width: 30%">
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab_documentos" role="tabpanel">
                                    Envie um documento de identidade e um comprovante de residência<br>

                                    <div id="dropzone">
                                        <form method="POST" action="{!! route('profile.uploaddocuments') !!}" enctype="multipart/form-data" class="dropzone" id='dropzoneDocuments'>
                                            @csrf
                                            <div class="dz-message needsclick">
                                                Arraste os arquivos aqui ou click para selecionar.<br/>
                                            </div>
                                            <input id="document_type" name="document_type" value="" type="hidden" class="form-control">
                                        </form>
                                        Documento de identidade aceitos : Documento oficial com foto.<br>
                                        Comprovante de residência: Conta de energia, água ou de serviços públicos<br>
                                    </div>
                                    <div class="row">
                                        <div class="panel-heading col-10">
                                            <h3 class="panel-title">Documentos Enviados</h3>
                                        </div>
                                        <table class="table table-hover table-striped table-bordered mt-2">
                                            <tbody>
                                                <tr class="text-center">
                                                    <td>
                                                        Documento de identidade
                                                    </td>
                                                    <td>
                                                        Aguardando envio.
                                                    </td>
                                                </tr>
                                                <tr class='text-center'>
                                                    <td>
                                                        Comprovante de residencia
                                                    </td>
                                                    <td>
                                                        Aguardando envio.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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
                                    <input id="new_password" type="password" class="form-control" placeholder="Nova senha">
                                    <label for="new_password_confirm" style="margin-top: 20px">Nova senha (confirmação)</label>
                                    <input id="new_password_confirm" type="password" class="form-control" placeholder="Nova senha (confirmação)">
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
        </div>
    </div>
    @push('scripts')
        <script src="{{asset('/modules/profile/js/dropzone.js')}}"></script>
        <script src="{{asset('/modules/profile/js/profile.js')}}"></script>
    @endpush
@endsection


