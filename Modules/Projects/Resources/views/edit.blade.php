<form id="atualizar_configuracoes" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" id="project_id" name="id" value="{{Hashids::encode($project->id)}}">
    <div style="width:100%">
        <h4>Configurações básicas</h4>
        <div class="row">
            <div class="form-group col-xl-12">
                <label for="nome">Nome do projeto</label>
                <input name="name" value="{{$project->name}}" type="text" class="form-control" id="nome" placeholder="Nome do projeto" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xl-12">
                <label for="descricao">Descrição</label>
                <input name="description" value="{{$project->description}}" type="text" class="form-control" id="descricao" placeholder="Descrição">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-12">
                <label for="visibilidade">Visibilidade</label>
                <select name="visibility" class="form-control" id="visibilidade" required>
                    <option value="publico" {{ $project->visibility == 'public' ? 'selected' : '' }}>Projeto público (visível na vitrine e disponível para afiliações)</option>
                    <option value="privado" {{ $project->visibility == 'private' ? 'selected' : '' }}>Projeto privado (completamente invisível para outros usuários, afiliações somente por convite)</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xl-6 col-lg-6">
                <label for="url_pagina">URL da página principal</label>
                <input name="url_page" value="{{$project->url_page}}" type="text" class="form-control" id="url_pagina" placeholder="URL da página">
            </div>
            <div class="form-group col-xl-6 col-lg-6">
                <label for="contato">Contato (checkout)</label>
                <input name="contact" value="{{$project->contact}}" type="text" class="form-control" id="contato" placeholder="Contato">
            </div>
        </div>
        {{--<h4>Configurações de afiliados</h4>
        <div class="row" id="div_dados_afiliados">
            <div class="form-group col-xl-6 col-lg-6">
                <label for="porcentagem_afiliados">Porcentagem para afiliados</label>
                <input name="percentage_affiliates" value="{{$project->percentage_affiliates}}" type="text" class="form-control" id="porcentagem_afiliados" placeholder="Porcentagem">
            </div>
            <div class="form-group col-xl-6 col-lg-6">
                <label for="afiliacao_automatica">Afiliação automática</label>
                <select name="automatic_affiliation" class="form-control" id="afiliacao_automatica" required>
                    <option value="1" {{ $project->automatic_affiliation == '1' ? 'selected' : '' }}>Sim</option>
                    <option value="0" {{ $project->automatic_affiliation == '0' ? 'selected' : '' }}>Não</option>
                </select>
            </div>
        </div>
        <div class="row" id="div_dados_afiliados">
            <div class="form-group col-xl-6 col-lg-6">
                <label for="duracao_cookie">Duração do cookie</label>
                <select name="cookie_duration" class="form-control">
                    <option value="60" {{$project->cookie_duration == '60' ? 'selected' : ''}}>60</option>
                    <option value="90" {{$project->cookie_duration == '90' ? 'selected' : ''}}>90</option>
                    <option value="120" {{$project->cookie_duration == '120' ? 'selected' : ''}}>120</option>
                    <option value="180" {{$project->cookie_duration == '180' ? 'selected' : ''}}>180</option>
                    <option value="-1" {{$project->cookie_duration == '-1' ? 'selected' : ''}}>Pra sempre</option>
                </select>
            </div>
            <div class="form-group col-xl-6 col-lg-6">
                <label for="url_cookies_checkout">Criar URL do checkout dos produtos</label>
                <select name="url_cookies_checkout" class="form-control" id="url_cookies_checkout" required>
                    <option value="1" {{$project->url_cookies_checkout == '1' ? 'selected' : ''}}>Sim</option>
                    <option value="0" {{$project->url_cookies_checkout == '0' ? 'selected' : ''}}>Não</option>
                </select>
            </div>
        </div>
        <div class="row" style="margin: 0 2% 0 2%">
            <div style="width:100%">
                <a id="adicionar_material_extra" class="btn btn-primary float-right" data-toggle='modal' data-target='#modal_add_material_extra' style="color: white">
                    <i class='icon wb-user-add' aria-hidden='true'></i> Adicionar material extra
                </a>
            </div>
            <div class="row">
                <h5>Materiais extras</h5>
            </div>
            <table class="table table-hover table-bordered">
                <thead>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Remover</th>
                </thead>
                <tbody>
                    @if(count($extraMaterials) == 0)
                        <tr>
                            <td colspan="3" style="text-align:center">Nenhum material extra adicionado</td>
                        </tr>
                    @else
                        @foreach($extraMaterials as $extraMaterial)
                            <tr>
                                <td>{{$extraMaterial->description}}</td>
                                <td>{{$extraMaterial->type}}</td>
                                <td style="width:70px">
                                    <button type="button" class="btn btn-danger excluir_material_extra" material-extra="{{Hashids::encode($extraMaterial->id)}}">Excluir</button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>--}}
        <h4> Configurações do frete</h4>
        <div class="row">
            <div class="form-group col-xl-6 col-lg-6">
                <label for="frete_projeto">Possui frete</label>
                <select name="shipment" type="text" class="form-control" id="frete_projeto">
                    <option value="1" {{$project->shipment == '1' ? 'selected' : '' }}>Sim</option>
                    <option value="0" {{$project->shipment == '0' ? 'select' : ''}}>Não</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div id="div_transportadora_projeto" class="form-group col-xl-6 col-lg-6" style="{!! $project['frete'] ? 'display:none' : '' !!}">
                <label for="transportadora">Transportadora</label>
                <select name="carrier" type="text" class="form-control" id="transportadora_projeto" required>
                <!--option value="1" {{$project->carrier == '1' ? 'selected' : ''  }}>Kapsula</option-->
                    <option value="2" {{$project->carrier == '2' ? 'selected' : '' }} >Despacho próprio</option>
                <!--option value="3" {{$project->carrier == '3' ? 'selected' : '' }} >Lift Gold</option-->
                </select>
            </div>
            <div id="div_responsavel_frete_projeto" class="form-group col-xl-6 col-lg-6" style="{!! $project['frete'] ? 'display:none' : '' !!}">
                <label for="responsavel_frete_projeto">Responsável pelo frete</label>
                <select name="shipment_responsible" type="text" class="form-control" id="responsavel_frete_projeto">
                    <option value="proprietario" {{$project->shipment_responsible == 'owner' ? 'selectd' : ''}}>Proprietário</option>
                    <option value="parceiros" {{$project->shipment_responsible == 'partners' ? 'selectd' : ''}}>Proprietário + parceiros</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div style="width:100%">
                <a id="add_shipping" class="btn btn-primary float-right" data-toggle='modal' data-target='#modal_add_shipping' style="color: white">
                    <i class='icon wb-user-add' aria-hidden='true'></i> Adicionar frete
                </a>
            </div>
            <h5 style="margin-top: 10px">Fretes oferecidos no checkout</h5>
            <table class="table table-hover table-bordered">
                <thead>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Informação</th>
                    <th>Status</th>
                    <th>Pré selecionado</th>
                    <th></th>
                </thead>
                <tbody>
                    @if(isset($shippings) && count($shippings) > 0)
                        @foreach($shippings as $shipping)
                            <tr>
                                <td class="shipping_id" style="display:none">{{$shipping->id}}</td>
                                <td class="shipping_type" style="display:none">{{$shipping->type}}</td>
                                <td class="shipping_value" style="display:none">{{$shipping->value}}</td>
                                <td class="shipping_zip_code_origin" style="display:none">{{$shipping->zip_code_origin}}</td>
                                <td class="shipping_name">{{$shipping->name}}</td>
                                <td class="shipping_type">{{$shipping->type == 'static'? $shipping->value : 'calculado automaticamente'}}</td>
                                <td class="shipping_information">{{$shipping->information}}</td>
                                <td class="shipping_status">{{$shipping->status == '1' ? 'Ativado': 'Desativado'}}</td>
                                <td class="shipping_pre_selected">{{$shipping->pre_selected == '1'? 'Sim' : 'Não'}}</td>
                                <td>
                                    <button type="button" class="btn btn-success edit_shipping" material-extra="{{Hashids::encode($shipping->id)}}" data-toggle='modal' data-target='#modal_edit_shipping'>Editar
                                    </button>
                                    <button type="button" class="btn btn-danger delete_shipping" material-extra="{{Hashids::encode($shipping->id)}}" data-toggle='modal' data-target='#modal_excluir'>Excluir
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="text-center">
                            <td colspan="5">Nenhum frete adicionado</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <h4>Configurações avançadas</h4>
        <div class="row">
            <div class="form-group col-12">
                <label for="descricao_fatura">Descrição na fatura (máximo 13 caracteres)</label>
                <input name="invoice_description" value="{{$project->invoice_description}}" type="text" class="form-control" id="descricao_fatura" placeholder="Descrição do projeto na fatura">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6 col-xs-12">
                <label for="qtd_parcelas">Qtd de parcelas (cartão de crédito)</label>
                <select name="installment_amount" class="form-control">
                    <option value="1" {{ $project->installment_amount == '1' ? 'selected' : '' }}>1</option>
                    <option value="2" {{ $project->installment_amount == '2' ? 'selected' : '' }}>2</option>
                    <option value="3" {{ $project->installment_amount == '3' ? 'selected' : '' }}>3</option>
                    <option value="4" {{ $project->installment_amount == '4' ? 'selected' : '' }}>4</option>
                    <option value="5" {{ $project->installment_amount == '5' ? 'selected' : '' }}>5</option>
                    <option value="6" {{ $project->installment_amount == '6' ? 'selected' : '' }}>6</option>
                    <option value="7" {{ $project->installment_amount == '7' ? 'selected' : '' }}>7</option>
                    <option value="8" {{ $project->installment_amount == '8' ? 'selected' : '' }}>8</option>
                    <option value="9" {{ $project->installment_amount == '9' ? 'selected' : '' }}>9</option>
                    <option value="10" {{ $project->installment_amount == '10' ? 'selected' : '' }}>10</option>
                    <option value="11" {{ $project->installment_amount == '11' ? 'selected' : '' }}>11</option>
                    <option value="12" {{ $project->installment_amount == '12' ? 'selected' : '' }}>12</option>
                </select>
            </div>
            <div class="col-6 col-xs-12">
                <label for="parcelas_sem_juros">Quantidade de parcelas sem juros</label>
                <select name="installments_interest_free" class="form-control">
                    <option value="1" {{ $project->installments_interest_free == '1' ? 'selected' : '' }}>1</option>
                    <option value="2" {{ $project->installments_interest_free == '2' ? 'selected' : '' }}>2</option>
                    <option value="3" {{ $project->installments_interest_free == '3' ? 'selected' : '' }}>3</option>
                    <option value="4" {{ $project->installments_interest_free == '4' ? 'selected' : '' }}>4</option>
                    <option value="5" {{ $project->installments_interest_free == '5' ? 'selected' : '' }}>5</option>
                    <option value="6" {{ $project->installments_interest_free == '6' ? 'selected' : '' }}>6</option>
                    <option value="7" {{ $project->installments_interest_free == '7' ? 'selected' : '' }}>7</option>
                    <option value="8" {{ $project->installments_interest_free == '8' ? 'selected' : '' }}>8</option>
                    <option value="9" {{ $project->installments_interest_free == '9' ? 'selected' : '' }}>9</option>
                    <option value="10" {{ $project->installments_interest_free == '10' ? 'selected' : '' }}>10</option>
                    <option value="11" {{ $project->installments_interest_free == '11' ? 'selected' : '' }}>11</option>
                    <option value="12" {{ $project->installments_interest_free == '12' ? 'selected' : '' }}>12</option>
                </select>
            </div>
            <div class="col-6 col-xs-12">
                <label for="emrpesa">Empresa</label>
                <select name="company" class="form-control" id="empresa" required>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ ($company->id == $emp) ? 'selected' : '' }}>{{$company->fantasy_name}}</option>
                     @endforeach
                </select>
            </div>
        </div>
        <h4>Imagem do projeto</h4>
        <div class="row">
            <div class="form-group col-12">
                <label for="selecionar_foto">Imagem do projeto</label>
                <br>
                 <input type="button" id="selecionar_foto" class="btn btn-default" value="Alterar foto do projeto" style="width:200px">
                <input name="project_photo" type="file" class="form-control" id="foto_projeto" style="display:none" accept="image/*">
                <div style="margin: 20px 0 0 30px;">
                    <img id="previewimage" alt="Selecione a foto do projeto" style="max-height: 250px; max-width: 350px;" src="{!! $project->photo !!}"/>
                </div>
                <input type="hidden" name="project_photo_x1"/> <input type="hidden" name="project_photo_y1"/>
                <input type="hidden" name="project_photo_w"/> <input type="hidden" name="project_photo_h"/>
            </div>
        </div>
        <div class="row" style="margin-top: 30px">
            <div class="form-group" style="width:100%">
                <button id="bt_atualizar_configuracoes" type="button" class="btn btn-success">Atualizar dados do projeto</button>
                <button id="bt_deletar_projeto" type="button" class="btn btn-danger" style="float: right" data-toggle='modal' data-target='#modal_excluir'>Deletar projeto</button>
            </div>
        </div>
    </div>
</form>


