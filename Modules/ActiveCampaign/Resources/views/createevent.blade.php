<form id="form-register-event" method="post" action="/api/apps/activecampaignevent" enctype="multipart/form-data" style="display:none">
    @csrf
    <div class="container-fluid">
        <div class="" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    {{-- <hr class='display-lg-none display-xlg-none'> --}}
                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Evento:</label>
                            <select id="events" name="events" class="form-control events">
                                {{--select no js--}}
                            </select>
                        </div>
                    </div>

                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Adicionar tags:</label>
                            <select id="add_tags" name="add_tags[]" multiple="multiple" class="form-control add_tags" data-plugin="" >
                                {{--select no js--}}
                            </select>
                        </div>
                    </div>

                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Remover tags:</label>
                            <select id="remove_tags" name="remove_tags[]" multiple="multiple" class="form-control remove_tags"  >
                                {{--select no js--}}
                            </select>
                        </div>
                    </div>

                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Adicionar na lista:</label>
                            <select id="add_list" name="add_list" class="form-control add_list">
                                {{--select no js--}}
                            </select>
                        </div>
                    </div>

                    <div class='col-sm-12 col-md-12 col-lg-12'>
                        <div class="form-group">
                            <label>Remover da lista:</label>
                            <select id="remove_list" name="remove_list" class="form-control remove_list">
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