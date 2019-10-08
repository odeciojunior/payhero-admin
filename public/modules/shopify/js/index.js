$(document).ready(function () {

    loadOnAny('.page-content');

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
            loadOnAny('.page-content', true);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            index();
            create(response.data);
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
                loadOnAny('.page-content', true);
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadOnAny('.page-content', true);
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

    $("#bt_integration").on("click", function () {

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
        } else {
            $('#integration-actions').show();
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
