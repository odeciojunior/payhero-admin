<div class="page-header">
    <h1 class="page-title">Editar layout</h1>
</div>

<div class="row">
    <div class="col-xxl-6 col-lg-6">
        <div class="card card-shadow">
            <form method="post" id="editar_layout" enctype='multipart/form-data'>
                @csrf
                <input type="hidden" value="{!! $layout->id !!}" name="id">
                <div class="page-content container-fluid">
                    <div class="panel" data-plugin="matchHeight">
                        <div style="width:100%">
                            <div class="row">
                                <div class="form-group col-xl-12">
                                    <label for="descricao">Descrição</label>
                                    <input value="{!! $layout->descricao != '' ? $layout->descricao : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-12">
                                    <label for="formato_logo">Formato do logo</label>
                                    <select name="formato_logo" class="form-control" id="formato_logo_cadastrar">
                                        <option value="quadrado" {!! $layout->formato_logo == 'quadrado' ? 'selected' : '' !!}>Quadrado</option>
                                        <option value="retangulo" {!! $layout->formato_logo == 'retangulo' ? 'selected' : '' !!}>Retangular</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="selecionar_foto_checkout_editar">Logo</label><br>
                                    <input type="button" id="selecionar_foto_checkout_editar" class="btn btn-default" value="Alterar o logo do checkout">
                                    <input name="foto_checkout" type="file" class="form-control" id="foto_checkout" accept="image/*" style="display:none">
                                    <div  style="margin: 20px 0 0 30px;">
                                        <img id="previewimage_checkout_editar" alt="logo não alterado" style="max-height: 250px; max-width: 350px;"/>
                                    </div>
                                    <input type="hidden" name="foto_checkout_editar_x1"/>
                                    <input type="hidden" name="foto_checkout_editar_y1"/>
                                    <input type="hidden" name="foto_checkout_editar_w"/>
                                    <input type="hidden" name="foto_checkout_editar_h"/>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 50px">
                                <div class="form-group col-xl-12 text-center">
                                    <button type="button" id="atualizar_preview_editar" class="btn btn-primary">
                                        Atualizar pré visualização do checkout
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form id="form_preview_editar" target="iframe-preview-editar" action="/layouts/preview" method="POST" enctype='multipart/form-data' style="display: none">
        <input id="preview_logo" type="hidden" name="tipo" value="editar"/>
        <input type="hidden" name="layout" value="{{ $layout->id }}"/>
        <input type="hidden" name="logo_formato" id="preview_logo_formato" value="{!! $layout['logo_formato'] !!}"/>
        <input type="hidden" name="preview_logo_x1"/>
        <input type="hidden" name="preview_logo_y1"/>
        <input type="hidden" name="preview_logo_w"/>
        <input type="hidden" name="preview_logo_h"/>
            {{ csrf_field() }}
        <input type="submit">
    </form>

    <div class="col-xxl-6 col-lg-6">
        <div id="view_checkout" class="card card-shadow">
            <iframe id="view_checkout" name="iframe-preview-editar" src="#" style="height: 650px"></iframe>
        </div>
    </div>
</div>
