<form id="atualizar_configuracoes" method="post" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{!! $projeto->id !!}">
    <div style="width:100%">
        <h4>Configurações básicas</h4>
        <div class="row">
            <div class="form-group col-xl-12">
                <label for="nome">Nome</label>
                <input name="nome" value="{!! $projeto->nome !!}" type="text" class="form-control" id="nome" placeholder="Nome do projeto" required>
            </div>
            {{--  <div class="form-group col-xl-6">
                <label for="emrpesa">Empresa</label>
                <select name="empresa" class="form-control" id="empresa" required>
                    <option value="">Selecione</option>
                    @foreach($empresas as $empresa)
                        <option value="{!! $empresa->id !!}" {!! ($empresa->id == $projeto->empresa) ? 'selected' : '' !!}>{!! $empresa->nome_fantasia !!}</option>
                    @endforeach
                </select>
            </div>  --}}
        </div>

        <div class="row">
            <div class="form-group col-xl-12">
                <label for="descricao">Descrição</label>
                <input name="descricao" value="{!! $projeto->descricao !!}" type="text" class="form-control" id="descricao" placeholder="Descrição">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-12">
                <label for="visibilidade">Visibilidade</label>
                <select name="visibilidade" class="form-control" id="visibilidade" required>
                    <option value="publico" {!! $projeto->visibilidade == 'publico' ? 'selected' : '' !!}>Projeto público (visível na vitrine e disponível para afiliações)</option>
                    <option value="privado" {!! $projeto->visibilidade == 'privado' ? 'selected' : '' !!}>Projeto privado (completamente invisível para outros usuários, afiliações somente por covite)</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-xl-12">
                <label for="url_pagina">URL da página principal</label>
                <input name="url_pagina" value="{!! $projeto->url_pagina !!}" type="text" class="form-control" id="url_pagina" placeholder="URL da página">
            </div>
        </div>
    
        <h4>Configurações de afiliados</h4>

        <div class="row" id="div_dados_afiliados">
            <div class="form-group col-xl-6">
                <label for="porcentagem_afiliados">Porcentagem para afiliados</label>
                <input name="porcentagem_afiliados" value="{!! $projeto->porcentagem_afiliados !!}" type="text" class="form-control" id="porcentagem_afiliados" placeholder="Porcentagem">
            </div>
            <div class="form-group col-xl-6">
                <label for="afiliacao_automatica">Afiliação automática</label>
                <select name="afiliacao_automatica" class="form-control" id="afiliacao_automatica" required>
                    <option value="1" {!! $projeto->afiliacao_automatica == '1' ? 'selected' : '' !!}>Sim</option>
                    <option value="0" {!! $projeto->afiliacao_automatica == '0' ? 'selected' : '' !!}>Não</option>
                </select>
            </div>
        </div>

        <h4> Configurações do frete </h4>

        <div class="row">
            <div class="form-group col-xl-6 col-lg-6">
                <label for="frete_plano_cadastrar">Possui frete</label>
                <select name="frete" type="text" class="form-control" id="frete_plano_cadastrar">
                    <option value="1" {!! $projeto['frete'] == '1' ? 'selected' : '' !!}>Sim</option>
                    <option value="0" {!! $projeto['frete'] == '0' ? 'selected' : '' !!}>Não</option>
                </select>
            </div>
            <div id="div_frete_fixo_cadastrar" class="form-group col-xl-6 col-lg-6" style="{!! !$projeto->frete ? 'display:none' : '' !!}">
                <label for="frete_fixo_plano_cadastrar">Frete fixo</label>
                <select name="frete_fixo" type="text" class="form-control" id="frete_fixo_plano_cadastrar">
                    <option value="0" {!! $projeto['frete_fixo'] == '0' ? 'selected' : '' !!}>Não</option>
                    <option value="1" {!! $projeto['frete_fixo'] == '1' ? 'selected' : '' !!}>Sim</option>
                </select>
            </div>
        </div>

        <div id="div_valor_frete_fixo_cadastrar" class="row" style="{!! (!$projeto->frete || !$projeto->frete_fixo ) ? 'display:none' : '' !!}">
            <div class="form-group col-xl-6 col-lg-6">
                <label for="valor_frete_cadastrar">Valor frete fixo</label>
                <input name="valor_frete" type="text" class="form-control dinheiro" id="valor_frete_cadastrar" value="0" placeholder="valor fixo">
            </div>
        </div>

        <div class="row">
            <div id="div_transportadora_cadastrar" class="form-group col-xl-6 col-lg-6" style="{!! !$projeto->frete ? 'display:none' : '' !!}">
                <label for="transportadora">Transportadora</label>
                <select name="transportadora" type="text" class="form-control" id="transportadora_projeto" required>
                    <option value="1" {!! $projeto['transportadora'] == '1' ? 'selected' : '' !!}>Kapsula</option>
                    <option value="2" {!! $projeto['transportadora'] == '2' ? 'selected' : '' !!}>Despacho próprio</option>
                    <option value="3" {!! $projeto['transportadora'] == '3' ? 'selected' : '' !!}>Lift Gold</option>
                </select>
            </div>
            <div id="div_responsavel_frete_cadastrar" class="form-group col-xl-6 col-lg-6" style="{!! !$projeto->frete ? 'display:none' : '' !!}">
                <label for="responsavel_frete_cadastrar">Responsável pelo frete</label>
                <select name="responsavel_frete" type="text" class="form-control" id="responsavel_frete_editar">
                    <option value="proprietario" {!! $projeto['responsavel_frete'] == 'proprietario' ? 'selected' : '' !!}>Proprietário</option>
                    <option value="parceiros" {!! $projeto['responsavel_frete'] == 'parceiros' ? 'selected' : '' !!}>Proprietário + parceiros</option>
                </select>
            </div>
        </div>

        <h4>Configurações avançadas</h4>

        <div class="row">
            <div class="form-group col-12">
                <label for="descricao_fatura">Descrição na fatura</label>
                <input name="descricao_fatura" value="{!! $projeto->descricao_fatura !!}" type="text" class="form-control" id="descricao_fatura" placeholder="Descrição do projeto na fatura">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-6 col-xs-12">
                <label for="qtd_parcelas">Qtd de parcelas (cartão de  crédito)</label>
                <select name="qtd_parcelas" class="form-control">
                    <option value="1" {!! $projeto['qtd_parcelas'] == '1' ? 'selected' : '' !!}>1</option>
                    <option value="2" {!! $projeto['qtd_parcelas'] == '2' ? 'selected' : '' !!}>2</option>
                    <option value="3" {!! $projeto['qtd_parcelas'] == '3' ? 'selected' : '' !!}>3</option>
                    <option value="4" {!! $projeto['qtd_parcelas'] == '4' ? 'selected' : '' !!}>4</option>
                    <option value="5" {!! $projeto['qtd_parcelas'] == '5' ? 'selected' : '' !!}>5</option>
                    <option value="6" {!! $projeto['qtd_parcelas'] == '6' ? 'selected' : '' !!}>6</option>
                    <option value="7" {!! $projeto['qtd_parcelas'] == '7' ? 'selected' : '' !!}>7</option>
                    <option value="8" {!! $projeto['qtd_parcelas'] == '8' ? 'selected' : '' !!}>8</option>
                    <option value="9" {!! $projeto['qtd_parcelas'] == '9' ? 'selected' : '' !!}>9</option>
                    <option value="10" {!! $projeto['qtd_parcelas'] == '10' ? 'selected' : '' !!}>10</option>
                    <option value="11" {!! $projeto['qtd_parcelas'] == '11' ? 'selected' : '' !!}>11</option>
                    <option value="12" {!! $projeto['qtd_parcelas'] == '12' ? 'selected' : '' !!}>12</option>
                </select>
            </div>
            <div class="col-6 col-xs-12">
                <label for="parcelas_sem_juros">Quantidade de parcelas sem juros</label>
                <select name="parcelas_sem_juros" class="form-control">
                    <option value="1" {!! $projeto['parcelas_sem_juros'] == '1' ? 'selected' : '' !!}>1</option>
                    <option value="2" {!! $projeto['parcelas_sem_juros'] == '2' ? 'selected' : '' !!}>2</option>
                    <option value="3" {!! $projeto['parcelas_sem_juros'] == '3' ? 'selected' : '' !!}>3</option>
                    <option value="4" {!! $projeto['parcelas_sem_juros'] == '4' ? 'selected' : '' !!}>4</option>
                    <option value="5" {!! $projeto['parcelas_sem_juros'] == '5' ? 'selected' : '' !!}>5</option>
                    <option value="6" {!! $projeto['parcelas_sem_juros'] == '6' ? 'selected' : '' !!}>6</option>
                    <option value="7" {!! $projeto['parcelas_sem_juros'] == '7' ? 'selected' : '' !!}>7</option>
                    <option value="8" {!! $projeto['parcelas_sem_juros'] == '8' ? 'selected' : '' !!}>8</option>
                    <option value="9" {!! $projeto['parcelas_sem_juros'] == '9' ? 'selected' : '' !!}>9</option>
                    <option value="10" {!! $projeto['parcelas_sem_juros'] == '10' ? 'selected' : '' !!}>10</option>
                    <option value="11" {!! $projeto['parcelas_sem_juros'] == '11' ? 'selected' : '' !!}>11</option>
                    <option value="12" {!! $projeto['parcelas_sem_juros'] == '12' ? 'selected' : '' !!}>12</option>
                </select>
            </div>    
        </div>

        <h4>Imagem do projeto</h4>

        <div class="row">
            <div class="form-group col-12">
                <label for="selecionar_foto">Imagem do projeto</label><br>
                <input type="button" id="selecionar_foto" class="btn btn-default" value="Alterar foto do projeto">
                <input name="foto_projeto" type="file" class="form-control" id="foto_projeto" style="display:none" accept="image/*">
                <div  style="margin: 20px 0 0 30px;">
                    <img id="previewimage" alt="Selecione a foto do projeto" style="max-height: 250px; max-width: 350px;" src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto->foto)!!}?dummy={!! uniqid() !!}"/>
                </div>
                <input type="hidden" name="foto_x1"/>
                <input type="hidden" name="foto_y1"/>
                <input type="hidden" name="foto_w"/>
                <input type="hidden" name="foto_h"/>
            </div>
        </div>

        <div class="row" style="margin-top: 30px">
            <div class="form-group">
                <button id="bt_atualizar_configuracoes" type="button" class="btn btn-success">Atualizar dados do projeto</button>
            </div>
        </div>

    </div>
</form>

