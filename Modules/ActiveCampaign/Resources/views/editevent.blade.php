<form id="form-update-event" method="PUT" action="/api/apps/activecampaignevent" enctype="multipart/form-data" style="display:none">
    @csrf
    @method('PUT')
    <div class="container-fluid">
        <div class="" data-plugin="matchHeight">
            <div style="width:100%">
                <input type="hidden" value="" name="event_id_edit" id='event_id_edit'>
                <div class="row">
                    {{-- <hr class='display-lg-none display-xlg-none'> --}}
                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Evento: <span id="event_name_edit"></span></label>
                        </div>
                    </div>

                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Adicionar tags:</label>
                            <select id="add_tags_edit" name="add_tags_edit[]" multiple="multiple" class="form-control add_tags_edit" style="width: 100%">
                                {{--select no js--}}
                            </select>
                        </div>
                    </div>

                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Remover tags:</label>
                            <select id="remove_tags_edit" name="remove_tags_edit[]" multiple="multiple" class="form-control remove_tags_edit" style="width: 100%">
                                {{--select no js--}}
                            </select>
                        </div>
                    </div>

                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Adicionar na lista:</label>
                            <select id="add_list_edit" name="add_list_edit" class="form-control add_list_edit" style="width: 100%">
                                {{--select no js--}}
                            </select>
                        </div>
                    </div>

                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Remover da lista:</label>
                            <select id="remove_list_edit" name="remove_list_edit" class="form-control remove_list_edit" style="width: 100%">
                                {{--select no js--}}
                            </select>
                        </div>
                    </div>
                    {{-- <hr class='mb-30 display-lg-none display-xlg-none'> --}}
                </div>
            </div>
        </div>
    </div>
</form>