$(document).ready(function () {
    index();
    function index(){
        $.ajax({
            method: "GET",
            url: "/api/apps/shopify",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                // loadingOnScreenRemove();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
                $('#content').html("");
                if(Object.keys(response.data).length === 0){
                    $("#no-integration-found").show();
                }
                else{
                    $(response.data).each(function(index, data){
                        $('#content').append(`
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" project=` + data.id +` style='cursor:pointer;'>
                                    <img class="card-img-top img-fluid w-full" src=` + data.project_photo +` onerror="this.onerror=null;this.src='{!! asset('modules/global/img/produto.png') !!}';" alt="{!! asset('modules/global/img/produto.png') !!}"/>
                                    <div class="card-body">
                                        <div class='row'>
                                            <div class='col-md-12'>
                                                <h4 class="card-title">` + data.project_name +`</h4>
                                                <p class="card-text sm">Criado em ` + data.created_at + `</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
            }
        });
    }
    // $('#btn-integration-model').on('click', function () {
    //     $('.modal_integration_shopify').modal('show');
    // });
    // $("#bt_add_integration").on("click", function () {
    //
    //     if ($('#token').val() == '' || $('#url_store').val() == '' || $('#company').val() == '') {
    //         alertCustom('error', 'Dados informados inválidos');
    //         return false;
    //     }
    //     loadingOnScreen();
    //
    //     var form_data = new FormData(document.getElementById('form_add_integration'));
    //
    //     $.ajax({
    //         method: "POST",
    //         url: "/apps/shopify",
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         processData: false,
    //         contentType: false,
    //         cache: false,
    //         data: form_data,
    //         error: function error(response) {
    //             loadingOnScreenRemove();
    //             alertCustom('error', response.responseJSON.message); //'Ocorreu algum erro'
    //         },
    //         success: function success(response) {
    //             loadingOnScreenRemove();
    //             alertCustom('success', response.message);
    //         }
    //     });
    // });
    function create(){
        $.ajax({
            method: "GET",
            url: "/companies/user-companies",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                $("#modal-content").hide();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function success(response) {
                if (Object.keys(response.data).length === 0) {
                    var route = '/companies/create';
                    $('#modal-project').modal('show');
                    $('#modal-project-title').text("Oooppsssss!");
                    $('#modal_project_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Você não possui empresa para realizar integração/strong></h3>' + '<h5 align="center">Deseja criar sua primeira empresa? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
                    $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                } else {
                    $(response.data).each(function(index, data){
                        $("#select_companies").append("<option value='" + data.id + "'>" + data.fantasy_name + "</option>");
                    });
                    $(".modal-title").html('Adicionar nova integração com Shopify');
                    $("#bt_integration").addClass('btn-save');
                    $("#bt_integration").text('Realizar integração');
                    $("#modal_add_integracao").modal('show');
                    $("#form_add_integration").show();

                    $('.check').on('click', function () {
                        if ($(this).is(':checked')) {
                            $(this).val(1);
                        } else {
                            $(this).val(0);
                        }
                    });

                    $(".btn-save").unbind('click');
                    $(".btn-save").on("click", function () {

                        if ($('#token').val() == '' || $('#url_store').val() == '' || $('#company').val() == '') {
                            alertCustom('error', 'Dados informados inválidos');
                            return false;
                        }
                        loadingOnScreen();

                        var form_data = new FormData(document.getElementById('form_add_integration'));

                        $.ajax({
                            method: "POST",
                            url: "/api/apps/shopify",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: form_data,
                            error: function error(response) {
                                loadingOnScreenRemove();
                                alertCustom('error', response.responseJSON.message); //'Ocorreu algum erro'
                            },
                            success: function success(response) {
                                loadingOnScreenRemove();
                                alertCustom('success', response.message);
                            }
                        });
                    });

                }
            }
        });
    }
    $('#btn-integration-model').on('click', function () {
        create();
    });
});

function openInNewWindow(url) {
    window.open(url);
}
