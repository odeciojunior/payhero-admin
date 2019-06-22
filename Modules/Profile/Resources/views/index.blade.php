@extends("layouts.master")
@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.scss')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
@endpush
@section('content')
    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Configuração do perfil</h1>
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
                                    <a class="nav-link" data-toggle="tab" href="#tab_documentos"
                                       aria-controls="tab_documentos" role="tab">Documentos
                                    </a>
                                </li>
                            </ul>

                            <div class="p-30 pt-20">

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab_user" role="tabpanel">
                                        <form method="POST" action="{!! route('profile.update', ['id' => 'self']) !!}" enctype="multipart/form-data" id='profile_update_form'>
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <h5 class="title-pad"> Dados Pessoais </h5>
                                                    <p class="sub-pad"> Precisamos saber um pouco sobre você  </p>
                                                </div>
                                                <div class="col">
                                                    
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="row">

                                                            <div class="form-group col-xl-6">
                                                                <label for="name">Nome Completo</label>
                                                                <input name="name" value="{!! $user->name !!}" type="text" class="form-control input-pad" id="name">
                                                            </div>

                                                            <div class="form-group col-xl-6">
                                                                <label for="email">Email</label>
                                                                <input name="email" value="{!! $user->email !!}" type="text" class="form-control input-pad" id="email">
                                                            </div>

                                                            <div class="form-group col-xl-6">
                                                                <label for="cpf">Documento</label>
                                                                <input name="document" value="{!! $user->document !!}" type="text" class="form-control input-pad" id="document">
                                                            </div>

                                                            <div class="form-group col-xl-6">
                                                                <label for="celular">Celular</label>
                                                                <input name="cellphone" value="{!! $user->cellphone !!}" type="text" class="form-control input-pad" id="cellphone">
                                                            </div>

                                                            <div class="form-group col-xl-6">
                                                                <label for="date_birth">Data de nascimento</label>
                                                                <input name="date_birth" value="{!! $user->date_birth !!}" type="date" class="form-control input-pad" id="date_birth">
                                                            </div>
                                                            
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4">
                                                    <div class="form-group col-6">
                                                        <label for="select_profile_photo">Foto de perfil</label>
                                                        <br>
                                                        <button id="select_profile_photo" class="btn btn-primary mt-15"> <i class="icon fa-cloud-upload" aria-hidden="true"></i> Upload </button>
                                                        <input name="profile_photo" type="file" class="form-control input-pad" id="profile_photo" style="display:none">
                                                        <div style="margin: 20px 0 0 30px;">
                                                            <img src="{!! $user->photo != '' ? $user->photo : asset('modules/global/assets/img/user-default.png') !!}" id="previewimage" alt="Nenhuma foto cadastrada" accept="image/*" style="max-height: 250px; max-width: 350px;"/>
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
                                                    <p class="sub-pad"> Não esqueça de enviar os comprovantes.  </p>
                                                </div>
                                                <div class="col">
                                                    
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-lg-3">
                                                    <label for="zip_code">CEP</label>
                                                    <input name="zip_code" value="{!! $user->zip_code !!}" type="text" class="form-control input-pad" id="zip_code">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-xl-6">
                                                    <label for="street">Rua</label>
                                                    <input name="street" value="{!! $user->street !!}" type="text" class="form-control input-pad" id="street">
                                                </div>
                                                <div class="form-group col-xl-2">
                                                    <label for="number">Número</label>
                                                    <input name="number" value="{!! $user->number !!}" type="text" class="form-control input-pad" id="number">
                                                </div>
                                                <div class="form-group col-xl-4">
                                                    <label for="neighborhood">Bairro</label>
                                                    <input name="neighborhood" value="{!! $user->neighborhood !!}" type="text" class="form-control input-pad" id="neighborhood">
                                                </div>

                                                <div class="form-group col-xl-4">
                                                    <label for="complement">Complemento</label>
                                                    <input name="complement" value="{!! $user->complement !!}" type="text" class="form-control input-pad" id="complement">
                                                </div>

                                                
                                                <div class="form-group col-xl-4">
                                                    <label for="city">Cidade</label>
                                                    <input name="city" value="{!! $user->city !!}" type="text" class="form-control input-pad" id="city">
                                                </div>

                                                <div class="form-group col-xl-4">
                                                    <label for="state">Estado</label>
                                                    <input name="state" value="{!! $user->state !!}" type="text" class="form-control input-pad" id="state">
                                                </div>
                                                
                                               
                                                <div class="col-lg-12 text-right" style="margin-top: 30px">
                                                    
                                                    <a href="#" data-toggle='modal' data-target='#modal_change_password' class="mr-10"> <i class="icon fa-lock" aria-hidden="true"></i> Alterar senha </a>

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
                                                        <form method="POST" action="{!! route('profile.uploaddocuments') !!}" enctype="multipart/form-data" class="dropzone" id='dropzoneDocuments'>
                                                            @csrf
                                                            <div class="dz-message needsclick">
                                                                Arraste ou clique para fazer upload.<br/>
                                                            </div>
                                                            <input id="document_type" name="document_type" value="" type="hidden" class="form-control input-pad">
                                                        </form>                                                        
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Documento</th>
                                                                <th scope="col">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="custom-t-body">
                                                            <tr>

                                                                <td>Identidade</td>
                                                                <td  id="td_personal_status"><span class="badge badge-pendente">{!! $user->personal_document_translate !!}</span>
                                                                </td>

                                                            </tr>

                                                            <tr>

                                                                <td>Residência</td>
                                                                <td id="td_address_status"><span class="badge badge-pendente"> {!! $user->address_document_translate !!}</span>
                                                                </td>

                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <div class="col-lg-12  mt-10">
                                                    <small class="text-muted" style="line-height: 1.5;"> Doc. de Identidade aceitos: RG ou CNH (oficial e com foto) <br> Comp. de Residência aceitos: conta de energia, água ou de serviços públicos. </small>
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
    </div>

    <style>
    .dropzone {
    display: flex !important;
    min-height: 150px;
    border: 1px solid #aaa;
    background: white;
    padding: 20px 20px;
    align-items: center;
    justify-content: center;
    margin: 0;
}
    </style>
    @push('scripts')
        <script src="{{asset('/modules/global/js/dropzone.js')}}"></script>
        <script src="{{asset('/modules/profile/js/profile.js')}}"></script>
    @endpush
@endsection


