$(document).ready(function () {

    var current_url = window.location.pathname;

    $('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical').css('scrollbar-width', 'none')
    $('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical').removeClass('scrollable scrollable-inverse scrollable-vertical')

    if (current_url.includes('dashboard')) {
        $("#dashboard_img").attr('src', '/modules/global/img/icon-red/dashboard-d.svg');
        $("#dashboard_img").parent().parent().addClass('menu-active');
        $("#dashboard_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('showcase')) {
        $("#showcase_img").attr('src', '/modules/global/img/icon-red/vitrine-d.svg');
        $("#showcase_img").parent().parent().addClass('menu-active');
        $("#showcase_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('sales') || current_url.includes('recovery')) {
        $("#sales_img").attr('src', '/modules/global/img/icon-red/vendas-d.svg');
        $("#sales_img").parent().parent().addClass('menu-active');
        $("#sales-link").addClass('menu-active');
        $("#sales_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('projects')) {
        $("#projects_img").attr('src', '/modules/global/img/icon-red/projetos-d.svg');
        $("#projects_img").parent().parent().addClass('menu-active');
        $("#projects-link").addClass('menu-active');
        $("#projects_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('products')) {
        $("#products_img").attr('src', '/modules/global/img/icon-red/produtos-d.svg');
        $("#products_img").parent().parent().addClass('menu-active');
        $("#products-link").addClass('menu-active');
        $("#products_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('atendimento')) {
        $("#attendance_img").attr('src', '/modules/global/img/icon-red/atendimento-d.svg');
        $("#attendance_img").parent().parent().addClass('menu-active');
        $("#attendance_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('afiliados')) {
        $("#affiliate_img").attr('src', '/modules/global/img/icon-red/afiliados-d.svg');
        $("#affiliate_img").parent().parent().addClass('menu-active');
        $("#affiliate_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('transfers')) {
        $("#finances_img").attr('src', '/modules/global/img/icon-red/financas-d.svg');
        $("#finances_img").parent().parent().addClass('menu-active');
        $("#finances_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('reports')) {
        $("#reports_img").attr('src', '/modules/global/img/icon-red/ferramentas-d.svg');
        $("#reports_img").parent().parent().addClass('menu-active');
        $("#reports_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('apps')) {
        $("#apps_img").attr('src', '/modules/global/img/icon-red/aplicativos-d.svg');
        $("#apps_img").parent().parent().addClass('menu-active');
        $("#apps-link").addClass('menu-active');
        $("#apps_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    } else if (current_url.includes('invitations')) {
        $("#invitations_img").attr('src', '/modules/global/img/icon-red/convites-d.svg');
        $("#invitations_img").parent().parent().addClass('menu-active');
        $("#invitations_img").css('height', '28px').css('width', '28px').css('margin-left', '-5px');
    }

    $(".mm-panels").css('scrollbar-width', 'none');

});

function alertCustom(type, message) {

    swal({
        position: 'bottom',
        type: type,
        toast: 'true',
        title: message,
        showConfirmButton: false,
        timer: 6000
    });
}

$(document).ajaxStart(function (event, jqXHR, ajaxOptions, data) {
    /*$(".foxLoader").fadeIn('slow').attr('src', '../modules/global/gif/cloudfox-loading-1.gif');*/
    $("#loader").addClass("loader").fadeIn('slow');
    $("#loaderCard").addClass("loader").fadeIn('slow');
})

$(document).ajaxError(function (event, jqXHR, ajaxOptions, data) {
    //$(".loading").css("visibility", "hidden");
    /*$(".foxLoader").fadeOut('slow');*/
    /*setTimeout(function(){
        $("#loader").removeClass('loader');
        $(".foxLoader").fadeOut('slow');
    },2000);*/
    $("#loader").removeClass('loader').fadeOut('slow');
    $("#loaderCard").removeClass('loader').fadeOut('slow');

})

$(document).ajaxSuccess(function (event, jqXHR, ajaxOptions, data) {
    //$(".loading").css("visibility", "hidden");
    /*$(".foxLoader").fadeOut('slow');*/
    /*setTimeout(function () {
        $(".foxLoader").fadeOut('slow');
        $(".loaderCard").removeClass('loaderCard').fadeOut('slow');
    }, 2000);*/
    $(".loaderCard").removeClass('loaderCard').fadeOut('slow');
})

function loading(elementId, loaderClass) {

    if (loaderClass == '') {
        $(elementId).html('');
        $(elementId).append('<div class="loading"></div>');
    } else if (loaderClass == '#loaderCard') {
        $(elementId).append('<a class="loaderCard"></a>');
    }
}

function loadingOnScreen() {
    $('#loadingOnScreen').html('');
    $('#loadingOnScreen').append("<div class='loading2'><div class='loader'></div></div>")
}

function loadingOnScreenRemove() {
    $('#loadingOnScreen').html('');
    $('#btn-modal').show();
}

function loadOnNotification(whereToLoad) {
    $(whereToLoad).html('');
    $(whereToLoad).append("<div class='loading' style='width:346px; height:150px'>" +
        "<span class='loaderNotification' >" +
        "</span>" +
        "</div>");
}

function loadOnModal(whereToLoad) {

    $(whereToLoad).children().hide('fast');
    $('#modal-title').html('Carregando ...')
    $(whereToLoad).append("<div id='loaderModal' class='loadingModal'>" +
        "<div class='loaderModal'>" +
        "</div>" +
        "</div>");
    $('#loadingOnScreen').append("<div class='blockScreen'></div>");
}

function loadOnTable(whereToLoad, tableReference) {
    $(whereToLoad).html('');
    $(tableReference).removeClass('table-striped');
    $(whereToLoad).append("<tr id='loaderLine'>" +
        "<td colspan='12' align='center' class='loadingTable' style='height:100px'>" +
        "<a id='loader' class='loaderTable'></a>" +
        "</td>" +
        "</tr>");
}

function loadOnAny(element, stop = false) {
    $('.element-loading').remove();
    if (!stop) {
        let loading = `<span class="loader loader-circle loader-any" style=""></span>`;
        let loader = $(element).clone().addClass('element-loading').html(loading);
        $(element).hide();
        $(element).parent().append(loader);
        loader.show();
    } else {
        if (!$(element).hasClass('tab-pane') ||
            ($(element).hasClass('tab-pane') &&
                $(element).hasClass('active'))) {
            $(element).show();
        }
    }
}

function modalClear(modalBody) {
    $(modalBody).html('');
}

function messageSwalSuccess(swalType, swalTitle, swalHtml, swalCloseButton, swalConfirmButton, swalFooter) {
    swal({
        type: swalType,
        title: swalTitle,
        html: swalHtml,
        showCloseButton: swalCloseButton,
        showConfirmButton: swalConfirmButton,
        footer: swalFooter
    })
}

$(document).ajaxComplete(function (jqXHR, textStatus) {

    switch (textStatus.status) {
        case 200:

            break;
        case 401:
            window.location.href = "/";
            break;
        case 404:
            break;
        case 500:
            break;
        case 413:
            alertCustom('error', 'O tamanho da imagem não pode exceder 2mb.')
            break;
        case 422:
            break;
        case 419:
            window.location.href = "/";
            break;
    }
});

$('.mm-panels.scrollable.scrollable-inverse.scrollable-vertical.is-enabled').attr('overflow', 'hidden')

function pagination(response, model, callback) {

    $("#pagination-" + model).html("");

    var first_page = "<button id='first_page' class='btn nav-btn'>1</button>";

    if (response.meta.last_page === 1) {
        return false;
    }

    $("#pagination-" + model).append(first_page);

    if (response.meta.current_page === 1) {
        $("#first_page").attr('disabled', true).addClass('nav-btn').addClass('active');
    }

    $('#first_page').on("click", function () {
        callback('?page=1');
    });

    for (x = 3; x > 0; x--) {

        if (response.meta.current_page - x <= 1) {
            continue;
        }

        $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

        $('#page_' + (response.meta.current_page - x)).on("click", function () {
            callback('?page=' + $(this).html());
        });
    }

    if (response.meta.current_page !== 1 && response.meta.current_page !== response.meta.last_page) {
        var current_page = "<button id='current_page' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

        $("#pagination-" + model).append(current_page);

        $("#current_page").attr('disabled', true).addClass('nav-btn').addClass('active');
    }
    for (x = 1; x < 4; x++) {

        if (response.meta.current_page + x >= response.meta.last_page) {
            continue;
        }

        $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

        $('#page_' + (response.meta.current_page + x)).on("click", function () {
            callback('?page=' + $(this).html());
        });
    }

    if (response.meta.last_page !== 1) {
        var last_page = "<button id='last_page' class='btn nav-btn'>" + response.meta.last_page + "</button>";

        $("#pagination-" + model).append(last_page);

        if (response.meta.current_page === response.meta.last_page) {
            $("#last_page").attr('disabled', true).addClass('nav-btn').addClass('active');
        }

        $('#last_page').on("click", function () {
            callback('?page=' + response.meta.last_page);
        });
    }
    $('table').addClass('table-striped')

}

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).html()).select();
    document.execCommand("copy");
    $temp.remove();
    alertCustom('success', 'Link copiado com sucesso')
}

function errorAjaxResponse(response) {
    if (response.status === 422) {
        for (error in response.responseJSON.errors) {
            alertCustom('error', response.responseJSON.errors[error]);
        }
    } else if (response.status === 404) { // Conteudo Não pode ser encontrado
        for (error in response.responseJSON.errors) {
            alertCustom('error', response.responseJSON.errors[error]);
        }
    } else if (response.status === 403) { // Não tem acesso ao conteudo
        for (error in response.responseJSON.errors) {
            alertCustom('error', response.responseJSON.errors[error]);
        }
    } else if (response.status === 401) { // Não esta autenticado
        window.location.href = window.location.origin + '/';
        for (error in response.responseJSON.errors) {
            alertCustom('error', response.responseJSON.errors[error]);
        }
    } else {
        alertCustom('error', response.responseJSON.message);
    }
}

function extractIdFromPathName() {
    let urlParams = window.location.pathname.split('/');
    if (urlParams.length >= 2 && urlParams[urlParams.length - 1] == 'edit') {
        return urlParams[urlParams.length - 2];
    } else if (urlParams.length > 0) {
        return urlParams[urlParams.length - 1];
    } else {
        return '';
    }
}

function isEmptyValue(value) {
    return value.length !== 0;
}

function fillAllFormInputsWithModel(formId, model, lists = null, functions = null) {
    let formData = $("#" + formId + " input, #" + formId + " select, #" + formId + " textarea");
    if (formData === undefined || formData === null) {
        return false;
    }
    formData.each(
        function (key, item) {
            let element = $(this);
            if (element.is('input')) {
                fillInputWithModelField(element, model);
            } else if (element.is('select')) {
                if (lists === undefined || lists === null) {
                    console.log('select lists not available!');
                } else {
                    fillSelectAndCheckWithModelFields(
                        element,
                        lists,
                        model,
                        functions);
                }

            } else if (element.is('textarea')) {
                console.log('textarea type not implemented!');
            } else {
                console.log('element type not implemented: ' + element[0].nodeName);
            }
            // console.log('Type: ' + input.attr('type') + ', Name: ' + input.attr('name') + ', Value: ' + input.val());
        }
    )
    return true;
}

function fillInputWithModelField(input, model) {
    let field = input.attr('name');
    if (field === undefined || model[field] === undefined || model[field] === "") {
        return false;
    }
    let value = model[field];
    switch (input.attr('type')) {
        case "text":
            if (input.data('mask') !== undefined) {
                let mask = input.data('mask').mask;
                input.unmask().val(value).mask(mask, {reverse: true});
            } else {
                input.attr('value', value);
            }
            break;
        case "hidden":
            input.attr('value', value);
            break;
        default:
            //Do Nothing
            console.log('Input type not implemented: ' + input.attr('type'));
            break;
    }
    return true;
}

function fillSelectAndCheckWithModelFields(select, items, modelSelected = null, functions = null) {
    let field = select.attr('name');
    if (field === undefined ||
        (modelSelected !== null && modelSelected !== undefined && modelSelected[field] === undefined) ||
        items === undefined || items[field] === undefined || items.length === 0) {
        return false;
    }
    let customItemsFunction = defaultSelectItemsFunction;
    if (customItemsFunction !== undefined && customItemsFunction !== null && functions[field] !== undefined && functions[field] !== null) {
        customItemsFunction = functions[field];
    }
    let value = "";
    if (modelSelected !== null && modelSelected !== undefined) {
        value = modelSelected[field];
    }
    items[field].forEach(
        function (item) {
            let optionItem = customItemsFunction(item);
            let option = new Option(optionItem.text, optionItem.value);
            if (value !== "" && value === optionItem.value) {
                option.selected = true;
            }
            /// jquerify the DOM object 'o' so we can use the html method
            // $(option).html("option text");
            select.append(option);
        }
    )
    return true;
}

function defaultSelectItemsFunction(item) {
    return {value: item.id_code, text: item.name};
}

