<form id='form_review' method="post" action="#" enctype="multipart/form-data">
    @csrf
{{--    @method('PUT')--}}
    <input type="hidden" value="" name="id" class="review-id">
    <div style="width:100%">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="text-center">
                            <input name="photo" type="file" class="form-control" id="photoReview"
                                   style="display:none;" accept="image/*">
                            <label for="photoReview">Selecione uma foto</label>
                            <div style="width:100%" class="text-center">
                                <img id="previewimagereview" alt="Selecione uma foto"
                                     src="{{mix('build/global/img/projeto.svg')}}"
                                     style="min-width: 250px; max-width: 250px;margin: auto; cursor: pointer;">
                            </div>
                            <input id='photo_x1' name='photo_x1' type='hidden'>
                            <input id='photo_y1' name='photo_y1' type='hidden'>
                            <input id='photo_w' name='photo_w' type='hidden'>
                            <input id='photo_h' name='photo_h' type='hidden'>
                            <p class='info pt-5' style='font-size: 10px;'>
                                <i class='icon wb-info-circle' aria-hidden='true'></i>
                                A imagem escolhida deve estar no formato JPG, JPEG ou PNG.
                                <br> Dimensões ideais: 300 x 300 pixels.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-8">
                        <div class='form-group col-12 mb-20'>
                            <label for="link">Nome</label>
                            <div class="d-flex input-group">
                                <input type="text" class="form-control" name="name" id="name" maxlength="255" placeholder="Informe o nome" style="height: 50px !important; border-radius: 8px;">
                            </div>
                        </div>

                        <div class='form-group col-12 mb-20'>
                            <label for="description_review">Descrição</label>
                            <div class="d-flex input-group">
                                <textarea type="text" class="form-control" name="description" id="description_review" maxlength="255" placeholder="Digite a descrição" style="height: 50px !important; border-radius: 8px;"></textarea>
                            </div>
                        </div>

                        <div class='form-group col-12 col-sm-6 mb-20'>
                            <label for="link">Classificação</label>
                            <div id="review_stars" style="font-size: 25px;"></div>
                        </div>

                        <div class='form-group col-12 mb-20'>
                            <label for="link">Mostrar nos planos</label>
                            <select name="apply_on_plans[]" id="review_apply_on_plans" class="form-control"
                                    style='width:100%'
                                    data-plugin="select2" multiple='multiple'> </select>
                        </div>

                        <div class='form-group col-12 col-sm-6 mb-20'>
                            <label for="link">Status</label>
                            <select name="active_flag" id="active_flag" class='sirius-select'>
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
