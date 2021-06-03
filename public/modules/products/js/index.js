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
                        dados = `
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                            <div class="card shadow mb-20 mx-0" style="flex: 1 1 100%">
                                <img style="min-height: 200px; cursor: pointer;"
                                    class="card-img-top product-image p-15" src="${value.image}" data-link="${value.link}"
                                    alt="Imagem do produto" data-code="${value.id}">
                                ${value.type_enum == 2
                                ? `<span class="ribbon-inner ribbon-primary" 
                                            style="background-color:${badgeList[value.status_enum]}"> ${statusList[value.status_enum]}
                                        </span>`
                                : ""
                            }
                                <hr>
                                <div class="card-body py-0">
                                    <h5 class="card-title">${value.name}</h5>
                                    <h5 class="card-description">${value.description}</h5>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <p class="text-muted card-text sm">Criado em  ${value.created_at}</p>
                                </div>
                            </div>
                        </div>
                        `;
                        // dados = '';
                        // dados += '<div class="col-xl-3 col-md-6 d-flex align-items-stretch">';
                        // dados += '<div class="card shadow" style="cursor:pointer;width:100%; ">';
                        // dados += '<img style="min-height: 200px;" class="card-img-top product-image" src="' + value.image + '"  ' +
                        //     'data-link=" + value.link + " alt="Imagem do produto" data-code="' + value.id + '">' +
                        //     '<span class="ribbon-inner ribbon-primary" style="background-color:' + badgeList[value.status_enum] + '">' + statusList[value.status_enum] + '</span>' +
                        //     '<div class="card-body">' +
                        //     '<div class="row align-items-end justify-content-between">' +
                        //     '<div class="col-10">' +
                        //     '<h5 class="card-title">' + value.name + '</h5>' +
                        //     '<h5 class="">' + value.description + '</h5>' +
                        //     '<p class="card-text sm">Criado em  ' + value.created_at + '</p>' +
                        //     '<p class="card-text sm">ID: ' + value.id_view + '</p>' +
                        //     '</div>' +
                        //     '</div>' +
                        //     '</div>' +
                        //     '</div>' +
                        //     '</div>';

                        $("#data-table-products").append(dados);
                    });

                    $(".product-image").on("click", function () {
                        var product = {
                            type: $("#type-products option:selected").val(),
                            project: $(
                                "#select-projects option:selected"
                            ).val(),
                            nameProduct: $("#name").val(),
                            page: pageCurrent !== null ? pageCurrent : null,
                        };
                        setCookie("filterProduct", 1, product);
                        if (verifyAccountFrozen() == false) {
                            window.location.href =
                                "/products/" + $(this).data("code") + "/edit";
                        }
                    });

                    $("img").on("error", function () {
                        $(this).attr(
                            "src",
                            "https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/product-default.png"
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
    })

});
