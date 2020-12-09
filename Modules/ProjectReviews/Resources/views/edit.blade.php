<form id='form_edit_review' method="PUT" action="#" style="display:none">
    @csrf
    @method('PUT')
    <input type="hidden" value="" name="id" class="review-id">
    <div style="width:100%">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class='col-md-4 col-sm-12'>
                        <div class='d-flex flex-column text-center' id='div-img-project' style='position: relative;'>
                            <input name='photo' type='file' class='form-control' id='reviewPhoto' style='display:none;'
                                   accept='image/*'>
                            <label for='photo'>Selecione uma imagem capa do projeto</label>
                            <div style="width:100%" class="text-center">
                                <img id='previewReviewImage' alt='Selecione a foto do review'
                                     src="{{asset('modules/global/img/projeto.png')}}"
                                     style="min-width: 250px; max-width: 250px;margin: auto">
                            </div>
                            <input type='hidden' id='photo_x1' name='photo_x1'>
                            <input id='photo_y1' type='hidden' name='photo_y1'>
                            <input type='hidden' id='photo_w' name='photo_w'>
                            <input id='photo_h' type='hidden' name='photo_h'>
                            <p class='info pt-5' style='font-size: 10px;'>
                                <i class='icon wb-info-circle' aria-hidden='true'></i> Foto que será exibida no review
                                <br/> A imagem escolhida deve estar no formato JPG, JPEG ou PNG.
                                <br/> Dimensões ideais: 300 x 300 pixels.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-8">
                        <div class='form-group col-12 mb-20'>
                            <label for="link">Nome</label>
                            <div class="d-flex input-group">
                                <input type="text" class="form-control" name="name" id="edit_name"
                                       maxlength="255" placeholder="Informe o nome">
                            </div>
                        </div>

                        <div class='form-group col-12 mb-20'>
                            <label for="edit_description_review">Descrição</label>
                            <div class="d-flex input-group">
                                <textarea type="text" class="form-control" name="description"
                                          id="edit_description_review" maxlength="255"
                                          placeholder="Digite a descrição"></textarea>
                            </div>
                        </div>

                        <div class='form-group col-12 col-sm-6 mb-20'>
                            <label for="link">Classificação</label>
                            <div id="review_edit_stars" style="font-size: 25px;"></div>
                        </div>

                        <div class='form-group col-12 mb-20'>
                            <label for="link">Mostrar nos planos</label>
                            <select name="apply_on_plans[]" id="edit_review_apply_on_plans" class="form-control"
                                    style='width:100%'
                                    data-plugin="select2" multiple='multiple'> </select>
                        </div>

                        <div class='form-group col-12 col-sm-6 mb-20'>
                            <label for="link">Status</label>
                            <select name="active_flag" id="edit_active_flag" class='form-control'>
                                <option value='1' selected='selected'>Ativo</option>
                                <option value='0'>Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
