$(document).ready(function () {
    var pageCurrent;
    let badgeList = {
        1: "#3E8EF7",
        2: "#41C26D",
        3: "#F54C52",
    };
    let statusList = {
        1: "Em análise",
        2: "Aprovado",
        3: "Recusado",
    };
    // Comportamentos da tela
    $("#type-products").on("change", function () {
        if ($(this).val() === "1") {
            $("#is-projects select")
                .prop("disabled", false)
                .removeClass("disabled");
            $("#opcao-vazia").remove();
        } else {
            $("#is-projects select")
                .prop("disabled", true)
                .addClass("disabled")
                .prepend(
                    '<option value="0" id="opcao-vazia" selected></option>'
                );
        }
    });

    $("#btn-filtro").on("click", function () {
        deleteCookie("filterProduct");
        updateProducts();
    });

    $("#pagination-products").on("click", function () {
        deleteCookie("filterProduct");
    });

    getTypeProducts();

    updateProducts();

    function getTypeProducts() {
        $.ajax({
            method: "GET",
            url: "/api/projects?select=true",
            data: {
                status: "active",
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                if (response.data) {
                    $("#select-projects").html("");
                    $.each(response.data, function (index, value) {
                        if (value.shopify) {
                            $("#select-projects").append(
                                $("<option>", {
                                    value: value.id,
                                    text: value.name,
                                })
                            );
                        }
                    });
                    $("#select-projects").prepend(
                        '<option value="0" id="opcao-vazia" selected></option>'
                    );
                }
            },
        });
    }

    getProjects();

    function getProjects() {
        loadingOnScreen();
        $.ajax({
            method: "GET",
            url: "api/projects?select=true",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    if (verifyAccountFrozen()) {
                        $("#div-create").hide();
                    } else {
                        $("#div-create").show();
                    }
                    updateProducts();
                } else {
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                    $("#div-create").hide();
                }

                loadingOnScreenRemove();
            },
        });
    }

    function updateProducts(link = null) {
        if (link !== null) {
            pageCurrent = link;
            deleteCookie("filterProduct");
        }

        loadOnAny(".page-content");

        let type = "";
        let project = "";
        let name = "";

        var cookie = getCookie("filterProduct");
        if (cookie == "") {
            type = $("#type-products").val();
            project = $("#select-projects").val();
            name = $("#name").val();
        } else {
            cookie = JSON.parse(cookie);
            type = cookie.type;
            project = cookie.project;
            name = cookie.nameProduct;
            link = cookie.page;

            $("#type-products").val(type);
        }

        if (link == null) {
            link =
                "/api/products?shopify=" +
                type +
                "&project=" +
                project +
                "&name=" +
                name;
        } else {
            link =
                "/api/products" +
                link +
                "&shopify=" +
                type +
                "&project=" +
                project +
                "&name=" +
                name;
        }

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadOnAny(".page-content", true);
                errorAjaxResponse(response);
            },
            success: function (response) {
                if (!isEmpty(response.data)) {
                    $(".products-is-empty").hide();
                    $("#data-table-products").html("");
                    let dados = "";

                    $.each(response.data, function (index, value) {
                        shopifyProduct = value.id != value.id_view;
                        dados = `
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                            <div class="card shadow mb-20 mx-0" style="flex: 1 1 100%">
                                <div style="margin: 10px 10px 0px 0px;position: absolute;right: 0px;">
                                    <button type="button" class="menu_product" data-id="${value.id}">&#8942;</button>
                                </div>
                                <img class="card-img-top product-image" src="${value.image}" alt="Imagem do produto">
                                ${value.type_enum == 2
                                ? `<span class="ribbon-inner ribbon-primary" 
                                            style="background-color:${badgeList[value.status_enum]}"> ${statusList[value.status_enum]}
                                        </span>`
                                : ""
                            }
                                <hr class="mt-0">
                                <div class="card-body py-0">
                                    <h5 class="card-title">${value.name}</h5>
                                    <h5 class="card-description">${value.description}</h5>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <p class="text-muted card-text sm">Criado em ${value.created_at}</p>
                                </div>
                            </div>
                            <div class="menu_product_tooltip" data-id="${value.id}">
                                <a id="bt_editar" href="/products/${value.id}/edit" class="mx-20"><span class="o-edit-1 mr-10" />Editar produto</a>
                                ${shopifyProduct == false
                                ? `
                                    <hr class="my-5">
                                    <a id="bt_excluir" href="#" class="mx-20" data-id="${value.id}"><span class="o-bin-1 mr-10" />Excluir produto</a>
                                `
                                : ""
                            }
                            </div>
                        </div>
                        `;
                        $("#data-table-products").append(dados);
                    });

                    function closeTooltips(except = "") {
                        $('.menu_product_tooltip[style*="display: block"]').each(function (_, tooltip) {
                            if (except[0] == tooltip) {
                                return;
                            }

                            tooltip.style.display = 'none';
                        });
                    }

                    $('.menu_product').on('click', function () {
                        var tooltip = $(`.menu_product_tooltip[data-id="${this.dataset.id}"]`)

                        closeTooltips(tooltip);

                        tooltip.toggle();
                    });

                    $('.menu_product').on('focusout', function (event) {
                        if ($(event.relatedTarget).hasClass('menu_product')) {
                            return;
                        }

                        setTimeout(() => closeTooltips(), 200);
                    });

                    $('#bt_excluir').on('click', function (event) {
                        event.preventDefault();

                        $("#bt_excluir_modal").attr('data-id', this.dataset.id);

                        $("#modal-delete").modal();
                    });

                    $('#bt_excluir_modal').on('click', function (event) {
                        event.preventDefault();

                        loadingOnScreen();

                        $.ajax({
                            method: 'DELETE',
                            url: '/api/products/' + this.dataset.id,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function (response) {
                                errorAjaxResponse(response);

                                $("#bt_excluir_modal").attr('data-id', "");
                                loadingOnScreenRemove();
                            },
                            success: function success(response) {
                                alertCustom('success', response.message);

                                window.location = "/products";
                            }
                        });
                    });

                    $("img").on("error", function () {
                        $(this).attr(
                            "src",
                            "https://cloudfox-files.s3.amazonaws.com/produto.svg"
                        );
                    });

                    pagination(response, "products", updateProducts);

                    $(".products-is-empty").hide();
                } else {
                    $("#data-table-products, #pagination-products").html("");
                    $(".products-is-empty").show();
                }
                setTimeout(() => {
                    loadOnAny(".page-content", true);
                }, 2000);
            },
        });
    }

    $(document).on("keypress", function (e) {
        if (e.keyCode == 13) {
            deleteCookie("filterProduct");
            updateProducts();
        }
    });

    $('#new-product-button').on('click', function (event) {
        event.preventDefault();

        $('#new-product-modal').show();
    });


});
