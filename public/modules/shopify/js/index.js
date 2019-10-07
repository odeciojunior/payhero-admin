$(document).ready(function () {
    $.ajax({
        method: "GET",
        url: "/api/companies?select=true",
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: function error(response) {
            $("#modal-content").hide();
            errorAjaxResponse(response);
        },
        success: function success(response) {
            create(response.data);
            index();
        }
    });

    /**
     * Companies
     */
    function index() {
        $.ajax({
            method: "GET",
            url: "/api/apps/shopify",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                // loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                createHtmlIntegrations(response.data);
            }
        });
    }

    /**
     * Monta html com todas as integrações do shopify
     * @param data
     */
    function createHtmlIntegrations(data) {
        $('#content').html("");

        if (isEmpty(data)) {
            $("#no-integration-found").show();
        } else {
            $(data).each(function (index, data) {
                $('#content').append(`
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" project="${data.id}">
                                    <img class="card-img-top img-fluid w-full" src="${!data.project_photo ? '/modules/global/img/produto.png' : data.project_photo}" />
                                    <div class="card-body">
                                        <div class='row'>
                                            <div class='col-md-12'>
                                                <h4 class="card-title">${data.project_name}</h4>
                                                <p class="card-text sm">Criado em ${data.created_at}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
            });
        }
    }

    $('#btn-integration-model').on('click', function () {
        $("#modal_add_integracao").modal('show');
        $("#form_add_integration").show();
    });

    $("#modal_add_integracao .btn-save").on("click", function () {

        if ($('#token').val() == '' || $('#url_store').val() == '' || $('#company').val() == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        } else {
            loadingOnScreen();
            saveIntegration();
        }

    });

    /**
     * Monta modal para realizar integração
     * @param data
     */
    function create(data) {
        if (isEmpty(data)) {
            $('#integration-actions, .page-content').hide();
            $('#empty-companies-error').show();
            // var route = '/companies/create';
            // $('#modal-project').modal('show');
            // $('#modal-project-title').text("Oooppsssss!");
            // $('#modal_project_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Você não possui empresa para realizar integração/strong></h3>' + '<h5 align="center">Deseja criar sua primeira empresa? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
            // $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
        } else {
            $('#integration-actions, .page-content').show();
            $('#empty-companies-error').hide();

            $("#select_companies").empty();
            $(data).each(function (index, data) {
                $("#select_companies").append("<option value='" + data.id + "'>" + data.name + "</option>");
            });
            $(".modal-title").html('Adicionar nova integração com Shopify');
            $("#bt_integration").addClass('btn-save');
            $("#bt_integration").text('Realizar integração');

            $('.check').on('click', function () {
                if ($(this).is(':checked')) {
                    $(this).val(1);
                } else {
                    $(this).val(0);
                }
            });

        }
    }

    /**
     * Ajax para realizar integração
     */
    function saveIntegration() {
        let form_data = new FormData(document.getElementById('form_add_integration'));
        $.ajax({
            method: "POST",
            url: "/api/apps/shopify",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                index();
                alertCustom('success', response.message);
            }
        });
    }

});

function openInNewWindow(url) {
    window.open(url);
}
