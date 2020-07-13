<form id='form_update_collaborator' method="post" action="#" style="display:none">
    <input type='hidden' id='collaborator_id' value=''/>
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row mt-20">
            <div class="col-md-12">
                <div class='form-group'>
                    <label for="role_edit">Função</label>
                    <select id='role_edit' name='role' class='form-control'>
                        <option value='admin' class='opt-admin'>Administrativo</option>
                        <option value='attendance' class='opt-attendance'>Atendimento</option>
{{--                        <option value='attendance' class='opt-attendance' data-permission=''>Atendimento (sem permissão de estorno)</option>--}}
                    </select>
                </div>
            </div>
            <div class="col-md-12 div-permission-edit mb-20" style='display:none;'>
                <div class="switch-holder">
                    {{--                    <label class='mb-10'>Permissões:</label>--}}
                    {{--                    <br>--}}
                    <label for='refund_permission_edit' class='mb-10'>Habilitar estorno:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='' name="refund_permission" id="refund_permission_edit">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class='form-group'>
                    <label for="name_edit">Nome</label>
                    <input id='name_edit' name='name' class='form-control' type='text' placeholder="Nome">
                </div>
            </div>
            <div class="col-md-12">
                <div class='form-group'>
                    <label for="email_edit">Email</label>
                    <input id='email_edit' name='email' class='form-control' type='text' placeholder="Email">
                </div>
            </div>
            <div class="col-md-12">
                <div class='form-group'>
                    <label for="cellphone_edit">Telefone</label>
                    <input id='cellphone_edit' name='cellphone' class='form-control' type='text' placeholder="Telefone">
                </div>
            </div>
            <div class="col-md-12">
                <div class='form-group'>
                    <label for="document_edit">Documento</label>
                    <input id='document_edit' name='document' class='form-control' type='text' placeholder="Documento">
                </div>
            </div>
            <div class="col-md-12">
                <div class='form-group'>
                    <label for="password_edit">Senha</label>
                    <div class="switch-holder mb-10">
                        <small for="token" class='mb-10'>Alterar senha:</small>
                        <label class="switch mx-2">
                            <input type="checkbox" value='1' name="boleto_paid" id="boleto_paid" class='check'>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <input id="password_edit" type="password" name="password" class='form-control' placeholder="Senha" disabled>
                </div>
            </div>
        </div>
    </div>
{{--    <input type='hidden' id='permission_edit' name='permission'>--}}
</form>
