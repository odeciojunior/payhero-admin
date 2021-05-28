$(document).ready(function () {
    let allCompanyNotApproved = false;
    let companyNotFound = false;
    let woocommerceIntegrationNotFound = false;

    loadingOnScreen();
    $('#btn-integration-model').hide();

    index();

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

            htmlAlertWooCommerce();
            loadingOnScreenRemove();
        }
    });

    function htmlAlertWooCommerce() {
        if (!companyNotFound) {
            $('#btn-integration-model').hide();
            $('#button-information').hide();
            $("#empty-companies-error").show();
        } else if (allCompanyNotApproved) {
            $('#btn-integration-model').hide();
            $('#button-information').hide();
            $("#companies-not-approved-getnet").show();
        } else if (!allCompanyNotApproved) {
            if (woocommerceIntegrationNotFound) {
                $("#no-integration-found").show();
            }else{
                $("#no-integration-found").hide();

            }

            $('#btn-integration-model').show();
            $('#button-information').show().addClass('d-flex').css('display', 'flex');
        }
    }

    function index() {
        $.ajax({
            method: "GET",
            url: "/api/apps/woocommerce",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                let data = response.data;

                $('#content').html("");

                if (isEmpty(data)) {
                    woocommerceIntegrationNotFound = true;
                    return;
                }

                $(data).each(function (index, data) {
                    $('#content').append(`
                        <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                            <div class="card shadow card-edit" project="${data.id}">
                                <img class="card-img-top img-fluid w-full" src="${!data.project_photo ? '/modules/global/img/produto.png' : data.project_photo}"  alt="Photo Project"/>
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
        });
    }

    function create(data) {
        let companyApproved = 0;
        if (isEmpty(data)) {
            $('#integration-actions, .page-content').hide();
            return;
        }

        companyNotFound = true;

        $('#integration-actions').show();

        $("#select_companies").empty();
        $(data).each(function (index, data) {
            if (data.capture_transaction_enabled) {
                companyApproved = companyApproved + 1;
                $("#select_companies").append(`<option value=${data.id}> ${data.name}</option>`);
            }
        });

        if (companyApproved == 0) {
            allCompanyNotApproved = true;
        }

        $(".modal-title").html('Adicionar nova integração com WooCommerce');
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

    $('#btn-integration-model').on('click', function () {
        $("#modal_add_integracao").modal('show');
        $("#form_add_integration").show();
    });

    $("#bt_integration").on("click", function () {
        if ($('#token').val() == '' || $('#url_store').val() == '' || $('#company').val() == '') {
            alertCustom('error', 'Dados informados inválidos');
            return false;
        }

        saveIntegration();
    });

    function saveIntegration() {
        let form_data = new FormData(document.getElementById('form_add_integration'));

        if (!form_data.get('company').length > 0) {
            alertCustom('error', 'A empresa precisa estar aprovada transacionar para realizar a integração! ');
            return false;
        }

        loadingOnScreen();

        $.ajax({
            method: "POST",
            url: "/api/apps/woocommerce",
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
                alertCustom('success', response.message);
            }
        });
    }
});
