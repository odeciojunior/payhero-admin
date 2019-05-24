
<div style="text-align: center">
    <h4> Cadastrar layout personalizado </h4>
</div>

<div class="row" style="margin-top: 30px">
    <div class="col-6">
        <form id="cadastrar_layout" method="post" enctype='multipart/form-data' style="padding: 10px">
            @csrf
            <div class="panel" data-plugin="matchHeight">
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="descricao">Descrição</label>
                        <input name="description" type="text" class="form-control" id="descricao" placeholder="Descrição" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="descricao">Status</label>
                        <select name="status" class="form-control" id="status">
                            <option value="Ativo">Ativo</option>
                            <option value="Desativado">Desativado</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="formato_logo">Formato do logo</label>
                        <select name="format_logo" class="form-control" id="formato_logo_cadastrar">
                            <option value="quadrado">Quadrado</option>
                            <option value="retangulo">Retangular</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        <label for="selecionar_foto_checkout_cadastrar">Logo</label><br>
                        <input type="button" id="selecionar_foto_checkout_cadastrar" class="btn btn-default" value="Selecionar logo do checkout">
                        <input name="foto_checkout" type="file" class="form-control" id="foto_checkout" accept="image/*" style="display:none">
                        <div  style="margin: 20px 0 0 30px;">
                            <img id="previewimage_checkout_cadastrar" alt="Selecione a logo do checkout" style="max-height: 250px; max-width: 350px;"/>
                        </div>
                        <input type="hidden" name="foto_checkout_cadastrar_x1"/>
                        <input type="hidden" name="foto_checkout_cadastrar_y1"/>
                        <input type="hidden" name="foto_checkout_cadastrar_w"/>
                        <input type="hidden" name="foto_checkout_cadastrar_h"/>
                    </div>
                </div>
    
                <div class="row" style="margin-top: 50px">
                    <div class="form-group col-xl-12 text-center">
                        <button type="button" id="atualizar_preview_cadastro" class="btn btn-primary">
                            Atualizar pré visualização do checkout
                        </button>
                    </div>
                </div>
                    
            </div>
        </form>
    </div>

    <div class="col-6">
        <div id="view_checkout" class="card card-shadow">
            <iframe id="view_checkout" name="iframe-preview"src="#" style="height: 650px"></iframe>
        </div>
    </div>
</div>

<form id="form-preview" target="iframe-preview" action="/layouts/preview" method="POST" enctype='multipart/form-data' style="display: none">
    <input id="preview_logo" type="hidden" name="tipo" value="cadastrar"/>
    <input type="hidden" name="logo_formato" id="preview_logo_formato" value="quadrado"/>
    <input type="hidden" name="preview_logo_x1"/>
    <input type="hidden" name="preview_logo_y1"/>
    <input type="hidden" name="preview_logo_w"/>
    <input type="hidden" name="preview_logo_h"/>
{{--  <input id="preview_botoes" type="hidden" name="botoes"/>  --}}
    {{ csrf_field() }}
    <input type="submit">
</form>

