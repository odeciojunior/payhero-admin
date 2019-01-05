<div class="page-header">
    <h1 class="page-title">Editar layout</h1>
</div>

<div class="row">
    <div class="col-xxl-6 col-lg-6">
        <div class="card card-shadow">
            <form method="post" action="/layouts/editarlayout" enctype='multipart/form-data'>
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
                                    <label for="logo">Logo</label>
                                    <input name="logo" id="logo" type="file" class="form-control" required>
                                </div>
                            </div>
                            {{--  <div class="row">
                                <div class="form-group col-xl-12">
                                    <label for="estilo">Estilo</label>
                                    <select name="estilo" id="estilo" class="form-control" required>
                                        <option value="">Selecione</option>
                                        <option value="Padrao" {!! ($layout->estilo == 'Padrao') ? 'selected' : '' !!}>Padrão</option>
                                        <option value="Backgoud Multi Camada" {!! ($layout->estilo == 'Backgoud Multi Camada') ? 'selected' : '' !!}>Background multi-camadas</option>
                                    </select>
                                </div>
                            </div>
                            <div id="cores_multi_camada" style="display: none">
                                <div class="row">
                                    <div class="form-group col-xl-12">
                                        <label for="cor1">Cor 1</label><br>
                                        <input id="cor1-multi-camadas" name="cor1-multi-camadas" type="text" style="width: 100%" class="asColorpicker form-control colorInputUi-input" data-plugin="asColorPicker" data-mode="simple">
                                        <a href="#" class="colorInputUi-clear">
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xl-12">
                                        <label for="cor2">Cor 2</label><br>
                                        <input id="cor2-multi-camadas" name="cor2-multi-camadas" type="text" style="width: 100%" class="asColorpicker form-control colorInputUi-input" data-plugin="asColorPicker" data-mode="simple">
                                        <a href="#" class="colorInputUi-clear">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div id="cores_padrao" style="display: none">
                                <div class="row">
                                    <div class="form-group col-xl-12">
                                        <label for="cor1-padrao">Background</label>
                                        <select name="cor1-padrao" id="cor1-padrao" class="form-control" id="cor1-padrao">
                                            <option value="">Selecione</option>
                                            <option value="bg-azul">Azul 1</option>
                                            <option value="bg-azul2">Azul 2</option>
                                            <option value="bg-vermelho">Vermelho</option>
                                            <option value="bg-vermelho2">Vermelho 2</option>
                                            <option value="bg-roxo">Roxo</option>
                                            <option value="bg-roxo1">Roxo 2</option>
                                            <option value="bg-verde">Verde</option>
                                            <option value="bg-verde2">Verde 2</option>
                                            <option value="bg-pink">Rosa</option>
                                            <option value="bg-laranja">Laranja</option>
                                            <option value="bg-cinza">Cinza</option>
                                            <option value="bg-cinzaescuro">Cinza escuro</option>
                                            <option value="bg-preto">Preto</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-12">
                                    <label for="botao">Botões</label>
                                    <select name="botao" id="botoes" class="form-control" id="botao" required>
                                        <option value="">Selecione</option>
                                        <option value="btn-laranja" {!! ($layout->botao == 'btn-laranja') ? 'selected' : '' !!}>Laranja</option>
                                        <option value="btn-roxo" {!! ($layout->botao == 'btn-roxo') ? 'selected' : '' !!}>Roxo</option>
                                        <option value="btn-vermelho" {!! ($layout->botao == 'btn-vermelho') ? 'selected' : '' !!}>Vermelho</option>
                                        <option value="btn-azul" {!! ($layout->botao == 'btn-azul') ? 'selected' : '' !!}>Azul</option>
                                    </select>
                                </div>
                            </div>  --}}

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form id="form-preview" target="iframe-preview" action="/layouts/preview" method="POST" enctype='multipart/form-data' style="display: none">
        <input id="preview_logo" type="hidden" name="tipo" value="editar"/>
        <input type="hidden" name="layout" value="{{ $layout->id }}"/>
        <input id="preview_estilo" type="hidden" name="estilo" value="{{ $layout->estilo }}"/>
        <input id="preview_cor1" type="hidden" name="cor1" value="{{ $layout->cor1 }}"/>
        <input id="preview_cor2" type="hidden" name="cor2" value="{{ $layout->cor2 }}"/>
        <input id="preview_botoes" type="hidden" name="botoes" value="{{ $layout->botao }}"/>
        {{ csrf_field() }}
        <input type="submit">
    </form>

    <div class="col-xxl-6 col-lg-6">
        <div id="view_checkout" class="card card-shadow">
            <iframe id="view_checkout" name="iframe-preview"src="#" style="height: 650px"></iframe>
        </div>
    </div>
</div>
